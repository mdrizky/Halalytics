<?php

namespace App\Services\AI;

use Illuminate\Support\Str;

class HealthRiskAnalyzer
{
    /**
     * Personalisasi analisis risiko berdasarkan profil user.
     * Mengembalikan peringatan personal, level risiko, dan rekomendasi konsultasi.
     */
    public function personalize(array $userContext, array $nutrition, array $halal): array
    {
        $warnings  = [];
        $history   = Str::lower((string) ($userContext['medical_history'] ?? ''));
        $allergy   = Str::lower((string) ($userContext['allergies'] ?? $userContext['allergy'] ?? ''));
        $bmi       = (float) ($userContext['bmi'] ?? 0);
        $age       = (int)   ($userContext['age'] ?? $userContext['user_age'] ?? 0);
        $ingredients = Str::lower((string) ($userContext['ingredients_text'] ?? ''));

        $sugarRisk  = $nutrition['sugar_risk']  ?? 'rendah';
        $sodiumRisk = $nutrition['sodium_risk'] ?? 'rendah';
        $fatRisk    = $nutrition['fat_risk']    ?? 'rendah';
        $healthScore = (int) ($nutrition['health_score'] ?? 100);

        // ─── Kondisi Medis ───────────────────────────────────────────────────

        // Diabetes
        if ($this->hasCondition($history, ['diabet', 'diabetes', 'gula darah', 'dm tipe'])) {
            if ($sugarRisk === 'tinggi') {
                $warnings[] = '⚠️ PERINGATAN KERAS: Produk ini tinggi gula — SANGAT TIDAK DIREKOMENDASIKAN untuk penderita diabetes. Konsumsi gula berlebih dapat memperburuk kadar gula darah.';
            } elseif ($sugarRisk === 'sedang') {
                $warnings[] = '⚠️ Perhatian: Produk ini mengandung gula sedang. Pantau kadar gula darah Anda setelah mengonsumsi.';
            }
        }

        // Hipertensi / Tekanan Darah Tinggi
        if ($this->hasCondition($history, ['hipertensi', 'darah tinggi', 'tekanan darah', 'hypertension'])) {
            if ($sodiumRisk === 'tinggi') {
                $warnings[] = '⚠️ PERINGATAN: Produk ini tinggi sodium/garam — tidak direkomendasikan untuk penderita hipertensi. Sodium tinggi dapat meningkatkan tekanan darah.';
            } elseif ($sodiumRisk === 'sedang') {
                $warnings[] = '⚠️ Perhatian: Kandungan sodium sedang. Batasi porsi dan pantau tekanan darah Anda.';
            }
        }

        // Obesitas / Kelebihan Berat Badan
        if ($this->hasCondition($history, ['obes', 'overweight', 'kelebihan berat']) || $bmi >= 27.5) {
            if ($sugarRisk === 'tinggi') {
                $warnings[] = '⚠️ Sangat tidak direkomendasikan untuk profil kelebihan berat badan/obesitas karena kandungan gula tinggi yang dapat menambah kalori kosong.';
            }
            if ($fatRisk === 'tinggi') {
                $warnings[] = '⚠️ Kandungan lemak tinggi — batasi konsumsi untuk menjaga berat badan ideal.';
            }
        }

        // Kolesterol Tinggi
        if ($this->hasCondition($history, ['kolesterol', 'cholesterol', 'dislipidemia'])) {
            if ($fatRisk === 'tinggi' || ($nutrition['sat_fat_risk'] ?? '') === 'tinggi') {
                $warnings[] = '⚠️ Produk ini tinggi lemak/lemak jenuh — tidak direkomendasikan untuk penderita kolesterol tinggi.';
            }
        }

        // Penyakit Jantung
        if ($this->hasCondition($history, ['jantung', 'kardiovaskular', 'heart disease', 'gagal jantung'])) {
            if ($sodiumRisk === 'tinggi' || $fatRisk === 'tinggi') {
                $warnings[] = '⚠️ PERINGATAN: Produk ini mengandung sodium/lemak tinggi — konsultasikan dengan dokter jantung Anda sebelum mengonsumsi.';
            }
        }

        // Penyakit Ginjal
        if ($this->hasCondition($history, ['ginjal', 'kidney', 'renal', 'gagal ginjal'])) {
            if ($sodiumRisk === 'tinggi') {
                $warnings[] = '⚠️ PERINGATAN KERAS: Sodium tinggi sangat berbahaya untuk penderita penyakit ginjal. Hindari produk ini.';
            }
            if ($fatRisk === 'tinggi') {
                $warnings[] = '⚠️ Kandungan lemak tinggi perlu diperhatikan untuk kondisi ginjal Anda.';
            }
        }

        // Asam Urat / Gout
        if ($this->hasCondition($history, ['asam urat', 'gout', 'hiperurisemia'])) {
            if (str_contains($ingredients, 'fructose') || str_contains($ingredients, 'fruktosa') ||
                str_contains($ingredients, 'high fructose')) {
                $warnings[] = '⚠️ Produk ini mengandung fruktosa yang dapat meningkatkan kadar asam urat.';
            }
        }

        // Maag / GERD
        if ($this->hasCondition($history, ['maag', 'gerd', 'asam lambung', 'gastritis', 'ulcer'])) {
            if (str_contains($ingredients, 'cuka') || str_contains($ingredients, 'vinegar') ||
                str_contains($ingredients, 'asam sitrat') || str_contains($ingredients, 'citric acid')) {
                $warnings[] = '⚠️ Produk ini mengandung bahan asam yang dapat memperburuk kondisi maag/GERD.';
            }
        }

        // Ibu Hamil / Menyusui
        if ($this->hasCondition($history, ['hamil', 'pregnant', 'menyusui', 'breastfeeding'])) {
            if (str_contains($ingredients, 'kafein') || str_contains($ingredients, 'caffeine') ||
                str_contains($ingredients, 'kopi') || str_contains($ingredients, 'teh hijau')) {
                $warnings[] = '⚠️ Produk ini mengandung kafein — batasi konsumsi saat hamil/menyusui (maks 200mg/hari).';
            }
            if (str_contains($ingredients, 'aspartame') || str_contains($ingredients, 'aspartam')) {
                $warnings[] = '⚠️ Produk ini mengandung aspartam — konsultasikan dengan dokter kandungan Anda.';
            }
        }

        // Anak-anak (usia < 12 tahun)
        if ($age > 0 && $age < 12) {
            if ($sugarRisk === 'tinggi') {
                $warnings[] = '⚠️ Produk ini tinggi gula — tidak direkomendasikan untuk anak-anak karena dapat merusak gigi dan mempengaruhi perkembangan.';
            }
            if (str_contains($ingredients, 'kafein') || str_contains($ingredients, 'caffeine')) {
                $warnings[] = '⚠️ Produk ini mengandung kafein — TIDAK DIREKOMENDASIKAN untuk anak-anak.';
            }
        }

        // ─── Deteksi Alergen ─────────────────────────────────────────────────

        if ($allergy !== '') {
            $allergenList = preg_split('/[,;|\/\n]+/', $allergy);
            foreach ($allergenList as $allergen) {
                $allergen = trim($allergen);
                if ($allergen === '') continue;

                // Cek di teks bahan
                if (str_contains($ingredients, $allergen)) {
                    $warnings[] = "🚨 WARNING ALERGI: Terdeteksi '{$allergen}' dalam komposisi produk ini. HINDARI jika Anda alergi terhadap bahan ini.";
                }

                // Cek sinonim umum alergen
                $synonyms = $this->allergenSynonyms($allergen);
                foreach ($synonyms as $synonym) {
                    if (str_contains($ingredients, $synonym)) {
                        $warnings[] = "🚨 WARNING ALERGI: Terdeteksi '{$synonym}' (sinonim dari '{$allergen}') dalam komposisi. HINDARI jika Anda alergi.";
                        break;
                    }
                }
            }
        }

        // ─── Cross-Domain Analysis (Halal vs Health) ─────────────────────────
        
        $halalStatus = $halal['halal_status'] ?? 'unknown';
        if (in_array($halalStatus, ['halal', 'Kemungkinan Halal'])) {
            if ($sugarRisk === 'tinggi' || $sodiumRisk === 'tinggi' || $fatRisk === 'tinggi') {
                $unhealthyFactors = [];
                if ($sugarRisk === 'tinggi') $unhealthyFactors[] = 'gula';
                if ($sodiumRisk === 'tinggi') $unhealthyFactors[] = 'sodium/garam';
                if ($fatRisk === 'tinggi') $unhealthyFactors[] = 'lemak';
                
                $factorsString = implode(', ', $unhealthyFactors);
                $warnings[] = "⚠️ PARADOX: Produk ini terindikasi HALAL, namun tinggi {$factorsString}. Tidak disarankan untuk konsumsi rutin demi menjaga kesehatan Anda.";
            }
        }

        // ─── Hitung Level Risiko ─────────────────────────────────────────────

        $criticalWarnings = collect($warnings)->filter(fn ($w) => str_contains($w, 'KERAS') || str_contains($w, '🚨'))->count();
        $normalWarnings   = count($warnings) - $criticalWarnings;

        $riskLevel = match (true) {
            $criticalWarnings >= 1 || count($warnings) >= 3 => 'high',
            $normalWarnings >= 1 || count($warnings) >= 1   => 'moderate',
            default                                          => 'low',
        };

        return [
            'personal_warnings'   => $warnings,
            'consumption_risk'    => $riskLevel,
            'consult_nutritionist'=> $riskLevel !== 'low' || $healthScore < 50,
        ];
    }

