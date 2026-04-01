<?php

namespace App\Services;

use App\Models\Medicine;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class FDAService
{
    private const OPEN_FDA_URL = 'https://api.fda.gov/drug/label.json';
    private const RXNAV_DRUGS_URL = 'https://rxnav.nlm.nih.gov/REST/drugs.json';
    private const RXCUI_URL = 'https://rxnav.nlm.nih.gov/REST/rxcui.json';
    private const DAILYMED_SPLS_URL = 'https://dailymed.nlm.nih.gov/dailymed/services/v2/spls.json';
    private const DAILYMED_SPL_DETAIL_URL = 'https://dailymed.nlm.nih.gov/dailymed/services/v2/spls/%s.json';
    private const DAILYMED_HTML_URL = 'https://dailymed.nlm.nih.gov/dailymed/drugInfo.cfm';
    private const OPEN_FDA_RATE_LIMIT_PER_MINUTE = 240;
    private const CACHE_TTL_SECONDS = 86400;

    public function search(string $query): array
    {
        $normalized = trim($query);

        if ($normalized === '') {
            return $this->emptyResult('Query obat tidak boleh kosong.');
        }

        $cacheKey = 'fda_search_' . md5(Str::lower($normalized));

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($normalized) {
            $localResults = $this->searchLocal($normalized);
            if (!empty($localResults)) {
                return [
                    'found' => true,
                    'source' => 'database_lokal',
                    'results' => $localResults,
                    'total' => count($localResults),
                ];
            }

            $openFdaResults = $this->searchOpenFda($normalized);
            if (!empty($openFdaResults)) {
                return [
                    'found' => true,
                    'source' => 'openfda',
                    'results' => $openFdaResults,
                    'total' => count($openFdaResults),
                ];
            }

            $rxNormResults = $this->searchRxNorm($normalized);
            if (!empty($rxNormResults)) {
                return [
                    'found' => true,
                    'source' => 'rxnorm',
                    'results' => $rxNormResults,
                    'total' => count($rxNormResults),
                ];
            }

            $localFallback = $this->searchLocal($normalized);

            return [
                'found' => !empty($localFallback),
                'source' => empty($localFallback) ? 'fallback' : 'database_lokal',
                'results' => $localFallback,
                'total' => count($localFallback),
                'message' => empty($localFallback)
                    ? 'Data FDA sedang tidak tersedia. Menampilkan fallback lokal jika ada.'
                    : 'OpenFDA tidak tersedia, menampilkan hasil dari database lokal.',
            ];
        });
    }

    public function importOrUpdate(array $externalMedicine, ?string $fallbackName = null): ?Medicine
    {
        $name = trim((string) ($externalMedicine['name'] ?? $externalMedicine['brand_name'] ?? $fallbackName));

        if ($name === '') {
            return null;
        }

        $ingredients = $externalMedicine['ingredients'] ?? $this->normalizeIngredients(
            $externalMedicine['active_ingredient'] ?? null
        );

        $attributes = [
            'generic_name' => $externalMedicine['generic_name'] ?? null,
            'brand_name' => $externalMedicine['brand_name'] ?? $name,
            'description' => $externalMedicine['description'] ?? null,
            'indications' => $externalMedicine['indications'] ?? null,
            'ingredients' => $ingredients,
            'active_ingredient' => $externalMedicine['active_ingredient'] ?? null,
            'dosage_info' => $externalMedicine['dosage_info'] ?? null,
            'side_effects' => $externalMedicine['side_effects'] ?? null,
            'warnings' => $externalMedicine['warnings'] ?? null,
            'contraindications' => $externalMedicine['contraindications'] ?? null,
            'route' => $externalMedicine['route'] ?? null,
            'halal_status' => $externalMedicine['halal_status'] ?? 'syubhat',
            'manufacturer' => $externalMedicine['manufacturer'] ?? null,
            'country_origin' => $externalMedicine['country_origin'] ?? 'US',
            'dosage_form' => $externalMedicine['dosage_form'] ?? null,
            'category' => $externalMedicine['category'] ?? 'medicine',
            'source' => $externalMedicine['source'] ?? 'openfda',
            'is_imported_from_fda' => true,
            'external_reference' => $externalMedicine['external_reference'] ?? $externalMedicine['rxcui'] ?? $externalMedicine['setid'] ?? null,
            'external_payload' => $externalMedicine['external_payload'] ?? null,
            'active' => true,
        ];

        return Medicine::updateOrCreate(
            ['name' => $name],
            $attributes
        );
    }

    private function searchLocal(string $query): array
    {
        return Medicine::query()
            ->where(function ($builder) use ($query) {
                $builder->where('name', 'like', "%{$query}%")
                    ->orWhere('generic_name', 'like', "%{$query}%")
                    ->orWhere('brand_name', 'like', "%{$query}%")
                    ->orWhere('active_ingredient', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function (Medicine $medicine) {
                return [
                    'id_medicine' => $medicine->id_medicine,
                    'name' => $medicine->name,
                    'generic_name' => $medicine->generic_name,
                    'brand_name' => $medicine->brand_name,
                    'manufacturer' => $medicine->manufacturer,
                    'active_ingredient' => $medicine->active_ingredient,
                    'ingredients' => $medicine->ingredients,
                    'indications' => $medicine->indications,
                    'contraindications' => $medicine->contraindications,
                    'side_effects' => $medicine->side_effects,
                    'warnings' => $medicine->warnings,
                    'dosage_info' => $medicine->dosage_info,
                    'route' => $medicine->route,
                    'dosage_form' => $medicine->dosage_form,
                    'country_origin' => $medicine->country_origin,
                    'halal_status' => $medicine->halal_status ?: 'syubhat',
                    'halal_label' => $medicine->halal_status === 'halal' ? 'Halal terverifikasi' : 'Perlu pengecekan manual',
                    'source' => $medicine->source ?: 'database_lokal',
                    'is_imported_from_fda' => (bool) $medicine->is_imported_from_fda,
                ];
            })
            ->all();
    }

    private function searchOpenFda(string $query): array
    {
        if (!$this->canHitOpenFda()) {
            return [];
        }

        try {
            $response = Http::timeout(20)
                ->get(self::OPEN_FDA_URL, [
                    'search' => sprintf(
                        'openfda.brand_name:"%1$s" OR openfda.generic_name:"%1$s" OR active_ingredient:"%1$s"',
                        addslashes($query)
                    ),
                    'limit' => 5,
                ]);

            $this->trackOpenFdaHit();

            if (!$response->successful()) {
                Log::warning('OpenFDA request failed', [
                    'query' => $query,
                    'status' => $response->status(),
                    'body' => Str::limit($response->body(), 500),
                ]);
                return [];
            }

            $results = $response->json('results', []);

            return collect($results)
                ->map(fn (array $entry) => $this->mapOpenFdaEntry($entry, $query))
                ->filter(fn (array $entry) => !empty($entry['name']))
                ->values()
                ->all();
        } catch (\Throwable $throwable) {
            Log::warning('OpenFDA search exception', [
                'query' => $query,
                'error' => $throwable->getMessage(),
            ]);
            return [];
        }
    }

    private function searchRxNorm(string $query): array
    {
        try {
            $drugResponse = Http::timeout(15)
                ->get(self::RXNAV_DRUGS_URL, ['name' => $query]);

            $drugGroups = $drugResponse->successful()
                ? $drugResponse->json('drugGroup.conceptGroup', [])
                : [];

            $results = collect($drugGroups)
                ->flatMap(function (array $group) {
                    return collect($group['conceptProperties'] ?? []);
                })
                ->take(5)
                ->map(function (array $entry) use ($query) {
                    $rxcui = $entry['rxcui'] ?? $this->lookupRxCui($query);
                    $dailyMed = $rxcui ? $this->fetchDailyMedByRxcui($rxcui, $query) : $this->fetchDailyMedByName($query);

                    return $this->mapRxNormEntry($entry, $query, $dailyMed);
                })
                ->filter(fn (array $entry) => !empty($entry['name']))
                ->values()
                ->all();

            if (!empty($results)) {
                return $results;
            }

            $dailyMedByName = $this->fetchDailyMedByName($query);

            return $dailyMedByName ? [$dailyMedByName] : [];
        } catch (\Throwable $throwable) {
            Log::warning('RxNorm search exception', [
                'query' => $query,
                'error' => $throwable->getMessage(),
            ]);
            return [];
        }
    }

    private function mapOpenFdaEntry(array $entry, string $query): array
    {
        $openFda = $entry['openfda'] ?? [];
        $name = Arr::first($openFda['brand_name'] ?? []) ?: Arr::first($openFda['generic_name'] ?? []) ?: $query;
        $activeIngredient = $this->extractText($entry['active_ingredient'] ?? []);
        $rxcui = Arr::first($openFda['rxcui'] ?? []) ?: $this->lookupRxCui($name);
        $dailyMed = $rxcui ? $this->fetchDailyMedByRxcui($rxcui, $name) : $this->fetchDailyMedByName($name);

        return array_filter([
            'name' => $name,
            'generic_name' => Arr::first($openFda['generic_name'] ?? []) ?: null,
            'brand_name' => Arr::first($openFda['brand_name'] ?? []) ?: $name,
            'manufacturer' => Arr::first($openFda['manufacturer_name'] ?? []) ?: null,
            'active_ingredient' => $activeIngredient,
            'ingredients' => $this->normalizeIngredients($activeIngredient),
            'indications' => $dailyMed['indications'] ?? $this->extractText($entry['indications_and_usage'] ?? []),
            'contraindications' => $dailyMed['contraindications'] ?? $this->extractText($entry['contraindications'] ?? []),
            'side_effects' => $dailyMed['side_effects'] ?? $this->extractText($entry['adverse_reactions'] ?? []),
            'warnings' => $dailyMed['warnings'] ?? $this->extractText($entry['warnings'] ?? []),
            'dosage_info' => $dailyMed['dosage_info'] ?? $this->extractText($entry['dosage_and_administration'] ?? []),
            'route' => Arr::first($openFda['route'] ?? []) ?: null,
            'dosage_form' => Arr::first($openFda['dosage_form'] ?? []) ?: null,
            'country_origin' => 'US',
            'halal_status' => 'syubhat',
            'halal_label' => 'Perlu pengecekan manual',
            'source' => 'openfda',
            'is_imported_from_fda' => true,
            'external_reference' => $rxcui ?: ($dailyMed['setid'] ?? null),
            'external_payload' => [
                'openfda' => $entry,
                'dailymed' => $dailyMed,
            ],
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function mapRxNormEntry(array $entry, string $query, ?array $dailyMed = null): array
    {
        $name = $entry['name'] ?? $query;

        return array_filter([
            'name' => $name,
            'generic_name' => $name,
            'brand_name' => $name,
            'manufacturer' => $dailyMed['manufacturer'] ?? null,
            'active_ingredient' => $dailyMed['active_ingredient'] ?? $name,
            'ingredients' => $this->normalizeIngredients($dailyMed['active_ingredient'] ?? $name),
            'indications' => $dailyMed['indications'] ?? null,
            'contraindications' => $dailyMed['contraindications'] ?? null,
            'side_effects' => $dailyMed['side_effects'] ?? null,
            'warnings' => $dailyMed['warnings'] ?? null,
            'dosage_info' => $dailyMed['dosage_info'] ?? null,
            'route' => $dailyMed['route'] ?? null,
            'dosage_form' => $dailyMed['dosage_form'] ?? null,
            'country_origin' => 'US',
            'halal_status' => 'syubhat',
            'halal_label' => 'Perlu pengecekan manual',
            'source' => 'rxnorm',
            'is_imported_from_fda' => true,
            'rxcui' => $entry['rxcui'] ?? null,
            'external_reference' => $entry['rxcui'] ?? null,
            'external_payload' => [
                'rxnorm' => $entry,
                'dailymed' => $dailyMed,
            ],
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function lookupRxCui(string $query): ?string
    {
        try {
            $response = Http::timeout(10)
                ->get(self::RXCUI_URL, ['name' => $query]);

            if (!$response->successful()) {
                return null;
            }

            return $response->json('idGroup.rxnormId.0');
        } catch (\Throwable $throwable) {
            Log::debug('RxCUI lookup failed', [
                'query' => $query,
                'error' => $throwable->getMessage(),
            ]);

            return null;
        }
    }

    private function fetchDailyMedByRxcui(string $rxcui, string $fallbackName): ?array
    {
        try {
            $response = Http::timeout(15)
                ->get(self::DAILYMED_SPLS_URL, [
                    'rxcui' => $rxcui,
                    'pagesize' => 1,
                ]);

            if (!$response->successful()) {
                return null;
            }

            $setId = $response->json('data.0.setid');

            return $setId ? $this->fetchDailyMedDetail($setId, $fallbackName) : null;
        } catch (\Throwable $throwable) {
            Log::debug('DailyMed rxcui lookup failed', [
                'rxcui' => $rxcui,
                'error' => $throwable->getMessage(),
            ]);
            return null;
        }
    }

    private function fetchDailyMedByName(string $query): ?array
    {
        try {
            $response = Http::timeout(15)
                ->get(self::DAILYMED_SPLS_URL, [
                    'drug_name' => $query,
                    'pagesize' => 1,
                ]);

            if (!$response->successful()) {
                return null;
            }

            $setId = $response->json('data.0.setid');

            return $setId ? $this->fetchDailyMedDetail($setId, $query) : null;
        } catch (\Throwable $throwable) {
            Log::debug('DailyMed drug_name lookup failed', [
                'query' => $query,
                'error' => $throwable->getMessage(),
            ]);
            return null;
        }
    }

    private function fetchDailyMedDetail(string $setId, string $fallbackName): ?array
    {
        try {
            $detailResponse = Http::timeout(20)
                ->get(sprintf(self::DAILYMED_SPL_DETAIL_URL, $setId));

            if ($detailResponse->successful()) {
                return $this->mapDailyMedDetail(
                    $detailResponse->json('data', []),
                    $setId,
                    $fallbackName
                );
            }
        } catch (\Throwable $throwable) {
            Log::debug('DailyMed detail JSON failed', [
                'setid' => $setId,
                'error' => $throwable->getMessage(),
            ]);
        }

        return $this->fetchDailyMedHtmlFallback($setId, $fallbackName);
    }

    private function fetchDailyMedHtmlFallback(string $setId, string $fallbackName): ?array
    {
        try {
            $htmlResponse = Http::timeout(20)
                ->get(self::DAILYMED_HTML_URL, ['setid' => $setId]);

            if (!$htmlResponse->successful()) {
                return null;
            }

            $html = $htmlResponse->body();

            return [
                'name' => $fallbackName,
                'setid' => $setId,
                'indications' => $this->extractHtmlSection($html, ['Indications and Usage', 'Uses']),
                'contraindications' => $this->extractHtmlSection($html, ['Contraindications']),
                'side_effects' => $this->extractHtmlSection($html, ['Adverse Reactions', 'Side Effects']),
                'warnings' => $this->extractHtmlSection($html, ['Warnings and Precautions', 'Warnings']),
                'dosage_info' => $this->extractHtmlSection($html, ['Dosage and Administration']),
            ];
        } catch (\Throwable $throwable) {
            Log::debug('DailyMed HTML fallback failed', [
                'setid' => $setId,
                'error' => $throwable->getMessage(),
            ]);
            return null;
        }
    }

    private function mapDailyMedDetail(array $data, string $setId, string $fallbackName): array
    {
        $title = $data['title'] ?? $fallbackName;
        $sections = collect($data['sections'] ?? []);

        return [
            'name' => $title,
            'setid' => $setId,
            'manufacturer' => $data['labeler'] ?? null,
            'route' => Arr::first($data['routes'] ?? []) ?: null,
            'dosage_form' => Arr::first($data['dosage_forms'] ?? []) ?: null,
            'active_ingredient' => $this->extractText($data['active_ingredient'] ?? []),
            'indications' => $this->extractSectionFromDailyMed($sections, ['INDICATIONS & USAGE', 'INDICATIONS AND USAGE']),
            'contraindications' => $this->extractSectionFromDailyMed($sections, ['CONTRAINDICATIONS']),
            'side_effects' => $this->extractSectionFromDailyMed($sections, ['ADVERSE REACTIONS', 'SIDE EFFECTS']),
            'warnings' => $this->extractSectionFromDailyMed($sections, ['WARNINGS', 'WARNINGS AND PRECAUTIONS']),
            'dosage_info' => $this->extractSectionFromDailyMed($sections, ['DOSAGE AND ADMINISTRATION']),
        ];
    }

    private function extractSectionFromDailyMed($sections, array $titles): ?string
    {
        $match = $sections->first(function (array $section) use ($titles) {
            $title = Str::upper((string) ($section['title'] ?? ''));

            foreach ($titles as $expected) {
                if (Str::contains($title, Str::upper($expected))) {
                    return true;
                }
            }

            return false;
        });

        if (!$match) {
            return null;
        }

        return $this->extractText($match['text'] ?? $match['body'] ?? []);
    }

    private function extractHtmlSection(string $html, array $titles): ?string
    {
        foreach ($titles as $title) {
            $pattern = sprintf(
                '/%s.*?<p[^>]*>(.*?)<\/p>/is',
                preg_quote($title, '/')
            );

            if (preg_match($pattern, $html, $matches)) {
                return trim(strip_tags($matches[1]));
            }
        }

        return null;
    }

    private function normalizeIngredients(null|string|array $activeIngredient): array
    {
        if (is_array($activeIngredient)) {
            return collect($activeIngredient)
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->values()
                ->all();
        }

        return collect(preg_split('/[,;]+/', (string) $activeIngredient))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    private function extractText($value): ?string
    {
        if (is_string($value)) {
            return trim(strip_tags($value));
        }

        if (is_array($value)) {
            return collect($value)
                ->map(function ($item) {
                    if (is_array($item)) {
                        return $this->extractText($item['text'] ?? $item['value'] ?? $item['content'] ?? null);
                    }

                    return $this->extractText($item);
                })
                ->filter()
                ->implode("\n\n");
        }

        return null;
    }

    private function canHitOpenFda(): bool
    {
        return !RateLimiter::tooManyAttempts('openfda:global', self::OPEN_FDA_RATE_LIMIT_PER_MINUTE);
    }

    private function trackOpenFdaHit(): void
    {
        RateLimiter::hit('openfda:global', 60);
    }

    private function emptyResult(string $message): array
    {
        return [
            'found' => false,
            'source' => 'fallback',
            'results' => [],
            'total' => 0,
            'message' => $message,
        ];
    }
}
