<?php

namespace App\Http\Controllers;

use App\Services\ActivityEventService;
use App\Services\OpenFoodFactsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ProductExternalController extends Controller
{
    protected $openFoodFactsService;
    protected $activityEventService;

    public function __construct(
        OpenFoodFactsService $openFoodFactsService,
        ActivityEventService $activityEventService
    )
    {
        $this->openFoodFactsService = $openFoodFactsService;
        $this->activityEventService = $activityEventService;
    }

    /**
     * Search products
     * GET /api/external/search?query=coca&page_size=20&page=1
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'page_size' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $query = $request->input('query');
        $pageSize = $request->input('page_size', 20);
        $page = $request->input('page', 1);

        $result = $this->openFoodFactsService->searchProducts($query, $pageSize, $page);

        if ($result['success']) {
            return response()->json([
                'response_code' => 200,
                'message' => "Found {$result['count']} products",
                'content' => $result
            ], 200);
        }

        return response()->json([
            'response_code' => 404,
            'message' => $result['message'] ?? 'No products found',
            'content' => ['products' => []]
        ], 404);
    }

    /**
     * Get product detail by barcode
     * GET /api/external/product/{barcode}
     */
    public function detail(string $barcode): JsonResponse
    {
        $traceId = (string) Str::uuid();

        if (!is_numeric($barcode) || strlen($barcode) < 8) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid barcode format',
                'source_error' => 'INVALID_BARCODE',
                'trace_id' => $traceId,
                'response_code' => 400,
                'content' => null,
            ], 400);
        }

        $result = $this->openFoodFactsService->getProductByBarcode($barcode);

        if ($result['success'] && $result['product']) {
            $product = $result['product'];

            // Add halal analysis
            if (!empty($product['ingredients_text']) || !empty($product['ingredients_text_en'])) {
                $ingredients = $product['ingredients_text'] ?? $product['ingredients_text_en'];
                $product['halal_analysis'] = $this->openFoodFactsService->analyzeIngredients($ingredients);
            }

            $normalized = [
                'source' => 'open_food_facts',
                'barcode' => $product['barcode'] ?? $barcode,
                'name' => $product['product_name'] ?? $product['product_name_en'] ?? 'Unknown Product',
                'brands' => $product['brands'] ?? null,
                'categories' => $product['categories'] ?? null,
                'ingredients_text' => $product['ingredients_text'] ?? $product['ingredients_text_en'] ?? null,
                'nutriments' => [
                    'energy' => data_get($product, 'nutriments.energy-kcal_100g')
                        ?? data_get($product, 'nutriments.energy-kcal')
                        ?? data_get($product, 'nutriments.energy_100g'),
                    'sugar' => data_get($product, 'nutriments.sugars_100g')
                        ?? data_get($product, 'nutriments.sugars'),
                    'fat' => data_get($product, 'nutriments.fat_100g')
                        ?? data_get($product, 'nutriments.fat'),
                    'salt' => data_get($product, 'nutriments.salt_100g')
                        ?? data_get($product, 'nutriments.salt'),
                ],
                'nutriscore_grade' => $product['nutriscore_grade'] ?? null,
                'labels' => $product['labels_tags'] ?? [],
                'halal_analysis' => $product['halal_analysis'] ?? [
                    'status' => 'unknown',
                    'suspicious_ingredients' => [],
                    'recommendation' => 'Tidak ada data ingredients untuk analisis.',
                ],
                'image_url' => $product['image_front_url'] ?? $product['image_url'] ?? null,
                'synced_at' => now()->toIso8601String(),
            ];

            $user = auth('sanctum')->user();
            $this->activityEventService->logEvent(
                eventType: 'external_scan',
                userId: $user?->id_user,
                username: $user?->username,
                entityRef: (string) ($normalized['barcode'] ?? $barcode),
                summary: 'External product detail viewed: ' . ($normalized['name'] ?? $barcode),
                status: 'success',
                payload: [
                    'source' => $normalized['source'],
                    'name' => $normalized['name'],
                    'nutriscore_grade' => $normalized['nutriscore_grade'],
                ]
            );

            return response()->json([
                'success' => true,
                'source' => $normalized['source'],
                'trace_id' => $traceId,
                'content' => $product,
                'data' => $normalized,
                'response_code' => 200,
                'message' => 'Product found',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'source_error' => 'PRODUCT_NOT_FOUND',
            'trace_id' => $traceId,
            'response_code' => 404,
            'message' => $result['message'] ?? 'Product not found',
            'content' => null
        ], 404);
    }

    /**
     * Search halal products
     * GET /api/external/halal?query=chicken&page_size=20
     */
    public function halal(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'nullable|string',
            'page_size' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $query = $request->input('query', '');
        $pageSize = $request->input('page_size', 20);
        $page = $request->input('page', 1);

        $result = $this->openFoodFactsService->searchByLabel('halal', $query, $pageSize, $page);

        if ($result['success']) {
            return response()->json([
                'response_code' => 200,
                'message' => "Found {$result['count']} halal products",
                'content' => $result
            ], 200);
        }

        return response()->json([
            'response_code' => 404,
            'message' => 'No halal products found',
            'content' => ['products' => []]
        ], 404);
    }

    /**
     * Search vegetarian products
     * GET /api/external/vegetarian?query=tofu
     */
    public function vegetarian(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'nullable|string',
            'page_size' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $query = $request->input('query', '');
        $pageSize = $request->input('page_size', 20);
        $page = $request->input('page', 1);

        $result = $this->openFoodFactsService->searchByLabel('vegetarian', $query, $pageSize, $page);

        if ($result['success']) {
            return response()->json([
                'response_code' => 200,
                'message' => "Found {$result['count']} vegetarian products",
                'content' => $result
            ], 200);
        }

        return response()->json([
            'response_code' => 404,
            'message' => 'No vegetarian products found',
            'content' => ['products' => []]
        ], 404);
    }

    /**
     * Search vegan products
     * GET /api/external/vegan?query=almond
     */
    public function vegan(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'nullable|string',
            'page_size' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $query = $request->input('query', '');
        $pageSize = $request->input('page_size', 20);
        $page = $request->input('page', 1);

        $result = $this->openFoodFactsService->searchByLabel('vegan', $query, $pageSize, $page);

        if ($result['success']) {
            return response()->json([
                'response_code' => 200,
                'message' => "Found {$result['count']} vegan products",
                'content' => $result
            ], 200);
        }

        return response()->json([
            'response_code' => 404,
            'message' => 'No vegan products found',
            'content' => ['products' => []]
        ], 404);
    }

    /**
     * Search by brand
     * GET /api/external/brand/{brand}
     */
    public function brand(string $brand, Request $request): JsonResponse
    {
        $request->validate([
            'page_size' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $pageSize = $request->input('page_size', 20);
        $page = $request->input('page', 1);

        $result = $this->openFoodFactsService->searchByBrand($brand, $pageSize, $page);

        if ($result['success']) {
            return response()->json([
                'response_code' => 200,
                'message' => "Found {$result['count']} products from {$brand}",
                'content' => $result
            ], 200);
        }

        return response()->json([
            'response_code' => 404,
            'message' => 'No products found for this brand',
            'content' => ['products' => []]
        ], 404);
    }

    /**
     * Search by category
     * GET /api/external/category/{category}
     */
    public function category(string $category, Request $request): JsonResponse
    {
        $request->validate([
            'page_size' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $pageSize = $request->input('page_size', 20);
        $page = $request->input('page', 1);

        $result = $this->openFoodFactsService->searchByCategory($category, $pageSize, $page);

        if ($result['success']) {
            return response()->json([
                'response_code' => 200,
                'message' => "Found {$result['count']} products in {$category}",
                'content' => $result
            ], 200);
        }

        return response()->json([
            'response_code' => 404,
            'message' => 'No products found in this category',
            'content' => ['products' => []]
        ], 404);
    }
}
