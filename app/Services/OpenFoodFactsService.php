<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OpenFoodFactsService
{
    private $baseUrl = 'https://world.openfoodfacts.org';
    private $cacheDuration = 3600; // 1 hour
    private $timeout = 30;

    /**
     * Search products by query
     */
    public function searchProducts($query, $pageSize = 20, $page = 1)
    {
        if (empty($query)) {
            return [
                'success' => false,
                'message' => 'Query is required',
                'products' => []
            ];
        }

        $cacheKey = "search_" . md5($query . $pageSize . $page);

        try {
            return Cache::remember($cacheKey, $this->cacheDuration, function () use ($query, $pageSize, $page) {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/cgi/search.pl", [
                        'search_terms' => $query,
                        'search_simple' => 1,
                        'action' => 'process',
                        'json' => 1,
                        'page_size' => $pageSize,
                        'page' => $page,
                        'fields' => 'code,product_name,product_name_en,brands,image_front_small_url,image_front_url,nutriscore_grade,quantity,categories,ingredients_text,ingredients_text_en,labels_tags'
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'count' => $data['count'] ?? 0,
                        'page' => $data['page'] ?? 1,
                        'page_size' => $data['page_size'] ?? $pageSize,
                        'products' => $data['products'] ?? []
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Failed to fetch products',
                    'products' => []
                ];
            });
        } catch (\Exception $e) {
            Log::error('OpenFoodFacts Search Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'products' => []
            ];
        }
    }

    /**
     * Search products by label (halal, vegetarian, vegan)
     */
    public function searchByLabel($label, $query = '', $pageSize = 20, $page = 1)
    {
        $cacheKey = "label_" . md5($label . $query . $pageSize . $page);

        try {
            return Cache::remember($cacheKey, $this->cacheDuration, function () use ($label, $query, $pageSize, $page) {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/cgi/search.pl", [
                        'search_terms' => $query,
                        'tagtype_0' => 'labels',
                        'tag_contains_0' => 'contains',
                        'tag_0' => "en:{$label}",
                        'action' => 'process',
                        'json' => 1,
                        'page_size' => $pageSize,
                        'page' => $page,
                        'fields' => 'code,product_name,product_name_en,brands,image_front_small_url,image_front_url,nutriscore_grade,quantity,categories,ingredients_text,ingredients_text_en,labels_tags'
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'count' => $data['count'] ?? 0,
                        'page' => $data['page'] ?? 1,
                        'page_size' => $data['page_size'] ?? $pageSize,
                        'filter' => $label,
                        'products' => $data['products'] ?? []
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Failed to fetch filtered products',
                    'products' => []
                ];
            });
        } catch (\Exception $e) {
            Log::error("OpenFoodFacts Label Search Error ({$label}): " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'products' => []
            ];
        }
    }

    /**
     * Get product detail by barcode
     */
    public function getProductByBarcode($barcode)
    {
        $cacheKey = "product_{$barcode}";

        try {
            return Cache::remember($cacheKey, $this->cacheDuration, function () use ($barcode) {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/api/v2/product/{$barcode}.json");

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['status']) && $data['status'] == 1 && isset($data['product'])) {
                        return [
                            'success' => true,
                            'product' => $data['product']
                        ];
                    }

                    return [
                        'success' => false,
                        'message' => 'Product not found',
                        'product' => null
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Failed to fetch product detail',
                    'product' => null
                ];
            });
        } catch (\Exception $e) {
            Log::error('OpenFoodFacts Product Detail Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'product' => null
            ];
        }
    }

    /**
     * Search by brand
     */
    public function searchByBrand($brand, $pageSize = 20, $page = 1)
    {
        $cacheKey = "brand_" . md5($brand . $pageSize . $page);

        try {
            return Cache::remember($cacheKey, $this->cacheDuration, function () use ($brand, $pageSize, $page) {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/cgi/search.pl", [
                        'tagtype_0' => 'brands',
                        'tag_contains_0' => 'contains',
                        'tag_0' => $brand,
                        'action' => 'process',
                        'json' => 1,
                        'page_size' => $pageSize,
                        'page' => $page,
                        'fields' => 'code,product_name,product_name_en,brands,image_front_small_url,image_front_url,nutriscore_grade,quantity,categories'
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'count' => $data['count'] ?? 0,
                        'brand' => $brand,
                        'products' => $data['products'] ?? []
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Failed to fetch brand products',
                    'products' => []
                ];
            });
        } catch (\Exception $e) {
            Log::error("OpenFoodFacts Brand Search Error ({$brand}): " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'products' => []
            ];
        }
    }

    /**
     * Search by category
     */
    public function searchByCategory($category, $pageSize = 20, $page = 1)
    {
        $cacheKey = "category_" . md5($category . $pageSize . $page);

        try {
            return Cache::remember($cacheKey, $this->cacheDuration, function () use ($category, $pageSize, $page) {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/cgi/search.pl", [
                        'tagtype_0' => 'categories',
                        'tag_contains_0' => 'contains',
                        'tag_0' => $category,
                        'action' => 'process',
                        'json' => 1,
                        'page_size' => $pageSize,
                        'page' => $page,
                        'fields' => 'code,product_name,product_name_en,brands,image_front_small_url,image_front_url,nutriscore_grade,quantity,categories'
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'count' => $data['count'] ?? 0,
                        'category' => $category,
                        'products' => $data['products'] ?? []
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Failed to fetch category products',
                    'products' => []
                ];
            });
        } catch (\Exception $e) {
            Log::error("OpenFoodFacts Category Search Error ({$category}): " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'products' => []
            ];
        }
    }

    /**
     * Analyze ingredients for halal status
     */
    public function analyzeIngredients($ingredients)
    {
        $nonHalalKeywords = [
            'pork', 'bacon', 'ham', 'lard', 'gelatin', 'gelatine',
            'alcohol', 'wine', 'beer', 'rum', 'vodka', 'whiskey',
            'e120', 'e441', 'e542', 'e904',
            'pepsin', 'rennet', 'lipase', 'tallow',
            'babi', 'daging babi', 'lemak babi'
        ];

        $ingredients = strtolower($ingredients);
        $foundNonHalal = [];

        foreach ($nonHalalKeywords as $keyword) {
            if (str_contains($ingredients, $keyword)) {
                $foundNonHalal[] = $keyword;
            }
        }

        $isPotentiallyHalal = empty($foundNonHalal);

        return [
            'status' => $isPotentiallyHalal ? 'potentially_halal' : 'contains_suspicious',
            'is_potentially_halal' => $isPotentiallyHalal,
            'suspicious_ingredients' => $foundNonHalal,
            'recommendation' => $isPotentiallyHalal
                ? 'Tidak ditemukan bahan mencurigakan'
                : 'Ditemukan bahan yang perlu dicek: ' . implode(', ', $foundNonHalal)
        ];
    }
}