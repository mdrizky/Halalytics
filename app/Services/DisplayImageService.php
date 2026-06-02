<?php

namespace App\Services;

use Illuminate\Support\Str;

class DisplayImageService
{
    public function __construct(
        protected ProductImageService $productImageService
    ) {
    }

    public function resolve(?string $value, array $context = [], string $type = 'product'): string
    {
        if (!blank($value)) {
            $managedLocalPath = $this->extractManagedLocalPath($value);

            if ($managedLocalPath !== null && file_exists($this->toFilesystemPath($managedLocalPath))) {
                return $managedLocalPath;
            }

            if ($this->isAbsoluteUrl($value)) {
                return $value;
            }

            $normalizedPath = $this->normalizeLocalPath($value);
            if ($normalizedPath !== null && file_exists($this->toFilesystemPath($normalizedPath))) {
                return $normalizedPath;
            }
        }

        $name = $context['name'] ?? null;

        return $this->productImageService->fallbackUrl($context['category'] ?? null, $type, $name);
    }

    private function normalizeLocalPath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = '/' . ltrim($path, '/');

        return match (true) {
            str_starts_with($path, '/public/') => '/storage/' . ltrim(substr($path, 8), '/'),
            str_starts_with($path, '/storage/'),
            str_starts_with($path, '/images/') => $path,
            default => $path,
        };
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

    private function toFilesystemPath(string $path): string
    {
        if (str_starts_with($path, '/storage/')) {
            return storage_path('app/public/' . ltrim(substr($path, 9), '/'));
        }

        return public_path(ltrim($path, '/'));
    }

    private function isAbsoluteUrl(string $value): bool
    {
        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }
}
