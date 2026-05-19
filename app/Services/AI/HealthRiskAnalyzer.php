<?php

namespace App\Services\AI;

use Illuminate\Support\Str;

class HealthRiskAnalyzer
{
    public function personalize(array $userContext, array $nutrition, array $halal): array
    {
        $warnings = [];
        $history = Str::lower((string) ($userContext['medical_history'] ?? ''));
        $allergy = Str::lower((string) ($userContext['allergies'] ?? $userContext['allergy'] ?? ''));

        if (str_contains($history, 'diabet') && ($nutrition['sugar_risk'] ?? '') === 'tinggi') {
            $warnings[] = 'PERINGATAN KERAS: Produk tinggi gula — tidak direkomendasikan untuk profil diabetes.';
        }
        if (str_contains($history, 'hipertensi') && ($nutrition['sodium_risk'] ?? '') === 'tinggi') {
            $warnings[] = 'PERINGATAN: Sodium tinggi — perhatikan tekanan darah.';
        }
        if (str_contains($history, 'obes') && ($nutrition['sugar_risk'] ?? '') === 'tinggi') {
            $warnings[] = 'Sangat tidak direkomendasikan untuk profil obesitas karena gula tinggi.';
        }

        if ($allergy !== '') {
            foreach (preg_split('/[,;|]/', $allergy) as $item) {
                $item = trim($item);
                if ($item !== '' && str_contains(Str::lower((string) ($userContext['ingredients_text'] ?? '')), $item)) {
                    $warnings[] = "WARNING ALERGI: Terdeteksi {$item}.";
                }
            }
        }

        $riskLevel = count($warnings) >= 2 ? 'high' : (count($warnings) === 1 ? 'moderate' : 'low');

        return [
            'personal_warnings' => $warnings,
            'consumption_risk' => $riskLevel,
            'consult_nutritionist' => $riskLevel !== 'low' || ($nutrition['health_score'] ?? 100) < 50,
        ];
    }
}
