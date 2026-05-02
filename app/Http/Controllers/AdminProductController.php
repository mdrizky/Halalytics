<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductModel;
use App\Models\ScanModel;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\Auth;
class AdminProductController extends Controller
{
    protected $universalService;
    protected $notificationService;
    protected $imageService;

    public function __construct(
        \App\Services\UniversalProductService $universalService,
        \App\Services\AdminBroadcastNotificationService $notificationService,
        \App\Services\ProductImageService $imageService
    )
    {
        $this->universalService = $universalService;
        $this->notificationService = $notificationService;
        $this->imageService = $imageService;
    }

    /**
     * AI-Assisted Batch Verification
     */
    public function batchAiVerify(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No products selected']);
        }

        $products = ProductModel::whereIn('id_product', $ids)->get();
        $gemini = app(\App\Services\GeminiService::class);
        $results = [];

        foreach ($products as $product) {
            $prompt = "Analyze these ingredients for '{$product->nama_product}': \"{$product->komposisi}\". 
            Determine Halal status. 
            Format as JSON: {'id': {$product->id_product}, 'status': 'halal/haram/syubhat', 'reason': 'text'}";
            
            $analysis = $gemini->generateCustomContent($prompt);
            
            // Check if analysis returned the expected structure
            if (isset($analysis['status'])) {
                $results[] = [
                    'id' => $product->id_product,
                    'name' => $product->nama_product,
                    'old_status' => $product->status,
                    'suggested_status' => $analysis['status'] == 'haram' ? 'tidak halal' : $analysis['status'],
                    'reason' => $analysis['reason'] ?? 'AI Analysis'
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    /**
     * Apply AI Suggestions
     */
    public function applyBatchAiVerify(Request $request)
    {
        $suggestions = $request->input('suggestions', []);
        
        foreach ($suggestions as $id => $status) {
            $product = ProductModel::find($id);
            if ($product) {
                $product->status = $status;
                $product->verification_status = 'verified';
                $product->save();
            }
        }

        return response()->json(['success' => true]);
    }

    // tampil semua produk dengan filter dan pagination
    public function admin_product(Request $request)
    {
        $externalFoodSources = ['open_food_facts', 'openfoodfacts', 'off_api', 'off'];

        // Base query with relations
        $baseQuery = ProductModel::with('kategori')->withCount('scans');

        // Apply shared filters
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function($q) use ($search) {
                $q->where('nama_product', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('category')) {
            $baseQuery->where('kategori_id', $request->category);
        }
        
        if ($request->filled('halal_status')) {
            $baseQuery->where('status', $request->halal_status);
        }

        if ($request->filled('active')) {
            $baseQuery->where('active', (int) $request->active === 1);
        }

        // Local products are internal/admin-managed records only.
        $localQuery = (clone $baseQuery)->where(function ($query) {
            $query->whereNull('source')
                ->orWhereRaw('LOWER(source) = ?', ['local']);
        });
        $localProducts = $localQuery->orderBy('id_product', 'desc')->paginate(10, ['*'], 'local_page')->withQueryString();

        // External/imported products on this page are restricted to Open Food Facts only.
        $apiQuery = (clone $baseQuery)->where(function ($query) use ($externalFoodSources) {
            foreach ($externalFoodSources as $index => $source) {
                if ($index === 0) {
                    $query->whereRaw('LOWER(COALESCE(source, "")) = ?', [$source]);
                    continue;
                }

                $query->orWhereRaw('LOWER(COALESCE(source, "")) = ?', [$source]);
            }
        });
        $apiProducts = $apiQuery->orderBy('id_product', 'desc')->paginate(10, ['*'], 'api_page')->withQueryString();

        $categories = KategoriModel::orderBy('nama_kategori')->get();
        $productStats = [
            'local_total' => (clone $localQuery)->count(),
            'local_verified' => (clone $localQuery)->where('verification_status', 'verified')->count(),
            'external_total' => (clone $apiQuery)->count(),
            'external_review' => (clone $apiQuery)->where('verification_status', '!=', 'verified')->count(),
        ];

        return view('admin.product', compact('localProducts', 'apiProducts', 'categories', 'productStats'));
    }

    // OCR Scanner page
    public function ocrScanner()
    {
        return view('admin.ocr_scanner');
    }

    // form tambah produk
    public function create()
    {
        $categories = KategoriModel::all();
        return view('admin.product_tambah', compact('categories'));
    }

    // simpan produk baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_product' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode',
            'komposisi' => 'nullable|string',
            'status' => 'required|in:halal,tidak halal,syubhat',
            'info_gizi' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'kategori_id' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data = $request->only([
            'nama_product', 'barcode', 'komposisi', 'status', 'info_gizi', 'price', 'kategori_id'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/products', $filename);
            $data['image'] = '/storage/products/' . $filename;
        }

        $product = ProductModel::create(array_merge($data, [
        'source' => 'local',
        'active' => true,
        'verification_status' => 'verified'
    ]));

        $this->notificationService->broadcast(
            'Produk baru ditambahkan',
            'Admin menambahkan produk: ' . $product->nama_product,
            'product',
            [
                'product_id' => (string)$product->id_product,
                'barcode' => (string)$product->barcode,
                'action_type' => 'open_product',
                'action_value' => (string)$product->barcode,
            ]
        );

        return redirect()->route('admin.product.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    // form edit produk
    public function edit($id)
    {
        $product = ProductModel::findOrFail($id);
        $categories = KategoriModel::all();
        
        $imageData = $this->imageService->getImages(
            productName: $product->nama_product,
            barcode: $product->barcode,
            source: $product->source ?? 'local',
            metadata: [
                'category' => optional($product->kategori)->nama_kategori,
                'existing_image' => $product->getRawOriginal('image'),
                'exclude_id' => $product->id_product,
            ]
        );
        
        return view('admin.product_edit', compact('product', 'categories', 'imageData'));
    }

    // update produk
    public function update(Request $request, $id)
    {
        $product = ProductModel::findOrFail($id);

        $request->validate([
            'nama_product' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode,'.$id.',id_product',
            'komposisi' => 'nullable|string',
            'status' => 'required|in:halal,tidak halal,syubhat',
            'verification_status' => 'nullable|in:verified,needs_review,rejected',
            'info_gizi' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'kategori_id' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_url' => 'nullable|url',
        ]);

        $data = $request->only([
            'nama_product', 'barcode', 'komposisi', 'status', 'verification_status', 'info_gizi', 'price', 'kategori_id'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/products', $filename);
            $data['image'] = '/storage/products/' . $filename;
            
            // Delete old image if exists
            $oldImage = $product->getRawOriginal('image');
            if ($oldImage && str_starts_with($oldImage, '/storage/')) {
                $oldPath = str_replace('/storage/', 'public/', $oldImage);
                if (file_exists(storage_path('app/' . $oldPath))) {
                    unlink(storage_path('app/' . $oldPath));
                }
            }
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        }

        $product->update($data);

        return redirect()->route('admin.product.index')->with('success', 'Produk berhasil diupdate!');
    }

    // hapus produk
    public function destroy($id)
    {
        $product = ProductModel::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.product.index')->with('success', 'Produk berhasil dihapus!');
    }

    // Toggle product active status
    public function toggleActive($id)
    {
        $product = ProductModel::findOrFail($id);
        $product->active = !$product->active;
        $product->save();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'active' => $product->active
            ]);
        }

        return redirect()
            ->route('admin.product.index', request()->query())
            ->with('success', 'Status produk berhasil diperbarui.');
    }

    // Cari produk by barcode (lokal + internasional)
    public function searchByBarcode($barcode)
    {
        $result = $this->universalService->findProduct($barcode);
        if (!$result['found']) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Produk tidak ditemukan di database lokal maupun API eksternal'
            ]);
        }

        $source = $result['source'] ?? 'unknown';
        $standardized = $result['standardized'] ?? [];
        $model = $result['data'] ?? null;

        // Save scan if product row exists.
        $authUser = Auth::user();
        $authUserId = $authUser->id_user ?? $authUser->id ?? null;

        if ($model instanceof ProductModel && is_numeric($authUserId)) {
            ScanModel::create([
                'user_id' => (int) $authUserId,
                'product_id' => $model->id_product,
                'nama_produk' => $model->nama_product,
                'barcode' => $model->barcode,
                'kategori' => $standardized['category'] ?? 'Tidak Ada',
                'status_halal' => $model->status ?? 'syubhat',
                'status_kesehatan' => 'syubhat',
                'tanggal_scan' => now(),
            ]);
        }

        return response()->json([
            'status' => $source === 'local' || $source === 'local_cache' ? 'local' : 'external',
            'source' => $source,
            'data' => $model,
            'standardized' => $standardized,
        ]);
    }
}
