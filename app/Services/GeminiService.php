<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    private string $model   = 'gemini-1.5-flash';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', '');
    }

    /**
     * Analisis foto makanan → return data gizi JSON
     */
    public function analyzeFood(string $base64Image, string $mimeType = 'image/jpeg'): array
    {
        $prompt = <<<PROMPT
Analisis gambar makanan ini dengan teliti.
Identifikasi setiap item makanan yang terlihat.
Estimasi berat masing-masing dalam gram.
Hitung nilai gizi per item.
PENTING: Jawab HANYA dalam format JSON ini, tanpa teks lain:
{
  "food_items": [
    {
      "name": "Nama makanan dalam Bahasa Indonesia",
      "weight_gram": 150,
      "calories": 200,
      "carbs": 25.5,
      "protein": 10.2,
      "fat": 8.0,
      "is_halal": true,
      "halal_note": "catatan jika syubhat atau haram"
    }
  ],
  "total_calories": 200,
  "total_carbs": 25.5,
  "total_protein": 10.2,
  "total_fat": 8.0,
  "analysis_note": "catatan singkat tentang makanan ini"
}
PROMPT;

        $response = Http::timeout(30)->post(
            "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
            [
                'contents' => [[
                    'parts' => [
                        ['text' => $prompt],
                        ['inline_data' => [
                            'mime_type' => $mimeType,
                            'data'      => $base64Image,
                        ]],
                    ],
                ]],
                'generationConfig' => [
                    'temperature'      => 0.1,
                    'response_mime_type' => 'application/json',
                ],
            ]
        );

        if (!$response->successful()) {
            Log::error('Gemini API error', ['body' => $response->body()]);
            throw new \Exception('Gemini API error: ' . $response->status());
        }

        $content = $response->json('candidates.0.content.parts.0.text');
        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Gemini returned invalid JSON');
        }

        return $decoded;
    }

    /**
     * Substitusi bahan halal menggunakan Gemini text
     */
    public function substituteIngredients(array $ingredients, string $context = ''): array
    {
        $ingredientList = implode(', ', $ingredients);
        $prompt = <<<PROMPT
Kamu adalah ahli gizi kuliner halal dan thoyyib.
Daftar bahan berikut perlu dicek: $ingredientList
Konteks resep: $context

Cek setiap bahan:
1. Apakah bahan tersebut halal?
2. Jika tidak halal atau syubhat, berikan substitusi yang halal.
3. Berikan juga alternatif lebih sehat jika memungkinkan.

Jawab HANYA dalam JSON ini:
{
  "ingredients": [
    {
      "original": "nama bahan asli",
      "status": "halal|syubhat|haram",
      "reason": "alasan singkat",
      "halal_substitute": "bahan pengganti halal atau null",
      "healthy_substitute": "bahan lebih sehat atau null",
      "substitute_note": "catatan penyesuaian rasa/tekstur"
    }
  ],
  "overall_note": "catatan keseluruhan resep"
}
PROMPT;

        $response = Http::timeout(30)->post(
            "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
            [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'response_mime_type' => 'application/json',
                ],
            ]
        );

        if (!$response->successful()) {
            throw new \Exception('Gemini API error: ' . $response->status());
        }

        $content = $response->json('candidates.0.content.parts.0.text');
        $content = preg_replace('/```json\s*|\s*```/', '', $content);
        $decoded = json_decode(trim($content), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Gemini returned invalid JSON for substitution');
        }

        return $decoded;
    }
}
