<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIAnalysisService
{
    private string $groqApiKey;
    private string $groqModel = 'llama-3.3-70b-versatile'; // Sesuai permintaan
    private string $geminiApiKey;
    private string $geminiModel = 'gemini-2.0-flash-lite'; // Sesuai permintaan

    public function __construct()
    {
        $this->groqApiKey = config('services.groq.api_key', env('GROQ_API_KEY'));
        $this->geminiApiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
    }

    /**
     * Analyze with Groq (primary AI engine).
     */
    public function analyzeWithGroq(string $prompt): array
    {
        if (empty($this->groqApiKey)) {
            Log::warning('Groq API Key is missing. Falling back to Gemini.');
            return $this->analyzeWithGemini($prompt);
        }

        try {
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->groqApiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => $this->groqModel,
                'messages'    => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.1,  // rendah = konsisten, tidak kreatif
                'max_tokens'  => 2000,
            ]);

            if ($response->failed()) {
                Log::error('Groq API Error: ' . $response->status() . ' - ' . $response->body());
                return $this->analyzeWithGemini($prompt); // Fallback to Gemini on Groq failure
            }

            $content = $response->json('choices.0.message.content');
            // Bersihkan response kalau AI nakal wrap JSON dengan ```
            $clean = preg_replace('/```json|```/', '', $content);
            $clean = trim($clean);

            $decoded = json_decode($clean, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Groq API response is not valid JSON: ' . json_last_error_msg() . '. Raw: ' . $clean);
                return $this->analyzeWithGemini($prompt); // Fallback if invalid JSON
            }
            return $decoded ?? [];
        } catch (\Exception $e) {
            Log::error('Groq API exception: ' . $e->getMessage() . '. Falling back to Gemini.');
            return $this->analyzeWithGemini($prompt); // Fallback on exception
        }
    }

    /**
     * Analyze with Gemini (fallback AI engine).
     */
    public function analyzeWithGemini(string $prompt): array
    {
        if (empty($this->geminiApiKey) || !str_starts_with($this->geminiApiKey, 'AIzaSy')) {
            Log::warning('Gemini API Key is missing or invalid. Using offline fallback.');
            return ['error' => 'AI Service Unavailable', 'message' => 'AI Service is currently offline. Please try again later.'];
        }

        try {
            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$this->geminiModel}:generateContent?key={$this->geminiApiKey}",
                [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.1,
                        'maxOutputTokens' => 2000,
                    ]
                ]
            );

            if ($response->failed()) {
                Log::error('Gemini API Error: ' . $response->status() . ' - ' . $response->body());
                return ['error' => 'AI Service Error', 'message' => 'There was an error processing your request with Gemini.'];
            }

            $content = $response->json('candidates.0.content.parts.0.text');
            $clean   = preg_replace('/```json|```/', '', $content);
            $clean = trim($clean);

            $decoded = json_decode($clean, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Gemini API response is not valid JSON: ' . json_last_error_msg() . '. Raw: ' . $clean);
                return ['error' => 'AI Service Error', 'message' => 'Invalid AI response format from Gemini.'];
            }
            return $decoded ?? [];
        } catch (\Exception $e) {
            Log::error('Gemini API exception: ' . $e->getMessage());
            return ['error' => 'AI Service Error', 'message' => 'An unexpected error occurred with Gemini AI.'];
        }
    }
}
