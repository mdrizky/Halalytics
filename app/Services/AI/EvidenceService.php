<?php

namespace App\Services\AI;

use App\Models\Ingredient;
use Illuminate\Support\Str;

class EvidenceService
{
    /**
     * Ambil data evidence dari database untuk satu bahan
     */
    public function enrich(string $ingredientName): ?array
    {
        if (! \Schema::hasTable('ingredients')) return null;

        $ing = Ingredient::query()
            ->active()
            ->where(function ($q) use ($ingredientName) {
                $q->where('name', 'like', "%{$ingredientName}%")
                  ->orWhere('e_number', $ingredientName);
            })
            ->first();

        if (! $ing) return null;

        return [
            'name'           => $ing->name,
            'halal_status'   => $ing->halal_status,
            'health_risk'    => $ing->health_risk,
            'description'    => $ing->description,
            'source'         => $ing->sources ?? 'Halalytics Ingredient DB',
            'evidence_level' => 'medium',
        ];
    }

    /**
     * Buat catatan efek jangka panjang berbasis evidence ilmiah.
     * Tidak pernah mengembalikan placeholder — selalu ada konten bermakna.
     */
    public function longTermNote(array $nutrition): string
    {
        $notes = [];

        $sugarRisk  = $nutrition['sugar_risk']  ?? 'rendah';
        $sodiumRisk = $nutrition['sodium_risk'] ?? 'rendah';
        $fatRisk    = $nutrition['fat_risk']    ?? 'rendah';
        $satFatRisk = $nutrition['sat_fat_risk'] ?? 'rendah';
        $isUltra    = (bool) ($nutrition['is_ultra_processed'] ?? false);
        $dominant   = Str::lower((string) ($nutrition['dominant_ingredient'] ?? ''));

        // Efek jangka panjang gula tinggi
        if ($sugarRisk === 'tinggi') {
            $notes[] = 'Konsumsi gula berlebihan secara rutin dalam jangka panjang dapat meningkatkan risiko: '
                . 'resistensi insulin → Diabetes Tipe 2, penumpukan lemak tubuh → Obesitas, '
                . 'kerusakan gigi (karies), dan peningkatan trigliserida darah. '
                . '(Sumber: WHO Sugar Intake Guidelines, American Diabetes Association)';
        } elseif ($sugarRisk === 'sedang') {
            $notes[] = 'Kandungan gula sedang. Konsumsi berlebihan dalam jangka panjang tetap berisiko '
                . 'meningkatkan kadar gula darah dan berat badan. Pantau total asupan gula harian Anda.';
        }

        // Efek jangka panjang sodium tinggi
        if ($sodiumRisk === 'tinggi') {
            $notes[] = 'Asupan sodium tinggi secara kronis dapat meningkatkan risiko: '
                . 'hipertensi (tekanan darah tinggi), penyakit kardiovaskular, stroke, '
                . 'dan kerusakan ginjal jangka panjang. '
                . '(Sumber: WHO Sodium Intake Guidelines, BPOM Indonesia)';
        } elseif ($sodiumRisk === 'sedang') {
            $notes[] = 'Kandungan sodium sedang. Batasi total asupan sodium harian di bawah 2000mg '
                . 'untuk menjaga kesehatan jantung dan ginjal.';
        }

        // Efek jangka panjang lemak jenuh tinggi
        if ($satFatRisk === 'tinggi') {
            $notes[] = 'Lemak jenuh tinggi dalam jangka panjang dapat meningkatkan kadar LDL (kolesterol jahat) '
                . 'dan risiko penyakit jantung koroner. '
                . '(Sumber: American Heart Association, WHO)';
        }

        // Efek ultra-processed food
        if ($isUltra) {
            $notes[] = 'Produk ini terindikasi ultra-processed food (NOVA Group 4). '
                . 'Konsumsi rutin ultra-processed food dikaitkan dengan peningkatan risiko: '
                . 'obesitas, diabetes tipe 2, penyakit kardiovaskular, dan beberapa jenis kanker. '
                . '(Sumber: NOVA Food Classification, BMJ 2019, Lancet 2021)';
        }

        // Gula sebagai bahan dominan
        if (str_contains($dominant, 'gula') || str_contains($dominant, 'sugar')) {
            $notes[] = 'Gula adalah bahan UTAMA produk ini (urutan pertama komposisi), '
                . 'artinya kandungan gula lebih banyak dari bahan lainnya. '
                . 'Produk ini lebih tepat dikategorikan sebagai produk manis, bukan produk bergizi.';
        }

        // Jika tidak ada catatan spesifik, berikan catatan umum yang tetap informatif
        if (empty($notes)) {
            $notes[] = 'Berdasarkan komposisi yang tersedia, produk ini tidak menunjukkan risiko nutrisi yang signifikan. '
                . 'Tetap perhatikan porsi dan frekuensi konsumsi. '
                . 'Variasikan pola makan dengan sayur, buah, protein, dan karbohidrat kompleks.';
        }

        // Selalu tambahkan disclaimer
        $notes[] = 'Catatan: Analisis ini bersifat edukatif berdasarkan data nutrisi yang tersedia. '
            . 'Konsultasikan dengan dokter atau ahli gizi untuk kondisi medis spesifik Anda.';

        return implode("\n\n", $notes);
    }
}
