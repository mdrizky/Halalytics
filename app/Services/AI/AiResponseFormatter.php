<?php

namespace App\Services\AI;

use Illuminate\Support\Str;

class AiResponseFormatter
{
    /**
     * Format Gemini/rule analysis for mobile AiAnalysisContent.
     *
     * @param  array<string, mixed>  $analysis
     * @return array<string, mixed>
     */
    public function forMobile(array $analysis): array
    {
        $status = Str::lower((string) ($analysis['status_halal'] ?? $analysis['status'] ?? 'unknown'));
        $ringkasan = trim((string) ($analysis['ringkasan'] ?? ''));
        $recommendation = trim((string) ($analysis['recommendation'] ?? ''));
        $watchouts = $analysis['watchouts'] ?? [];
        if (! is_array($watchouts)) {
            $watchouts = [];
        }

        $healthRisk = Str::lower((string) ($analysis['health_risk'] ?? 'low'));
        $confidence = $this->confidenceFromStatus($status, $analysis);

        // Build a real, informative analysis text — NEVER a placeholder
        $analysisText = $this->buildAnalysisParagraph($status, $ringkasan, $recommendation, $watchouts, $analysis);

        // Ensure ringkasan is never empty or a placeholder
        if ($ringkasan === '' || $this->isPlaceholder($ringkasan)) {
            $ringkasan = $this->halalSummary($status);
        }

        // Ensure recommendation is never empty
        if ($recommendation === '' || $this->isPlaceholder($recommendation)) {
            $recommendation = $this->defaultRecommendation($status, $analysis);
        }

        return [
            'status' => $this->displayStatus($status),
            'confidence' => $confidence,
            'analysis' => $analysisText,
            'ringkasan' => $ringkasan,
            'red_flags' => array_values(array_filter(array_map('strval', $watchouts))),
            'health_risk' => in_array($healthRisk, ['high', 'moderate', 'low'], true) ? $healthRisk : 'low',
            'halal_score' => (int) ($analysis['halal_score'] ?? $this->scoreFromStatus($status)),
            'health_score' => (int) ($analysis['health_score'] ?? $this->healthScoreFromRisk($healthRisk)),
            'personalized_message' => (string) ($analysis['personalized_message'] ?? $recommendation),
            'recommendations' => $analysis['recommendations'] ?? ($recommendation !== '' ? [$recommendation] : []),
            'long_term_consideration' => (string) ($analysis['long_term_consideration'] ?? ''),
            'short_term_effect' => (string) ($analysis['short_term_effect'] ?? ''),
            'dominant_ingredient' => $analysis['dominant_ingredient'] ?? null,
            'dangerous_ingredients_found' => $analysis['dangerous_ingredients_found'] ?? [],
            'consult_nutritionist' => (bool) ($analysis['consult_nutritionist'] ?? false),
            'data_source' => (string) ($analysis['data_source'] ?? 'AI Halalytics'),
            'ai_confidence' => (string) ($analysis['ai_confidence'] ?? 'medium'),
        ];
    }

    /**
     * Check if a string is a placeholder/generic response that should be replaced.
     */
    private function isPlaceholder(string $text): bool
    {
        $placeholders = [
            'analisis sementara tersedia',
            'silakan gunakan hasil ini sebagai referensi awal',
            'verifikasi dengan sumber resmi',
            'analisis belum tersedia',
            'data tidak tersedia',
        ];

        $lower = Str::lower($text);
        foreach ($placeholders as $placeholder) {
            if (str_contains($lower, $placeholder)) {
                return true;
            }
        }

        return false;
    }

