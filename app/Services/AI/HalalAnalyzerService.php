<?php

namespace App\Services\AI;

use App\Models\ForbiddenIngredient;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HalalAnalyzerService
{
    private array $haramKeywords = [
        // Babi dan turunannya
        'babi', 'pork', 'lard', 'bacon', 'ham', 'porcine', 'swine', 'pig',
        'gelatin babi', 'pork gelatin', 'porcine gelatin',
        // Alkohol
        'wine', 'alkohol', 'ethanol', 'alcohol', 'beer', 'bir',
        'gin', 'rum', 'whisky', 'whiskey', 'vodka', 'sake', 'mirin', 'brandy',
        // Bahan haram lainnya
        'karmin', 'carmine', 'cochineal', 'e120',
        'darah', 'blood',
        'e441',  // gelatin
        'e542',  // bone phosphate
        'l-cysteine pork', 'cysteine from pork',
    ];

    private array $syubhatKeywords = [
        // Gelatin (perlu cek sumber)
        'gelatin', 'gelatine',
        // Emulsifier (perlu cek sumber)
        'e471', 'e472', 'e472a', 'e472b', 'e472c', 'e472e',
        'e473', 'e474', 'e475', 'e476', 'e477', 'e481', 'e482',
        'mono and diglycerides', 'monoglycerides', 'diglycerides',
        // Glycerin/Glycerol (perlu cek sumber)
        'glycerin', 'glycerol', 'e422',
        // Enzim (perlu cek sumber)
        'enzim', 'enzyme', 'rennet', 'pepsin', 'lipase',
        // Perisa (perlu cek sumber)
        'perisa', 'natural flavor', 'artificial flavor', 'flavoring',
        // Lainnya
        'stearic acid', 'stearate', 'lanolin', 'collagen',
        'l-cysteine', 'cysteine', 'e920',
        'lecithin', 'lesitin', 'e322',
        'carrageenan', 'karagenan',
    ];

    private array $dangerousHealthKeywords = [
        // Pengawet berbahaya
        'tbhq', 'bha', 'bht', 'e319', 'e320', 'e321',
        'sodium benzoate', 'natrium benzoat', 'e211',
        'potassium bromate', 'e924',
        // Pewarna berbahaya
        'tartrazine', 'e102', 'sunset yellow', 'e110',
        'allura red', 'e129', 'brilliant blue', 'e133',
        // Pemanis berbahaya
        'aspartame', 'aspartam', 'e951',
        'saccharin', 'sakarin', 'e954',
        // Penguat rasa
        'msg', 'monosodium glutamate', 'e621',
        // Nitrit/Nitrat
        'sodium nitrite', 'natrium nitrit', 'e250',
        'sodium nitrate', 'e251',
    ];

    public function analyze(string $ingredientsText): array
    {
        $normalized = Str::lower($ingredientsText);
        $flags = [];

        // Check haram keywords
        foreach ($this->haramKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                $flags[] = [
                    'name' => $keyword,
                    'status' => 'haram',
                    'note' => 'Bahan terindikasi tidak halal — ' . $this->haramNote($keyword),
                ];
            }
        }

        // Check syubhat keywords
        foreach ($this->syubhatKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                // Don't add duplicate if already flagged as haram
                $alreadyFlagged = collect($flags)->contains(fn ($f) => Str::lower($f['name']) === $keyword);
                if (! $alreadyFlagged) {
                    $flags[] = [
                        'name' => $keyword,
                        'status' => 'syubhat',
                        'note' => 'Perlu verifikasi sumber bahan — ' . $this->syubhatNote($keyword),
                    ];
                }
            }
        }

        // Check health danger keywords (separate from halal)
        $healthWarnings = [];
        foreach ($this->dangerousHealthKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                $healthWarnings[] = $keyword;
            }
        }

        // Check database: ForbiddenIngredients (admin-managed blacklist)
        $dbForbidden = Cache::remember('forbidden_ingredients_active', 3600, function () {
            if (! \Schema::hasTable('forbidden_ingredients')) {
                return collect();
            }
            return ForbiddenIngredient::query()->where('is_active', true)->get();
        });

        foreach ($dbForbidden as $forbidden) {
            $name = Str::lower($forbidden->name ?? '');
            if ($name !== '' && str_contains($normalized, $name)) {
                $flags[] = [
                    'name' => $forbidden->name,
                    'status' => $forbidden->status ?? 'haram',
                    'note' => $forbidden->reason ?? 'Termasuk dalam daftar bahan terlarang Halalytics.',
                ];
            }
        }

        // Check database: Ingredients knowledge base
        if (\Schema::hasTable('ingredients')) {
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
        }

        // Deduplicate flags by name
        $seen = [];
        $uniqueFlags = [];
        foreach ($flags as $flag) {
            $key = Str::lower($flag['name']);
            if (! isset($seen[$key])) {
                $seen[$key] = true;
                $uniqueFlags[] = $flag;
            }
        }

        // Determine overall status
        $status = 'halal';
        if (collect($uniqueFlags)->contains(fn ($f) => ($f['status'] ?? '') === 'haram')) {
            $status = 'haram';
        } elseif (collect($uniqueFlags)->contains(fn ($f) => ($f['status'] ?? '') === 'syubhat')) {
            $status = 'syubhat';
        }

        $score = match ($status) {
            'haram' => 15,
            'syubhat' => 55,
            default => empty($uniqueFlags) ? 88 : 75,
        };

        return [
            'halal_status' => $status,
            'halal_score' => $score,
            'flags' => $uniqueFlags,
            'health_warnings' => $healthWarnings,
            'summary' => $this->summary($status, $uniqueFlags),
        ];
    }

    private function haramNote(string $keyword): string
    {
        return match (true) {
            in_array($keyword, ['babi', 'pork', 'lard', 'bacon', 'ham', 'porcine', 'swine', 'pig']) => 'Berasal dari babi.',
            in_array($keyword, ['wine', 'alkohol', 'ethanol', 'alcohol', 'beer', 'bir', 'gin', 'rum', 'whisky', 'whiskey', 'vodka', 'sake', 'mirin', 'brandy']) => 'Mengandung alkohol/minuman keras.',
            in_array($keyword, ['karmin', 'carmine', 'cochineal', 'e120']) => 'Berasal dari serangga (cochineal).',
            default => 'Terindikasi bahan tidak halal.',
        };
    }

    private function syubhatNote(string $keyword): string
    {
        return match (true) {
            in_array($keyword, ['gelatin', 'gelatine']) => 'Gelatin bisa berasal dari babi, sapi, atau ikan — perlu cek sumber.',
            in_array($keyword, ['glycerin', 'glycerol', 'e422']) => 'Gliserin bisa berasal dari hewani atau nabati — perlu cek sumber.',
            str_starts_with($keyword, 'e47') => 'Emulsifier ini bisa berasal dari lemak hewani atau nabati — perlu cek sumber.',
            in_array($keyword, ['lecithin', 'lesitin', 'e322']) => 'Lesitin bisa dari kedelai (halal) atau telur/hewani — perlu cek sumber.',
            in_array($keyword, ['perisa', 'natural flavor', 'artificial flavor', 'flavoring']) => 'Perisa bisa mengandung bahan hewani — perlu verifikasi produsen.',
            default => 'Perlu verifikasi sumber bahan dari produsen atau sertifikasi halal.',
        };
    }

    private function summary(string $status, array $flags): string
    {
        if ($status === 'haram') {
            $haramItems = collect($flags)
                ->where('status', 'haram')
                ->pluck('name')
                ->implode(', ');
            return "Terindikasi mengandung bahan tidak halal: {$haramItems}. Hindari hingga diverifikasi.";
        }

        if ($status === 'syubhat') {
            $syubhatItems = collect($flags)
                ->where('status', 'syubhat')
                ->pluck('name')
                ->implode(', ');
            return "Ada bahan syubhat: {$syubhatItems}. Verifikasi sertifikasi halal atau sumber bahan dari produsen.";
        }

        return count($flags) > 0
            ? 'Tidak ada bahan haram jelas, namun tetap cek label resmi dan sertifikasi MUI/BPJPH.'
            : 'Tidak ditemukan bahan haram jelas dari komposisi yang tersedia (Kemungkinan Halal — AI Analysis). Sertifikasi MUI/BPJPH belum terverifikasi otomatis.';
    }
}
