<?php

namespace App\Services\AI;

use App\Models\AiLog;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;

class FoodAnalysisOrchestrator
{
    public function __construct(
        private readonly GeminiService $gemini,
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

        $nutriments = $productData['nutriments'] ?? $productData['nutrition_estimate'] ?? [];
        $halal = $this->halalAnalyzer->analyze($ingredientsText);
        $nutrition = $this->nutritionAnalyzer->analyze($nutriments, $ingredientsText);

        $userContext['ingredients_text'] = $ingredientsText;
        $personal = $this->healthRiskAnalyzer->personalize($userContext, $nutrition, $halal);

        $behavior = $userId > 0 ? $this->behaviorAnalyzer->weeklySummary($userId) : [];

        $promptVars = array_merge($userContext, $behavior, [
            'user_name' => $userContext['name'] ?? 'Pengguna',
            'product_name' => $productData['product_name'] ?? $productData['name'] ?? 'Produk',
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

        $geminiResult = [];
        try {
            // Build full prompt from DB template (admin-editable) with all context injected
            $prompt = $this->promptBuilder->build('food_analysis', $promptVars);

            // Send the full contextual prompt to Gemini — NOT just ingredients text
            $rawResponse = $this->gemini->generateCustomContent($prompt, 0.3, 3000);

            if (is_array($rawResponse)) {
                $geminiResult = $rawResponse;
            } elseif (is_string($rawResponse) && trim($rawResponse) !== '') {
                // Try to parse if string returned
                $decoded = json_decode($rawResponse, true);
                $geminiResult = is_array($decoded) ? $decoded : [];
            }

            // Fallback: if Gemini returned empty, use the ingredient-only analysis
            if (empty($geminiResult)) {
                $geminiResult = $this->gemini->analyzeIngredients($ingredientsText, $userContext);
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini analyze failed, using rule engine: ' . $e->getMessage());
            // Rule engine fallback — still returns meaningful data
            try {
                $geminiResult = $this->gemini->analyzeIngredients($ingredientsText, $userContext);
            } catch (\Throwable $e2) {
                Log::error('Rule engine also failed: ' . $e2->getMessage());
            }
        }

        $merged = array_merge($geminiResult, [
            // Halal — rule engine selalu override Gemini untuk akurasi
            'status'          => $halal['halal_status'] ?? ($geminiResult['status_halal'] ?? 'unknown'),
            'status_halal'    => $halal['halal_status'] ?? ($geminiResult['status_halal'] ?? 'unknown'),
            'halal_score'     => $halal['halal_score']  ?? ($geminiResult['halal_score']  ?? 70),
            'halal_flags'     => $halal['flags']        ?? [],
            'health_warnings' => $halal['health_warnings'] ?? [],

            // Nutrisi — rule engine selalu override
            'health_score'       => $nutrition['health_score']       ?? ($geminiResult['health_score'] ?? 70),
            'health_status'      => $nutrition['health_status']      ?? null,
            'sugar_risk'         => $nutrition['sugar_risk']         ?? 'rendah',
            'sodium_risk'        => $nutrition['sodium_risk']        ?? 'rendah',
            'fat_risk'           => $nutrition['fat_risk']           ?? 'rendah',
            'dominant_ingredient'=> $nutrition['dominant_ingredient'] ?? null,
            'is_ultra_processed' => $nutrition['is_ultra_processed'] ?? false,
            'nutrition_flags'    => $nutrition['nutrition_flags']    ?? [],
            'nutrition_values'   => $nutrition['nutrition_values']   ?? [],
            'nutrition_estimate' => $nutrition['nutrition_estimate'] ?? [],

            // Ringkasan — Gemini lebih baik, fallback ke rule engine
            'ringkasan'   => (! empty($geminiResult['ringkasan']) && ! $this->isPlaceholder($geminiResult['ringkasan'] ?? ''))
                ? $geminiResult['ringkasan']
                : $halal['summary'],

            'recommendation' => (! empty($geminiResult['recommendation']) && ! $this->isPlaceholder($geminiResult['recommendation'] ?? ''))
                ? $geminiResult['recommendation']
                : null,

            // Peringatan gabungan dari semua sumber
            'watchouts' => array_values(array_unique(array_merge(
                $geminiResult['watchouts']          ?? [],
                $personal['personal_warnings']      ?? [],
                $nutrition['nutrition_flags']        ?? [],
                $halal['health_warnings']            ?? [],
            ))),

            // Pesan personal
            'personalized_message' => ! empty($personal['personal_warnings'])
                ? implode("\n", $personal['personal_warnings'])
                : ($geminiResult['personalized_message'] ?? ''),

            // Rekomendasi dari service
            'recommendations'          => $this->recommendationService->suggest($nutrition, $halal, $personal),
            'long_term_consideration'  => $this->evidenceService->longTermNote($nutrition),
            'short_term_effect'        => $geminiResult['short_term_effect'] ?? '',

            // Metadata
            'ai_confidence'              => empty($geminiResult) ? 'medium' : 'high',
            'data_source'                => $productData['source'] ?? 'AI Halalytics + Gemini',
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
}
