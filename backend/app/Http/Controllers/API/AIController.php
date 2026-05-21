<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AI\GeminiService;
use App\Services\AI\IntentClassifierService;
use App\Services\AI\PromptBuilderService;
use App\Services\AI\AiResponseFormatter;
use App\Services\AI\MedicalRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AIController extends Controller
{
    public function __construct(
        private readonly GeminiService $geminiService,
        private readonly IntentClassifierService $intentClassifier,
        private readonly PromptBuilderService $promptBuilder,
        private readonly AiResponseFormatter $formatter,
        private readonly MedicalRecommendationService $medicalRecommendationService,
    ) {}

    public function chat(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $rateLimitKey = 'ai_chat_' . ($request->user()?->id ?? $request->ip());
        if (RateLimiter::tooManyAttempts($rateLimitKey, 20)) {
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak permintaan. Tunggu sebentar.',
            ], 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        $intent = $this->intentClassifier->classify($payload['message']);
        $prompt = $this->promptBuilder->buildPrompt($intent, $payload['message'], $request->user());
        $reply = $this->geminiService->chat($prompt, $payload['message']);
        $recommendation = in_array($intent, ['HEALTH_QUESTION', 'MEDICINE_QUESTION'], true)
            ? $this->medicalRecommendationService->recommend($payload['message'], $request->user())
            : [];

        return response()->json([
            'success' => true,
            'data' => $this->formatter->format($intent, $reply, $recommendation),
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }
}
