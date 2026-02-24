<?php

namespace App\Services;

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
                $response = Http::timeout(10)
                    ->get("https://api.fda.gov/drug/label.json", [
                        'search' => "openfda.brand_name:\"{$drugName}\"",
                        'limit' => 3,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['results']) && count($data['results']) > 0) {
                        $drug = $data['results'][0];
                        return [
                            'found' => true,
                            'source' => 'openfda',
                            'nama_produk' => $drug['openfda']['brand_name'][0] ?? $drugName,
                            'generic_name' => $drug['openfda']['generic_name'][0] ?? null,
                            'manufacturer' => $drug['openfda']['manufacturer_name'][0] ?? null,
                            'route' => $drug['openfda']['route'][0] ?? null,
                            'dosage_form' => $drug['openfda']['dosage_form'][0] ?? null,
                            'active_ingredient' => $drug['active_ingredient'][0] ?? null,
                            'warnings' => $drug['warnings'][0] ?? null,
                            'indications' => $drug['indications_and_usage'][0] ?? null,
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
}
