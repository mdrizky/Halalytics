<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use App\Models\User;
use App\Models\ProductModel;
use App\Models\ScanModel;
use App\Models\ReportModel;
use App\Models\KategoriModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    private $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * 📊 Get comprehensive dashboard statistics
     */
    public function getStats(Request $request)
    {
        // 🚀 Use cached dashboard statistics
        $stats = $this->cacheService->getDashboardStats();
        $days = $this->parsePeriodInput($request->get('period', 30));
        
        [$labels, $data] = $this->buildScanChartData($days);

        // Halal distribution (cached separately)
        $cacheKey = "halal_distribution";
        $halalStatus = Cache::remember($cacheKey, CacheService::DEFAULT_TTL, function () {
            return ProductModel::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');
        });

        return response()->json([
            'success' => true,
            'stats' => [
                'totalKategori' => KategoriModel::count(),
                'totalUsers' => $stats['total_users'],
                'totalProduk' => $stats['total_products'],
                'totalScan' => ScanModel::where('tanggal_scan', '>=', Carbon::now()->subDays($days))->count(),
                'scanToday' => $stats['scan_today'],
                'laporanMasuk' => $stats['pending_reports'],
            ],
            'chart' => [
                'labels' => $labels,
                'data' => $data
            ],
            'distribution' => [
                'halal' => $halalStatus['halal'] ?? 0,
                'diragukan' => $halalStatus['diragukan'] ?? 0,
                'haram' => $halalStatus['tidak halal'] ?? 0,
            ]
        ]);
    }

    /**
     * 📈 Build scan chart data
     */
    private function buildScanChartData(int $days): array
    {
        $cacheKey = "scan_chart_data:{$days}";
        
        return Cache::remember($cacheKey, CacheService::SHORT_TTL, function () use ($days) {
            $data = ScanModel::select(
                    DB::raw('DATE(tanggal_scan) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('tanggal_scan', '>=', Carbon::now()->subDays($days))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $labels = [];
            $counts = [];

            // Fill missing dates with 0
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $labels[] = Carbon::now()->subDays($i)->format('M j');
                
                $count = $data->where('date', $date)->first();
                $counts[] = $count ? $count->count : 0;
            }

            return [$labels, $counts];
        });
    }

    /**
     * 🗄️ Clear cache endpoint
     */
    public function clearCache(Request $request)
    {
        $pattern = $request->get('pattern');
        $cleared = $this->cacheService->clearCache($pattern);
        
        return response()->json([
            'success' => true,
            'message' => "Cleared {$cleared} cache entries",
            'cleared_count' => $cleared,
        ]);
    }

    /**
     * 📊 Get cache statistics
     */
    public function getCacheStats()
    {
        return response()->json([
            'success' => true,
            'data' => $this->cacheService->getCacheStats(),
        ]);
    }

    /**
     * 🔄 Warm up cache
     */
    public function warmUpCache()
    {
        $results = $this->cacheService->warmUpCache();
        
        return response()->json([
            'success' => true,
            'message' => 'Cache warmed up successfully',
            'data' => array_keys($results),
        ]);
    }

    /**
     * 🔧 Parse period input
     */
    private function parsePeriodInput($period): int
    {
        $periodMap = [
            '7' => 7,
            '30' => 30,
            '90' => 90,
            '365' => 365,
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            'year' => 365,
        ];

        return $periodMap[$period] ?? 30;
    }
}
