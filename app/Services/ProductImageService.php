<?php

namespace App\Services;

use App\Models\ProductImageFallback;
use App\Models\ProductModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageService
{
    private const USER_AGENT = 'Halalytics/1.0 (contact@halalytics.id)';
    private const TIMEOUT_SECONDS = 6;

    private array $defaultFallbackMap = [
        'food' => '/images/default/food.svg',
        'drink' => '/images/default/drink.svg',
        'seasoning' => '/images/default/seasoning.svg',
        'cosmetic' => '/images/default/cosmetic.svg',
        'medicine' => '/images/default/medicine.svg',
        'article' => '/images/placeholders/article-placeholder.svg',
        'banner' => '/images/placeholders/banner-placeholder.svg',
        'ingredient' => '/images/placeholders/ingredient-placeholder.svg',
        'category' => '/images/placeholders/product-placeholder.svg',
        'bpom' => '/images/placeholders/product-placeholder.svg',
        'street_food' => '/images/placeholders/food-placeholder.svg',
        'general' => '/images/default/general.svg',
    ];

    private array $bundledAssetMap = [
        'kecap abc' => '/images/products/kecap_abc.png',
        'sari gandum' => '/images/products/sari_gandum.png',
        'silverqueen' => '/images/products/silverqueen.png',
    ];

    public function getImages(string $productName, ?string $barcode = null, string $source = 'internal', array $metadata = []): array
    {
        $categoryKey = $this->resolveCategoryKey(
            $metadata['category'] ?? null,
            $productName,
            $source
        );

        $candidates = $this->discoverCandidates(
            productName: $productName,
            barcode: $barcode,
            source: $source,
            metadata: $metadata,
            categoryKey: $categoryKey,
            stopAtFirstVerified: false
        );

        if ($candidates === []) {
            $fallback = $this->buildFallbackCandidate($categoryKey);

            return [
                'source' => 'fallback',
                'images' => [$fallback],
            ];
        }

        return [
            'source' => $candidates[0]['type'] ?? 'fallback',
            'images' => $candidates,
        ];
    }

    public function auditProduct(ProductModel $product): array
    {
        $rawImage = $product->getRawOriginal('image');
        $categoryName = optional($product->kategori)->nama_kategori;
        $categoryKey = $this->resolveCategoryKey($categoryName, $product->nama_product, $product->source);
        $fallbackUrl = $this->fallbackUrl($categoryName);

        if (blank($rawImage)) {
            return [
                'state' => 'missing',
                'raw_image' => null,
                'resolved_image' => $fallbackUrl,
                'category_key' => $categoryKey,
                'fallback_url' => $fallbackUrl,
                'needs_sync' => true,
                'verified' => false,
            ];
        }

        $managedLocalPath = $this->extractManagedLocalPath($rawImage);

        if ($managedLocalPath !== null) {
            $pathExists = file_exists($this->toFilesystemPath($managedLocalPath));
            $isFallback = $this->isFallbackPath($managedLocalPath);

            return [
                'state' => $pathExists ? ($isFallback ? 'ok_fallback' : 'ok_local') : 'broken_local',
                'raw_image' => $managedLocalPath,
                'resolved_image' => $pathExists ? $this->toPublicUrl($managedLocalPath) : $fallbackUrl,
                'category_key' => $categoryKey,
                'fallback_url' => $fallbackUrl,
                'needs_sync' => !$pathExists || $isFallback,
                'verified' => $pathExists,
            ];
        }

        if ($this->isAbsoluteUrl($rawImage)) {
            $verified = $this->isReachableRemoteImage($rawImage);

            return [
                'state' => $verified ? 'ok_remote' : 'broken_remote',
                'raw_image' => $rawImage,
                'resolved_image' => $verified ? $rawImage : $fallbackUrl,
                'category_key' => $categoryKey,
                'fallback_url' => $fallbackUrl,
                'needs_sync' => !$verified,
                'verified' => $verified,
            ];
        }

        $normalizedPath = $this->normalizeLocalPath($rawImage);
        $pathExists = $normalizedPath !== null && file_exists($this->toFilesystemPath($normalizedPath));
        $isFallback = $normalizedPath !== null && $this->isFallbackPath($normalizedPath);

        return [
            'state' => $pathExists ? ($isFallback ? 'ok_fallback' : 'ok_local') : 'broken_local',
            'raw_image' => $normalizedPath,
            'resolved_image' => $pathExists ? $this->toPublicUrl($normalizedPath) : $fallbackUrl,
            'category_key' => $categoryKey,
            'fallback_url' => $fallbackUrl,
            'needs_sync' => !$pathExists || $isFallback,
            'verified' => $pathExists,
        ];
    }

    public function syncProduct(ProductModel $product, bool $forceRefresh = false): array
    {
        $audit = $this->auditProduct($product);

        if (!$forceRefresh && !$audit['needs_sync']) {
            return [
                'product_id' => $product->id_product,
                'product_name' => $product->nama_product,
                'status' => 'kept',
                'source' => $audit['state'],
                'image' => $audit['raw_image'],
                'resolved_image' => $audit['resolved_image'],
                'fallback_url' => $audit['fallback_url'],
            ];
        }

        $categoryName = optional($product->kategori)->nama_kategori;
        $categoryKey = $this->resolveCategoryKey($categoryName, $product->nama_product, $product->source);

        $candidates = $this->discoverCandidates(
            productName: $product->nama_product,
            barcode: $product->barcode,
            source: $product->source ?? 'local',
            metadata: [
                'category' => $categoryName,
                'existing_image' => $product->getRawOriginal('image'),
                'exclude_id' => $product->id_product,
            ],
            categoryKey: $categoryKey,
            stopAtFirstVerified: true
        );

        $selected = collect($candidates)->first(fn (array $candidate) => !$candidate['is_fallback']);

        if ($selected === null) {
            $storedPath = $this->generateProductCard($product, $categoryKey);
            $selected = [
                'url' => $this->toPublicUrl($storedPath),
                'path' => $storedPath,
                'type' => 'generated_card',
                'label' => 'Generated product card',
                'verified' => true,
                'is_local' => true,
                'is_fallback' => false,
                'meta' => [],
            ];
        } elseif ($selected['is_local']) {
            $storedPath = $selected['path'];
        } else {
            $storedPath = $this->downloadRemoteImage($selected['url'], $product);

            if ($storedPath === null) {
                $storedPath = $this->generateProductCard($product, $categoryKey);
                $selected = [
                    'url' => $this->toPublicUrl($storedPath),
                    'path' => $storedPath,
                    'type' => 'generated_card',
                    'label' => 'Generated product card',
                    'verified' => true,
                    'is_local' => true,
                    'is_fallback' => false,
                    'meta' => [],
                ];
            }
        }

        $product->forceFill([
            'image' => $storedPath,
        ])->save();

        return [
            'product_id' => $product->id_product,
            'product_name' => $product->nama_product,
            'status' => $selected['type'] === 'generated_card' ? 'generated' : 'updated',
            'source' => $selected['type'],
            'image' => $storedPath,
            'resolved_image' => $this->toPublicUrl($storedPath),
            'fallback_url' => $this->fallbackUrl($categoryName),
        ];
    }

    public function fallbackPath(?string $category = null, string $type = 'product'): string
    {
        $key = $this->resolveCategoryKey($category, null, null, $type);
        $fallbacks = $this->fallbackMap();

        return $fallbacks[$key]
            ?? $fallbacks['general']
            ?? '/images/placeholders/' . $type . '-placeholder.svg';
    }

    public function fallbackUrl(?string $category = null, string $type = 'product', ?string $name = null): string
    {
        if ($type === 'product' && !blank($name)) {
            $searchTerm = Str::slug($name, '+');
            return "https://source.unsplash.com/400x400/?{$searchTerm}";
        }

        $categorySearch = match (Str::lower($category ?? '')) {
            'makanan', 'food' => 'food',
            'minuman', 'drink', 'beverage' => 'beverage',
            'kosmetik', 'cosmetic' => 'cosmetics',
            'obat', 'medicine' => 'medicine',
            'ingredient' => 'ingredients',
            default => blank($category) ? 'product' : Str::slug($category, '+'),
        };

        return "https://source.unsplash.com/400x400/?{$categorySearch}";
    }

    public function resolveCategoryKey(
        ?string $category = null,
        ?string $productName = null,
        ?string $source = null,
        string $type = 'product'
    ): string {
        if ($type !== 'product') {
            return array_key_exists($type, $this->fallbackMap()) ? $type : 'general';
        }

        $value = Str::of(($category ?? '') . ' ' . ($productName ?? '') . ' ' . ($source ?? ''))
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9\s]+/', ' ')
            ->squish()
            ->value();

        $seasoningKeywords = ['bumbu', 'sambal', 'saus', 'sauce', 'dressing', 'kecap', 'seasoning'];
        $drinkKeywords = ['minuman', 'drink', 'beverage', 'jus', 'juice', 'tea', 'teh', 'kopi', 'coffee', 'milk drink'];
        $cosmeticKeywords = ['kosmetik', 'cosmetic', 'skincare', 'beauty', 'lotion', 'serum', 'cream', 'cleanser', 'sunscreen'];
        $medicineKeywords = ['obat', 'medicine', 'health', 'kesehatan', 'suplemen', 'supplement', 'herbal', 'jamu', 'vitamin'];
        $foodKeywords = ['makanan', 'food', 'snack', 'bakery', 'bread', 'frozen', 'dairy', 'mie', 'wafer', 'biscuit', 'candy', 'coklat', 'chocolate'];

        foreach ($seasoningKeywords as $keyword) {
            if (str_contains($value, $keyword)) {
                return 'seasoning';
            }
        }

        foreach ($drinkKeywords as $keyword) {
            if (str_contains($value, $keyword)) {
                return 'drink';
            }
        }

        foreach ($foodKeywords as $keyword) {
            if (str_contains($value, $keyword)) {
                return 'food';
            }
        }

        foreach ($cosmeticKeywords as $keyword) {
            if (str_contains($value, $keyword)) {
                return 'cosmetic';
            }
        }

        foreach ($medicineKeywords as $keyword) {
            if (str_contains($value, $keyword)) {
                return 'medicine';
            }
        }

        return 'general';
    }

    public function toPublicUrl(?string $path): string
    {
        if (blank($path)) {
            return $this->fallbackUrl();
        }

        $managedLocalPath = $this->extractManagedLocalPath($path);

        if ($managedLocalPath !== null) {
            return $managedLocalPath;
        }

        if ($this->isAbsoluteUrl($path)) {
            return $path;
        }

        $normalizedPath = $this->normalizeLocalPath($path);

        return $normalizedPath ?? $this->fallbackPath();
    }

    private function discoverCandidates(
        string $productName,
        ?string $barcode,
        string $source,
        array $metadata,
        string $categoryKey,
        bool $stopAtFirstVerified = false
    ): array {
        $candidates = [];
        $existingImage = $metadata['existing_image'] ?? null;

        if (!blank($existingImage)) {
            $existingCandidate = $this->buildImageCandidate($existingImage, 'existing_image', 'Current image');
            if ($existingCandidate !== null && $existingCandidate['verified']) {
                $candidates[] = $existingCandidate;
                if ($stopAtFirstVerified && !$existingCandidate['is_fallback']) {
                    return $this->deduplicateCandidates($candidates);
                }
            }
        }

        $localMatch = $this->findLocalProductCandidate($productName, $metadata['exclude_id'] ?? null);
        if ($localMatch !== null) {
            $candidates[] = $localMatch;
            if ($stopAtFirstVerified) {
                return $this->deduplicateCandidates($candidates);
            }
        }

        $bundledAsset = $this->findBundledAssetCandidate($productName);
        if ($bundledAsset !== null) {
            $candidates[] = $bundledAsset;
            if ($stopAtFirstVerified) {
                return $this->deduplicateCandidates($candidates);
            }
        }

        $apiOrder = $categoryKey === 'cosmetic'
            ? ['beauty_barcode', 'beauty_search', 'food_barcode', 'food_search']
            : ['food_barcode', 'food_search', 'beauty_barcode', 'beauty_search'];

        foreach ($apiOrder as $apiStep) {
            $candidate = match ($apiStep) {
                'food_barcode' => $this->fetchOpenFoodFactsByBarcode($barcode),
                'food_search' => $this->fetchOpenFoodFactsByName($productName),
                'beauty_barcode' => $this->fetchOpenBeautyFactsByBarcode($barcode),
                'beauty_search' => $this->fetchOpenBeautyFactsByName($productName),
                default => null,
            };

            if ($candidate !== null) {
                $candidates[] = $candidate;
                if ($stopAtFirstVerified) {
                    return $this->deduplicateCandidates($candidates);
                }
            }
        }

        $candidates[] = $this->buildFallbackCandidate($categoryKey);

        return $this->deduplicateCandidates($candidates);
    }

    private function findLocalProductCandidate(string $productName, ?int $excludeId = null): ?array
    {
        $normalizedInput = $this->normalizeComparisonValue($productName);

        $bestCandidate = ProductModel::query()
            ->when($excludeId !== null, fn ($query) => $query->where('id_product', '!=', $excludeId))
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->get(['id_product', 'nama_product', 'image'])
            ->map(function (ProductModel $candidate) use ($normalizedInput) {
                similar_text($normalizedInput, $this->normalizeComparisonValue($candidate->nama_product), $score);

                return [
                    'candidate' => $candidate,
                    'score' => $score,
                ];
            })
            ->sortByDesc('score')
            ->first();

        if ($bestCandidate === null || $bestCandidate['score'] < 72) {
            return null;
        }

        return $this->buildImageCandidate(
            $bestCandidate['candidate']->getRawOriginal('image'),
            'local_match',
            'Similar local product',
            [
                'score' => round($bestCandidate['score'], 1),
            ]
        );
    }

    private function findBundledAssetCandidate(string $productName): ?array
    {
        $normalized = $this->normalizeComparisonValue($productName);

        foreach ($this->bundledAssetMap as $keyword => $assetPath) {
            if (str_contains($normalized, $keyword)) {
                return $this->buildImageCandidate($assetPath, 'bundled_asset', 'Bundled product asset');
            }
        }

        return null;
    }

    private function fetchOpenFoodFactsByBarcode(?string $barcode): ?array
    {
        if (blank($barcode)) {
            return null;
        }

        return Cache::remember(
            'product_image_off_barcode_' . md5($barcode),
            now()->addDay(),
            function () use ($barcode) {
                try {
                    $response = Http::timeout(self::TIMEOUT_SECONDS)
                        ->withUserAgent(self::USER_AGENT)
                        ->get("https://world.openfoodfacts.org/api/v0/product/{$barcode}.json");

                    if (!$response->successful() || (int) $response->json('status') !== 1) {
                        return null;
                    }

                    $product = $response->json('product', []);
                    $image = $product['image_front_url'] ?? $product['image_url'] ?? null;

                    return $this->buildImageCandidate($image, 'open_food_facts', 'Open Food Facts barcode');
                } catch (\Throwable $throwable) {
                    Log::warning('Open Food Facts barcode image sync failed', [
                        'barcode' => $barcode,
                        'error' => $throwable->getMessage(),
                    ]);
                }

                return null;
            }
        );
    }

    private function fetchOpenFoodFactsByName(string $productName): ?array
    {
        $cacheKey = 'product_image_off_search_' . md5($this->normalizeComparisonValue($productName));

        return Cache::remember($cacheKey, now()->addDay(), function () use ($productName) {
            try {
                $response = Http::timeout(self::TIMEOUT_SECONDS)
                    ->withUserAgent(self::USER_AGENT)
                    ->get('https://world.openfoodfacts.org/cgi/search.pl', [
                        'search_terms' => $productName,
                        'search_simple' => 1,
                        'action' => 'process',
                        'json' => 1,
                        'page_size' => 10,
                    ]);

                if (!$response->successful()) {
                    return null;
                }

                $bestMatch = collect($response->json('products', []))
                    ->map(function (array $product) use ($productName) {
                        $name = $product['product_name'] ?? $product['product_name_en'] ?? '';
                        similar_text($this->normalizeComparisonValue($productName), $this->normalizeComparisonValue($name), $score);

                        return [
                            'product' => $product,
                            'score' => $score,
                        ];
                    })
                    ->sortByDesc('score')
                    ->first();

                if ($bestMatch === null || $bestMatch['score'] < 55) {
                    return null;
                }

                $image = $bestMatch['product']['image_front_url'] ?? $bestMatch['product']['image_url'] ?? null;

                return $this->buildImageCandidate($image, 'open_food_facts_search', 'Open Food Facts search', [
                    'score' => round($bestMatch['score'], 1),
                ]);
            } catch (\Throwable $throwable) {
                Log::warning('Open Food Facts name image sync failed', [
                    'product' => $productName,
                    'error' => $throwable->getMessage(),
                ]);
            }

            return null;
        });
    }

    private function fetchOpenBeautyFactsByBarcode(?string $barcode): ?array
    {
        if (blank($barcode)) {
            return null;
        }

        return Cache::remember(
            'product_image_obf_barcode_' . md5($barcode),
            now()->addDay(),
            function () use ($barcode) {
                try {
                    $response = Http::timeout(self::TIMEOUT_SECONDS)
                        ->withUserAgent(self::USER_AGENT)
                        ->get("https://world.openbeautyfacts.org/api/v0/product/{$barcode}.json");

                    if (!$response->successful() || (int) $response->json('status') !== 1) {
                        return null;
                    }

                    $product = $response->json('product', []);
                    $image = $product['image_front_url'] ?? $product['image_url'] ?? null;

                    return $this->buildImageCandidate($image, 'open_beauty_facts', 'Open Beauty Facts barcode');
                } catch (\Throwable $throwable) {
                    Log::warning('Open Beauty Facts barcode image sync failed', [
                        'barcode' => $barcode,
                        'error' => $throwable->getMessage(),
                    ]);
                }

                return null;
            }
        );
    }

    private function fetchOpenBeautyFactsByName(string $productName): ?array
    {
        $cacheKey = 'product_image_obf_search_' . md5($this->normalizeComparisonValue($productName));

        return Cache::remember($cacheKey, now()->addDay(), function () use ($productName) {
            try {
                $response = Http::timeout(self::TIMEOUT_SECONDS)
                    ->withUserAgent(self::USER_AGENT)
                    ->get('https://world.openbeautyfacts.org/cgi/search.pl', [
                        'search_terms' => $productName,
                        'search_simple' => 1,
                        'action' => 'process',
                        'json' => 1,
                        'page_size' => 10,
                    ]);

                if (!$response->successful()) {
                    return null;
                }

                $bestMatch = collect($response->json('products', []))
                    ->map(function (array $product) use ($productName) {
                        $name = $product['product_name'] ?? $product['product_name_en'] ?? '';
                        similar_text($this->normalizeComparisonValue($productName), $this->normalizeComparisonValue($name), $score);

                        return [
                            'product' => $product,
                            'score' => $score,
                        ];
                    })
                    ->sortByDesc('score')
                    ->first();

                if ($bestMatch === null || $bestMatch['score'] < 50) {
                    return null;
                }

                $image = $bestMatch['product']['image_front_url'] ?? $bestMatch['product']['image_url'] ?? null;

                return $this->buildImageCandidate($image, 'open_beauty_facts_search', 'Open Beauty Facts search', [
                    'score' => round($bestMatch['score'], 1),
                ]);
            } catch (\Throwable $throwable) {
                Log::warning('Open Beauty Facts name image sync failed', [
                    'product' => $productName,
                    'error' => $throwable->getMessage(),
                ]);
            }

            return null;
        });
    }

    private function buildFallbackCandidate(string $categoryKey): array
    {
        $path = $this->fallbackPath($categoryKey);

        return [
            'url' => $this->toPublicUrl($path),
            'path' => $path,
            'type' => 'fallback',
            'label' => 'Category fallback',
            'verified' => true,
            'is_local' => true,
            'is_fallback' => true,
            'meta' => [],
        ];
    }

    private function buildImageCandidate(?string $value, string $type, string $label, array $meta = []): ?array
    {
        if (blank($value)) {
            return null;
        }

        $managedLocalPath = $this->extractManagedLocalPath($value);

        if ($managedLocalPath !== null) {
            if (!file_exists($this->toFilesystemPath($managedLocalPath))) {
                return null;
            }

            return [
                'url' => $this->toPublicUrl($managedLocalPath),
                'path' => $managedLocalPath,
                'type' => $type,
                'label' => $label,
                'verified' => true,
                'is_local' => true,
                'is_fallback' => $this->isFallbackPath($managedLocalPath),
                'meta' => $meta,
            ];
        }

        if ($this->isAbsoluteUrl($value)) {
            $verified = $this->isReachableRemoteImage($value);

            if (!$verified) {
                return null;
            }

            return [
                'url' => $value,
                'path' => null,
                'type' => $type,
                'label' => $label,
                'verified' => true,
                'is_local' => false,
                'is_fallback' => false,
                'meta' => $meta,
            ];
        }

        $path = $this->normalizeLocalPath($value);

        if ($path === null || !file_exists($this->toFilesystemPath($path))) {
            return null;
        }

        return [
            'url' => $this->toPublicUrl($path),
            'path' => $path,
            'type' => $type,
            'label' => $label,
            'verified' => true,
            'is_local' => true,
            'is_fallback' => $this->isFallbackPath($path),
            'meta' => $meta,
        ];
    }

    private function deduplicateCandidates(array $candidates): array
    {
        $unique = [];

        foreach ($candidates as $candidate) {
            $key = $candidate['path'] ?? $candidate['url'];
            $unique[$key] = $candidate;
        }

        return array_values($unique);
    }

    private function normalizeComparisonValue(?string $value): string
    {
        return Str::of($value ?? '')
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9\s]+/', ' ')
            ->replaceMatches('/\b(ml|g|gr|gram|kg|oz)\b/', ' ')
            ->squish()
            ->value();
    }

    private function isReachableRemoteImage(string $url): bool
    {
        return Cache::remember(
            'product_image_head_' . md5($url),
            now()->addHours(12),
            function () use ($url) {
                try {
                    $head = Http::timeout(10)
                        ->withUserAgent(self::USER_AGENT)
                        ->head($url);

                    if ($head->successful()) {
                        $contentType = strtolower((string) $head->header('Content-Type', ''));

                        return $contentType === '' || str_contains($contentType, 'image/');
                    }
                } catch (\Throwable $throwable) {
                    Log::debug('Remote image HEAD failed, falling back to GET', [
                        'url' => $url,
                        'error' => $throwable->getMessage(),
                    ]);
                }

                try {
                    $get = Http::timeout(10)
                        ->withUserAgent(self::USER_AGENT)
                        ->get($url);

                    if (!$get->successful()) {
                        return false;
                    }

                    $contentType = strtolower((string) $get->header('Content-Type', ''));

                    return $contentType === '' || str_contains($contentType, 'image/');
                } catch (\Throwable $throwable) {
                    Log::warning('Remote image verification failed', [
                        'url' => $url,
                        'error' => $throwable->getMessage(),
                    ]);
                }

                return false;
            }
        );
    }

    private function downloadRemoteImage(string $url, ProductModel $product): ?string
    {
        // Hotlink external images from OpenFoodFacts/OpenBeautyFacts to save bandwidth and avoid 403s
        if (str_contains($url, 'openfoodfacts.org') || str_contains($url, 'openbeautyfacts.org')) {
            return $url;
        }

        try {
            $response = Http::timeout(10)
                ->withUserAgent(self::USER_AGENT)
                ->get($url);

            if (!$response->successful()) {
                return null;
            }

            $contentType = strtolower((string) $response->header('Content-Type', ''));
            if ($contentType !== '' && !str_contains($contentType, 'image/')) {
                return null;
            }

            $extension = $this->determineExtension($url, $contentType);
            $filename = $product->id_product . '-' . Str::slug($product->nama_product) . '.' . $extension;
            $path = 'products/synced/' . $filename;

            Storage::disk('public')->put($path, $response->body());

            return '/storage/' . $path;
        } catch (\Throwable $throwable) {
            Log::warning('Product image download failed', [
                'product_id' => $product->id_product,
                'url' => $url,
                'error' => $throwable->getMessage(),
            ]);
        }

        return null;
    }

    private function determineExtension(string $url, string $contentType): string
    {
        return match (true) {
            str_contains($contentType, 'png') => 'png',
            str_contains($contentType, 'webp') => 'webp',
            str_contains($contentType, 'gif') => 'gif',
            str_contains($contentType, 'svg') => 'svg',
            str_contains($contentType, 'jpeg'),
            str_contains($contentType, 'jpg') => 'jpg',
            default => $this->sanitizeExtension(
                pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION)
            ),
        };
    }

    private function sanitizeExtension(string $extension): string
    {
        $extension = Str::lower(trim($extension));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], true)
            ? ($extension === 'jpeg' ? 'jpg' : $extension)
            : 'jpg';
    }

    private function normalizeLocalPath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = '/' . ltrim($path, '/');

        return match (true) {
            str_starts_with($path, '/public/') => '/storage/' . ltrim(Str::after($path, '/public/'), '/'),
            str_starts_with($path, '/storage/'),
            str_starts_with($path, '/images/') => $path,
            default => $path,
        };
    }

    private function toFilesystemPath(string $path): string
    {
        if (str_starts_with($path, '/storage/')) {
            return storage_path('app/public/' . ltrim(Str::after($path, '/storage/'), '/'));
        }

        return public_path(ltrim($path, '/'));
    }

    private function isFallbackPath(string $path): bool
    {
        return str_starts_with($path, '/images/default/');
    }

    private function isAbsoluteUrl(string $value): bool
    {
        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }

    private function extractManagedLocalPath(?string $value): ?string
    {
        if (blank($value) || !$this->isAbsoluteUrl($value)) {
            return null;
        }

        $host = Str::lower((string) parse_url($value, PHP_URL_HOST));
        $path = (string) parse_url($value, PHP_URL_PATH);
        $appHost = Str::lower((string) parse_url((string) config('app.url'), PHP_URL_HOST));

        if (!in_array($host, array_filter([$appHost, 'localhost', '127.0.0.1']), true)) {
            return null;
        }

        return $this->normalizeLocalPath($path);
    }

    private function fallbackMap(): array
    {
        return Cache::remember('product_image_fallbacks_map', now()->addHours(12), function () {
            return array_replace(
                $this->defaultFallbackMap,
                ProductImageFallback::query()
                ->where('is_active', true)
                ->pluck('image_path', 'category_key')
                ->all()
            );
        });
    }

    private function generateProductCard(ProductModel $product, string $categoryKey): string
    {
        $palette = $this->paletteForCategory($categoryKey);
        $filename = $product->id_product . '-' . Str::slug($product->nama_product) . '.svg';
        $path = 'products/generated/' . $filename;
        $nameLines = $this->wrapText($product->nama_product, 18, 3);
        $categoryLabel = optional($product->kategori)->nama_kategori ?: Str::headline($categoryKey);
        $barcode = $product->barcode ?: 'Barcode unavailable';

        $textSvg = '';
        $y = 520;
        foreach ($nameLines as $line) {
            $textSvg .= '<text x="120" y="' . $y . '" fill="' . $palette['text'] . '" font-family="Arial, sans-serif" font-size="92" font-weight="700">' . $this->escapeSvg($line) . '</text>';
            $y += 108;
        }

        $svg = '<svg width="1200" height="1200" viewBox="0 0 1200 1200" fill="none" xmlns="http://www.w3.org/2000/svg">'
            . '<rect width="1200" height="1200" rx="96" fill="' . $palette['base'] . '"/>'
            . '<rect x="80" y="80" width="1040" height="1040" rx="72" fill="url(#heroGradient)"/>'
            . '<circle cx="920" cy="264" r="132" fill="' . $palette['accentSoft'] . '"/>'
            . '<circle cx="968" cy="884" r="188" fill="' . $palette['accentSoft'] . '" fill-opacity="0.9"/>'
            . '<rect x="120" y="156" width="320" height="58" rx="29" fill="' . $palette['chipBg'] . '"/>'
            . '<text x="152" y="194" fill="' . $palette['chipText'] . '" font-family="Arial, sans-serif" font-size="32" font-weight="700">' . $this->escapeSvg(Str::upper($categoryLabel)) . '</text>'
            . $textSvg
            . '<text x="120" y="898" fill="' . $palette['muted'] . '" font-family="Arial, sans-serif" font-size="44" font-weight="600">Barcode: ' . $this->escapeSvg($barcode) . '</text>'
            . '<text x="120" y="996" fill="' . $palette['muted'] . '" font-family="Arial, sans-serif" font-size="40" font-weight="500">Generated by Halalytics image sync</text>'
            . '<defs><linearGradient id="heroGradient" x1="120" y1="120" x2="1080" y2="1080" gradientUnits="userSpaceOnUse">'
            . '<stop stop-color="' . $palette['gradientFrom'] . '"/>'
            . '<stop offset="1" stop-color="' . $palette['gradientTo'] . '"/>'
            . '</linearGradient></defs>'
            . '</svg>';

        Storage::disk('public')->put($path, $svg);

        return '/storage/' . $path;
    }

    private function paletteForCategory(string $categoryKey): array
    {
        return match ($categoryKey) {
            'drink' => [
                'base' => '#EDF9FF',
                'gradientFrom' => '#DBF1FF',
                'gradientTo' => '#E5F6EE',
                'accentSoft' => '#AFE8F7',
                'chipBg' => '#D8F6FF',
                'chipText' => '#045B73',
                'text' => '#0C2A3A',
                'muted' => '#486877',
            ],
            'seasoning' => [
                'base' => '#FFF6EC',
                'gradientFrom' => '#FFF0D8',
                'gradientTo' => '#F7E6CC',
                'accentSoft' => '#F2C88C',
                'chipBg' => '#FFE7BE',
                'chipText' => '#7A4709',
                'text' => '#3E260D',
                'muted' => '#7B654B',
            ],
            'cosmetic' => [
                'base' => '#FFF6FA',
                'gradientFrom' => '#FBE7F0',
                'gradientTo' => '#F3EAFB',
                'accentSoft' => '#F0B9D6',
                'chipBg' => '#FFE0EE',
                'chipText' => '#893B69',
                'text' => '#3E2131',
                'muted' => '#77596D',
            ],
            'medicine' => [
                'base' => '#F2F8FF',
                'gradientFrom' => '#E1EFFF',
                'gradientTo' => '#E5F8EF',
                'accentSoft' => '#BCD7F7',
                'chipBg' => '#D8ECFF',
                'chipText' => '#1E5C8A',
                'text' => '#203247',
                'muted' => '#586C7F',
            ],
            default => [
                'base' => '#F4FBF7',
                'gradientFrom' => '#DFF3EA',
                'gradientTo' => '#F4F6D9',
                'accentSoft' => '#B8E5CF',
                'chipBg' => '#D7F4E7',
                'chipText' => '#17694E',
                'text' => '#163832',
                'muted' => '#55706A',
            ],
        };
    }

    private function wrapText(string $value, int $maxChars, int $maxLines): array
    {
        $words = preg_split('/\s+/', trim($value)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $candidate = trim($current . ' ' . $word);
            if (mb_strlen($candidate) <= $maxChars) {
                $current = $candidate;
                continue;
            }

            if ($current !== '') {
                $lines[] = $current;
            }

            $current = $word;

            if (count($lines) === $maxLines - 1) {
                break;
            }
        }

        if ($current !== '' && count($lines) < $maxLines) {
            $lines[] = $current;
        }

        $lines = array_slice($lines, 0, $maxLines);
        if (count($lines) === $maxLines && mb_strlen(trim($value)) > mb_strlen(implode(' ', $lines))) {
            $lines[$maxLines - 1] = Str::limit($lines[$maxLines - 1], $maxChars, '...');
        }

        return $lines === [] ? ['Halalytics Product'] : $lines;
    }

    private function escapeSvg(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1);
    }
}
