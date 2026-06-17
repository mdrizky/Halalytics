<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CacheService
{
    private const DEFAULT_TTL = 3600; // 1 hour
    private const LONG_TTL = 86400; // 24 hours
    private const SHORT_TTL = 300; // 5 minutes

    /**
     * 🗄️ Cache user statistics
     */
    public function getUserStats(int $userId): array
    {
        $cacheKey = "user_stats:{$userId}";
        
        return Cache::remember($cacheKey, self::SHORT_TTL, function () use ($userId) {
            $user = \App\Models\User::withCount(['scans', 'favorites', 'reports'])
                ->find($userId);

            if (!$user) {
                return [];
            }

            return [
                'total_scans' => $user->scans_count,
                'total_favorites' => $user->favorites_count,
                'total_reports' => $user->reports_count,
                'last_scan' => $user->scans()->latest()->first()?->created_at,
                'scan_streak' => $this->calculateScanStreak($userId),
                'points' => $user->onboarding_points ?? 0,
                'level' => $user->onboarding_level ?? 'Newcomer',
            ];
        });
    }

    /**
     * 📊 Cache dashboard statistics
     */
    public function getDashboardStats(): array
    {
        $cacheKey = 'dashboard_stats';
        
        return Cache::remember($cacheKey, self::SHORT_TTL, function () {
            // Overall Stats
            $stats = [
                'total_users' => \App\Models\User::count(),
                'total_products' => \App\Models\ProductModel::count(),
                'total_scans' => \App\Models\ScanModel::count(),
                'scan_today' => \App\Models\ScanModel::whereDate('tanggal_scan', Carbon::today())->count(),
                'scan_this_week' => \App\Models\ScanModel::whereBetween('tanggal_scan', [
                    Carbon::now()->startOfWeek(), 
                    Carbon::now()->endOfWeek()
                ])->count(),
                'pending_reports' => \App\Models\ReportModel::where('status', 'pending')->count(),
                'ocr_pending' => \App\Models\OCRProduct::where('status', 'pending_admin_review')->count(),
                'active_users_today' => \App\Models\User::whereDate('last_login', Carbon::today())->count(),
                'new_users_today' => \App\Models\User::whereDate('created_at', Carbon::today())->count(),
            ];

            // User Growth (Last 30 Days)
            $stats['user_growth'] = \App\Models\User::select([
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                ])
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->toArray();

            // Scan Activity (Last 14 Days)
            $stats['scan_activity'] = \App\Models\ScanModel::select([
                    DB::raw('DATE(tanggal_scan) as date'),
                    DB::raw("SUM(CASE WHEN status_halal = 'halal' THEN 1 ELSE 0 END) as halal"),
                    DB::raw("SUM(CASE WHEN status_halal = 'syubhat' THEN 1 ELSE 0 END) as syubhat"),
                    DB::raw("SUM(CASE WHEN status_halal = 'haram' THEN 1 ELSE 0 END) as haram")
                ])
                ->where('tanggal_scan', '>=', Carbon::now()->subDays(14))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->toArray();

            // Halal Stats Detailed (Overall)
            $stats['halal_count'] = \App\Models\ScanModel::where('status_halal', 'halal')->count();
            $stats['haram_count'] = \App\Models\ScanModel::where('status_halal', 'haram')->count();
            $stats['syubhat_count'] = \App\Models\ScanModel::where('status_halal', 'syubhat')->count();

            return $stats;
        });
    }

    /**
     * 🔍 Cache product search results
     */
    public function getProductSearch(string $query, int $page = 1): array
    {
        $cacheKey = "product_search:" . md5($query) . ":page:{$page}";
        
        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($query, $page) {
            $products = \App\Models\ProductModel::with('kategori')
                ->where(function ($q) use ($query) {
                    $q->where('nama_product', 'LIKE', "%{$query}%")
                        ->orWhere('barcode', 'LIKE', "%{$query}%");
                })
                ->paginate(20, ['*'], 'page', $page);

            return [
                'data' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ];
        });
    }

    /**
     * 🏷️ Cache categories
     */
    public function getCategories(): array
    {
        $cacheKey = 'categories_all';
        
        return Cache::remember($cacheKey, self::LONG_TTL, function () {
            return \App\Models\KategoriModel::withCount('products')
                ->orderBy('nama_kategori')
                ->get()
                ->toArray();
        });
    }

    /**
     * 🌟 Cache popular products
     */
    public function getPopularProducts(int $limit = 10): array
    {
        $cacheKey = "popular_products:{$limit}";
        
        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($limit) {
            return \App\Models\ScanModel::select('nama_produk', 'barcode', 'status_halal')
                ->selectRaw('COUNT(*) as scan_count')
                ->whereNotNull('nama_produk')
                ->groupBy('nama_produk', 'barcode', 'status_halal')
                ->orderByDesc('scan_count')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * 📈 Cache top scanned products with details
     */
    public function getTopScannedProducts(int $limit = 5): array
    {
        $cacheKey = "top_scanned:{$limit}";
        
        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($limit) {
            $topScanBase = \App\Models\ScanModel::query()
                ->select('nama_produk', 'barcode', 'status_halal')
                ->selectRaw('COUNT(*) as scan_count')
                ->whereNotNull('nama_produk')
                ->groupBy('nama_produk', 'barcode', 'status_halal')
                ->orderByDesc('scan_count')
                ->limit($limit)
                ->get();

            $barcodes = $topScanBase->pluck('barcode')->filter()->values();
            
            $products = \App\Models\ProductModel::with('kategori')
                ->whereIn('barcode', $barcodes)
                ->get()
                ->keyBy('barcode');

            return $topScanBase->map(function ($row) use ($products) {
                $product = $products->get($row->barcode);
                
                return [
                    'product_name' => $row->nama_produk,
                    'barcode' => $row->barcode,
                    'halal_status' => $product->status ?? $row->status_halal,
                    'category_name' => optional($product?->kategori)->nama_kategori ?? 'Uncategorized',
                    'scan_count' => (int) $row->scan_count,
                    'image' => $product?->image ?? null,
                ];
            })->toArray();
        });
    }

    /**
     * 🔥 Cache forbidden ingredients
     */
    public function getForbiddenIngredients(): array
    {
        $cacheKey = 'forbidden_ingredients';
        
        return Cache::remember($cacheKey, self::LONG_TTL, function () {
            return \App\Models\ForbiddenIngredient::orderBy('name')
                ->get()
                ->toArray();
        });
    }

    /**
     * 📱 Cache mobile API responses
     */
    public function cacheApiResponse(string $endpoint, array $params = [], $data = null, int $ttl = self::SHORT_TTL)
    {
        $cacheKey = "api_response:" . md5($endpoint . serialize($params));
        
        if ($data !== null) {
            Cache::put($cacheKey, $data, $ttl);
            return true;
        }
        
        return Cache::get($cacheKey);
    }

    /**
     * 🧹 Clear cache for specific keys or patterns
     */
    public function clearCache(string $pattern = null): int
    {
        if ($pattern) {
            // Clear cache by pattern (Redis only)
            if (config('cache.default') === 'redis') {
                $keys = Redis::keys("{$pattern}*");
                if (!empty($keys)) {
                    return Redis::del($keys);
                }
            }
            return 0;
        }
        
        // Clear specific cache keys
        $keys = [
            'dashboard_stats',
            'categories_all',
            'forbidden_ingredients',
            'popular_products:10',
            'top_scanned:5',
        ];
        
        $cleared = 0;
        foreach ($keys as $key) {
            if (Cache::forget($key)) {
                $cleared++;
            }
        }
        
        return $cleared;
    }

    /**
     * 📊 Calculate user scan streak
     */
    private function calculateScanStreak(int $userId): int
    {
        $cacheKey = "scan_streak:{$userId}";
        
        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($userId) {
            $scans = \App\Models\ScanModel::where('user_id', $userId)
                ->orderBy('tanggal_scan', 'desc')
                ->pluck('tanggal_scan')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->unique()
                ->values();

            if ($scans->isEmpty()) {
                return 0;
            }

            $streak = 0;
            $currentDate = Carbon::today();
            
            foreach ($scans as $scanDate) {
                $scanCarbon = Carbon::parse($scanDate);
                
                if ($scanCarbon->eq($currentDate) || $scanCarbon->eq($currentDate->copy()->subDay())) {
                    $streak++;
                    $currentDate = $scanCarbon->copy()->subDay();
                } else {
                    break;
                }
            }
            
            return $streak;
        });
    }

    /**
     * 🔄 Warm up cache
     */
    public function warmUpCache(): array
    {
        $results = [];
        
        try {
            // Warm up dashboard stats
            $results['dashboard_stats'] = $this->getDashboardStats();
            
            // Warm up categories
            $results['categories'] = $this->getCategories();
            
            // Warm up popular products
            $results['popular_products'] = $this->getPopularProducts();
            
            // Warm up forbidden ingredients
            $results['forbidden_ingredients'] = $this->getForbiddenIngredients();
            
        } catch (\Exception $e) {
            Log::error('Cache warm-up failed', ['error' => $e->getMessage()]);
        }
        
        return $results;
    }

    /**
     * 📊 Get cache statistics
     */
    public function getCacheStats(): array
    {
        $stats = [
            'driver' => config('cache.default'),
            'prefix' => config('cache.prefix'),
        ];
        
        if (config('cache.default') === 'redis') {
            try {
                $info = Redis::info();
                $stats['redis'] = [
                    'used_memory' => $info['used_memory_human'] ?? 'Unknown',
                    'connected_clients' => $info['connected_clients'] ?? 'Unknown',
                    'keyspace_hits' => $info['keyspace_hits'] ?? 'Unknown',
                    'keyspace_misses' => $info['keyspace_misses'] ?? 'Unknown',
                ];
            } catch (\Exception $e) {
                $stats['redis']['error'] = $e->getMessage();
            }
        }
        
        return $stats;
    }
}
