<?php

namespace App\Http\Controllers;

use App\Services\OpenFoodFactsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductExternalController extends Controller
{
    protected $openFoodFactsService;

    public function __construct(OpenFoodFactsService $openFoodFactsService)
    {
        $this->openFoodFactsService = $openFoodFactsService;
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
        if (!is_numeric($barcode) || strlen($barcode) < 8) {
            return response()->json([
                'response_code' => 400,
                'message' => 'Invalid barcode format'
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

            return response()->json([
                'response_code' => 200,
                'message' => 'Product found',
                'content' => $product
            ], 200);
        }

        return response()->json([
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