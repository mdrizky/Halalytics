<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAnalysisResult;
use App\Models\ScanModel;
use App\Models\User;
use App\Models\ProductModel;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    private $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function getStats(Request $request): JsonResponse
    {
        $cachedStats = $this->cacheService->getDashboardStats();

        $recentScans = ScanModel::with(['user', 'product'])
            ->orderByDesc('tanggal_scan')
            ->limit(5)
            ->get()
            ->map(function ($scan) {
                return [
                    'id' => $scan->id,
                    'product_name' => $scan->nama_produk ?? $scan->product?->nama_product ?? 'Unknown',
                    'barcode' => $scan->barcode,
                    'status_halal' => $scan->product?->status ?? $scan->status_halal ?? 'unknown',
                    'created_at' => $scan->tanggal_scan->toIso8601String(),
                    'user_name' => $scan->user?->username ?? 'Anonymous',
                ];
            });

        $topProducts = collect($this->cacheService->getTopScannedProducts(5))
            ->map(function ($product, $index) {
                return [
                    'index' => $index,
                    'product_name' => $product['product_name'],
                    'barcode' => $product['barcode'],
                    'scan_count' => $product['scan_count'],
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $cachedStats['total_users'] ?? 0,
                'total_products' => ProductModel::count(),
                'total_scans' => $cachedStats['total_scans'] ?? 0,
                'online_users' => $this->getOnlineUsers(),
                'recent_scans' => $recentScans->toArray(),
                'top_products' => $topProducts->toArray(),
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function clearCache(Request $request): JsonResponse
    {
        Cache::flush();
        return response()->json(['success' => true, 'message' => 'Cache cleared successfully']);
    }

    public function getCacheStats(Request $request): JsonResponse
    {
        try {
            $redis = Cache::getStore()->getRedis();
            $info = $redis->info('stats');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'hits' => $info['hits'] ?? 0,
                    'misses' => $info['misses'] ?? 0,
                    'evictions' => $info['evicted_keys'] ?? 0,
                    'memory_used' => $info['used_memory_human'] ?? 'N/A',
                    'connected_clients' => $info['connected_clients'] ?? 0,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch cache stats: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function warmUpCache(Request $request): JsonResponse
    {
        try {
            $this->cacheService->getDashboardStats();
            $this->cacheService->getTopScannedProducts(10);
            
            return response()->json(['success' => true, 'message' => 'Cache warmed up successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache warm-up failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function analysisResults()
    {
        $results = ProductAnalysisResult::with(['product', 'user', 'expert'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => ProductAnalysisResult::count(),
            'verified' => ProductAnalysisResult::where('is_verified_by_expert', true)->count(),
            'pending' => ProductAnalysisResult::where('is_verified_by_expert', false)->count(),
            'halal_count' => ProductAnalysisResult::where('halal_verdict', 'HALAL')->count(),
            'haram_count' => ProductAnalysisResult::where('halal_verdict', 'HARAM')->count(),
            'syubhat_count' => ProductAnalysisResult::where('halal_verdict', 'SYUBHAT')->count(),
        ];

        return view('admin.analysis', compact('results', 'stats'));
    }

    private function getOnlineUsers(): int
    {
        return User::where('last_activity_at', '>=', Carbon::now()->subMinutes(15))->count();
    }
}
