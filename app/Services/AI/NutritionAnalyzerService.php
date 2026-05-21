<?php

namespace App\Services\AI;

use App\Models\NutritionRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class NutritionAnalyzerService
{
    /**
     * Analisis nutrisi produk secara lengkap.
     * Mengembalikan health_score, flag, dan detail risiko.
     */
    public function analyze(array $nutriments, string $ingredientsText = ''): array
    {
        // Ambil aturan dari database (admin bisa ubah threshold tanpa coding)
        $rules = Cache::remember('nutrition_rules_active', 3600, function () {
            if (! \Schema::hasTable('nutrition_rules')) {
                return $this->defaultRules();
            }
            $dbRules = NutritionRule::query()->where('is_active', true)->get()->keyBy('rule_key')->all();
            return ! empty($dbRules) ? $dbRules : $this->defaultRules();
        });

        // Normalisasi nilai nutrisi (support berbagai format dari OFF/OBF/FDA)
        $sugars   = (float) ($nutriments['sugars']          ?? $nutriments['sugars_100g']          ?? 0);
        $sodium   = (float) ($nutriments['sodium']          ?? $nutriments['sodium_100g']          ?? 0);
        $protein  = (float) ($nutriments['proteins']        ?? $nutriments['proteins_100g']        ?? 0);
        $fat      = (float) ($nutriments['fat']             ?? $nutriments['fat_100g']             ?? 0);
        $satFat   = (float) ($nutriments['saturated-fat']   ?? $nutriments['saturated_fat_100g']   ?? 0);
        $calories = (float) ($nutriments['energy-kcal']     ?? $nutriments['energy_kcal_100g']     ?? $nutriments['calories'] ?? 0);
        $fiber    = (float) ($nutriments['fiber']           ?? $nutriments['fiber_100g']           ?? 0);
        $carbs    = (float) ($nutriments['carbohydrates']   ?? $nutriments['carbohydrates_100g']   ?? 0);

        // Threshold dari database atau default
        $sugarThreshold  = (float) ($rules['sugar_high']->threshold_value  ?? 20);
        $sodiumThreshold = (float) ($rules['sodium_high']->threshold_value ?? 600);
        $fatThreshold    = (float) ($rules['fat_high']->threshold_value    ?? 20);
        $satFatThreshold = (float) ($rules['sat_fat_high']->threshold_value ?? 5);
        $calThreshold    = (float) ($rules['calories_high']->threshold_value ?? 400);

        // Hitung level risiko
        $sugarRisk  = $this->riskLevel($sugars,  $sugarThreshold);
        $sodiumRisk = $this->riskLevel($sodium,  $sodiumThreshold);
        $fatRisk    = $this->riskLevel($fat,     $fatThreshold);
        $satFatRisk = $this->riskLevel($satFat,  $satFatThreshold);

        // Deteksi bahan dominan (bahan pertama dalam komposisi = paling banyak)
        $dominant = $this->detectDominantIngredient($ingredientsText);

        // Deteksi ultra-processed (NOVA group 4 indicators)
        $isUltraProcessed = $this->detectUltraProcessed($ingredientsText);

        // Kumpulkan flag peringatan
        $flags = [];
        if ($sugarRisk === 'tinggi')  $flags[] = 'Tinggi Gula';
        if ($sugarRisk === 'sedang')  $flags[] = 'Gula Sedang';
        if ($sodiumRisk === 'tinggi') $flags[] = 'Tinggi Sodium/Garam';
        if ($sodiumRisk === 'sedang') $flags[] = 'Sodium Sedang';
        if ($fatRisk === 'tinggi')    $flags[] = 'Tinggi Lemak';
        if ($satFatRisk === 'tinggi') $flags[] = 'Tinggi Lemak Jenuh';
        if ($protein < 3 && $sugars > $sugarThreshold) $flags[] = 'Kalori Kosong (tinggi gula, rendah protein)';
        if ($dominant && str_contains(Str::lower($dominant), 'gula')) {
            $flags[] = 'Gula sebagai bahan UTAMA (urutan pertama komposisi)';
        }
        if ($dominant && str_contains(Str::lower($dominant), 'sugar')) {
            $flags[] = 'Sugar sebagai bahan UTAMA (urutan pertama komposisi)';
        }
        if ($isUltraProcessed) $flags[] = 'Terindikasi Ultra-Processed Food';
        if ($calories > $calThreshold) $flags[] = 'Kalori Tinggi per 100g';

        // Hitung health score (100 = sempurna, turun berdasarkan flag)
        $healthScore = 100;
        $healthScore -= $sugarRisk  === 'tinggi' ? 35 : ($sugarRisk  === 'sedang' ? 15 : 0);
        $healthScore -= $sodiumRisk === 'tinggi' ? 25 : ($sodiumRisk === 'sedang' ? 10 : 0);
        $healthScore -= $fatRisk    === 'tinggi' ? 15 : ($fatRisk    === 'sedang' ?  5 : 0);
        $healthScore -= $satFatRisk === 'tinggi' ? 10 : 0;
        $healthScore -= ($protein < 3 && $sugars > 15) ? 15 : 0;
        $healthScore -= $isUltraProcessed ? 10 : 0;
        $healthScore -= ($dominant && str_contains(Str::lower($dominant), 'gula')) ? 10 : 0;
        $healthScore  = max(5, min(100, $healthScore));

        // Status kesehatan berdasarkan skor
        $healthStatus = match (true) {
            $healthScore >= 75 => 'Cukup Sehat',
            $healthScore >= 50 => 'Kurang Sehat',
            $healthScore >= 30 => 'Tidak Sehat untuk Konsumsi Sering',
            default            => 'Sangat Tidak Sehat — Batasi Ketat',
        };

        return [
            'health_score'       => $healthScore,
            'health_status'      => $healthStatus,
            'sugar_risk'         => $sugarRisk,
            'sodium_risk'        => $sodiumRisk,
            'fat_risk'           => $fatRisk,
            'sat_fat_risk'       => $satFatRisk,
            'dominant_ingredient'=> $dominant,
            'is_ultra_processed' => $isUltraProcessed,
            'nutrition_flags'    => $flags,
            'nutrition_values'   => [
                'sugars_g'    => $sugars,
                'sodium_mg'   => $sodium,
                'fat_g'       => $fat,
                'sat_fat_g'   => $satFat,
                'protein_g'   => $protein,
                'calories'    => $calories,
                'fiber_g'     => $fiber,
                'carbs_g'     => $carbs,
            ],
            // Untuk kompatibilitas dengan nutrition_estimate di AI pipeline
            'nutrition_estimate' => [
                'sugar_g'    => $sugars,
                'sodium_mg'  => $sodium,
                'calories'   => $calories,
                'carbs_g'    => $carbs,
                'protein_g'  => $protein,
                'fat_g'      => $fat,
            ],
        ];
    }

    /**
     * Hitung level risiko: rendah / sedang / tinggi
     */
    private function riskLevel(float $value, float $threshold): string
    {
        if ($value > $threshold)          return 'tinggi';
        if ($value > $threshold * 0.5)    return 'sedang';
        return 'rendah';
    }

    /**
     * Deteksi bahan dominan (bahan pertama = paling banyak dalam produk)
     */
    private function detectDominantIngredient(string $text): ?string
    {
        if (trim($text) === '') return null;
        $parts = preg_split('/[,;\n]+/', $text);
        $first = trim((string) ($parts[0] ?? ''));
        return $first !== '' ? $first : null;
    }

    /**
     * Deteksi ultra-processed food berdasarkan bahan-bahan khas NOVA group 4
     */
    private function detectUltraProcessed(string $ingredientsText): bool
    {
        if (trim($ingredientsText) === '') return false;

        $normalized = Str::lower($ingredientsText);
        $ultraProcessedMarkers = [
            'high fructose corn syrup', 'hfcs', 'sirup jagung',
            'hydrogenated', 'terhidrogenasi', 'partially hydrogenated',
            'modified starch', 'pati termodifikasi',
            'artificial flavor', 'artificial colour', 'artificial color',
            'tbhq', 'bha', 'bht',
            'sodium benzoate', 'natrium benzoat',
            'carrageenan', 'karagenan',
            'maltodextrin', 'maltodekstrin',
            'interesterified', 'fractionated',
        ];

        $count = 0;
        foreach ($ultraProcessedMarkers as $marker) {
            if (str_contains($normalized, $marker)) {
                $count++;
                if ($count >= 2) return true; // 2+ marker = ultra-processed
            }
        }

        return false;
    }

    /**
     * Default rules jika tabel nutrition_rules belum ada
     */
    private function defaultRules(): array
    {
        return [
            'sugar_high'    => (object) ['threshold_value' => 20],
            'sodium_high'   => (object) ['threshold_value' => 600],
            'fat_high'      => (object) ['threshold_value' => 20],
            'sat_fat_high'  => (object) ['threshold_value' => 5],
            'calories_high' => (object) ['threshold_value' => 400],
        ];
    }
}
