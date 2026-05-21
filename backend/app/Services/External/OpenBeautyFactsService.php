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
                $product = (array) $response->json('product', []);
                return [
                    'name' => $product['product_name'] ?? 'Unknown Cosmetic Product',
                    'ingredients' => $product['ingredients_text'] ?? '',
                    'allergens' => $product['allergens_tags'] ?? [],
                    'additives' => $product['additives_tags'] ?? [],
                    'nutriments' => [
                        'sugars' => 0,
                        'sodium' => 0,
                        'fat' => 0,
                        'protein' => 0,
                        'calories' => 0,
                    ],
                    'source' => 'OpenBeautyFacts',
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('OBF barcode fetch failed: ' . $e->getMessage());
        }

        return null;
    }
}
