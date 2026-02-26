<?php

namespace App\Services;

use App\Models\Medicine;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ExternalApiService
{
    /**
     * Cari produk kosmetik/skincare di Open Beauty Facts
     * API: https://world.openbeautyfacts.org/api/v2/
     */
    public function searchOpenBeautyFacts($barcode)
    {
        $cacheKey = "obf_barcode_{$barcode}";

        return Cache::remember($cacheKey, 3600, function () use ($barcode) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'Halalytics/1.0 (contact@halalytics.id)'])
                    ->get("https://world.openbeautyfacts.org/api/v2/product/{$barcode}.json");

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['product']) && $data['status'] == 1) {
                        $product = $data['product'];
                        return [
                            'found' => true,
                            'source' => 'open_beauty_facts',
                            'nama_produk' => $product['product_name'] ?? $product['product_name_en'] ?? 'Unknown',
                            'merk' => $product['brands'] ?? null,
                            'kategori' => $this->mapBeautyCategory($product['categories'] ?? ''),
                            'barcode' => $barcode,
                            'ingredients_text' => $product['ingredients_text'] ?? $product['ingredients_text_en'] ?? null,
                            'image_url' => $product['image_url'] ?? $product['image_front_url'] ?? null,
                            'origin_country' => $product['countries'] ?? null,
                            'labels' => $product['labels'] ?? null,
                            'raw_data' => $product,
                        ];
                    }
                }

                return ['found' => false, 'source' => 'open_beauty_facts'];
            } catch (\Exception $e) {
                Log::error('Open Beauty Facts Error: ' . $e->getMessage());
                return ['found' => false, 'source' => 'open_beauty_facts', 'error' => $e->getMessage()];
            }
        });
    }

    /**
     * Cari produk makanan di Open Food Facts
     * API: https://world.openfoodfacts.org/api/v2/
     */
    public function searchOpenFoodFacts($barcode)
    {
        $cacheKey = "off_barcode_{$barcode}";

        return Cache::remember($cacheKey, 3600, function () use ($barcode) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'Halalytics/1.0 (contact@halalytics.id)'])
                    ->get("https://world.openfoodfacts.org/api/v2/product/{$barcode}.json");

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['product']) && $data['status'] == 1) {
                        $product = $data['product'];
                        return [
                            'found' => true,
                            'source' => 'open_food_facts',
                            'nama_produk' => $product['product_name'] ?? $product['product_name_en'] ?? 'Unknown',
                            'merk' => $product['brands'] ?? null,
                            'kategori' => 'pangan',
                            'barcode' => $barcode,
                            'ingredients_text' => $product['ingredients_text'] ?? $product['ingredients_text_en'] ?? null,
                            'image_url' => $product['image_url'] ?? $product['image_front_url'] ?? null,
                            'nutriscore' => $product['nutriscore_grade'] ?? null,
                            'nova_group' => $product['nova_group'] ?? null,
                            'allergens' => $product['allergens'] ?? null,
                            'origin_country' => $product['countries'] ?? null,
                            'raw_data' => $product,
                        ];
                    }
                }

                return ['found' => false, 'source' => 'open_food_facts'];
            } catch (\Exception $e) {
                Log::error('Open Food Facts Error: ' . $e->getMessage());
                return ['found' => false, 'source' => 'open_food_facts', 'error' => $e->getMessage()];
            }
        });
    }

    /**
     * Cari data obat di OpenFDA
     * API: https://api.fda.gov/drug/
     */
    public function searchOpenFDA($drugName)
    {
        $cacheKey = "fda_drug_" . md5($drugName);

        return Cache::remember($cacheKey, 3600, function () use ($drugName) {
            try {
                $normalizedQuery = trim($drugName);
                if ($normalizedQuery === '') {
                    return ['found' => false, 'source' => 'openfda'];
                }

                $response = Http::timeout(10)
                    ->get("https://api.fda.gov/drug/label.json", [
                        'search' => "openfda.brand_name:\"{$normalizedQuery}\" OR openfda.generic_name:\"{$normalizedQuery}\" OR openfda.substance_name:\"{$normalizedQuery}\"",
                        'limit' => 3,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['results']) && count($data['results']) > 0) {
                        $drug = $data['results'][0];
                        $dosageText = $this->safeFirstText($drug['dosage_and_administration'] ?? null);
                        $instructionsText = $this->safeFirstText($drug['information_for_patients'] ?? null);
                        $combinedInstructions = trim($dosageText . ' ' . $instructionsText);

                        return [
                            'found' => true,
                            'source' => 'openfda',
                            'nama_produk' => $drug['openfda']['brand_name'][0] ?? $normalizedQuery,
                            'generic_name' => $drug['openfda']['generic_name'][0] ?? null,
                            'brand_name' => $drug['openfda']['brand_name'][0] ?? null,
                            'manufacturer' => $drug['openfda']['manufacturer_name'][0] ?? null,
                            'route' => $drug['openfda']['route'][0] ?? null,
                            'dosage_form' => $drug['openfda']['dosage_form'][0] ?? null,
                            'active_ingredient' => $drug['active_ingredient'][0] ?? null,
                            'warnings' => $this->safeFirstText($drug['warnings'] ?? null),
                            'indications' => $this->safeFirstText($drug['indications_and_usage'] ?? null),
                            'contraindications' => $this->safeFirstText($drug['contraindications'] ?? null),
                            'dosage_info' => $dosageText,
                            'frequency_per_day' => $this->inferFrequencyPerDay($combinedInstructions),
                            'meal_timing' => $this->inferMealTiming($combinedInstructions),
                            'raw' => $drug,
                        ];
                    }
                }

                return ['found' => false, 'source' => 'openfda'];
            } catch (\Exception $e) {
                Log::error('OpenFDA Error: ' . $e->getMessage());
                return ['found' => false, 'source' => 'openfda', 'error' => $e->getMessage()];
            }
        });
    }

    /**
     * Save / update OpenFDA medicine to local medicines table.
     */
    public function upsertMedicineFromOpenFDA(array $fdaData, ?string $fallbackName = null): ?Medicine
    {
        if (!($fdaData['found'] ?? false)) {
            return null;
        }

        $name = $fdaData['nama_produk'] ?? $fallbackName;
        if (!$name) {
            return null;
        }

        $frequency = $fdaData['frequency_per_day'] ?? null;
        $dosageInfo = trim(($fdaData['dosage_info'] ?? '') . ' ' . ($fdaData['meal_timing'] ?? ''));
        $dosageInfo = trim($dosageInfo) ?: null;

        $medicine = Medicine::updateOrCreate(
            ['name' => $name],
            [
                'generic_name' => $fdaData['generic_name'] ?? null,
                'brand_name' => $fdaData['brand_name'] ?? null,
                'description' => 'Synced from OpenFDA label data',
                'indications' => $fdaData['indications'] ?? null,
                'ingredients' => $fdaData['active_ingredient'] ?? null,
                'dosage_info' => $dosageInfo,
                'frequency_per_day' => $frequency ? (string)$frequency : null,
                'side_effects' => $fdaData['warnings'] ?? null,
                'contraindications' => $fdaData['contraindications'] ?? null,
                'route' => $fdaData['route'] ?? null,
                'manufacturer' => $fdaData['manufacturer'] ?? null,
                'dosage_form' => $fdaData['dosage_form'] ?? null,
                'category' => 'obat',
                'source' => 'openfda',
                'halal_status' => 'syubhat',
                'is_verified_by_admin' => false,
                'active' => true,
            ]
        );

        return $medicine;
    }

    /**
     * Gateway logic: Cari produk di semua sumber berurutan
     * 1. Database lokal → 2. Open Food Facts / Open Beauty Facts → 3. OpenFDA → 4. Gemini AI
     */
    public function searchGateway($barcode, $productType = 'auto')
    {
        $results = [
            'barcode' => $barcode,
            'sources_checked' => [],
            'found' => false,
        ];

        // Auto-detect product type jika tidak di-specify
        if ($productType === 'auto') {
            // Check Open Beauty Facts dulu (kosmetik)
            $beautyResult = $this->searchOpenBeautyFacts($barcode);
            $results['sources_checked'][] = 'open_beauty_facts';

            if ($beautyResult['found']) {
                $results['found'] = true;
                $results['data'] = $beautyResult;
                $results['product_type'] = 'kosmetik';
                return $results;
            }

            // Check Open Food Facts (makanan)
            $foodResult = $this->searchOpenFoodFacts($barcode);
            $results['sources_checked'][] = 'open_food_facts';

            if ($foodResult['found']) {
                $results['found'] = true;
                $results['data'] = $foodResult;
                $results['product_type'] = 'pangan';
                return $results;
            }
        } elseif ($productType === 'kosmetik') {
            $beautyResult = $this->searchOpenBeautyFacts($barcode);
            $results['sources_checked'][] = 'open_beauty_facts';
            if ($beautyResult['found']) {
                $results['found'] = true;
                $results['data'] = $beautyResult;
                $results['product_type'] = 'kosmetik';
                return $results;
            }
        } elseif ($productType === 'pangan') {
            $foodResult = $this->searchOpenFoodFacts($barcode);
            $results['sources_checked'][] = 'open_food_facts';
            if ($foodResult['found']) {
                $results['found'] = true;
                $results['data'] = $foodResult;
                $results['product_type'] = 'pangan';
                return $results;
            }
        }

        return $results;
    }

    /**
     * Map kategori dari Open Beauty Facts ke kategori internal
     */
    private function mapBeautyCategory($categories)
    {
        $categories = strtolower($categories);
        if (str_contains($categories, 'skin') || str_contains($categories, 'cream') || str_contains($categories, 'face')) {
            return 'kosmetik';
        }
        if (str_contains($categories, 'hair') || str_contains($categories, 'shampoo')) {
            return 'kosmetik';
        }
        if (str_contains($categories, 'makeup') || str_contains($categories, 'lipstick')) {
            return 'kosmetik';
        }
        return 'kosmetik';
    }

    private function safeFirstText($value): ?string
    {
        if (is_array($value)) {
            $text = trim((string)($value[0] ?? ''));
            return $text !== '' ? $text : null;
        }

        if (is_string($value)) {
            $text = trim($value);
            return $text !== '' ? $text : null;
        }

        return null;
    }

    private function inferFrequencyPerDay(?string $text): ?int
    {
        if (!$text) {
            return null;
        }

        $normalized = strtolower($text);
        $patterns = [
            1 => '/(once daily|once a day|1 time daily|1x daily|once every day)/',
            2 => '/(twice daily|twice a day|2 times daily|2x daily|every 12 hours)/',
            3 => '/(three times daily|3 times daily|3x daily|every 8 hours)/',
            4 => '/(four times daily|4 times daily|4x daily|every 6 hours)/',
        ];

        foreach ($patterns as $frequency => $pattern) {
            if (preg_match($pattern, $normalized)) {
                return $frequency;
            }
        }

        return null;
    }

    private function inferMealTiming(?string $text): ?string
    {
        if (!$text) {
            return null;
        }

        $normalized = strtolower($text);
        if (str_contains($normalized, 'empty stomach') || str_contains($normalized, 'before meal')) {
            return 'Sebelum makan';
        }
        if (str_contains($normalized, 'with food') || str_contains($normalized, 'after meal') || str_contains($normalized, 'with meals')) {
            return 'Sesudah makan';
        }

        return null;
    }
}
