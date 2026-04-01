<?php

namespace App\Services;

use App\Models\BpomData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CosmeticExternalService
{
    private const OPEN_BEAUTYFACTS_PRODUCT = 'https://world.openbeautyproducts.org/api/v0/product/%s.json';
    private const OPEN_BEAUTYFACTS_SEARCH = 'https://world.openbeautyfacts.org/cgi/search.pl';

    public function search(string $query): array
    {
        $normalized = trim($query);

        if ($normalized === '') {
            return [];
        }

        $cacheKey = 'cosmetic_external_' . md5(Str::lower($normalized));

        return Cache::remember($cacheKey, 86400, function () use ($normalized) {
            if (ctype_digit($normalized)) {
                $barcodeResult = $this->searchByBarcode($normalized);
                if ($barcodeResult !== null) {
                    return [$barcodeResult];
                }
            }

            return $this->searchByKeyword($normalized);
        });
    }

    public function importOrUpdate(array $product): BpomData
    {
        return BpomData::updateOrCreate(
            [
                'barcode' => $product['barcode'] ?? null,
                'nama_produk' => $product['name'],
            ],
            [
                'merk' => $product['brand'] ?? null,
                'kategori' => 'kosmetik',
                'ingredients_text' => $product['ingredients'] ?? null,
                'image_url' => $product['image_url'] ?? null,
                'status_keamanan' => $product['status_keamanan'] ?? 'aman',
                'status_halal' => $product['status_halal'] ?? 'belum_diverifikasi',
                'analisis_kandungan' => json_encode($product['dangerous_ingredients'] ?? []),
                'sumber_data' => 'open_beauty_facts_api',
                'verification_status' => 'pending',
                'last_synced_at' => now(),
            ]
        );
    }

    private function searchByBarcode(string $barcode): ?array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Halalytics/1.0 (contact@halalytics.id)'])
                ->get(sprintf(self::OPEN_BEAUTYFACTS_PRODUCT, $barcode));

            if (!$response->successful()) {
                return null;
            }

            $payload = $response->json();
            $product = $payload['product'] ?? null;

            if (!is_array($product)) {
                return null;
            }

            return $this->mapProduct($product);
        } catch (\Throwable $throwable) {
            Log::warning('OpenBeautyFacts barcode search failed', [
                'barcode' => $barcode,
                'error' => $throwable->getMessage(),
            ]);

            return null;
        }
    }

    private function searchByKeyword(string $query): array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Halalytics/1.0 (contact@halalytics.id)'])
                ->get(self::OPEN_BEAUTYFACTS_SEARCH, [
                    'search_terms' => $query,
                    'search_simple' => 1,
                    'action' => 'process',
                    'json' => 1,
                    'page_size' => 20,
                ]);

            if (!$response->successful()) {
                return [];
            }

            return collect($response->json('products', []))
                ->map(fn (array $product) => $this->mapProduct($product))
                ->filter(fn (array $product) => !empty($product['name']))
                ->values()
                ->all();
        } catch (\Throwable $throwable) {
            Log::warning('OpenBeautyFacts keyword search failed', [
                'query' => $query,
                'error' => $throwable->getMessage(),
            ]);

            return [];
        }
    }

    private function mapProduct(array $product): array
    {
        $ingredients = $product['ingredients_text'] ?? $product['ingredients_text_en'] ?? '';

        return [
            'name' => $product['product_name'] ?? $product['product_name_en'] ?? 'Unknown',
            'brand' => $product['brands'] ?? null,
            'barcode' => $product['code'] ?? null,
            'categories' => $product['categories'] ?? null,
            'ingredients' => $ingredients,
            'image_url' => $product['image_url'] ?? $product['image_front_url'] ?? null,
            'countries' => $product['countries'] ?? null,
            'status_keamanan' => empty($this->detectDangerousIngredients($ingredients)) ? 'aman' : 'waspada',
            'status_halal' => 'belum_diverifikasi',
            'dangerous_ingredients' => $this->detectDangerousIngredients($ingredients),
        ];
    }

    private function detectDangerousIngredients(string $ingredientsText): array
    {
        $ingredientsLower = Str::lower($ingredientsText);
        $watchlist = [
            'mercury' => 'Merkuri',
            'mercuric' => 'Senyawa merkuri',
            'hydroquinone' => 'Hydroquinone',
            'tretinoin' => 'Tretinoin',
            'formaldehyde' => 'Formaldehida',
            'lead' => 'Timbal',
            'rhodamine' => 'Rhodamine B',
        ];

        $detected = [];
        foreach ($watchlist as $needle => $label) {
            if (Str::contains($ingredientsLower, $needle)) {
                $detected[] = $label;
            }
        }

        return array_values(array_unique($detected));
    }
}
