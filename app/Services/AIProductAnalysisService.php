<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIProductAnalysisService
{
    protected string $geminiApiKey;
    protected string $geminiModel;
    protected int $maxTokens;
    protected float $temperature;
    protected string $geminiBaseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct()
    {
        $this->geminiApiKey = config('services.gemini.key', env('GEMINI_API_KEY'));
        $this->geminiModel = config('services.gemini.model', env('GEMINI_MODEL', 'gemini-2.0-flash-lite'));
        $this->maxTokens = config('services.gemini.max_tokens', env('GEMINI_MAX_TOKENS', 4096));
        $this->temperature = config('services.gemini.temperature', env('GEMINI_TEMPERATURE', 0.3));
    }

    /**
     * Analyze product ingredients and provide halal analysis
     */
    public function analyzeProductIngredients(string $productName, array $ingredients): array
    {
        try {
            $ingredientsText = implode(', ', $ingredients);

            $prompt = "Analisis bahan-bahan produk berikut untuk status halal dan nutrisi:\n\n" .
                     "Nama Produk: {$productName}\n" .
                     "Bahan-bahan: {$ingredientsText}\n\n" .
                     "Berikan analisis dalam format JSON dengan struktur:\n" .
                     "{\n" .
                     "  \"halal_status\": \"halal|haram|mushbooh|tergantung_bahan\",\n" .
                     "  \"halal_concerns\": [\"daftar masalah halal jika ada\"],\n" .
                     "  \"recommendations\": \"saran untuk konsumen muslim\",\n" .
                     "  \"nutrition_per_serving\": {\n" .
                     "    \"serving_size_g\": 100,\n" .
                     "    \"calories\": 250,\n" .
                     "    \"protein_g\": 8,\n" .
                     "    \"carbs_g\": 30,\n" .
                     "    \"fat_g\": 12,\n" .
                     "    \"fiber_g\": 2,\n" .
                     "    \"sugar_g\": 5,\n" .
                     "    \"sodium_mg\": 600\n" .
                     "  }\n" .
                     "}\n\n" .
                     "Fokus pada:\n" .
                     "- Bahan yang mengandung alkohol, gelatin, atau enzim non-halal\n" .
                     "- Cross-contamination dengan bahan haram\n" .
                     "- Perkiraan nutrisi berdasarkan komposisi bahan\n" .
                     "- Status halal yang akurat";

            $response = $this->callGeminiAPI($prompt);

            if (!$response) {
                return $this->getDefaultAnalysis($productName, $ingredients);
            }

            $analysis = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Failed to parse AI response as JSON', [
                    'response' => $response,
                    'product' => $productName
                ]);
                return $this->getDefaultAnalysis($productName, $ingredients);
            }

            return $analysis;

        } catch (\Exception $e) {
            Log::error('AI Product Analysis failed', [
                'error' => $e->getMessage(),
                'product' => $productName
            ]);

            return $this->getDefaultAnalysis($productName, $ingredients);
        }
    }

    /**
     * Analyze street food for nutrition and halal status
     */
    public function analyzeStreetFood(string $foodName, string $description, array $ingredients): array
    {
        try {
            $ingredientsText = implode(', ', $ingredients);

            $prompt = "Analisis makanan jalanan Indonesia berikut untuk nutrisi dan status halal:\n\n" .
                     "Nama Makanan: {$foodName}\n" .
                     "Deskripsi: {$description}\n" .
                     "Bahan umum: {$ingredientsText}\n\n" .
                     "Berikan analisis dalam format JSON:\n" .
                     "{\n" .
                     "  \"halal_status\": \"halal|haram|mushbooh|tergantung_bahan\",\n" .
                     "  \"halal_concerns\": [\"daftar masalah halal\"],\n" .
                     "  \"recommendations\": \"saran kesehatan dan halal\",\n" .
                     "  \"nutrition_per_serving\": {\n" .
                     "    \"serving_size_g\": 150,\n" .
                     "    \"calories\": 250,\n" .
                     "    \"protein_g\": 8,\n" .
                     "    \"carbs_g\": 30,\n" .
                     "    \"fat_g\": 12,\n" .
                     "    \"fiber_g\": 2,\n" .
                     "    \"sugar_g\": 5,\n" .
                     "    \"sodium_mg\": 600\n" .
                     "  }\n" .
                     "}\n\n" .
                     "Pertimbangkan:\n" .
                     "- Makanan Indonesia umumnya halal tapi perhatikan minyak dan bumbu\n" .
                     "- Perkiraan nutrisi berdasarkan porsi standar\n" .
                     "- Masalah kesehatan seperti tinggi sodium atau lemak";

            $response = $this->callGeminiAPI($prompt);

            if (!$response) {
                return $this->getDefaultStreetFoodAnalysis($foodName, $ingredients);
            }

            $analysis = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Failed to parse street food AI response', [
                    'response' => $response,
                    'food' => $foodName
                ]);
                return $this->getDefaultStreetFoodAnalysis($foodName, $ingredients);
            }

            return $analysis;

        } catch (\Exception $e) {
            Log::error('AI Street Food Analysis failed', [
                'error' => $e->getMessage(),
                'food' => $foodName
            ]);

            return $this->getDefaultStreetFoodAnalysis($foodName, $ingredients);
        }
    }

    /**
     * Calculate nutrition for a given food
     */
    public function calculateNutrition(string $foodName, array $ingredients, float $servingSize = 100): array
    {
        try {
            $ingredientsText = implode(', ', $ingredients);

            $prompt = "Hitung informasi nutrisi untuk makanan berikut per {$servingSize}g porsi:\n\n" .
                     "Nama Makanan: {$foodName}\n" .
                     "Bahan: {$ingredientsText}\n" .
                     "Ukuran porsi: {$servingSize}g\n\n" .
                     "Berikan dalam format JSON:\n" .
                     "{\n" .
                     "  \"serving_size_g\": {$servingSize},\n" .
                     "  \"calories\": 250,\n" .
                     "  \"protein_g\": 8,\n" .
                     "  \"carbs_g\": 30,\n" .
                     "  \"fat_g\": 12,\n" .
                     "  \"fiber_g\": 2,\n" .
                     "  \"sugar_g\": 5,\n" .
                     "  \"sodium_mg\": 600\n" .
                     "}\n\n" .
                     "Gunakan data nutrisi standar dan perkiraan akurat.";

            $response = $this->callGeminiAPI($prompt);

            if (!$response) {
                return $this->getDefaultNutrition($servingSize);
            }

            $nutrition = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->getDefaultNutrition($servingSize);
            }

            return $nutrition;

        } catch (\Exception $e) {
            Log::error('Nutrition calculation failed', [
                'error' => $e->getMessage(),
                'food' => $foodName
            ]);

            return $this->getDefaultNutrition($servingSize);
        }
    }

    /**
     * Suggest product image based on name and ingredients
     */
    public function suggestProductImage(string $productName, array $ingredients): array
    {
        try {
            $ingredientsText = implode(', ', $ingredients);

            $prompt = "Berdasarkan nama produk dan bahan-bahan, sarankan gambar yang sesuai untuk produk halal:\n\n" .
                     "Nama Produk: {$productName}\n" .
                     "Bahan: {$ingredientsText}\n\n" .
                     "Berikan saran dalam format JSON:\n" .
                     "{\n" .
                     "  \"image_description\": \"deskripsi gambar yang menarik\",\n" .
                     "  \"image_keywords\": [\"kata kunci untuk pencarian gambar\"],\n" .
                     "  \"image_style\": \"realistic|photographic|illustration\",\n" .
                     "  \"halal_focus\": \"fokus halal seperti sertifikat atau bahan\" \n" .
                     "}\n\n" .
                     "Pastikan saran gambar menekankan aspek halal dan menarik.";

            $response = $this->callGeminiAPI($prompt);

            if (!$response) {
                return $this->getDefaultImageSuggestion($productName);
            }

            $suggestion = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->getDefaultImageSuggestion($productName);
            }

            return $suggestion;

        } catch (\Exception $e) {
            Log::error('Image suggestion failed', [
                'error' => $e->getMessage(),
                'product' => $productName
            ]);

            return $this->getDefaultImageSuggestion($productName);
        }
    }

    /**
     * Call Gemini API
     */
    private function callGeminiAPI(string $prompt): ?string
    {
        try {
            $url = $this->geminiBaseUrl . $this->geminiModel . ':generateContent?key=' . $this->geminiApiKey;

            $response = Http::timeout(30)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => $this->temperature,
                    'maxOutputTokens' => $this->maxTokens,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            Log::warning('Gemini API call failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Gemini API call exception', [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Get default analysis when AI fails
     */
    private function getDefaultAnalysis(string $productName, array $ingredients): array
    {
        $ingredientsText = strtolower(implode(' ', $ingredients));

        // Check for common haram ingredients
        $haramIndicators = ['alcohol', 'wine', 'beer', 'gelatin', 'pork', 'bacon', 'wine', 'vodka', 'whiskey'];
        $hasHaram = false;
        foreach ($haramIndicators as $indicator) {
            if (strpos($ingredientsText, $indicator) !== false) {
                $hasHaram = true;
                break;
            }
        }

        return [
            'halal_status' => $hasHaram ? 'haram' : 'mushbooh',
            'halal_concerns' => $hasHaram ? ['Mengandung bahan haram'] : ['Perlu verifikasi lebih lanjut'],
            'recommendations' => 'Periksa label halal dan konsultasikan dengan ahli',
            'nutrition_per_serving' => [
                'serving_size_g' => 100,
                'calories' => 200,
                'protein_g' => 5,
                'carbs_g' => 25,
                'fat_g' => 8,
                'fiber_g' => 2,
                'sugar_g' => 10,
                'sodium_mg' => 300
            ]
        ];
    }

    /**
     * Get default street food analysis
     */
    private function getDefaultStreetFoodAnalysis(string $foodName, array $ingredients): array
    {
        return [
            'halal_status' => 'halal',
            'halal_concerns' => ['Umumnya halal dengan bahan standar'],
            'recommendations' => 'Konsumsi seimbang dan proporsi yang tepat',
            'nutrition_per_serving' => [
                'serving_size_g' => 150,
                'calories' => 250,
                'protein_g' => 8,
                'carbs_g' => 30,
                'fat_g' => 12,
                'fiber_g' => 2,
                'sugar_g' => 5,
                'sodium_mg' => 600
            ]
        ];
    }

    /**
     * Get default nutrition data
     */
    private function getDefaultNutrition(float $servingSize): array
    {
        return [
            'serving_size_g' => $servingSize,
            'calories' => 200,
            'protein_g' => 5,
            'carbs_g' => 25,
            'fat_g' => 8,
            'fiber_g' => 2,
            'sugar_g' => 10,
            'sodium_mg' => 300
        ];
    }

    /**
     * Get default image suggestion
     */
    private function getDefaultImageSuggestion(string $productName): array
    {
        return [
            'image_description' => "Produk {$productName} dengan label halal",
            'image_keywords' => ['halal', 'food', 'product', strtolower($productName)],
            'image_style' => 'photographic',
            'halal_focus' => 'Label halal dan kemasan bersih'
        ];
    }
}