<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Services\OpenFoodFactsService;
use Illuminate\Http\Request;

class OpenFoodFactsAdminController extends Controller
{
    protected $offService;

    public function __construct(OpenFoodFactsService $offService)
    {
        $this->offService = $offService;
    }

    /**
     * Main OpenFoodFacts search page
     */
    public function index()
    {
        return view('admin.products.openfoodfacts.index');
    }

    /**
     * Search OpenFoodFacts API
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3'
        ]);

        $results = $this->offService->searchProducts($request->query, $request->page ?? 1);

        if ($request->ajax()) {
            return response()->json($results);
        }

        return view('admin.products.openfoodfacts.search', [
            'results' => $results,
            'query' => $request->query
        ]);
    }

    /**
     * Preview product before import
     */
    public function preview($offId)
    {
        $result = $this->offService->getProductByBarcode($offId);

        if (!$result['success'] || !isset($result['product'])) {
            return redirect()->route('admin.products.off.index')
                ->with('error', 'Produk tidak ditemukan di OpenFoodFacts');
        }

        $offProduct = $result['product'];

        // Check if already in local database
        $existingProduct = ProductModel::where('off_product_id', $offId)
            ->orWhere('barcode', $offProduct['barcode'])
            ->first();

        $halalIssues = $this->offService->analyzeIngredients($offProduct['ingredients_text'] ?? '');

        return view('admin.products.openfoodfacts.preview', [
            'offProduct' => $offProduct,
            'existingProduct' => $existingProduct,
            'halalIssues' => $halalIssues
        ]);
    }

    /**
     * Import product from OpenFoodFacts to local database
     */
    public function import(Request $request, $offId)
    {
        $result = $this->offService->getProductByBarcode($offId);

        if (!$result['success'] || !isset($result['product'])) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan di database global');
        }

        $offProduct = $result['product'];

        // Check if already exists
        $existing = ProductModel::where('off_product_id', $offId)->first();
        
        if ($existing) {
            return redirect()->route('admin.product.edit', $existing->id_product)
                ->with('info', 'Produk sudah pernah diimport. Anda dapat mengeditnya di sini.');
        }

        // Import with admin-specified halal status
        $product = ProductModel::create([
            'nama_product' => $offProduct['product_name'],
            'barcode' => $offProduct['barcode'],
            'image' => $offProduct['image_url'] ?? $offProduct['image_front_url'],
            'komposisi' => json_encode($offProduct['ingredients_list']),
            'info_gizi' => json_encode($offProduct['nutriments']),
            'source' => 'open_food_facts',
            'off_product_id' => $offProduct['_id'],
            'off_last_synced' => now(),
            'is_imported_from_off' => true,
            'verification_status' => 'verified', // Admin import = verified
            'status' => $request->input('halal_status', 'syubhat'),
            'data_completeness_score' => $offProduct['completeness'] ?? 0,
            'active' => true,
            'needs_manual_review' => false
        ]);

        return redirect()->route('admin.product.edit', $product->id_product)
            ->with('success', 'Produk berhasil diimport! Silakan tinjau dan lengkapi data jika diperlukan.');
    }

    /**
     * Show auto-imported products that need review
     */
    public function autoImported()
    {
        $products = ProductModel::where('source', 'open_food_facts')
            ->where('is_imported_from_off', true)
            ->where('verification_status', 'needs_review')
            ->orderBy('auto_imported_at', 'desc')
            ->paginate(20);

        return view('admin.products.openfoodfacts.auto_imported', compact('products'));
    }
}
