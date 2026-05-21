<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AI\GeminiService;
use App\Services\AI\IntentClassifierService;
use App\Services\AI\PromptBuilderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AIController extends Controller
{
    public function __construct(
        private readonly GeminiService $geminiService,
        private readonly IntentClassifierService $intentClassifier,
        private readonly PromptBuilderService $promptBuilder,
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

        return response()->json([
            'success' => true,
            'data' => [
                'intent' => $intent,
                'message' => $reply,
            ],
        ]);
    }
}