    private function defaultRecommendation(string $status, array $analysis): string
    {
        $healthScore = (int) ($analysis['health_score'] ?? 70);
        $sugarRisk = $analysis['sugar_risk'] ?? 'rendah';
        $sodiumRisk = $analysis['sodium_risk'] ?? 'rendah';

        $parts = [];

        if ($status === 'haram') {
            $parts[] = 'Hindari produk ini karena terindikasi mengandung bahan tidak halal.';
        } elseif ($status === 'syubhat') {
            $parts[] = 'Verifikasi sertifikasi halal resmi (MUI/BPJPH) sebelum mengonsumsi secara rutin.';
        }

        if ($sugarRisk === 'tinggi') {
            $parts[] = 'Batasi konsumsi karena kandungan gula tinggi. Pilih alternatif rendah gula.';
        }
        if ($sodiumRisk === 'tinggi') {
            $parts[] = 'Perhatikan asupan sodium harian, terutama jika memiliki riwayat hipertensi.';
        }
        if ($healthScore < 50) {
            $parts[] = 'Konsumsi sesekali saja, bukan sebagai makanan utama sehari-hari.';
        }

        if (empty($parts)) {
            $parts[] = 'Produk relatif aman untuk konsumsi sesekali. Tetap baca label dan perhatikan porsi.';
        }

        $parts[] = 'Konsultasikan dengan ahli gizi untuk rencana diet yang sesuai kondisi Anda.';

        return implode(' ', $parts);
    }

    /**
     * @param  array<string, mixed>  $analysis
     */
    private function buildAnalysisParagraph(
        string $status,
        string $ringkasan,
        string $recommendation,
        array $watchouts,
        array $analysis
    ): string {
        $parts = [];

        $halalLabel = match ($status) {
            'haram' => 'Berisiko — terindikasi bahan tidak halal',
            'syubhat' => 'Syubhat — perlu verifikasi sertifikasi/sumber bahan',
            'halal' => 'Kemungkinan Halal (analisis AI)',
            default => 'Status halal belum dapat dipastikan',
        };
        $parts[] = "Status halal: {$halalLabel}.";

        if ($ringkasan !== '') {
            $parts[] = $ringkasan;
        }

        if (! empty($watchouts)) {
            $parts[] = 'Peringatan: ' . implode('; ', array_slice($watchouts, 0, 5)) . '.';
        }

        if ($recommendation !== '') {
            $parts[] = $recommendation;
        }

        $nutrition = $analysis['nutrition_estimate'] ?? null;
        if (is_array($nutrition)) {
            $sugar = $nutrition['sugar_g'] ?? null;
            $sodium = $nutrition['sodium_mg'] ?? null;
            if ($sugar !== null && (float) $sugar > 20) {
                $parts[] = "Kandungan gula tinggi ({$sugar}g/100g) — batasi jika Anda punya risiko diabetes/obesitas.";
            }
            if ($sodium !== null && (float) $sodium > 600) {
                $parts[] = "Kandungan sodium tinggi ({$sodium}mg/100g) — perhatikan jika hipertensi.";
            }
        }

        $parts[] = 'Konsultasikan ahli gizi atau dokter untuk kondisi medis spesifik Anda.';

        return implode("\n\n", array_filter($parts));
    }

    private function displayStatus(string $status): string
    {
        return match ($status) {
            'haram' => 'Haram',
            'syubhat' => 'Syubhat',
            'halal' => 'Halal',
            default => 'Unknown',
        };
    }

    private function halalSummary(string $status): string
    {
        return match ($status) {
            'haram' => 'Ditemukan indikasi bahan yang tidak halal. Hindari hingga diverifikasi.',
            'syubhat' => 'Ada bahan syubhat — verifikasi sertifikasi MUI/BPJPH atau sumber bahan.',
            'halal' => 'Tidak ditemukan bahan haram jelas dari komposisi yang tersedia.',
            default => 'Data komposisi terbatas — analisis bersifat indikatif.',
        };
    }

    /**
     * @param  array<string, mixed>  $analysis
     */
    private function confidenceFromStatus(string $status, array $analysis): int
    {
        if (isset($analysis['ai_confidence'])) {
            return match (Str::lower((string) $analysis['ai_confidence'])) {
                'high' => 90,
                'medium' => 70,
                'low' => 45,
                default => 60,
            };
        }

        return match ($status) {
            'halal', 'haram' => 85,
            'syubhat' => 65,
            default => 50,
        };
    }

    private function scoreFromStatus(string $status): int
    {
        return match ($status) {
            'halal' => 85,
            'syubhat' => 55,
            'haram' => 20,
            default => 50,
        };
    }

    private function healthScoreFromRisk(string $healthRisk): int
    {
        return match ($healthRisk) {
            'high' => 25,
            'moderate' => 50,
            default => 75,
        };
    }
}
