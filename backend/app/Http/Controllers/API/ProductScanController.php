<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AI\GeminiService;
use App\Services\Analysis\ProductAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductScanController extends Controller
{
    public function __construct(
        private readonly ProductAnalysisService $analysisService,
        private readonly GeminiService $geminiService,
    ) {}

    public function analyze(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'product' => ['required', 'array'],
            'product.name' => ['required', 'string'],
            'product.ingredients' => ['nullable', 'string'],
            'product.nutriments' => ['nullable', 'array'],
        ]);

        $analysis = $this->analysisService->analyze($payload['product']);

        $prompt = "Berikan ringkasan singkat dalam Bahasa Indonesia untuk produk {$payload['product']['name']}. "
            . "Data analisis: " . json_encode($analysis, JSON_UNESCAPED_UNICODE);
        $aiSummary = $this->geminiService->chat($prompt, 'Jelaskan risiko kesehatan dan status halal secara ringkas.');

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $payload['product'],
                'analysis' => $analysis,
                'ai_summary' => $aiSummary,
            ],
        ]);
    }
}
