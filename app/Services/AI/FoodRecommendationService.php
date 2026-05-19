<?php

namespace App\Services\AI;

class FoodRecommendationService
{
    public function suggest(array $nutrition, array $halal, array $personal): array
    {
        $recs = [];

        if (($nutrition['sugar_risk'] ?? '') === 'tinggi') {
            $recs[] = 'Batasi porsi; pilih alternatif rendah gula.';
            $recs[] = 'Perbanyak air putih dan makanan utuh (sayur, protein tanpa gula tambahan).';
        }
        if (($halal['halal_status'] ?? '') === 'syubhat') {
            $recs[] = 'Verifikasi sertifikasi halal resmi (MUI/BPJPH) sebelum rutin mengonsumsi.';
        }
        if (($personal['consult_nutritionist'] ?? false) === true) {
            $recs[] = 'Konsultasi ahli gizi Halalytics untuk rencana diet personal.';
        }
        if (empty($recs)) {
            $recs[] = 'Produk relatif aman untuk konsumsi sesekali — tetap baca label dan porsi.';
        }

        return array_values(array_unique($recs));
    }
}
