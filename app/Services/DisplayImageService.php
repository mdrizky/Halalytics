<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DisplayImageService
{
    private array $fallbackPool = [
        'images/product/1.jpg',
        'images/product/2.jpg',
        'images/product/3.jpg',
        'images/product/4.jpg',
        'images/product/5.jpg',
        'images/product/6.jpg',
        'images/product/7.jpg',
    ];

    public function resolve(?string $value, array $context = [], string $type = 'product'): string
    {
        $normalized = $this->normalizeInput($value);
        if ($normalized !== null) {
            return $normalized;
        }

        $candidate = $this->discoverExternalImage($context, $type);
        if ($candidate !== null) {
            return $candidate;
        }

        return $this->fallback($context, $type);
    }

    public function fallback(array $context = [], string $type = 'product'): string
    {
        $seed = implode('|', array_filter([
            $type,
            data_get($context, 'barcode'),
            data_get($context, 'name'),
            data_get($context, 'category'),
        ]));

        if ($seed === '') {
            return asset('images/placeholders/product-placeholder.svg');
        }

        $index = abs(crc32($seed)) % count($this->fallbackPool);

        return asset($this->fallbackPool[$index]);
    }

    private function normalizeInput(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        if (Str::startsWith($value, ['/storage/', 'storage/'])) {
            return asset(ltrim($value, '/'));
        }

        if (Str::startsWith($value, ['/images/', 'images/'])) {
            return asset(ltrim($value, '/'));
        }

        $publicRelative = ltrim($value, '/');
        if (file_exists(public_path($publicRelative))) {
            return asset($publicRelative);
        }

        $storageRelative = Str::startsWith($publicRelative, 'public/')
            ? Str::after($publicRelative, 'public/')
            : $publicRelative;

        if (file_exists(storage_path('app/public/' . $storageRelative))) {
            return asset('storage/' . $storageRelative);
        }

        return null;
    }

    private function discoverExternalImage(array $context, string $type): ?string
    {
        $candidates = array_filter([
            data_get($context, 'image_url'),
            data_get($context, 'image'),
            data_get($context, 'image_front'),
            data_get($context, 'image_back'),
        ]);

        foreach ($candidates as $candidate) {
            $normalized = $this->normalizeInput((string) $candidate);
            if ($normalized !== null) {
                return $normalized;
            }
        }

        $barcode = preg_replace('/\D+/', '', (string) data_get($context, 'barcode'));
        if ($barcode !== '') {
            if (in_array($type, ['cosmetic', 'bpom', 'beauty'], true)) {
                $obf = $this->lookupOpenBeautyFactsImage($barcode);
                if ($obf !== null) {
                    return $obf;
                }
            }

            $off = $this->lookupOpenFoodFactsImage($barcode);
            if ($off !== null) {
                return $off;
            }
        }

        $google = $this->lookupGoogleCustomSearchImage($context);
        if ($google !== null) {
            return $google;
        }

        return null;
    }

    private function lookupOpenFoodFactsImage(string $barcode): ?string
    {
        return Cache::remember("display_image_off_{$barcode}", 86400, function () use ($barcode) {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Halalytics/1.0'])
                ->get("https://world.openfoodfacts.org/api/v2/product/{$barcode}.json", [
                    'fields' => 'image_url,image_front_url,image_front_small_url',
                ]);

            if (!$response->successful()) {
                return null;
            }

            return $this->normalizeInput(
                $response->json('product.image_url')
                ?: $response->json('product.image_front_url')
                ?: $response->json('product.image_front_small_url')
            );
        });
    }

    private function lookupOpenBeautyFactsImage(string $barcode): ?string
    {
        return Cache::remember("display_image_obf_{$barcode}", 86400, function () use ($barcode) {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Halalytics/1.0'])
                ->get("https://world.openbeautyfacts.org/api/v2/product/{$barcode}.json", [
                    'fields' => 'image_url,image_front_url,image_front_small_url',
                ]);

            if (!$response->successful()) {
                return null;
            }

            return $this->normalizeInput(
                $response->json('product.image_url')
                ?: $response->json('product.image_front_url')
                ?: $response->json('product.image_front_small_url')
            );
        });
    }

    private function lookupGoogleCustomSearchImage(array $context): ?string
    {
        $apiKey = (string) config('services.google.custom_search_key');
        $cx = (string) config('services.google.custom_search_engine_id');

        if ($apiKey === '' || $cx === '') {
            return null;
        }

        $query = trim(implode(' ', array_filter([
            data_get($context, 'name'),
            data_get($context, 'brand'),
            data_get($context, 'category'),
            'product packaging',
        ])));

        if ($query === '') {
            return null;
        }

        return Cache::remember('display_image_google_' . md5($query), 86400, function () use ($apiKey, $cx, $query) {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Halalytics/1.0'])
                ->get('https://www.googleapis.com/customsearch/v1', [
                    'key' => $apiKey,
                    'cx' => $cx,
                    'q' => $query,
                    'searchType' => 'image',
                    'num' => 1,
                    'safe' => 'active',
                    'gl' => 'id',
                    'hl' => 'id',
                ]);

            if (!$response->successful()) {
                return null;
            }

            return $this->normalizeInput(data_get($response->json(), 'items.0.link'));
        });
    }
}