    /**
     * Cek apakah riwayat medis mengandung salah satu kondisi
     */
    private function hasCondition(string $history, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($history, $keyword)) return true;
        }
        return false;
    }

    /**
     * Sinonim alergen umum untuk deteksi lebih akurat
     */
    private function allergenSynonyms(string $allergen): array
    {
        $map = [
            'kacang'    => ['peanut', 'groundnut', 'arachis', 'kacang tanah'],
            'peanut'    => ['kacang', 'groundnut', 'arachis', 'kacang tanah'],
            'susu'      => ['milk', 'dairy', 'lactose', 'whey', 'casein', 'laktosa'],
            'milk'      => ['susu', 'dairy', 'lactose', 'whey', 'casein'],
            'telur'     => ['egg', 'albumin', 'ovomucin', 'lysozyme'],
            'egg'       => ['telur', 'albumin', 'ovomucin'],
            'gandum'    => ['wheat', 'gluten', 'flour', 'tepung terigu'],
            'wheat'     => ['gandum', 'gluten', 'flour', 'tepung terigu'],
            'gluten'    => ['gandum', 'wheat', 'barley', 'rye'],
            'kedelai'   => ['soy', 'soya', 'tofu', 'tempe', 'edamame'],
            'soy'       => ['kedelai', 'soya', 'tofu'],
            'seafood'   => ['udang', 'shrimp', 'crab', 'kepiting', 'lobster', 'kerang'],
            'udang'     => ['shrimp', 'prawn', 'seafood'],
            'ikan'      => ['fish', 'tuna', 'salmon', 'anchovy', 'teri'],
            'fish'      => ['ikan', 'tuna', 'salmon', 'anchovy'],
        ];

        return $map[$allergen] ?? [];
    }
}
