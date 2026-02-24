<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Services\OpenFoodFactsService;
use App\Models\ScanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnifiedScanController extends Controller
{
    protected $universalService;

    public function __construct(\App\Services\UniversalProductService $universalService)
    {
        $this->universalService = $universalService;
    }

    /**
     * UNIFIED SCAN ENDPOINT
     * Priority: BpomData -> Local Cache -> OpenFoodFacts -> OpenBeautyFacts
     */
    public function scan(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $barcode = $request->barcode;
        $user = $request->user();

        // Use Universal Service
        $result = $this->universalService->findProduct($barcode);

        if ($result['found']) {
            $productData = $result['data']; // Model (ProductModel or BpomData)
            $standardized = $result['standardized'];
            $source = $result['source'];

            // Record Scan Logic
            // We need a product_id. If source is BPOM, we might not have a ProductModel ID to link to scans table.
            // But ScanModel links to `product_id`.
            // If strictly BPOM, we might need to "fake" it or just skip recording if structure doesn't allow.
            // ideally we should treat BpomData as a valid product to scan.
            
            // For now, only record if it's a ProductModel (Local Cache or Imported OFF/OBF)
            if ($source !== 'bpom' && $productData instanceof ProductModel) {
                 $this->recordScan($user, $productData);
            }

            return response()->json([
                'success' => true,
                'source' => $source,
                'data' => $this->formatStandardizedResponse($standardized, $source, $productData),
                'message' => 'Produk ditemukan (' . $source . ')',
                'needs_verification' => $source !== 'bpom' && ($productData->verification_status ?? '') !== 'verified',
            ]);
        }

        // ===== NOT FOUND =====
        return response()->json([
            'success' => false,
            'source' => 'none',
            'message' => 'Produk tidak ditemukan',
            'action' => 'manual_input',
            'instructions' => [
                'Apakah ini produk UMKM?',
                'Silakan foto kemasan untuk verifikasi'
            ]
        ], 404);
    }

    private function formatStandardizedResponse($stdData, $source, $originalData)
    {
        return [
            'id' => $originalData->id_product ?? $originalData->id ?? 0,
            'nama_product' => $stdData['name'],
            'barcode' => $stdData['barcode'],
            'image' => $stdData['image_url'],
            'halal_status' => $stdData['status_halal'],
            'verification_status' => $originalData->verification_status ?? ($source === 'bpom' ? 'verified' : 'needs_review'),
            'source' => $source,
            'is_verified' => ($originalData->verification_status ?? '') === 'verified' || $source === 'bpom',
            'komposisi' => $stdData['ingredients_text'], // String or raw
            'info_gizi' => $originalData->info_gizi ?? null,
            'kategori' => $stdData['category'],
        ];
    }

    /**
     * Record scan to history
     */
    private function recordScan($user, $product)
    {
        ScanModel::create([
            'user_id' => $user->id,
            'product_id' => $product->id_product,
            'scanned_at' => now(),
            'status' => 'success'
        ]);
    }

    /**
     * Format product response
     */
    private function formatProductResponse($product)
    {
        return [
            'id' => $product->id_product,
            'nama_product' => $product->nama_product,
            'barcode' => $product->barcode,
            'image' => $product->image,
            'halal_status' => $product->status,
            'verification_status' => $product->verification_status,
            'source' => $product->source,
            'is_verified' => $product->verification_status === 'verified',
            'komposisi' => is_string($product->komposisi) ? json_decode($product->komposisi) : $product->komposisi,
            'info_gizi' => is_string($product->info_gizi) ? json_decode($product->info_gizi) : $product->info_gizi,
            'kategori' => $product->kategori ? $product->kategori->nama_kategori : 'Umum',
        ];
    }
}
