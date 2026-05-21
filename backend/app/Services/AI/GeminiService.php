<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.api_key');
        $this->model = (string) config('services.gemini.model', 'gemini-2.0-flash');
        $this->baseUrl = (string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
    }

    public function chat(string $systemPrompt, string $userMessage): string
    {
        if ($this->apiKey === '' || $this->apiKey === 'your_api_key_here') {
            return 'Maaf, layanan AI sedang dalam konfigurasi. Silakan coba lagi nanti.';
        }

        try {
            $response = Http::timeout(30)->post(
                "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'contents' => [[
                        'parts' => [[
                            'text' => $systemPrompt . "\n\nPertanyaan user: " . $userMessage,
                        ]],
                    ]],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 1024,
                    ],
                ]
            );

            if ($response->successful()) {
                return trim((string) $response->json('candidates.0.content.parts.0.text', ''));
            }

            Log::error('Gemini HTTP error', ['status' => $response->status(), 'body' => $response->body()]);
        } catch (\Throwable $e) {
            Log::error('Gemini exception: ' . $e->getMessage());
        }

        return 'AI Halalytics sedang sibuk saat ini. Silakan coba lagi dalam beberapa saat.';
    }
}
