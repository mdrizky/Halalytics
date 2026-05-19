<?php

namespace App\Services\AI;

use App\Models\Ingredient;
use Illuminate\Support\Str;

class EvidenceService
{
    public function enrich(string $ingredientName): ?array
    {
        $ing = Ingredient::query()
            ->active()
            ->where('name', 'like', $ingredientName)
            ->orWhere('e_number', $ingredientName)
            ->first();

        if (! $ing) {
            return null;
        }

        return [
            'name' => $ing->name,
            'halal_status' => $ing->halal_status,
            'health_risk' => $ing->health_risk,
            'description' => $ing->description,
            'source' => $ing->sources ?? 'Halalytics Ingredient DB',
            'evidence_level' => 'medium',
        ];
    }

    public function longTermNote(array $nutrition): string
    {
        $notes = [];
        if (($nutrition['sugar_risk'] ?? '') === 'tinggi') {
            $notes[] = 'Konsumsi gula berlebihan dalam jangka panjang dapat meningkatkan risiko diabetes tipe 2 dan obesitas (WHO).';
        }
        if (($nutrition['sodium_risk'] ?? '') === 'tinggi') {
            $notes[] = 'Sodium tinggi kronis dapat memengaruhi tekanan darah (BPOM/WHO).';
        }

        return implode(' ', $notes) ?: 'Pantau porsi dan frekuensi konsumsi sesuai kondisi kesehatan Anda.';
    }
}
