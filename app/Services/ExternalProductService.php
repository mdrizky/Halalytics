<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExternalProductService
{
    private const CACHE_TTL = 604800; // 7 days

    /**
     * Fetch food product from Open Food Facts
     */
    public function getFood($barcode)
    {
        $cacheKey = "external_food_{$barcode}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($barcode) {
            try {
                $response = Http::timeout(10)->get("https://world.openfoodfacts.org/api/v2/product/{$barcode}.json");
                
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['product'])) {
                        return $this->normalizeOffData($data['product'], 'food');
                    }
                }
                return null;
            } catch (\Throwable $e) {
                Log::error("OFF Food API Error: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Fetch beauty product from Open Beauty Facts
     */
    public function getBeauty($barcode)
    {
        $cacheKey = "external_beauty_{$barcode}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($barcode) {
            try {
                $response = Http::timeout(10)->get("https://world.openbeautyfacts.org/api/v2/product/{$barcode}.json");
                
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['product'])) {
                        return $this->normalizeOffData($data['product'], 'beauty');
                    }
                }
                return null;
            } catch (\Throwable $e) {
                Log::error("OBF Beauty API Error: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Search food products from Open Food Facts
     */
    public function searchFood($query, $pageSize = 20, $page = 1)
    {
        $cacheKey = "external_search_food_" . md5($query . $pageSize . $page);

        return Cache::remember($cacheKey, 3600, function () use ($query, $pageSize, $page) {
            try {
                $response = Http::timeout(10)->get("https://world.openfoodfacts.org/cgi/search.pl", [
                    'search_terms' => $query,
                    'search_simple' => 1,
                    'action' => 'process',
                    'page' => $page,
                    'page_size' => $pageSize,
                    'json' => 1
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $products = array_map(function($p) {
                        return $this->normalizeOffData($p, 'food');
                    }, $data['products'] ?? []);
                    
                    return [
                        'success' => true,
                        'products' => $products,
                        'count' => $data['count'] ?? 0
                    ];
                }
                return ['success' => false, 'products' => [], 'count' => 0];
            } catch (\Throwable $e) {
                Log::error("OFF Food Search Error: " . $e->getMessage());
                return ['success' => false, 'products' => [], 'count' => 0];
            }
        });
    }

    /**
     * Search beauty products from Open Beauty Facts
     */
    public function searchBeauty($query, $pageSize = 20, $page = 1)
    {
        $cacheKey = "external_search_beauty_" . md5($query . $pageSize . $page);

        return Cache::remember($cacheKey, 3600, function () use ($query, $pageSize, $page) {
            try {
                $response = Http::timeout(10)->get("https://world.openbeautyfacts.org/cgi/search.pl", [
                    'search_terms' => $query,
                    'search_simple' => 1,
                    'action' => 'process',
                    'page' => $page,
                    'page_size' => $pageSize,
                    'json' => 1
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $products = array_map(function($p) {
                        return $this->normalizeOffData($p, 'beauty');
                    }, $data['products'] ?? []);
                    
                    return [
                        'success' => true,
                        'products' => $products,
                        'count' => $data['count'] ?? 0
                    ];
                }
                return ['success' => false, 'products' => [], 'count' => 0];
            } catch (\Throwable $e) {
                Log::error("OBF Beauty Search Error: " . $e->getMessage());
                return ['success' => false, 'products' => [], 'count' => 0];
            }
        });
    }

    /**
     * Fetch drug data from OpenFDA
     */
    public function getDrug($id)
    {
        $cacheKey = "external_drug_" . md5($id);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            try {
                // OpenFDA can be searched by brand_name or barcode (upc)
                $query = is_numeric($id) 
                    ? "openfda.upc:\"{$id}\"" 
                    : "openfda.brand_name:\"{$id}\"";

                $response = Http::timeout(10)->get("https://api.fda.gov/drug/label.json", [
                    'search' => $query,
                    'limit' => 1
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['results'][0])) {
                        return $this->normalizeFdaData($data['results'][0]);
                    }
                }
                return null;
            } catch (\Throwable $e) {
                Log::error("OpenFDA API Error: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Normalize Open Food/Beauty Facts data
     */
    private function normalizeOffData(array $product, string $type): array
    {
        return [
            'name' => $product['product_name'] ?? $product['product_name_en'] ?? 'Unknown Product',
            'brand' => $product['brands'] ?? 'Unknown Brand',
            'barcode' => $product['code'] ?? '',
            'category' => $type,
            'image' => $product['image_url'] ?? $product['image_front_url'] ?? null,
            'ingredients' => $product['ingredients_text'] ?? $product['ingredients_text_en'] ?? 'No ingredients data available.',
            'halal_status' => $this->inferHalalStatus($product),
            'nutrition' => $product['nutriments'] ?? [],
            'source' => $type === 'food' ? 'Open Food Facts' : 'Open Beauty Facts',
            'url' => "https://world." . ($type === 'food' ? 'openfoodfacts' : 'openbeautyfacts') . ".org/product/" . ($product['code'] ?? '')
        ];
    }

    /**
     * Normalize OpenFDA data
     */
    private function normalizeFdaData(array $result): array
    {
        $openfda = $result['openfda'] ?? [];
        
        return [
            'name' => $openfda['brand_name'][0] ?? 'Unknown Medicine',
            'generic_name' => $openfda['generic_name'][0] ?? null,
            'brand' => $openfda['manufacturer_name'][0] ?? 'Unknown Manufacturer',
            'barcode' => $openfda['upc'][0] ?? '',
            'category' => 'drug',
            'image' => null, // OpenFDA usually doesn't provide images in label API
            'ingredients' => $result['active_ingredient'][0] ?? $result['inactive_ingredient'][0] ?? 'Technical data available in source.',
            'dosage' => $result['dosage_and_administration'][0] ?? null,
            'warnings' => $result['warnings'][0] ?? null,
            'halal_status' => 'unknown', // Drugs need clinical review
            'source' => 'OpenFDA',
            'url' => "https://open.fda.gov/drugs/label/"
        ];
    }

    /**
     * Simple heuristic for halal status based on ingredients
     */
    private function inferHalalStatus(array $product): string
    {
        $ingredients = strtolower($product['ingredients_text'] ?? '');
        
        if (str_contains($ingredients, 'pork') || str_contains($ingredients, 'lard') || str_contains($ingredients, 'gelatin (porcine)')) {
            return 'haram';
        }
        
        if (isset($product['ingredients_analysis_tags'])) {
            if (in_array('en:non-halal', $product['ingredients_analysis_tags'])) return 'haram';
            if (in_array('en:halal', $product['ingredients_analysis_tags'])) return 'halal';
        }
        
        return 'unknown';
    }
}
