<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\DB;

class MedicalRecommendationService
{
    public function recommend(string $message, mixed $user = null): array
    {
        $text = mb_strtolower($message);
        $diseases = $this->normalizeList($user?->diseases ?? []);
        $allergies = $this->normalizeList($user?->allergies ?? []);

        $candidates = [];
        $rules = DB::table('medical_symptom_rules')->get();
        foreach ($rules as $rule) {
            if (!str_contains($text, mb_strtolower((string) $rule->keyword))) {
                continue;
            }
            $candidates[] = [
                'name' => (string) $rule->drug_name,
                'type' => (string) $rule->drug_type,
                'indication' => (string) $rule->indication,
                'severity_score' => (int) $rule->severity_score,
                'warnings' => json_decode((string) ($rule->warnings ?? '[]'), true) ?: [],
            ];
        }

        $filtered = [];
        foreach ($candidates as $item) {
            $contra = [];

            $contraRules = DB::table('medical_contraindication_rules')
                ->where('drug_name', $item['name'])
                ->get();

            foreach ($contraRules as $rule) {
                $keyword = mb_strtolower((string) $rule->condition_keyword);
                if (in_array($keyword, $diseases, true) || in_array($keyword, $allergies, true)) {
                    $contra[] = (string) $rule->warning_text;
                }
            }

            $item['contraindication_warning'] = $contra;
            $filtered[] = $item;
        }

        $maxSeverity = empty($filtered) ? 0 : max(array_map(fn ($r) => (int) ($r['severity_score'] ?? 0), $filtered));

        $interactionWarnings = [];
        foreach ($filtered as $rec) {
            $interactions = DB::table('drug_interaction_blacklists')
                ->where('drug_a', $rec['name'])
                ->orWhere('drug_b', $rec['name'])
                ->get();
            foreach ($interactions as $it) {
                $interactionWarnings[] = [
                    'drug_a' => $it->drug_a,
                    'drug_b' => $it->drug_b,
                    'risk_level' => $it->risk_level,
                    'warning_text' => $it->warning_text,
                ];
            }
        }

        return [
            'otc_only' => true,
            'recommendations' => $filtered,
            'max_severity_score' => $maxSeverity,
            'drug_interaction_warnings' => $interactionWarnings,
            'safety_filter' => [
                'no_prescription_drugs' => true,
                'requires_doctor_if_red_flag' => true,
            ],
        ];
    }

    private function normalizeList(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map(fn ($v) => mb_strtolower(trim((string) $v)), $value)));
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', mb_strtolower($raw)))));
    }
}
