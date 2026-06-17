<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetCacheHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->method() === 'GET') {
            $cacheTime = $this->getCacheTime($request->path());
            
            if ($cacheTime === 0) {
                $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
                $response->header('Pragma', 'no-cache');
                $response->header('Expires', '0');
            } elseif ($cacheTime > 0) {
                $response->header('Cache-Control', 'public, max-age=' . $cacheTime);
                $response->header('Expires', gmdate('D, d M Y H:i:s T', time() + $cacheTime));
            }
        }

        return $response;
    }

    private function getCacheTime(string $path): int
    {
        $sensitivePatterns = [
            'api/nutrition/consultations',
            'api/nutritionist/dashboard',
            'api/user/profile',
            'api/health',
            'api/medical-records',
            'api/family',
            'api/admin',
        ];

        foreach ($sensitivePatterns as $pattern) {
            if (strpos($path, $pattern) !== false) {
                return 0;
            }
        }

        $shortCachePatterns = [
            'api/user/metrics',
            'api/health/daily',
            'api/nutrition/daily',
        ];

        foreach ($shortCachePatterns as $pattern) {
            if (strpos($path, $pattern) !== false) {
                return 60;
            }
        }

        $productPatterns = [
            'api/products',
            'api/scan',
            'api/halal',
        ];

        foreach ($productPatterns as $pattern) {
            if (strpos($path, $pattern) !== false) {
                return 300;
            }
        }

        return 60;
    }
}
