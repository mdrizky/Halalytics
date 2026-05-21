<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Analysis\ProductAnalysisService;
use App\Services\External\OpenFoodFactsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductDetailController extends Controller
{
    public function __construct(
        private readonly OpenFoodFactsService $offService,
        private readonly ProductAnalysisService $analysisService,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'barcode' => ['required', 'string', 'min:8', 'max:32'],
        ]);

        $barcode = $payload['barcode'];

        $product = Cache::remember("product_detail_{$barcode}", now()->addHours(6), function () use ($barcode) {
            return $this->offService->getByBarcode($barcode);
        });

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $user = $request->user();
        $userContext = [
            'name' => $user?->name,
            'age' => $user?->age,
            'bmi' => $user?->bmi,
            'diseases' => $user?->diseases,
            'allergies' => $user?->allergies,
        ];

        $analysis = $this->analysisService->analyzeDetailed($product, $userContext);

        return response()->json([
            'success' => true,
            'data' => [
                'barcode' => $barcode,
                'product' => $product,
                'halal_status' => $analysis['halal_status'],
                'halal_score' => $analysis['halal_score'],
                'health_status' => $analysis['health_status'],
                'health_score' => $analysis['health_score'],
                'dominant_ingredient' => $analysis['dominant_ingredient'],
                'short_term_effect' => $analysis['short_term_effect'],
                'long_term_effect' => $analysis['long_term_effect'],
                'personalized_recommendation' => $analysis['personalized_recommendation'],
                'confidence' => $analysis['confidence'],
                'sources' => $analysis['sources'],
                'warnings' => $analysis['warnings'],
            ],
        ]);
    }
}
