<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    private string $apiKey;
    private string $baseUrl = 'https://openrouter.ai/api/v1';
    private string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.openrouter.key', env('OPENROUTER_API_KEY', ''));
        // Default to deepseek as recommended
        $this->model = (string) config('services.openrouter.model', env('OPENROUTER_MODEL', 'deepseek/deepseek-chat'));
    }

    public function analyzeIngredients(string $ingredientsText, array $userContext = []): array
    {
        if (empty($this->apiKey)) {
            Log::warning('OpenRouter API Key not set. Returning empty array for fallback.');
            return [];
        }

        $systemPrompt = "You are an Islamic halal food ingredient analyst.\n"
            . "Analyze ingredients carefully.\n"
            . "Rules:\n"
            . "- Pork derived = haram\n"
            . "- Alcohol beverages = haram\n"
            . "- Unknown gelatin = doubtful\n"
            . "- Unknown emulsifier = risky\n"
            . "- Plant-based ingredients = safe\n\n"
            . "User Context: " . json_encode($userContext) . "\n\n"
            . "Return JSON only:\n"
            . "{\n"
            . " \"status\":\"halal|doubtful|haram\",\n"
            . " \"score\":0,\n" // 0-100
            . " \"reason\":\"string\",\n"
            . " \"risky_ingredients\":[]\n"
            . "}";

        $userPrompt = "Analyze this ingredient.\n\nIngredients:\n{$ingredientsText}\n\nReturn JSON only.";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => config('app.url', 'https://halalytics.app'),
                'X-Title' => config('app.name', 'Halalytics'),
            ])->timeout(30)->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.1,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                if ($content) {
                    // Extract JSON if model wrapped it in markdown
                    $content = preg_replace('/```json/i', '', $content);
                    $content = preg_replace('/```/i', '', $content);
                    $decoded = json_decode(trim($content), true);
                    return is_array($decoded) ? $decoded : [];
                }
            } else {
                Log::error('OpenRouter API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('OpenRouter Exception: ' . $e->getMessage());
        }

        return [];
    }
}
