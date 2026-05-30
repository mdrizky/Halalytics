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
     * Display product detail
     */
    public function show($id)
    {
        $product = ProductModel::with('kategori')->withCount('scans')->find($id);
        
        // If not found in ProductModel, check Medicine model
        if (!$product) {
            $medicine = \App\Models\Medicine::where('id_medicine', $id)->first();
            if ($medicine) {
                return view('admin.product_show', ['product' => $medicine, 'type' => 'medicine']);
            }
            return redirect()->route('admin.product.index')->with('error', 'Product not found');
        }

        return view('admin.product_show', ['product' => $product, 'type' => 'general']);
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
        $offSources = ['open_food_facts', 'openfoodfacts', 'off_api', 'off'];
        
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

        $obfSources = ['open_beauty_facts', 'openbeautyfacts', 'obf_api', 'obf'];

        // 1. Local products (internal/admin-managed)
        $localQuery = (clone $baseQuery)->where(function ($query) {
            $query->whereNull('source')
                ->orWhereRaw('LOWER(source) = ?', ['local']);
        });
        $localProducts = $localQuery->orderBy('id_product', 'desc')->paginate(10, ['*'], 'local_page')->withQueryString();

        // 2. Open Food Facts
        $offQuery = (clone $baseQuery)->whereIn('source', $offSources);
        $offProducts = $offQuery->orderBy('id_product', 'desc')->paginate(10, ['*'], 'off_page')->withQueryString();

        // 3. Open Beauty Facts
        $obfQuery = (clone $baseQuery)->whereIn('source', $obfSources);
        $obfProducts = $obfQuery->orderBy('id_product', 'desc')->paginate(10, ['*'], 'obf_page')->withQueryString();

        // 4. OpenFDA Medicines
        $fdaQuery = \App\Models\Medicine::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $fdaQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand_name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        $fdaProducts = $fdaQuery->whereIn('source', ['openfda', 'open_fda'])->orderBy('id_medicine', 'desc')->paginate(10, ['*'], 'fda_page')->withQueryString();

        $categories = KategoriModel::orderBy('nama_kategori')->get();
        $productStats = [
            'local_total' => (clone $localQuery)->count(),
            'local_verified' => (clone $localQuery)->where('verification_status', 'verified')->count(),
            'off_total' => (clone $offQuery)->count(),
            'obf_total' => (clone $obfQuery)->count(),
            'fda_total' => (clone $fdaQuery)->whereIn('source', ['openfda', 'open_fda'])->count(),
        ];

        return view('admin.product', compact('localProducts', 'offProducts', 'obfProducts', 'fdaProducts', 'categories', 'productStats'));
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

    /**
     * External API Synchronization
     */
    public function syncExternal(Request $request, $source)
    {
        try {
            $count = 0;
            switch ($source) {
                case 'off':
                    $service = app(\App\Services\OpenFoodFactsService::class);
                    // Fetch some popular food categories to populate the local cache
                    $categories = ['snacks', 'beverages', 'dairy', 'biscuits'];
                    foreach ($categories as $cat) {
                        $result = $service->searchProducts($cat, 10);
                        if ($result['success'] && !empty($result['products'])) {
                            foreach ($result['products'] as $p) {
                                $this->saveExternalProduct([
                                    'barcode' => $p['barcode'] ?? $p['code'] ?? null,
                                    'product_name' => $p['name'] ?? null,
                                    'ingredients_text' => $p['ingredients'] ?? null,
                                    'image_url' => $p['image'] ?? null,
                                ], 'open_food_facts');
                                $count++;
                            }
                        }
                    }
                    break;
                case 'obf':
                    $service = app(\App\Services\External\OpenBeautyFactsService::class);
                    $categories = ['face-creams', 'shampoos', 'soaps'];
                    foreach ($categories as $cat) {
                        $products = $service->search($cat, 1, 10);
                        foreach ($products as $p) {
                            $this->saveExternalProduct([
                                'barcode' => $p['code'] ?? null,
                                'product_name' => $p['product_name'] ?? null,
                                'ingredients_text' => $p['ingredients_text'] ?? null,
                                'image_url' => $p['image_url'] ?? null,
                            ], 'open_beauty_facts');
                            $count++;
                        }
                    }
                    break;
                case 'fda':
                    $service = app(\App\Services\External\OpenFDAService::class);
                    // OpenFDA search is different, usually by brand or class
                    $terms = ['paracetamol', 'ibuprofen', 'amoxicillin'];
                    foreach ($terms as $term) {
                        $results = $service->searchDrug($term);
                        foreach ($results as $p) {
                            $openfda = $p['openfda'] ?? [];
                            $this->saveExternalMedicine([
                                'id' => $p['set_id'] ?? null,
                                'brand_name' => $openfda['brand_name'][0] ?? null,
                                'generic_name' => $openfda['generic_name'][0] ?? null,
                                'labeler_name' => $openfda['manufacturer_name'][0] ?? null,
                                'product_ndc' => $openfda['product_ndc'][0] ?? null
                            ]);
                            $count++;
                        }
                    }
                    break;
            }

            return back()->with('success', "Successfully synced $count items from $source.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Sync $source failed: " . $e->getMessage());
            return back()->with('error', "Sync failed: " . $e->getMessage());
        }
    }

    private function saveExternalProduct($data, $source)
    {
        // Map source to allowed enum values in database
        $allowedSources = ['local', 'open_food_facts', 'umkm', 'user_ocr', 'open_beauty_facts', 'openfda'];
        $dbSource = in_array($source, $allowedSources) ? $source : 'open_food_facts';

        return ProductModel::updateOrCreate(
            ['barcode' => $data['barcode']],
            [
                'nama_product' => $data['product_name'] ?? 'Unknown External Product',
                'komposisi' => $data['ingredients_text'] ?? null,
                'status' => 'syubhat', // Default for external
                'source' => $dbSource,
                'image' => $data['image_url'] ?? null,
                'verification_status' => 'needs_review'
            ]
        );
    }

    private function saveExternalMedicine($data)
    {
        // Use name as the key for updateOrCreate since it's unique in database
        $name = $data['brand_name'] ?? $data['generic_name'] ?? 'Unknown Medicine';
        
        return \App\Models\Medicine::updateOrCreate(
            ['name' => $name], // Use name as unique identifier
            [
                'brand_name' => $data['brand_name'] ?? null,
                'generic_name' => $data['generic_name'] ?? null,
                'manufacturer' => $data['labeler_name'] ?? null,
                'source' => 'openfda',
                'halal_status' => 'syubhat',
                'barcode' => $data['product_ndc'] ?? null,
                'active' => true
            ]
        );
    }

    // form edit produk
    public function edit($id)
    {
        $product = ProductModel::find($id);
        $type = 'general';

        if (!$product) {
            $product = \App\Models\Medicine::where('id_medicine', $id)->firstOrFail();
            $type = 'medicine';
        }

        $categories = KategoriModel::all();
        
        $imageData = $this->imageService->getImages(
            productName: $product->nama_product ?? $product->name,
            barcode: $product->barcode,
            source: $product->source ?? 'local',
            metadata: [
                'category' => $type === 'general' ? optional($product->kategori)->nama_kategori : ($product->category ?? 'Medicine'),
                'existing_image' => $type === 'general' ? $product->getRawOriginal('image') : $product->image_url,
                'exclude_id' => $id,
            ]
        );
        
        return view('admin.product_edit', compact('product', 'categories', 'imageData', 'type'));
    }

    // update produk
    public function update(Request $request, $id)
    {
        $product = ProductModel::find($id);
        $medicine = null;

        if (!$product) {
            $medicine = \App\Models\Medicine::where('id_medicine', $id)->firstOrFail();
        }

        $request->validate([
            'nama_product' => 'required_without:name|string|max:255',
            'name' => 'required_without:nama_product|string|max:255',
            'barcode' => 'required|string',
            'status' => 'nullable|in:halal,tidak halal,syubhat',
            'halal_status' => 'nullable|in:halal,haram,syubhat,unknown',
        ]);

        if ($product) {
            $data = $request->only(['nama_product', 'barcode', 'komposisi', 'status', 'verification_status', 'info_gizi', 'price', 'kategori_id']);
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/products');
                $data['image'] = str_replace('public/', 'storage/', $path);
            }
            $product->update($data);
        } else {
            $data = $request->only(['name', 'generic_name', 'brand_name', 'barcode', 'halal_status', 'manufacturer', 'dosage_form']);
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('public/medicines');
                $medicine->image_url = str_replace('public/', 'storage/', $path);
            }
            $medicine->update($data);
        }

        return redirect()->route('admin.product.index')->with('success', 'Data produk berhasil diperbarui!');
    }

    // hapus produk
    public function destroy($id)
    {
        $product = ProductModel::find($id);
        
        if ($product) {
            $product->delete();
        } else {
            $medicine = \App\Models\Medicine::where('id_medicine', $id)->firstOrFail();
            $medicine->delete();
        }

        return redirect()->route('admin.product.index')->with('success', 'Data berhasil dihapus!');
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
