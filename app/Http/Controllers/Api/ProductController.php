<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Models\HalalProduct;
use App\Models\ActivityModel;
use App\Services\HalalCertificationService;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $halalService;
    protected $universalProductService;

    public function __construct(
        HalalCertificationService $halalService,
        \App\Services\UniversalProductService $universalProductService
    ) {
        $this->halalService = $halalService;
        $this->universalProductService = $universalProductService;
    }

    /**
     * Get product details from universal sources (Local -> OFF -> OBF)
     */
    public function show($barcode)
    {
        // Use Universal Service
        $result = $this->universalProductService->findProduct($barcode);

        if ($result['found']) {
            // Check Halal Status via HalalService if not already verified
            // Or just use the data we found.
            // Existing app expects 'halal_info'.
            
            // Map standardized data to response format
            $productData = $result['standardized'];
            
            // Construct Halal Info Object (Mocking the structure app expects if not from local DB)
            $halalInfo = [
                'halal_status' => $productData['status_halal'] ?? 'unknown',
                'halal_certificate_number' => $productData['halal_certificate'],
                'certification_body' => null,
                'source' => $result['source']
            ];

            // Determine ID based on source model
            $model = $result['data'];
            $productId = 0;
            if ($model instanceof \App\Models\BpomData) {
                $productId = $model->id;
            } elseif ($model instanceof \App\Models\ProductModel) {
                $productId = $model->id_product;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'product' => [
                        'id' => $productId,
                        'barcode' => $productData['barcode'],
                        'name' => $productData['name'],
                        'brand' => $productData['brand'],
                        'image_front_url' => $productData['image_url'],
                        'image' => $productData['image_url'], // Map for Android
                        'ingredients_text' => $productData['ingredients_text'],
                        'category' => $productData['category'],
                        'nutriscore' => $productData['nutriscore'] ?? null,
                        'additives' => $productData['additives'] ?? [],
                        'allergens' => $productData['allergens'] ?? []
                    ],
                    'halal_info' => $halalInfo,
                    'halal_source' => $result['source']
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found'
        ], 404);
    }

    /**
     * Check halal status only
     */
    public function checkHalal(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'product_name' => 'required|string',
            'brand' => 'nullable|string'
        ]);

        $result = $this->halalService->verifyAndStore(
            $request->barcode,
            $request->product_name,
            $request->brand
        );

        // Record Activity
        if (auth('sanctum')->check()) {
            ActivityModel::create([
                'id_user' => auth('sanctum')->id(),
                'aktivitas' => "Mengecek status halal: " . $request->product_name,
                'status' => $result['data']->halal_status
            ]);
        }

        return response()->json([
            'success' => true,
            'halal_status' => $result['data']->halal_status,
            'certificate_number' => $result['data']->halal_certificate_number,
            'certification_body' => $result['data']->certification_body,
            'valid_until' => $result['data']->certificate_valid_until,
            'last_checked' => $result['data']->last_checked_at,
            'source' => $result['source']
        ]);
    }

    /**
     * Batch check multiple products
     */
    public function batchCheckHalal(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.barcode' => 'required|string',
            'products.*.name' => 'required|string',
            'products.*.brand' => 'nullable|string'
        ]);

        $results = [];

        foreach ($request->products as $product) {
            $result = $this->halalService->verifyAndStore(
                $product['barcode'],
                $product['name'],
                $product['brand'] ?? ''
            );

            $results[] = [
                'barcode' => $product['barcode'],
                'halal_status' => $result['data']->halal_status,
                'certificate_number' => $result['data']->halal_certificate_number
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Get halal alternatives for a consumer product using AI
     */
    public function alternatives(Request $request, $barcode)
    {
        try {
            // First, find the product so we know its name and ingredients
            $result = $this->universalProductService->findProduct($barcode);
            
            if (!$result['found']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan, tidak bisa mencari alternatif.'
                ], 404);
            }

            $productData = $result['standardized'];
            $productName = $productData['name'];
            $ingredients = $productData['ingredients_text'];
            $category = $productData['category'] ?? 'makanan/minuman umum';

            // Invoke Gemini AI to find alternatives
            $geminiService = app(GeminiService::class);
            $aiResponse = $geminiService->findProductHalalAlternative($productName, $ingredients, $category);

            // Record Activity
            if (auth('sanctum')->check()) {
                ActivityModel::create([
                    'id_user' => auth('sanctum')->id(),
                    'aktivitas' => "Mencari alternatif halal untuk: " . $productName,
                    'status' => 'success'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $aiResponse
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Halal Product Alternative Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari alternatif produk: ' . $e->getMessage()
            ], 500);
        }
    }
}
