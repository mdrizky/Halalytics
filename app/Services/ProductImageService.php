<?php
// app/Services/ProductImageService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ProductImageService
{
    /**
     * Ambil foto produk dengan fallback berlapis
     * Urutan: API Eksternal -> Unsplash -> Placeholder
     */
    public function getImages(string $productName, ?string $barcode = null, string $source = 'internal'): array
    {
        $cacheKey = "product_images_{$barcode}_{$source}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($productName, $barcode, $source) {
            // Skenario A: produk dari API eksternal (Open Food Facts, dll)
            if ($source === 'external' && $barcode) {
                $images = $this->fetchFromExternalApi($barcode);
                if (!empty($images)) {
                    return ['source' => 'external_api', 'images' => $images];
                }
            }

            // Fallback: cari dari Unsplash berdasarkan nama produk
            $images = $this->fetchFromUnsplash($productName);
            if (!empty($images)) {
                return ['source' => 'unsplash', 'images' => $images];
            }

            // Ultimate fallback: placeholder
            return ['source' => 'placeholder', 'images' => [$this->getPlaceholder($productName)]];
        });
    }

    private function fetchFromExternalApi(string $barcode): array
    {
        try {
            // Contoh: Open Food Facts
            $response = Http::timeout(5)->get("https://world.openfoodfacts.org/api/v0/product/{$barcode}.json");

            if ($response->ok() && $response->json('status') === 1) {
                $product = $response->json('product');
                $images = [];

                // Kumpulkan semua jenis foto dari API
                $imageFields = ['image_url', 'image_front_url', 'image_ingredients_url', 'image_nutrition_url'];
                foreach ($imageFields as $field) {
                    if (!empty($product[$field])) {
                        $images[] = [
                            'url'   => $product[$field],
                            'type'  => str_replace(['image_', '_url'], '', $field),
                            'label' => ucfirst(str_replace('_', ' ', str_replace(['image_', '_url'], '', $field))),
                        ];
                    }
                }

                return $images;
            }
        } catch (\Exception $e) {
            \Log::warning("External API error for barcode {$barcode}: " . $e->getMessage());
        }

        return [];
    }

    private function fetchFromUnsplash(string $productName): array
    {
        $accessKey = config('services.unsplash.access_key');
        if (!$accessKey) return [];

        try {
            $query = $this->buildSearchQuery($productName);
            $response = Http::timeout(5)
                ->withHeaders(['Authorization' => "Client-ID {$accessKey}"])
                ->get('https://api.unsplash.com/search/photos', [
                    'query'    => $query,
                    'per_page' => 4,
                    'orientation' => 'squarish',
                ]);

            if ($response->ok()) {
                return collect($response->json('results', []))
                    ->map(fn($photo) => [
                        'url'   => $photo['urls']['regular'],
                        'thumb' => $photo['urls']['thumb'],
                        'type'  => 'unsplash',
                        'label' => 'Foto ilustrasi',
                        'credit'=> $photo['user']['name'],
                    ])
                    ->toArray();
            }
        } catch (\Exception $e) {
            \Log::warning("Unsplash error for '{$productName}': " . $e->getMessage());
        }

        return [];
    }

    /**
     * Buat query yang relevan berdasarkan nama produk
     * Mendeteksi kategori: makanan, minuman, obat, kosmetik, dll
     */
    private function buildSearchQuery(string $productName): string
    {
        $name = strtolower($productName);

        $categoryMap = [
            ['keywords' => ['obat', 'tablet', 'kapsul', 'sirup', 'vitamin'],  'suffix' => 'medicine product'],
            ['keywords' => ['sabun', 'shampo', 'lotion', 'krim', 'parfum'],   'suffix' => 'cosmetic product'],
            ['keywords' => ['susu', 'jus', 'minuman', 'air', 'teh', 'kopi'],  'suffix' => 'drink beverage'],
            ['keywords' => ['mie', 'nasi', 'snack', 'biskuit', 'keripik'],    'suffix' => 'food snack'],
            ['keywords' => ['minyak', 'saus', 'bumbu', 'kecap'],              'suffix' => 'cooking ingredient'],
        ];

        foreach ($categoryMap as $cat) {
            foreach ($cat['keywords'] as $kw) {
                if (str_contains($name, $kw)) {
                    return "{$productName} {$cat['suffix']}";
                }
            }
        }

        return "{$productName} product packaging";
    }

    private function getPlaceholder(string $productName): array
    {
        $encoded = urlencode($productName);
        return [
            'url'   => "https://placehold.co/400x400/e2e8f0/64748b?text={$encoded}",
            'type'  => 'placeholder',
            'label' => 'Foto belum tersedia',
        ];
    }
}
