<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHandlerService
{
    /**
     * Get image URL or placeholder
     * 
     * @param string|null $path
     * @param string $type
     * @return string
     */
    public static function getImageUrl($path, $type = 'product')
    {
        if (empty($path)) {
            return self::getPlaceholder($type);
        }

        $managedLocalPath = self::extractManagedLocalPath($path);

        if ($managedLocalPath !== null) {
            return $managedLocalPath;
        }

        // If it's a full URL (external API)
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // If it's from storage
        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        $normalizedPath = '/' . ltrim((string) $path, '/');
        if (str_starts_with($normalizedPath, '/storage/')) {
            $storagePath = ltrim(Str::after($normalizedPath, '/storage/'), '/');
            if (Storage::disk('public')->exists($storagePath)) {
                return '/storage/' . $storagePath;
            }
        }
        if (str_starts_with($normalizedPath, '/images/') && file_exists(public_path(ltrim($normalizedPath, '/')))) {
            return $normalizedPath;
        }

        return self::getPlaceholder($type);
    }

    /**
     * Get placeholder based on type
     * 
     * @param string $type
     * @return string
     */
    public static function getPlaceholder($type)
    {
        $placeholders = [
            'product'    => '/images/placeholders/product-placeholder.svg',
            'medicine'   => '/images/placeholders/medicine-placeholder.svg',
            'cosmetic'   => '/images/placeholders/cosmetic-placeholder.svg',
            'food'       => '/images/placeholders/food-placeholder.svg',
            'ingredient' => '/images/placeholders/ingredient-placeholder.svg',
            'article'    => '/images/placeholders/article-placeholder.svg',
            'banner'     => '/images/placeholders/banner-placeholder.svg',
            'user'       => '/images/default/general.svg',
            'blood'      => '/images/default/general.svg',
        ];

        return $placeholders[$type] ?? '/images/default/general.svg';
    }

    /**
     * Validate if an external image exists
     * 
     * @param string $url
     * @return bool
     */
    public static function validateImageExists($url)
    {
        try {
            $response = Http::timeout(2)->head($url);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Optimize image before saving (placeholder for logic)
     */
    public static function resizeAndOptimize($file)
    {
        // Integration with Intervention Image could go here
        return $file;
    }

    private static function extractManagedLocalPath(?string $value): ?string
    {
        if (blank($value) || !filter_var($value, FILTER_VALIDATE_URL)) {
            return null;
        }

        $host = Str::lower((string) parse_url($value, PHP_URL_HOST));
        $path = '/' . ltrim((string) parse_url($value, PHP_URL_PATH), '/');
        $appHost = Str::lower((string) parse_url((string) config('app.url'), PHP_URL_HOST));

        if (!in_array($host, array_filter([$appHost, 'localhost', '127.0.0.1']), true)) {
            return null;
        }

        if (str_starts_with($path, '/storage/') || str_starts_with($path, '/images/')) {
            return $path;
        }

        return null;
    }
}
