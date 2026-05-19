<?php

namespace App\Services\External;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenBeautyFactsService
{
    private string $baseUrl = 'https://world.openbeautyfacts.org/api/v2';

    public function getByBarcode(string $barcode): ?array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/product/{$barcode}.json");
            if ($response->successful() && (int) $response->json('status') === 1) {
                return $this->parseProduct($response->json('product', []));
            }
        } catch (\Throwable $e) {
            Log::error('OpenBeautyFacts error: ' . $e->getMessage());
        }

        return null;
    }

    public function search(string $query, int $page = 1, int $pageSize = 20): array
    {
        try {
            $response = Http::timeout(10)->get('https://world.openbeautyfacts.org/cgi/search.pl', [
                'search_terms' => $query,
                'search_simple' => 1,
                'action' => 'process',
                'json' => 1,
                'page' => $page,
                'page_size' => $pageSize,
            ]);

            return $response->json('products', []) ?? [];
        } catch (\Throwable $e) {
            Log::error('OBF search error: ' . $e->getMessage());

            return [];
        }
    }

    private function parseProduct(array $product): array
    {
        return [
            'name' => $product['product_name'] ?? 'Unknown',
            'brand' => $product['brands'] ?? '',
            'category' => 'kosmetik',
            'ingredients' => $product['ingredients_text'] ?? '',
            'image' => $product['image_url'] ?? null,
            'halal_labels' => $product['labels_tags'] ?? [],
            'allergens' => $product['allergens_tags'] ?? [],
            'source' => 'OpenBeautyFacts',
        ];
    }
}
