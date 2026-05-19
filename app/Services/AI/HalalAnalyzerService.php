<?php

namespace App\Services\AI;

use App\Models\Ingredient;
use Illuminate\Support\Str;

class HalalAnalyzerService
{
    private array $haramKeywords = [
        'babi', 'pork', 'lard', 'bacon', 'ham', 'wine', 'alkohol', 'ethanol', 'beer',
        'gin', 'rum', 'whisky', 'mirin', 'karmin', 'e120', 'e441', 'e542',
    ];

    private array $syubhatKeywords = [
        'gelatin', 'e471', 'e472', 'e473', 'e474', 'e475', 'e476', 'e477',
        'mono and diglycerides', 'glycerin', 'glycerol', 'enzim', 'perisa',
    ];

    public function analyze(string $ingredientsText): array
    {
        $normalized = Str::lower($ingredientsText);
        $flags = [];

        foreach ($this->haramKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                $flags[] = ['name' => $keyword, 'status' => 'haram', 'note' => 'Bahan terindikasi tidak halal.'];
            }
        }

        foreach ($this->syubhatKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                $flags[] = ['name' => $keyword, 'status' => 'syubhat', 'note' => 'Perlu verifikasi sumber bahan.'];
            }
        }

        $dbMatches = Ingredient::query()
            ->active()
            ->whereIn('halal_status', ['haram', 'syubhat'])
            ->get()
            ->filter(fn ($ing) => str_contains($normalized, Str::lower($ing->name)))
            ->map(fn ($ing) => [
                'name' => $ing->name,
                'status' => $ing->halal_status,
                'note' => $ing->description ?? 'Ditemukan di database bahan Halalytics.',
            ]);

        $flags = array_merge($flags, $dbMatches->values()->all());

        $status = 'halal';
        if (collect($flags)->contains(fn ($f) => ($f['status'] ?? '') === 'haram')) {
            $status = 'haram';
        } elseif (collect($flags)->contains(fn ($f) => ($f['status'] ?? '') === 'syubhat')) {
            $status = 'syubhat';
        }

        $score = match ($status) {
            'haram' => 20,
            'syubhat' => 55,
            default => empty($flags) ? 85 : 75,
        };

        return [
            'halal_status' => $status,
            'halal_score' => $score,
            'flags' => $flags,
            'summary' => $this->summary($status, $flags),
        ];
    }

    private function summary(string $status, array $flags): string
    {
        return match ($status) {
            'haram' => 'Terindikasi mengandung bahan tidak halal. Hindari hingga diverifikasi.',
            'syubhat' => 'Ada bahan syubhat — verifikasi sertifikasi atau sumber produsen.',
            default => count($flags) > 0
                ? 'Tidak ada bahan haram jelas, namun tetap cek label resmi.'
                : 'Tidak ditemukan bahan haram jelas dari komposisi yang tersedia (Kemungkinan Halal — AI).',
        };
    }
}
