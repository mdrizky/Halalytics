<?php

namespace App\Services\AI;

use App\Models\AiLog;
use App\Services\ImageSearchService;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;

class FoodAnalysisOrchestrator
{
    public function __construct(
        private readonly GeminiService $gemini,
        private readonly OpenRouterService $openRouter,
        private readonly ImageSearchService $imageSearch,
        private readonly PromptBuilderService $promptBuilder,
        private readonly HalalAnalyzerService $halalAnalyzer,
        private readonly NutritionAnalyzerService $nutritionAnalyzer,
        private readonly HealthRiskAnalyzer $healthRiskAnalyzer,
        private readonly UserBehaviorAnalyzer $behaviorAnalyzer,
        private readonly FoodRecommendationService $recommendationService,
        private readonly EvidenceService $evidenceService,
        private readonly IntentClassifierService $intentClassifier
    ) {
    }

    /**
     * Full product / ingredient analysis pipeline.
     *
     * @param  array<string, mixed>  $userContext
     * @param  array<string, mixed>  $productData
     */
    public function analyzeIngredients(
        string $ingredientsText,
        array $userContext = [],
        array $productData = []
    ): array {
        $start = microtime(true);
        $userId = (int) ($userContext['user_id'] ?? 0);

        $productName = $productData['product_name'] ?? $productData['name'] ?? 'Produk';
        $existingImage = $productData['image_url'] ?? null;

        // 1. RULE ENGINE: Halal & Nutrition analysis
        $nutriments = $productData['nutriments'] ?? $productData['nutrition_estimate'] ?? [];
        $halal = $this->halalAnalyzer->analyze($ingredientsText);
        $nutrition = $this->nutritionAnalyzer->analyze($nutriments, $ingredientsText);

        $userContext['ingredients_text'] = $ingredientsText;
        $personal = $this->healthRiskAnalyzer->personalize($userContext, $nutrition, $halal);
        $behavior = $userId > 0 ? $this->behaviorAnalyzer->weeklySummary($userId) : [];

        // 2. OPEN ROUTER AI ANALYSIS (The Core Reasoning Engine)
        $aiResult = [];
        try {
            $aiResult = $this->openRouter->analyzeIngredients($ingredientsText, $userContext);
        } catch (\Throwable $e) {
            Log::warning('OpenRouter analyze failed: ' . $e->getMessage());
        }

        // Fallback to Gemini if OpenRouter fails or returns empty
        if (empty($aiResult)) {
            $promptVars = array_merge($userContext, $behavior, [
                'user_name' => $userContext['name'] ?? 'Pengguna',
                'product_name' => $productName,
                'barcode' => $productData['barcode'] ?? '-',
                'product_category' => $productData['category'] ?? 'makanan',
                'ingredients_text' => $ingredientsText,
                'sugars' => $nutriments['sugars'] ?? $nutriments['sugars_100g'] ?? 0,
                'sodium' => $nutriments['sodium'] ?? $nutriments['sodium_100g'] ?? 0,
                'fat' => $nutriments['fat'] ?? $nutriments['fat_100g'] ?? 0,
                'protein' => $nutriments['proteins'] ?? $nutriments['proteins_100g'] ?? 0,
                'calories' => $nutriments['energy_kcal'] ?? $nutriments['calories'] ?? 0,
                'halal_label' => $productData['halal_label'] ?? '-',
                'data_source' => $productData['source'] ?? 'AI Halalytics',
            ]);
            try {
                $prompt = $this->promptBuilder->build('food_analysis', $promptVars);
                $rawResponse = $this->gemini->generateCustomContent($prompt, 0.3, 3000);
                if (is_array($rawResponse)) {
                    $aiResult = $rawResponse;
                } elseif (is_string($rawResponse) && trim($rawResponse) !== '') {
                    $decoded = json_decode($rawResponse, true);
                    $aiResult = is_array($decoded) ? $decoded : [];
                }
                if (empty($aiResult)) {
                    $aiResult = $this->gemini->analyzeIngredients($ingredientsText, $userContext);
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini fallback failed: ' . $e->getMessage());
            }
        }

        // 3. IMAGE SEARCH SYSTEM
        $imageUrl = $this->imageSearch->findProductImage($productName, $existingImage);

        // 4. ASSEMBLE FINAL RESPONSE
        $merged = array_merge($aiResult, [
            // API Specifics
            'product'         => $productName,
            'image_url'       => $imageUrl,

            // Halal — rule engine always overrides AI for basic status if AI is doubtful
            'status'          => $halal['halal_status'] ?? ($aiResult['status'] ?? 'unknown'),
            'status_halal'    => $halal['halal_status'] ?? ($aiResult['status'] ?? 'unknown'),
            'halal_score'     => $aiResult['score'] ?? $halal['halal_score'] ?? 70,
            'reason'          => $aiResult['reason'] ?? $halal['summary'] ?? '',
            'risky_ingredients'=> $aiResult['risky_ingredients'] ?? $halal['flags'] ?? [],
            
            // Nutrisi
            'health_score'       => $nutrition['health_score'] ?? 70,
            'health_status'      => $nutrition['health_status'] ?? null,
            'sugar_risk'         => $nutrition['sugar_risk'] ?? 'rendah',
            'sodium_risk'        => $nutrition['sodium_risk'] ?? 'rendah',
            'fat_risk'           => $nutrition['fat_risk'] ?? 'rendah',
            'dominant_ingredient'=> $nutrition['dominant_ingredient'] ?? null,
            'is_ultra_processed' => $nutrition['is_ultra_processed'] ?? false,
            'nutrition_flags'    => $nutrition['nutrition_flags'] ?? [],
            'nutrition_values'   => $nutrition['nutrition_values'] ?? [],
            'nutrition_estimate' => $nutrition['nutrition_estimate'] ?? [],

            // Peringatan gabungan
            'watchouts' => array_values(array_unique(array_merge(
                $aiResult['watchouts'] ?? [],
                $personal['personal_warnings'] ?? [],
                $nutrition['nutrition_flags'] ?? [],
                $halal['health_warnings'] ?? [],
                $aiResult['risky_ingredients'] ?? []
            ))),

            // Pesan personal
            'personalized_message' => !empty($personal['personal_warnings'])
                ? implode("\n", $personal['personal_warnings'])
                : ($aiResult['personalized_message'] ?? ''),

            // Rekomendasi
            'recommendations'          => $this->recommendationService->suggest($nutrition, $halal, $personal),
            'long_term_consideration'  => $this->evidenceService->longTermNote($nutrition),

            // Metadata
            'ai_confidence'              => empty($aiResult) ? 'low' : 'high',
            'data_source'                => $productData['source'] ?? 'AI Halalytics + OpenRouter',
            'consult_nutritionist'       => $personal['consult_nutritionist'] ?? false,
            'consumption_pattern_message'=> $behavior['consumption_pattern_message'] ?? '',
        ]);

        if ($userId > 0) {
            $this->behaviorAnalyzer->recordScan($userId, array_merge($nutrition, [
                'category' => $productData['category'] ?? 'makanan',
            ]));
        }

        $elapsed = (int) ((microtime(true) - $start) * 1000);
        $this->logAi('food_analysis', $ingredientsText, $merged, $userId, $elapsed);

        return $merged;
    }

    public function chat(string $message, array $userContext): string
    {
        $start = microtime(true);
        $intent = $this->intentClassifier->classify($message);
        $type = match ($intent) {
            'HALAL_QUESTION' => 'halal_check',
            'DIET_ADVICE' => 'recommendation',
            default => 'user_chat',
        };

        $prompt = $this->promptBuilder->build($type, array_merge($userContext, [
            'user_message' => $message,
        ]), "Anda adalah AI Halalytics. Intent: {$intent}. Jawab dengan jelas, personal, tanpa placeholder.\n\nPertanyaan: {user_message}");

        $reply = trim((string) $this->gemini->generateText($prompt));
        $elapsed = (int) ((microtime(true) - $start) * 1000);
        $this->logAi($type, $message, ['reply' => $reply], (int) ($userContext['user_id'] ?? 0), $elapsed);

        return $reply;
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function logAi(string $type, string $input, array $response, int $userId, int $ms): void
    {
        if (! \Schema::hasTable('ai_logs')) {
            return;
        }

        try {
            AiLog::create([
                'user_id' => $userId ?: null,
                'prompt_type' => $type,
                'input_data' => mb_substr($input, 0, 5000),
                'ai_response' => mb_substr(json_encode($response, JSON_UNESCAPED_UNICODE), 0, 65000),
                'response_time_ms' => $ms,
            ]);
        } catch (\Throwable $e) {
            Log::debug('AI log skip: ' . $e->getMessage());
        }
    }

    /**
     * Cek apakah respons dari AI hanyalah kalimat placeholder.
     */
    private function isPlaceholder(mixed $text): bool
    {
        if (!is_string($text)) {
            if (is_array($text) && empty($text)) return true;
            return false;
        }
        $text = strtolower(trim($text));
        if ($text === '') return true;
        
        $placeholders = [
            'maaf', 'saya adalah ai', 'sebagai ai', 'tidak memiliki informasi', 
            'belum bisa memberikan', 'tidak ada data', 'string', 'placeholder'
        ];
        
        foreach ($placeholders as $ph) {
            if (str_contains($text, $ph)) {
                return true;
            }
        }
        
        return false;
    }
}
