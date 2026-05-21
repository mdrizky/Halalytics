<?php

namespace App\Services\Analysis;

class ProductAnalysisService
{
    public function analyze(array $product, array $userContext = []): array
    {
        $ingredients = strtolower((string)($product['ingredients'] ?? ''));
        $sugar = (float)($product['nutriments']['sugars'] ?? 0);
        $sodium = (float)($product['nutriments']['sodium'] ?? 0);

        $halalStatus = 'Kemungkinan Halal (AI Analysis)';
        $halalScore = 85;
        $notes = [];

        $haramKeywords = ['pork', 'lard', 'babi', 'blood'];
        foreach ($haramKeywords as $kw) {
            if (str_contains($ingredients, $kw)) {
                $halalStatus = 'Berisiko — terindikasi bahan haram';
                $halalScore = 20;
                $notes[] = "Terdeteksi bahan berisiko: {$kw}";
                break;
            }
        }

        $healthScore = 80;
        $warnings = [];
        if ($sugar > 20) {
            $healthScore -= 30;
            $warnings[] = 'Tinggi gula';
        }
        if ($sodium > 600) {
            $healthScore -= 25;
            $warnings[] = 'Tinggi sodium';
        }

        $sideEffects = [];
        if (str_contains($ingredients, 'caffeine') || str_contains($ingredients, 'kafein')) {
            $sideEffects[] = 'Kafein berlebih dapat menyebabkan gangguan tidur dan jantung berdebar pada sebagian orang.';
        }
        if ($sugar > 20) {
            $sideEffects[] = 'Konsumsi gula berlebihan jangka panjang dapat meningkatkan risiko resistensi insulin dan diabetes tipe 2.';
        }

        return [
            'halal_status' => $halalStatus,
            'halal_score' => max(0, min(100, $halalScore)),
            'health_score' => max(0, min(100, $healthScore)),
            'warnings' => $warnings,
            'analysis' => empty($notes) ? 'Tidak ditemukan bahan jelas haram dari data komposisi saat ini.' : implode('; ', $notes),
            'side_effects' => $sideEffects,
            'recommendation' => 'Konsumsi secukupnya. Konsultasikan ke ahli gizi/dokter untuk kondisi spesifik.',
        ];
    }

    public function analyzeDetailed(array $product, array $userContext = []): array
    {
        $base = $this->analyze($product, $userContext);

        $ingredientsText = (string) ($product['ingredients'] ?? '');
        $dominantIngredient = 'Tidak diketahui';
        if ($ingredientsText !== '') {
            $parts = array_map('trim', explode(',', $ingredientsText));
            $dominantIngredient = $parts[0] ?? 'Tidak diketahui';
        }

        $healthStatus = $base['health_score'] >= 70
            ? 'Cukup Sehat'
            : ($base['health_score'] >= 40 ? 'Perlu Perhatian' : 'Kurang Sehat');

        $shortTerm = empty($base['warnings'])
            ? 'Tidak ada efek jangka pendek signifikan dari data saat ini jika dikonsumsi wajar.'
            : 'Dalam jangka pendek, konsumsi berlebihan dapat memicu keluhan sesuai faktor risiko: ' . implode(', ', $base['warnings']) . '.';

        $longTerm = empty($base['side_effects'])
            ? 'Belum ada sinyal risiko jangka panjang dominan dari data ini.'
            : implode(' ', $base['side_effects']);

        $personal = 'Konsumsi secukupnya dan seimbangkan dengan pola makan sehat.';
        $diseases = strtolower((string) json_encode($userContext['diseases'] ?? []));
        if (str_contains($diseases, 'diabetes') && in_array('Tinggi gula', $base['warnings'], true)) {
            $personal = 'Karena profil menunjukkan risiko diabetes, produk tinggi gula sebaiknya dibatasi ketat dan konsultasikan ke ahli gizi.';
        }

        return [
            'halal_status' => $base['halal_status'],
            'halal_score' => $base['halal_score'],
            'health_status' => $healthStatus,
            'health_score' => $base['health_score'],
            'dominant_ingredient' => $dominantIngredient,
            'short_term_effect' => $shortTerm,
            'long_term_effect' => $longTerm,
            'personalized_recommendation' => $personal,
            'confidence' => 'medium',
            'sources' => ['OpenFoodFacts', 'Rule-Based Analyzer v1'],
            'warnings' => $base['warnings'],
        ];
    }

}
