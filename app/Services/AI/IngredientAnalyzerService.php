<?php

namespace App\Services\AI;

use Illuminate\Support\Str;

class IngredientAnalyzerService
{
    public function __construct(
        private readonly HalalAnalyzerService $halalAnalyzer,
        private readonly EvidenceService $evidenceService
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function analyzeList(string $ingredientsText): array
    {
        $parts = collect(preg_split('/[,;\n]+/', $ingredientsText))
            ->map(fn ($p) => trim((string) $p))
            ->filter()
            ->take(15);

        return $parts->map(function (string $name) {
            $halal = $this->halalAnalyzer->analyze($name);
            $evidence = $this->evidenceService->enrich($name);

            return [
                'name' => $name,
                'status_halal' => $halal['halal_status'] ?? 'unknown',
                'risk_level' => $evidence['health_risk'] ?? 'low',
                'note' => $evidence['description'] ?? ($halal['summary'] ?? ''),
            ];
        })->values()->all();
    }
}
