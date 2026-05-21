<?php

namespace App\Services\External;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenFoodFactsService
{
    private string $baseUrl = 'https://world.openfoodfacts.org/api/v2';

    public function getByBarcode(string $barcode): ?array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/product/{$barcode}.json");
            if ($response->successful() && (int) $response->json('status') === 1) {
                $product = (array) $response->json('product', []);
                return [
                    'name' => $product['product_name'] ?? 'Unknown Product',
                    'ingredients' => $product['ingredients_text'] ?? '',
                    'allergens' => $product['allergens_tags'] ?? [],
                    'additives' => $product['additives_tags'] ?? [],
                    'nutriments' => [
                        'sugars' => (float) ($product['nutriments']['sugars_100g'] ?? 0),
                        'sodium' => (float) ($product['nutriments']['sodium_100g'] ?? 0),
                        'fat' => (float) ($product['nutriments']['fat_100g'] ?? 0),
                        'protein' => (float) ($product['nutriments']['proteins_100g'] ?? 0),
                        'calories' => (float) ($product['nutriments']['energy-kcal_100g'] ?? 0),
                    ],
                    'source' => 'OpenFoodFacts',
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('OFF barcode fetch failed: ' . $e->getMessage());
        }

        return null;
    }
}
