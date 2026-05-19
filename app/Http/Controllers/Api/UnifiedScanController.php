<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Services\AI\CategoryDetectorService;
use App\Services\OpenFoodFactsService;
use App\Models\ScanModel;
use App\Services\CrowdSourcedReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnifiedScanController extends Controller
{
    protected $universalService;
    protected $crowdService;
    protected CategoryDetectorService $categoryDetector;

    public function __construct(
        \App\Services\UniversalProductService $universalService,
        CrowdSourcedReportService $crowdService,
        CategoryDetectorService $categoryDetector
    ) {
        $this->universalService = $universalService;
        $this->crowdService = $crowdService;
        $this->categoryDetector = $categoryDetector;
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
            if ($source !== 'bpom' && $productData instanceof ProductModel) {
                 $this->recordScan($user, $productData);
            }

            $detectedCategory = $this->categoryDetector->detect([
                'name' => $standardized['name'] ?? '',
                'ingredients' => $standardized['ingredients_text'] ?? '',
                'category' => $standardized['category'] ?? '',
            ]);

            $response = [
                'success' => true,
                'source' => $source,
                'data' => $this->formatStandardizedResponse($standardized, $source, $productData),
                'detected_category' => $detectedCategory,
                'message' => 'Produk ditemukan (' . $source . ')',
                'needs_verification' => $source !== 'bpom' && ($productData->verification_status ?? '') !== 'verified',
            ];

            // Add crowd-sourced status
            if ($productData instanceof ProductModel) {
                $crowdStatus = $this->crowdService->getCrowdStatus($productData->id_product);
                $response['crowd_status'] = $crowdStatus;
            }

            return response()->json($response);
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
            'brand' => $stdData['brand'] ?? 'Unknown',
            'quantity' => $stdData['quantity'] ?? null,
            'packaging' => $stdData['packaging'] ?? null,
            'labels' => $stdData['labels'] ?? null,
            'stores' => $stdData['stores'] ?? null,
            'countries' => $stdData['countries'] ?? null,
            'nutriscore' => $stdData['nutriscore'] ?? null,
            'nova_group' => $stdData['nova_group'] ?? null,
            'ai_summary' => $originalData->halal_analysis['summary'] ?? null,
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
