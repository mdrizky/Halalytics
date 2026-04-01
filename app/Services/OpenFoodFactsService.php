<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenFoodFactsService
{
    private const BASE_URL = 'https://world.openfoodfacts.org/api/v2';
    private const REQUEST_TIMEOUT_SECONDS = 15;
    private const CONNECT_TIMEOUT_SECONDS = 7;

    private function offClient()
    {
        return Http::connectTimeout(self::CONNECT_TIMEOUT_SECONDS)
            ->timeout(self::REQUEST_TIMEOUT_SECONDS)
            ->retry(1, 250)
            ->withUserAgent('Halalytics/1.0 (Android; +https://halalytics.app)');
    }
    
    /**
     * Search product by barcode
     */
    public function getProductByBarcode(string $barcode): array
    {
        return Cache::remember("off_barcode_{$barcode}", 86400, function () use ($barcode) {
            try {
                $response = $this->offClient()->get(self::BASE_URL . "/product/{$barcode}.json");

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['status']) && $data['status'] == 1) {
                        return [
                            'success' => true,
                            'product' => $this->normalizeProduct($data['product'])
                        ];
                    }
                }

                return ['success' => false, 'message' => 'Produk tidak ditemukan di OpenFoodFacts.'];
            } catch (\Exception $e) {
                Log::error("OpenFoodFacts API Error: " . $e->getMessage());
                return ['success' => false, 'message' => 'Layanan OpenFoodFacts sedang lambat. Silakan coba lagi.'];
            }
        });
    }

    /**
     * Search products by name
     */
    public function searchProducts(string $query, int $pageSize = 20, int $page = 1): array
    {
        $cacheKey = 'off_search_' . md5(strtolower($query) . "|{$pageSize}|{$page}");

        return Cache::remember($cacheKey, 86400, function () use ($query, $pageSize, $page) {
            try {
                $baseUrl = 'https://world.openfoodfacts.org';
                $response = $this->offClient()->get($baseUrl . '/cgi/search.pl', [
                        'search_terms' => $query,
                        'search_simple' => 1,
                        'action' => 'process',
                        'page' => $page,
                        'page_size' => $pageSize,
                        'json' => 1
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    return [
                        'success' => true,
                        'products' => array_map(
                            fn($product) => $this->normalizeProduct($product),
                            $data['products'] ?? []
                        ),
                        'count' => $data['count'] ?? 0,
                        'page' => $data['page'] ?? 1,
                        'page_count' => $data['page_count'] ?? 1
                    ];
                }

                return ['success' => false, 'products' => [], 'count' => 0, 'message' => 'OpenFoodFacts tidak merespons dengan data yang valid.'];
            } catch (\Exception $e) {
                Log::error("OpenFoodFacts Search Error: " . $e->getMessage());
                return ['success' => false, 'products' => [], 'count' => 0, 'message' => 'Pencarian ke OpenFoodFacts timeout. Coba ulang.'];
            }
        });
    }

    /**
     * Normalize OFF product to our format (matching Android ProductItem)
     */
    private function normalizeProduct(array $offProduct): array
    {
        $normalized = [
            '_id' => $offProduct['code'] ?? $offProduct['_id'] ?? null,
            'code' => $offProduct['code'] ?? null,
            'product_name' => $offProduct['product_name'] ?? $offProduct['product_name_en'] ?? 'Unknown Product',
            'product_name_en' => $offProduct['product_name_en'] ?? null,
            'barcode' => $offProduct['code'] ?? null,
            'image_url' => $offProduct['image_url'] ?? null,
            'image_front_url' => $offProduct['image_front_url'] ?? null,
            'image_front_small_url' => $offProduct['image_front_small_url'] ?? null,
            'image_front_thumb_url' => $offProduct['image_front_thumb_url'] ?? null,
            'brands' => $offProduct['brands'] ?? null,
            'brands_tags' => $offProduct['brands_tags'] ?? [],
            'quantity' => $offProduct['quantity'] ?? null,
            'categories' => $offProduct['categories'] ?? null,
            'categories_tags' => $offProduct['categories_tags'] ?? [],
            'countries' => $offProduct['countries'] ?? null,
            'countries_tags' => $offProduct['countries_tags'] ?? [],
            'ingredients_text' => $offProduct['ingredients_text'] ?? null,
            'ingredients_text_en' => $offProduct['ingredients_text_en'] ?? null,
            'nutriscore_grade' => $offProduct['nutriscore_grade'] ?? null,
            'nutriscore_score' => $offProduct['nutriscore_score'] ?? null,
            'nova_group' => $offProduct['nova_group'] ?? null,
            'allergens' => $offProduct['allergens'] ?? null,
            'allergens_tags' => $offProduct['allergens_tags'] ?? [],
            'labels' => $offProduct['labels'] ?? null,
            'labels_tags' => $offProduct['labels_tags'] ?? [],
            'manufacturing_places' => $offProduct['manufacturing_places'] ?? null,
            'origin' => $offProduct['origins'] ?? null,
            'packaging' => $offProduct['packaging'] ?? null,
            'stores' => $offProduct['stores'] ?? null,
            
            // Legacy/Internal mappings
            'nama_product' => $offProduct['product_name'] ?? $offProduct['product_name_en'] ?? 'Unknown Product',
            'ingredients_list' => $this->extractIngredientsList($offProduct),
            'nutriments' => $offProduct['nutriments'] ?? [],
            'completeness' => $offProduct['completeness'] ?? 0,
            'last_modified' => $offProduct['last_modified_t'] ?? null,
        ];

        // Add automatic halal analysis
        $ingredients = $offProduct['ingredients_text'] ?? $offProduct['ingredients_text_en'] ?? '';
        if ($ingredients) {
            $normalized['halal_analysis'] = $this->analyzeIngredients($ingredients);
        } else {
            $normalized['halal_analysis'] = [
                'status' => 'Unknown',
                'is_potentially_halal' => false,
                'suspicious_ingredients' => [],
                'recommendation' => 'No ingredients data available for analysis.'
            ];
        }

        return $normalized;
    }

    /**
     * Extract ingredients as array
     */
    private function extractIngredientsList(array $offProduct): array
    {
        if (isset($offProduct['ingredients'])) {
            return array_map(
                fn($ing) => $ing['text'] ?? $ing['id'] ?? 'Unknown',
                $offProduct['ingredients']
            );
        }

        if (isset($offProduct['ingredients_text'])) {
            return array_map(
                'trim',
                explode(',', $offProduct['ingredients_text'])
            );
        }

        return [];
    }

    /**
     * Search products by label (halal, vegetarian, vegan)
     */
    public function searchByLabel(string $label, string $query = '', int $pageSize = 20, int $page = 1): array
    {
        try {
            $params = [
                'json' => true,
                'page' => $page,
                'page_size' => $pageSize,
            ];

            if ($query) {
                $params['search_terms'] = $query;
            }

            // OpenFoodFacts uses tags for labels
            $tag = match($label) {
                'halal' => 'en:halal',
                'vegetarian' => 'en:vegetarian',
                'vegan' => 'en:vegan',
                default => $label
            };

            $response = $this->offClient()->get(self::BASE_URL . "/tag/labels/{$tag}.json", $params);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'products' => array_map(fn($p) => $this->normalizeProduct($p), $data['products'] ?? []),
                    'count' => $data['count'] ?? 0,
                    'page' => $data['page'] ?? $page,
                    'page_count' => $data['page_count'] ?? 1
                ];
            }

            return ['success' => false, 'products' => [], 'count' => 0];
        } catch (\Exception $e) {
            Log::error("OFF Label Search Error: " . $e->getMessage());
            return ['success' => false, 'products' => [], 'count' => 0];
        }
    }

    /**
     * Search products by brand
     */
    public function searchByBrand(string $brand, int $pageSize = 20, int $page = 1): array
    {
        try {
            $response = $this->offClient()->get(self::BASE_URL . "/brand/{$brand}.json", [
                    'json' => true,
                    'page' => $page,
                    'page_size' => $pageSize,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'products' => array_map(fn($p) => $this->normalizeProduct($p), $data['products'] ?? []),
                    'count' => $data['count'] ?? 0,
                    'page' => $data['page'] ?? $page,
                    'page_count' => $data['page_count'] ?? 1
                ];
            }

            return ['success' => false, 'products' => [], 'count' => 0];
        } catch (\Exception $e) {
            Log::error("OFF Brand Search Error: " . $e->getMessage());
            return ['success' => false, 'products' => [], 'count' => 0];
        }
    }

    /**
     * Search products by category
     */
    public function searchByCategory(string $category, int $pageSize = 20, int $page = 1): array
    {
        try {
            $response = $this->offClient()->get(self::BASE_URL . "/category/{$category}.json", [
                    'json' => true,
                    'page' => $page,
                    'page_size' => $pageSize,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'products' => array_map(fn($p) => $this->normalizeProduct($p), $data['products'] ?? []),
                    'count' => $data['count'] ?? 0,
                    'page' => $data['page'] ?? $page,
                    'page_count' => $data['page_count'] ?? 1
                ];
            }

            return ['success' => false, 'products' => [], 'count' => 0];
        } catch (\Exception $e) {
            Log::error("OFF Category Search Error: " . $e->getMessage());
            return ['success' => false, 'products' => [], 'count' => 0];
        }
    }

    /**
     * Analyze ingredients for halal status (matching Android HalalAnalysis)
     */
    public function analyzeIngredients(string $ingredients): array
    {
        $issues = [];
        $haram_keywords = [
            'pork', 'lard', 'bacon', 'ham', 'wine', 'alcohol', 'beer', 'cider',
            'gelatin', 'carmine', 'E120', 'cochineal', 'rennet', 'pepsin'
        ];

        $lowerIngredients = strtolower($ingredients);
        foreach ($haram_keywords as $keyword) {
            if (str_contains($lowerIngredients, $keyword)) {
                $issues[] = ucfirst($keyword);
            }
        }

        $isHalal = count($issues) === 0;
        $statusLabel = $isHalal ? 'Halal' : 'Haram';
        $recommendation = $isHalal 
            ? 'This product appears to contain only halal ingredients.' 
            : 'This product contains ' . implode(', ', $issues) . ' which are not halal.';

        return [
            'status' => $statusLabel,
            'is_potentially_halal' => $isHalal,
            'suspicious_ingredients' => $issues,
            'recommendation' => $recommendation,
            'is_halal' => $isHalal // legacy
        ];
    }
}
