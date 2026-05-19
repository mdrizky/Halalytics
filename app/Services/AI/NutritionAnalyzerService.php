<?php

namespace App\Services\AI;

use App\Models\NutritionRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class NutritionAnalyzerService
{
    public function analyze(array $nutriments, string $ingredientsText = ''): array
    {
        $rules = Cache::remember('nutrition_rules_active', 3600, function () {
            if (! \Schema::hasTable('nutrition_rules')) {
                return $this->defaultRules();
            }

            return NutritionRule::query()->where('is_active', true)->get()->keyBy('rule_key')->all()
                ?: $this->defaultRules();
        });

        $sugars = (float) ($nutriments['sugars'] ?? $nutriments['sugars_100g'] ?? 0);
        $sodium = (float) ($nutriments['sodium'] ?? $nutriments['sodium_100g'] ?? 0);
        $protein = (float) ($nutriments['proteins'] ?? $nutriments['proteins_100g'] ?? 0);
        $fat = (float) ($nutriments['fat'] ?? $nutriments['fat_100g'] ?? 0);

        $sugarThreshold = (float) ($rules['sugar_high']->threshold_value ?? 20);
        $sodiumThreshold = (float) ($rules['sodium_high']->threshold_value ?? 600);

        $sugarRisk = $sugars > $sugarThreshold ? 'tinggi' : ($sugars > $sugarThreshold * 0.5 ? 'sedang' : 'rendah');
        $sodiumRisk = $sodium > $sodiumThreshold ? 'tinggi' : ($sodium > $sodiumThreshold * 0.5 ? 'sedang' : 'rendah');

        $dominant = $this->detectDominantIngredient($ingredientsText);
        $flags = [];
        if ($sugarRisk === 'tinggi') {
            $flags[] = 'Tinggi Gula';
        }
        if ($sodiumRisk === 'tinggi') {
            $flags[] = 'Tinggi Sodium';
        }
        if ($protein < 3 && $sugars > $sugarThreshold) {
            $flags[] = 'Kalori Kosong';
        }
        if ($dominant && str_contains(Str::lower($dominant), 'gula')) {
            $flags[] = 'Gula sebagai bahan dominan';
        }

        $healthScore = 100;
        $healthScore -= $sugarRisk === 'tinggi' ? 35 : ($sugarRisk === 'sedang' ? 15 : 0);
        $healthScore -= $sodiumRisk === 'tinggi' ? 25 : ($sodiumRisk === 'sedang' ? 10 : 0);
        $healthScore -= ($protein < 3 && $sugars > 15) ? 20 : 0;
        $healthScore = max(5, min(100, $healthScore));

        return [
            'health_score' => $healthScore,
            'health_status' => $healthScore >= 70 ? 'Cukup Sehat' : ($healthScore >= 40 ? 'Kurang Sehat' : 'Tidak Sehat untuk konsumsi sering'),
            'sugar_risk' => $sugarRisk,
            'sodium_risk' => $sodiumRisk,
            'dominant_ingredient' => $dominant,
            'nutrition_flags' => $flags,
        ];
    }

    private function detectDominantIngredient(string $text): ?string
    {
        $parts = preg_split('/[,;\n]+/', $text);

        return trim((string) ($parts[0] ?? '')) ?: null;
    }

    private function defaultRules(): array
    {
        return [
            'sugar_high' => (object) ['threshold_value' => 20],
            'sodium_high' => (object) ['threshold_value' => 600],
        ];
    }
}
