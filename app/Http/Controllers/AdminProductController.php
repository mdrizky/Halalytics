<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductModel;
use App\Models\ScanModel;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
class AdminProductController extends Controller
{
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

        // Clone for Local Products
        $localQuery = (clone $baseQuery)->where('source', 'local');
        $localProducts = $localQuery->orderBy('id_product', 'desc')->paginate(10, ['*'], 'local_page')->withQueryString();

        // Clone for API Products (All non-local sources)
        $apiQuery = (clone $baseQuery)->where('source', '!=', 'local');
        $apiProducts = $apiQuery->orderBy('id_product', 'desc')->paginate(10, ['*'], 'api_page')->withQueryString();
        
        $categories = KategoriModel::all();
        
        return view('admin.product-new', compact('localProducts', 'apiProducts', 'categories'));
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
            'kategori_id' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data = $request->only([
            'nama_product', 'barcode', 'komposisi', 'status', 'info_gizi', 'kategori_id'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/products', $filename);
            $data['image'] = '/storage/products/' . $filename;
        }

        ProductModel::create(array_merge($data, [
        'source' => 'local',
        'active' => true,
        'verification_status' => 'verified'
    ]));

        return redirect()->route('admin.product.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    // form edit produk
    public function edit($id)
    {
        $product = ProductModel::findOrFail($id);
        $categories = KategoriModel::all();
        return view('admin.product_edit', compact('product', 'categories'));
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
            'kategori_id' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data = $request->only([
            'nama_product', 'barcode', 'komposisi', 'status', 'verification_status', 'info_gizi', 'kategori_id'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/products', $filename);
            $data['image'] = '/storage/products/' . $filename;
            
            // Delete old image if exists
            if ($product->image) {
                $oldPath = str_replace('/storage/', 'public/', $product->image);
                if (file_exists(storage_path('app/' . $oldPath))) {
                    unlink(storage_path('app/' . $oldPath));
                }
            }
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
        
        return response()->json([
            'success' => true,
            'active' => $product->active
        ]);
    }

    // Cari produk by barcode (lokal + internasional)
    public function searchByBarcode($barcode)
    {
        // Cek dulu di database lokal
        $product = ProductModel::where('barcode', $barcode)->first();

        if ($product) {
            // Simpan scan otomatis
            ScanModel::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id_product,
                'nama_produk' => $product->nama_product,
                'barcode' => $product->barcode,
                'kategori' => $product->kategori_id ? $product->kategori_id : 'Tidak Ada',
                'status_halal' => $product->status,
                'status_kesehatan' => 'syubhat',
                'tanggal_scan' => now(),
            ]);

            return response()->json([
                'status' => 'local',
                'data' => $product
            ]);
        }

        // Kalau tidak ada → ambil dari API OpenFoodFacts
        $url = "https://world.openfoodfacts.org/api/v0/product/{$barcode}.json";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['status']) && $data['status'] == 1) {
                $productData = $data['product'];

                // Guess category
                $guessName = ' ' . strtolower($productData['product_name'] ?? '') . ' ';
                $kategoriId = null;
                $rules = [
                    12 => ['vitamin', 'suplemen', 'supplement', 'tablet', 'panadol', 'paracetamol'],
                    11 => ['sabun', 'soap', 'shampoo', 'sampo', 'lotion', 'parfum', 'perfume', 'lulur'],
                    2 => ['drink', 'minuman', 'teh', 'tea', 'kopi', 'coffee', 'jus', 'juice', 'soda', 'water', 'air mineral'],
                    5 => ['susu', 'milk', 'cheese', 'keju', 'yogurt', 'butter', 'indomilk', 'frisian flag', 'ultra milk'],
                    3 => ['snack', 'keripik', 'chips', 'wafer', 'biskuit', 'chiki', 'oreo', 'pringles', 'chocolate', 'cokelat'],
                    4 => ['bumbu', 'seasoning', 'kecap', 'sauce', 'saus', 'garam', 'gula', 'sugar'],
                    1 => ['mie', 'noodle', 'nasi', 'rice', 'roti', 'bread', 'sereal', 'indomie', 'pop mie'],
                ];

                foreach ($rules as $id => $keywords) {
                    foreach ($keywords as $keyword) {
                        if (strpos($guessName, $keyword) !== false) {
                            $kategoriId = $id;
                            break 2;
                        }
                    }
                }

                $newProduct = ProductModel::create([
                    'nama_product' => $productData['product_name'] ?? 'Produk Tidak Dikenal',
                    'barcode'      => $barcode,
                    'komposisi'    => $productData['ingredients_text'] ?? null,
                    'status'       => 'syubhat',
                    'image'        => $productData['image_url'] ?? null,
                    'info_gizi'    => isset($productData['nutriments']) ? json_encode($productData['nutriments']) : null,
                    'kategori_id'  => $kategoriId,
                    'source'       => 'OpenFoodFacts',
                ]);

                // Simpan scan internasional
                ScanModel::create([
                    'user_id' => Auth::id(),
                    'product_id' => $newProduct->id_product,
                    'nama_produk' => $newProduct->nama_product,
                    'barcode' => $newProduct->barcode,
                    'kategori' => 'Internasional',
                    'status_halal' => 'syubhat',
                    'status_kesehatan' => 'syubhat',
                    'tanggal_scan' => now(),
                ]);

                return response()->json([
                    'status' => 'international',
                    'data' => $newProduct
                ]);
            }
        }

        return response()->json([
            'status' => 'not_found',
            'message' => 'Produk tidak ditemukan di database lokal maupun internasional'
        ]);
    }
}
