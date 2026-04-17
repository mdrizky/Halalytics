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
        $normalized = $this->normalizeInput($value);
        if ($normalized !== null) {
            return $normalized;
        }

        $catalogMatch = $this->matchCatalogImage($context, $type);
        if ($catalogMatch !== null) {
            return $catalogMatch;
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

    private function matchCatalogImage(array $context, string $type): ?string
    {
        $name = Str::lower(trim((string) data_get($context, 'name', '')));
        $brand = Str::lower(trim((string) data_get($context, 'brand', '')));
        $category = Str::lower(trim((string) data_get($context, 'category', '')));
        $haystack = trim($name . ' ' . $brand . ' ' . $category);

        if ($haystack === '') {
            return null;
        }

        $catalog = $this->catalogForType($type, $category);

        foreach ($catalog as $keyword => $url) {
            if (Str::contains($haystack, Str::lower($keyword))) {
                return $this->normalizeInput($url);
            }
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
            'paracetamol' => 'https://static2.helosehat.com/wp-content/uploads/2016/11/paracetamol.jpg',
            'amoxicillin' => 'https://d2qjkwm11akmwu.cloudfront.net/products/126986_19-3-2019_10-33-3.jpg',
            'ibuprofen' => 'https://upload.wikimedia.org/wikipedia/commons/5/5f/Ibuprofen_200mg_tablets.jpg',
            'cetirizine' => 'https://upload.wikimedia.org/wikipedia/commons/6/6a/Cetirizine_tablets.jpg',
            'omeprazole' => 'https://upload.wikimedia.org/wikipedia/commons/3/3f/Omeprazole_capsules.jpg',
            'metformin' => 'https://upload.wikimedia.org/wikipedia/commons/1/1c/Metformin_500mg_tablets.jpg',
            'promag' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/4/22/0c79f97d-6c17-48f8-b364-7936a7edc8e6.jpg',
            'obh combi' => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//94/MTA-3801452/obh-combi_obh-combi-batuk---flu-menthol-obat-kesehatan--100-ml-_full02.jpg',
            'panadol extra' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2020/10/29/7a94e54f-3133-4fd1-8338-2d2e10f5e8dc.jpg',
            'betadine' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2020/10/29/1f2cf1a8-5b0c-4e2f-8d93-1bf6a0e39a5b.jpg',
            'komix' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/2/6/9d2cdb98-0f90-4b56-8cf3-4cb0b0b65d7c.jpg',
            'enervon-c' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/4/22/6e8e5c47-fada-4dfa-91a6-69d7224d8043.jpg',
            'imboost' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/5/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'sangobion' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/6/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'antangin' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/7/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'tolak angin' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/8/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'salonpas' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/9/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'hansaplast' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/10/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
            'betadine' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/11/15/8e5c47f9-fedc-440a-91a6-69d7224d8043.jpg',
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
            'promo ramadan' => asset('images/promo/ss-home-1.jpg'),
            'ramadhan' => asset('images/promo/ss-home-1.jpg'),
            'bpom' => asset('images/promo/ss-home-2.jpg'),
            'health' => asset('images/promo/ss-home-3.jpg'),
        ];

        $articleCatalog = [
            'halal' => asset('images/placeholders/article-placeholder.svg'),
            'bpom' => asset('images/placeholders/article-placeholder.svg'),
        ];

        return match ($type) {
            'cosmetic', 'beauty' => $cosmeticCatalog,
            'medicine' => $medicineCatalog,
            'street_food' => $streetFoodCatalog,
            'ingredient' => $ingredientCatalog,
            'banner' => $bannerCatalog,
            'article' => $articleCatalog,
            default => $productCatalog,
        };
    }
}
