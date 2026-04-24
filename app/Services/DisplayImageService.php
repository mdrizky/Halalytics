<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DisplayImageService
{
    private array $fallbackPool = [
        'images/placeholders/product-placeholder.svg',
    ];

    private array $typeFallbacks = [
        'product' => 'images/placeholders/product-placeholder.svg',
        'bpom' => 'images/placeholders/product-placeholder.svg',
        'category' => 'images/placeholders/food-placeholder.svg',
        'street_food' => 'images/placeholders/food-placeholder.svg',
        'medicine' => 'images/placeholders/medicine-placeholder.svg',
        'cosmetic' => 'images/placeholders/cosmetic-placeholder.svg',
        'beauty' => 'images/placeholders/cosmetic-placeholder.svg',
        'ingredient' => 'images/placeholders/ingredient-placeholder.svg',
        'banner' => 'images/placeholders/banner-placeholder.svg',
        'article' => 'images/placeholders/article-placeholder.svg',
    ];

    public function resolve(?string $value, array $context = [], string $type = 'product'): string
    {
        $catalogMatch = $this->matchCatalogImage($context, $type);
        if ($catalogMatch !== null) {
            return $catalogMatch;
        }

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
        if (isset($this->typeFallbacks[$type])) {
            return asset($this->typeFallbacks[$type]);
        }

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

        if (Str::contains($value, ['placeholder.svg', 'product-placeholder', 'default-product', 'no-image', 'images/placeholders'])) {
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
        
        $nameSearch = trim(implode(' ', array_filter([data_get($context, 'name'), data_get($context, 'brand')])));
        if ($nameSearch !== '') {
            if (in_array($type, ['cosmetic', 'bpom', 'beauty'], true)) {
                $obfName = $this->searchOpenBeautyFactsImage($nameSearch);
                if ($obfName !== null) return $obfName;
            } else {
                $offName = $this->searchOpenFoodFactsImage($nameSearch);
                if ($offName !== null) return $offName;
            }
        }

        $google = $this->lookupGoogleCustomSearchImage($context);
        if ($google !== null) {
            return $google;
        }

        $ddg = $this->lookupDuckDuckGoImage($context);
        if ($ddg !== null) {
            return $ddg;
        }

        return null;
    }

    private function lookupOpenFoodFactsImage(string $barcode): ?string
    {
        return Cache::remember("display_image_off_{$barcode}", 86400, function () use ($barcode) {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Halalytics/1.0'])
                ->get("https://world.openfoodfacts.org/api/v2/product/{$barcode}.json", [
                    'fields' => 'image_url,image_front_url,image_front_small_url,image_small_url,image_thumb_url',
                ]);

            if (!$response->successful()) {
                return null;
            }

            return $this->normalizeInput(
                $response->json('product.image_url')
                ?: $response->json('product.image_front_url')
                ?: $response->json('product.image_small_url')
                ?: $response->json('product.image_thumb_url')
                ?: $response->json('product.image_front_small_url')
            );
        });
    }

    private function searchOpenFoodFactsImage(string $query): ?string
    {
        return Cache::remember('display_image_off_search_' . md5($query), 86400, function () use ($query) {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Halalytics/1.0'])
                ->get('https://world.openfoodfacts.org/cgi/search.pl', [
                    'search_terms' => $query,
                    'search_simple' => 1,
                    'action' => 'process',
                    'json' => 1,
                ]);

            if (!$response->successful() || empty($response->json('products'))) {
                return null;
            }

            $product = $response->json('products.0');
            return $this->normalizeInput(
                data_get($product, 'image_url')
                ?: data_get($product, 'image_front_url')
                ?: data_get($product, 'image_front_small_url')
            );
        });
    }

    private function lookupOpenBeautyFactsImage(string $barcode): ?string
    {
        return Cache::remember("display_image_obf_{$barcode}", 86400, function () use ($barcode) {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Halalytics/1.0'])
                ->get("https://world.openbeautyfacts.org/api/v2/product/{$barcode}.json", [
                    'fields' => 'image_url,image_front_url,image_front_small_url,image_small_url,image_thumb_url',
                ]);

            if (!$response->successful()) {
                return null;
            }

            return $this->normalizeInput(
                $response->json('product.image_url')
                ?: $response->json('product.image_front_url')
                ?: $response->json('product.image_small_url')
                ?: $response->json('product.image_thumb_url')
                ?: $response->json('product.image_front_small_url')
            );
        });
    }

    private function searchOpenBeautyFactsImage(string $query): ?string
    {
        return Cache::remember('display_image_obf_search_' . md5($query), 86400, function () use ($query) {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Halalytics/1.0'])
                ->get('https://world.openbeautyfacts.org/cgi/search.pl', [
                    'search_terms' => $query,
                    'search_simple' => 1,
                    'action' => 'process',
                    'json' => 1,
                ]);

            if (!$response->successful() || empty($response->json('products'))) {
                return null;
            }

            $product = $response->json('products.0');
            return $this->normalizeInput(
                data_get($product, 'image_url')
                ?: data_get($product, 'image_front_url')
                ?: data_get($product, 'image_front_small_url')
            );
        });
    }

    private function lookupGoogleCustomSearchImage(array $context): ?string
    {
        $query = trim(implode(' ', array_filter([data_get($context, 'name'), data_get($context, 'brand')])));
        if ($query === '') {
            return null;
        }

        $apiKey = (string) config('services.google.custom_search_key');
        $cx = (string) config('services.google.custom_search_engine_id');

        if ($apiKey !== '' && $cx !== '') {
            $googleCache = Cache::remember('display_image_google_' . md5($query), 86400, function () use ($apiKey, $cx, $query) {
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
            if ($googleCache) return $googleCache;
        }

        return Cache::remember('display_image_yahoo_' . md5($query), 86400, function () use ($query) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36'
                    ])
                    ->get('https://images.search.yahoo.com/search/images?p=' . urlencode($query . ' packaging product'));
                
                if ($response->successful()) {
                    if (preg_match_all('/"iurl":"(https:[^"]+?)"/i', $response->body(), $matches)) {
                        foreach ($matches[1] as $url) {
                            $imgUrl = str_replace(['\\', '&amp;'], ['', '&'], $url);
                            if (!str_contains($imgUrl, 'b.scorecardresearch.com') && !str_contains($imgUrl, 'yimg.com')) {
                                return $imgUrl;
                            }
                        }
                    }
                    if (preg_match('/<img[^>]+src=["\'](https:\/\/[^"\']+?)["\']/i', $response->body(), $matches)) {
                        return str_replace('&amp;', '&', $matches[1]);
                    }
                }
            } catch (\Throwable $e) {}
            return null;
        });
    }

    private function lookupDuckDuckGoImage(array $context): ?string
    {
        $query = trim(implode(' ', array_filter([data_get($context, 'name'), data_get($context, 'brand')])));
        if ($query === '') return null;

        return Cache::remember('display_image_ddg_' . md5($query), 86400, function () use ($query) {
            try {
                // Use the VQD-less endpoint or proxy logic if needed, but standard mobile search usually works
                $response = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Mobile Safari/537.36'
                    ])
                    ->get('https://duckduckgo.com/html/?q=' . urlencode($query . ' product photo'));

                if ($response->successful()) {
                    // DuckDuckGo HTML results sometimes contain <img> tags in the results div
                    if (preg_match_all('/class="tile--img__img"[^>]+src=["\']\/\/([^"\']+?)["\']/i', $response->body(), $matches)) {
                        return 'https://' . $matches[1][0];
                    }
                }
            } catch (\Throwable $e) {}
            return null;
        });
    }

    private function matchCatalogImage(array $context, string $type): ?string
    {
        $barcode = preg_replace('/\D+/', '', (string) data_get($context, 'barcode'));
        $barcodeCatalog = $this->getBarcodeCatalog();

        // 1. Barcode is absolute priority (100% match)
        if ($barcode !== '' && isset($barcodeCatalog[$barcode])) {
            return $this->normalizeInput($barcodeCatalog[$barcode]);
        }

        $name = Str::lower(trim((string) data_get($context, 'name', '')));
        $brand = Str::lower(trim((string) data_get($context, 'brand', '')));
        $category = Str::lower(trim((string) data_get($context, 'category', '')));
        $haystack = trim($name . ' ' . $brand . ' ' . $category);

        if ($haystack === '') {
            return null;
        }

        $catalog = $this->catalogForType($type, $category);
        
        // 2. Select the LONGEST matching keyword for maximum specificity
        $bestMatch = null;
        $maxLength = 0;

        foreach ($catalog as $keyword => $url) {
            $keywordLower = Str::lower($keyword);
            if (Str::contains($haystack, $keywordLower)) {
                $len = strlen($keywordLower);
                if ($len > $maxLength) {
                    $maxLength = $len;
                    $bestMatch = $url;
                }
            }
        }

        if ($bestMatch) {
            return $this->normalizeInput($bestMatch);
        }

        return null;
    }

    private function catalogForType(string $type, string $category): array
    {
        $productCatalog = [
            'indomie goreng' => 'https://assets.klikindomaret.com/products/10003517/10003517_1.jpg',
            'samyang' => 'https://images.openfoodfacts.org/images/products/880/107/331/1534/front_en.111.400.jpg',
            'pocari' => 'https://images.openfoodfacts.org/images/products/049/870/351/3141/front_en.85.400.jpg',
            'teh botol' => 'https://pkh.s3.ap-southeast-1.amazonaws.com/images/products/202102/teh-botol-sosro-original-pet-450ml.jpg',
            'bango kecap' => 'https://images.openfoodfacts.org/images/products/899/100/400/0004/front_en.4.400.jpg',
            'kecap abc' => 'https://images.openfoodfacts.org/images/products/899/100/400/0004/front_id.3.400.jpg',
            'abc sambal' => 'https://images.openfoodfacts.org/images/products/899/100/500/0005/front_en.5.400.jpg',
            'ultra milk' => 'https://assets.klikindomaret.com/products/20002621/20002621_1.jpg',
            'silverqueen' => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//96/MTA-2775604/silverqueen_silverqueen-cashew-65-gr_full02.jpg',
            'oreo original' => 'https://www.oreo.com/media/catalog/product/o/r/oreo_original_1.png',
            'pringles original' => 'https://images.openfoodfacts.org/images/products/003/800/084/4492/front_en.6.400.jpg',
            'bear brand' => 'https://images.openfoodfacts.org/images/products/955/600/112/1026/front_en.7.400.jpg',
            'chitato sapi' => 'https://image.cermati.com/q_70,w_1200,h_800,c_fit/v1/page/h1k7z8z8z8z8/chitato-sapi-panggang.jpg',
            'sari roti' => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//98/MTA-14993591/sari_roti_sari_roti_tawar_spesial_400g_full01_5dbb8596.jpg',
            'indomilk susu' => 'https://images.openfoodfacts.org/images/products/899/600/130/0077/front_id.6.400.jpg',
            'tango wafer' => 'https://images.openfoodfacts.org/images/products/899/600/130/0350/front_id.10.400.jpg',
            'teh pucuk' => 'https://assets.klikindomaret.com/products/20042457/20042457_1.jpg',
            'mie sedaap' => 'https://images.openfoodfacts.org/images/products/899/886/620/0333/front_id.8.400.jpg',
            'pop mie' => 'https://images.openfoodfacts.org/images/products/899/274/191/1110/front_id.26.400.jpg',
            'kapal api' => 'https://images.openfoodfacts.org/images/products/899/100/210/1115/front_id.3.400.jpg',
            'luwak white koffie' => 'https://images.openfoodfacts.org/images/products/899/281/210/1113/front_id.4.400.jpg',
            'good day' => 'https://images.openfoodfacts.org/images/products/899/100/230/1119/front_id.5.400.jpg',
            'milo' => 'https://images.openfoodfacts.org/images/products/002/800/017/6323/front_en.11.400.jpg',
            'maggi' => 'https://images.openfoodfacts.org/images/products/761/303/344/1112/front_en.12.400.jpg',
            'kfc' => 'https://upload.wikimedia.org/wikipedia/en/thumb/b/bf/KFC_logo.svg/1200px-KFC_logo.svg.png',
            'mcdonalds' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/36/McDonald%27s_Golden_Arches.svg/1200px-McDonald%27s_Golden_Arches.svg.png',
        ];

        $commonFoodCatalog = [
            'sari gandum' => 'https://images.openfoodfacts.org/images/products/899/600/130/2347/front_id.15.400.jpg',
            'roma biscuit' => 'https://images.openfoodfacts.org/images/products/899/600/134/0134/front_id.4.400.jpg',
            'beng beng' => 'https://images.openfoodfacts.org/images/products/899/600/130/3016/front_id.21.400.jpg',
            'chocolatos' => 'https://images.openfoodfacts.org/images/products/899/274/194/1117/front_id.10.400.jpg',
            'gery' => 'https://images.openfoodfacts.org/images/products/899/274/194/1216/front_id.5.400.jpg',
            'oreo' => 'https://images.openfoodfacts.org/images/products/004/400/003/2022/front_en.112.400.jpg',
            'silverqueen' => 'https://images.openfoodfacts.org/images/products/899/100/111/1111/front_id.4.400.jpg',
            'kitkat' => 'https://images.openfoodfacts.org/images/products/400/058/221/2012/front_en.35.400.jpg',
            'kinder joy' => 'https://images.openfoodfacts.org/images/products/800/050/022/2014/front_fr.20.400.jpg',
            'pringles' => 'https://images.openfoodfacts.org/images/products/505/399/012/3452/front_en.100.400.jpg',
            'taro' => 'https://images.openfoodfacts.org/images/products/899/274/191/1219/front_id.4.400.jpg',
            'twistko' => 'https://images.openfoodfacts.org/images/products/899/274/191/1318/front_id.4.400.jpg',
            'cheetos' => 'https://images.openfoodfacts.org/images/products/002/840/008/1111/front_en.15.400.jpg',
            'lays' => 'https://images.openfoodfacts.org/images/products/002/840/019/3333/front_en.10.400.jpg',
            'doritos' => 'https://images.openfoodfacts.org/images/products/002/840/003/1116/front_en.12.400.jpg',
            'kusuka' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/9/3/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'qtela' => 'https://images.openfoodfacts.org/images/products/899/100/230/2222/front_id.4.400.jpg',
            'potabee' => 'https://images.openfoodfacts.org/images/products/899/274/191/3336/front_id.5.400.jpg',
            'hapytos' => 'https://images.openfoodfacts.org/images/products/899/274/191/4442/front_id.4.400.jpg',
            'frisian flag' => 'https://images.openfoodfacts.org/images/products/899/274/195/1116/front_id.10.400.jpg',
            'ultramilk' => 'https://images.openfoodfacts.org/images/products/899/100/230/3335/front_id.6.400.jpg',
            'cimory' => 'https://images.openfoodfacts.org/images/products/899/281/210/5555/front_id.4.400.jpg',
            'yakult' => 'https://images.openfoodfacts.org/images/products/490/308/011/1118/front_en.10.400.jpg',
            'nu green tea' => 'https://images.openfoodfacts.org/images/products/899/281/210/6668/front_id.4.400.jpg',
            'ichitan' => 'https://images.openfoodfacts.org/images/products/899/281/210/7774/front_id.4.400.jpg',
            'pocari sweat' => 'https://images.openfoodfacts.org/images/products/498/703/508/9315/front_en.10.400.jpg',
            'aqua' => 'https://images.openfoodfacts.org/images/products/899/281/210/8880/front_id.4.400.jpg',
            'le minerale' => 'https://images.openfoodfacts.org/images/products/899/281/210/9993/front_id.4.400.jpg',
            'vit' => 'https://images.openfoodfacts.org/images/products/899/281/211/1118/front_id.4.400.jpg',
            'ades' => 'https://images.openfoodfacts.org/images/products/899/281/211/2221/front_id.4.400.jpg',
            'nestle pure life' => 'https://images.openfoodfacts.org/images/products/761/303/444/1111/front_en.10.400.jpg',
        ];

        $cosmeticCatalog = [
            'cetaphil gentle skin cleanser' => 'https://images.openbeautyfacts.org/images/products/349/932/000/7389/front_en.5.400.jpg',
            'pixy white aqua' => 'https://images.openbeautyfacts.org/images/products/899/990/903/0192/front_id.4.400.jpg',
            'the ordinary niacinamide' => 'https://images.openbeautyfacts.org/images/products/076/692/215/7323/front_en.6.400.jpg',
            'safi white natural' => 'https://images.openbeautyfacts.org/images/products/955/600/125/4379/front_en.3.400.jpg',
            'vaseline healthy white' => 'https://images.openbeautyfacts.org/images/products/899/999/905/1948/front_id.4.400.jpg',
            'wardah uv shield' => 'https://images.openbeautyfacts.org/images/products/899/313/769/1515/front_id.5.400.jpg',
            'emina bright stuff' => 'https://images.openbeautyfacts.org/images/products/899/313/769/1492/front_id.4.400.jpg',
            'somethinc niacinamide' => 'https://images.openbeautyfacts.org/images/products/899/313/769/4103/front_id.4.400.jpg',
            'skintific 5x ceramide' => 'https://images.openbeautyfacts.org/images/products/899/999/905/2433/front_id.4.400.jpg',
            'nivea soft' => 'https://images.openbeautyfacts.org/images/products/400/590/012/7777/front_en.11.400.jpg',
            'garnier micellar' => 'https://images.openbeautyfacts.org/images/products/360/054/159/5012/front_en.22.400.jpg',
            'cream pemutih xl' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/6/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
        ];

        $medicineCatalog = [
            'bodrex extra' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/7/3/2a5ebbf1-3a4f-429e-b3b4-40fb3e0cf7ec.jpg',
            'bodrex migra' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/2/20/234403d8-fbe4-418c-a5e0-66d3de7f6cd4.jpg',
            'bodrex flu' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/6/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'paracetamol' => 'https://static2.helosehat.com/wp-content/uploads/2016/11/paracetamol.jpg',
            'amoxicillin' => 'https://d2qjkwm11akmwu.cloudfront.net/products/126986_19-3-2019_10-33-3.jpg',
            'ibuprofen' => 'https://upload.wikimedia.org/wikipedia/commons/5/5f/Ibuprofen_200mg_tablets.jpg',
            'cetirizine' => 'https://upload.wikimedia.org/wikipedia/commons/6/6a/Cetirizine_tablets.jpg',
            'omeprazole' => 'https://upload.wikimedia.org/wikipedia/commons/3/3f/Omeprazole_capsules.jpg',
            'metformin' => 'https://upload.wikimedia.org/wikipedia/commons/1/1c/Metformin_500mg_tablets.jpg',
            'promag' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/4/22/0c79f97d-6c17-48f8-b364-7936a7edc8e6.jpg',
            'obh combi' => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//94/MTA-3801452/obh-combi_obh-combi-batuk---flu-menthol-obat-kesehatan--100-ml-_full02.jpg',
            'panadol extra' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2020/10/29/7a94e54f-3133-4fd1-8338-2d2e10f5e8dc.jpg',
            'panadol anak' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/10/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'betadine' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2020/10/29/1f2cf1a8-5b0c-4e2f-8d93-1bf6a0e39a5b.jpg',
            'stimuno' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/1/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'curcuma' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/2/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'vick' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/3/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'insto' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/4/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'rohto' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/5/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'diapet' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/6/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'entrostop' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/7/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'mylanta' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/8/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'polysilane' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/9/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'sanmol' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/10/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'biogesic' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/11/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'tempra' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2022/12/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'panadol' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/1/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'bejo' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/2/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'bintang toedjoe' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/3/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'neurobion' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/4/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'sangobion' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/5/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'hemaviton' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/6/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'fatigon' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/7/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'CDR' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/8/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'redoxon' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/9/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'vitacimin' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/10/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'vicee' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/11/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'hansaplast' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/10/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'counterpain' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/12/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
        ];

        $streetFoodCatalog = [
            'nasi goreng' => 'https://upload.wikimedia.org/wikipedia/commons/9/9a/Nasi_goreng_served_on_plate.jpg',
            'mie goreng' => 'https://upload.wikimedia.org/wikipedia/commons/8/8f/Mie_goreng.JPG',
            'mie rebus' => 'https://upload.wikimedia.org/wikipedia/commons/2/20/Mie_rebus.JPG',
            'martabak manis' => 'https://upload.wikimedia.org/wikipedia/commons/1/1f/Martabak_manis.jpg',
            'martabak telur' => 'https://upload.wikimedia.org/wikipedia/commons/1/1d/Martabak_telur.jpg',
            'lontong sayur' => 'https://upload.wikimedia.org/wikipedia/commons/5/57/Lontong_sayur.jpg',
            'pecel' => 'https://upload.wikimedia.org/wikipedia/commons/6/67/Pecel.jpg',
            'soto ayam' => 'https://upload.wikimedia.org/wikipedia/commons/7/73/Soto_ayam.jpg',
            'bakso' => 'https://upload.wikimedia.org/wikipedia/commons/6/6f/Bakso_malang.jpg',
            'gado-gado' => 'https://upload.wikimedia.org/wikipedia/commons/6/6f/Gado-gado.jpg',
            'nasi uduk' => 'https://upload.wikimedia.org/wikipedia/commons/4/4a/Nasi_uduk.jpg',
            'ketoprak' => 'https://upload.wikimedia.org/wikipedia/commons/3/3c/Ketoprak_Jakarta.jpg',
        ];

        $ingredientCatalog = [
            'gelatin' => 'https://upload.wikimedia.org/wikipedia/commons/8/88/Gelatine_powder.jpg',
            'carmine' => 'https://upload.wikimedia.org/wikipedia/commons/6/6f/Cochineal_insects.jpg',
            'msg' => 'https://upload.wikimedia.org/wikipedia/commons/5/5d/MSG_powder.jpg',
            'carrageenan' => 'https://upload.wikimedia.org/wikipedia/commons/7/7a/Seaweed_for_carrageenan.jpg',
            'aspartame' => 'https://upload.wikimedia.org/wikipedia/commons/3/3c/Aspartame_powder.jpg',
            'sodium nitrite' => 'https://upload.wikimedia.org/wikipedia/commons/1/12/Sodium_nitrite.jpg',
            'sodium nitrate' => 'https://upload.wikimedia.org/wikipedia/commons/2/2c/Sodium_nitrate.jpg',
            'l-carnitine' => 'https://upload.wikimedia.org/wikipedia/commons/1/1d/L-carnitine_powder.jpg',
            'potassium sorbate' => 'https://upload.wikimedia.org/wikipedia/commons/8/8e/Potassium_sorbate.jpg',
            'titanium dioxide' => 'https://upload.wikimedia.org/wikipedia/commons/5/5f/Titanium_dioxide_powder.jpg',
            'polysorbate' => 'https://upload.wikimedia.org/wikipedia/commons/2/2a/Polysorbate80.jpg',
        ];

        $bannerCatalog = [
            'promo ramadan' => 'https://images.pexels.com/photos/337901/pexels-photo-337901.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'ramadhan' => 'https://images.pexels.com/photos/337901/pexels-photo-337901.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'halal lifestyle' => 'https://images.pexels.com/photos/8164391/pexels-photo-8164391.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'health check' => 'https://images.pexels.com/photos/4056723/pexels-photo-4056723.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'nutrition guide' => 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'bpom' => 'https://images.pexels.com/photos/5910952/pexels-photo-5910952.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'health' => 'https://images.pexels.com/photos/1153369/pexels-photo-1153369.jpeg?auto=compress&cs=tinysrgb&w=1200',
            'medical' => 'https://images.pexels.com/photos/3683074/pexels-photo-3683074.jpeg?auto=compress&cs=tinysrgb&w=1200',
        ];

        $articleCatalog = [
            'halal' => 'https://images.pexels.com/photos/8164391/pexels-photo-8164391.jpeg?auto=compress&cs=tinysrgb&w=800',
            'health' => 'https://images.pexels.com/photos/1153369/pexels-photo-1153369.jpeg?auto=compress&cs=tinysrgb&w=800',
            'nutrition' => 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg?auto=compress&cs=tinysrgb&w=800',
            'medicine' => 'https://images.pexels.com/photos/3683074/pexels-photo-3683074.jpeg?auto=compress&cs=tinysrgb&w=800',
            'beauty' => 'https://images.pexels.com/photos/3762875/pexels-photo-3762875.jpeg?auto=compress&cs=tinysrgb&w=800',
            'tips' => 'https://images.pexels.com/photos/4056723/pexels-photo-4056723.jpeg?auto=compress&cs=tinysrgb&w=800',
            'news' => 'https://images.pexels.com/photos/3944454/pexels-photo-3944454.jpeg?auto=compress&cs=tinysrgb&w=800',
            'bpom' => 'https://images.pexels.com/photos/5910952/pexels-photo-5910952.jpeg?auto=compress&cs=tinysrgb&w=800',
        ];

        return match ($type) {
            'cosmetic', 'beauty' => $cosmeticCatalog,
            'medicine' => $medicineCatalog,
            'street_food' => $streetFoodCatalog,
            'ingredient' => $ingredientCatalog,
            'banner' => $bannerCatalog,
            'article' => $articleCatalog,
            'product', 'food', 'bpom' => array_merge($commonFoodCatalog, $productCatalog),
            default => $productCatalog,
        };
    }

    private function getBarcodeCatalog(): array
    {
        return [
            '0498703513141' => 'https://images.openfoodfacts.org/images/products/498/703/508/9315/front_en.10.400.jpg', // Pocari 500
            '4987035131414' => 'https://images.openfoodfacts.org/images/products/498/703/508/9315/front_en.10.400.jpg', // Pocari 500 (EAN)
            '8991001001851' => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//96/MTA-2775604/silverqueen_silverqueen-cashew-65-gr_full02.jpg', // SilverQueen Cashew
            '8991001001868' => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//96/MTA-2775604/silverqueen_silverqueen-cashew-65-gr_full02.jpg', // SilverQueen Nut
            '8996001300077' => 'https://images.openfoodfacts.org/images/products/899/600/130/0077/front_id.6.400.jpg', // Sari Gandum Sandwich
            '8996001302347' => 'https://images.openfoodfacts.org/images/products/899/600/130/2347/front_id.15.400.jpg', // Sari Gandum 240g
            '089686010377' => 'https://images.openfoodfacts.org/images/products/899/100/400/0004/front_id.3.400.jpg', // Kecap ABC
            '8992741911110' => 'https://images.openfoodfacts.org/images/products/899/274/191/1110/front_id.26.400.jpg', // Pop Mie
            '8993137694103' => 'https://images.openbeautyfacts.org/images/products/899/313/769/4103/front_id.4.400.jpg', // Somethinc Niacinamide
            '6941571400262' => 'https://images.openbeautyfacts.org/images/products/899/999/905/2433/front_id.4.400.jpg', // Skintific 5X
            '0028400028905' => 'https://images.openfoodfacts.org/images/products/002/840/003/1116/front_en.12.400.jpg', // Lays
            '0038000844492' => 'https://images.openfoodfacts.org/images/products/003/800/084/4492/front_en.6.400.jpg', // Pringles
            '089686040029' => 'https://images.openfoodfacts.org/images/products/899/600/130/0350/front_id.10.400.jpg', // Tango Wafer
            '8888166328006' => 'https://assets.klikindomaret.com/products/20002621/20002621_1.jpg', // Ultra Milk
        ];
    }
}
