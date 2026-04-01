<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\HealthTracking;
use App\Models\NotificationCampaign;
use App\Models\ProductModel;
use App\Models\ScanHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $data = [
            'overview' => $this->getOverview(),
            'user_growth' => $this->getUserGrowth(),
            'scan_activity' => $this->getScanActivity(),
            'halal_stats' => $this->getHalalStats(),
            'top_products' => $this->getTopScannedProducts(),
            'health_trends' => $this->getHealthTrends(),
        ];

        return view('admin.analytics.index', compact('data'));
    }

    public function users()
    {
        $registrations = collect($this->getUserGrowth());

        $totalUsers = User::count();
        $activeToday = ScanHistory::whereDate('created_at', today())
            ->distinct('user_id')
            ->count('user_id');
        $activeWeek = ScanHistory::where('created_at', '>=', now()->subWeek())
            ->distinct('user_id')
            ->count('user_id');
        $activeMonth = ScanHistory::where('created_at', '>=', now()->subMonth())
            ->distinct('user_id')
            ->count('user_id');

        return view('admin.analytics.users', compact(
            'registrations',
            'totalUsers',
            'activeToday',
            'activeWeek',
            'activeMonth'
        ));
    }

    public function products()
    {
        $topScanned = collect($this->getTopScannedProducts())->map(function ($row) {
            return (object) [
                'nama_produk' => $row['product_name'],
                'scan_count' => $row['scan_count'],
            ];
        });

        $statusDistribution = ProductModel::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->orderByDesc('count')
            ->get()
            ->map(function ($row) {
                $row->status_halal = $row->status ?: 'unknown';
                return $row;
            });

        $categoryDistribution = DB::table('products')
            ->leftJoin('kategori', 'products.kategori_id', '=', 'kategori.id_kategori')
            ->select(
                DB::raw("COALESCE(kategori.nama_kategori, 'Tanpa Kategori') as name"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $totalProducts = ProductModel::count();

        return view('admin.analytics.products', compact(
            'topScanned',
            'statusDistribution',
            'categoryDistribution',
            'totalProducts'
        ));
    }

    public function ai()
    {
        $dailyUsage = AiUsageLog::selectRaw('DATE(created_at) as date, COUNT(*) as count, AVG(response_time_ms) as avg_response_time')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $statusBreakdown = AiUsageLog::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $featureUsage = AiUsageLog::selectRaw('feature, COUNT(*) as count')
            ->groupBy('feature')
            ->orderByDesc('count')
            ->get();

        $totalRequests = AiUsageLog::count();
        $errorRate = $totalRequests > 0
            ? (AiUsageLog::where('status', 'error')->count() / $totalRequests) * 100
            : 0;
        $avgResponseTime = AiUsageLog::where('status', 'success')->avg('response_time_ms') ?? 0;

        return view('admin.analytics.ai', compact(
            'dailyUsage',
            'statusBreakdown',
            'featureUsage',
            'totalRequests',
            'errorRate',
            'avgResponseTime'
        ));
    }

    public function growth()
    {
        $dau = ScanHistory::selectRaw('DATE(created_at) as date, COUNT(DISTINCT user_id) as users')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $scansPerDay = ScanHistory::selectRaw('DATE(created_at) as date, COUNT(*) as scans')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $contributionsPerWeek = DB::table('contributions')
            ->selectRaw('YEARWEEK(created_at) as week, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(3))
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        return view('admin.analytics.growth', compact('dau', 'scansPerDay', 'contributionsPerWeek'));
    }

    public function export($type)
    {
        $data = match ($type) {
            'users' => User::select('id_user', 'username', 'full_name', 'email', 'created_at')->get(),
            'scans' => ScanHistory::limit(10000)->get(),
            'ai' => AiUsageLog::limit(10000)->get(),
            'campaigns' => NotificationCampaign::limit(10000)->get(),
            default => collect(),
        };

        $filename = 'halalytics_' . $type . '_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            if ($data->isNotEmpty()) {
                fputcsv($file, array_keys($data->first()->toArray()));

                foreach ($data as $row) {
                    fputcsv($file, $row->toArray());
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getOverview(): array
    {
        $totalUsers = User::count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $newUsersThisWeek = User::whereBetween('created_at', [now()->startOfWeek(), now()])->count();
        $usersLastWeek = User::whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();
        $growthRate = $usersLastWeek > 0
            ? round((($newUsersThisWeek - $usersLastWeek) / $usersLastWeek) * 100, 1)
            : ($newUsersThisWeek > 0 ? 100.0 : 0.0);

        $totalScans = ScanHistory::count();
        $scansToday = ScanHistory::whereDate('created_at', today())->count();
        $activeUsersToday = ScanHistory::whereDate('created_at', today())
            ->distinct('user_id')
            ->count('user_id');

        return [
            'total_users' => $totalUsers,
            'new_users_today' => $newUsersToday,
            'new_users_week' => $newUsersThisWeek,
            'growth_rate' => $growthRate,
            'total_scans' => $totalScans,
            'scans_today' => $scansToday,
            'active_users_today' => $activeUsersToday,
            'campaigns_sent' => NotificationCampaign::where('status', 'sent')->count(),
        ];
    }

    private function getUserGrowth(): array
    {
        return User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($row) {
                return [
                    'date' => $row->date,
                    'count' => (int) $row->count,
                ];
            })
            ->toArray();
    }

    private function getScanActivity(): array
    {
        return ScanHistory::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN halal_status = 'halal' THEN 1 ELSE 0 END) as halal"),
            DB::raw("SUM(CASE WHEN halal_status = 'haram' THEN 1 ELSE 0 END) as haram"),
            DB::raw("SUM(CASE WHEN halal_status = 'syubhat' THEN 1 ELSE 0 END) as syubhat")
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($row) {
                return [
                    'date' => $row->date,
                    'total' => (int) $row->total,
                    'halal' => (int) $row->halal,
                    'haram' => (int) $row->haram,
                    'syubhat' => (int) $row->syubhat,
                ];
            })
            ->toArray();
    }

    private function getHalalStats(): array
    {
        $total = ScanHistory::count();

        if ($total === 0) {
            return [
                'halal' => 0,
                'haram' => 0,
                'syubhat' => 0,
            ];
        }

        $stats = ScanHistory::select('halal_status', DB::raw('COUNT(*) as count'))
            ->groupBy('halal_status')
            ->pluck('count', 'halal_status')
            ->toArray();

        return [
            'halal' => round((($stats['halal'] ?? 0) / $total) * 100, 1),
            'haram' => round((($stats['haram'] ?? 0) / $total) * 100, 1),
            'syubhat' => round((($stats['syubhat'] ?? 0) / $total) * 100, 1),
        ];
    }

    private function getTopScannedProducts(): array
    {
        return ScanHistory::select(
            'product_name',
            'barcode',
            'halal_status',
            DB::raw('COUNT(*) as scan_count')
        )
            ->groupBy('product_name', 'barcode', 'halal_status')
            ->orderByDesc('scan_count')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                return [
                    'product_name' => $row->product_name,
                    'barcode' => $row->barcode,
                    'halal_status' => $row->halal_status,
                    'scan_count' => (int) $row->scan_count,
                ];
            })
            ->toArray();
    }

    private function getHealthTrends(): array
    {
        return HealthTracking::select(
            'metric_type',
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('metric_type')
            ->orderByDesc('count')
            ->get()
            ->map(function ($row) {
                return [
                    'metric_type' => $row->metric_type,
                    'count' => (int) $row->count,
                ];
            })
            ->toArray();
    }
}
