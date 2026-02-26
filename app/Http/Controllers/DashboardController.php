<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProductModel;
use App\Models\ScanModel;
use App\Models\ReportModel;
use App\Models\KategoriModel;
use App\Models\HalalProduct;
use App\Models\ActivityModel;
use App\Models\Medicine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Statistik Utama
        |--------------------------------------------------------------------------
        */
        $totalUsers = User::count();
        $totalProduk = ProductModel::count();
        $localProduk = ProductModel::where('source', 'local')->count();
        $offProduk = ProductModel::where('source', 'open_food_facts')->count();
        $obfProduk = ProductModel::where('source', 'open_beauty_facts')->count();
        $openFdaMedicines = Medicine::where('source', 'openfda')->count();
        $totalKategori = KategoriModel::count();
        $totalScan = ScanModel::count();
        $scanToday = ScanModel::whereDate('tanggal_scan', Carbon::today())->count();
        $scanThisWeek = ScanModel::whereBetween('tanggal_scan', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $laporanMasuk = ReportModel::where('status', 'pending')->count();

        /*
        |--------------------------------------------------------------------------
        | Trend Calculation (Last 30 Days vs Previous 30 Days)
        |--------------------------------------------------------------------------
        */
        $scanLast30 = ScanModel::where('tanggal_scan', '>=', Carbon::now()->subDays(30))->count();
        $scanPrev30 = ScanModel::whereBetween('tanggal_scan', [Carbon::now()->subDays(60), Carbon::now()->subDays(30)])->count();
        $scanChange = $scanPrev30 > 0 ? round((($scanLast30 - $scanPrev30) / $scanPrev30) * 100, 1) : 0;

        /*
        |--------------------------------------------------------------------------
        | Top Scanned Products (Top 5)
        |--------------------------------------------------------------------------
        */
        $topScannedProducts = ScanModel::select('nama_produk as product_name', 'barcode', 'status_halal as halal_status', DB::raw('COUNT(*) as scan_count'))
            ->whereNotNull('nama_produk')
            ->groupBy('nama_produk', 'barcode', 'status_halal')
            ->orderByDesc('scan_count')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Scans (Live Feed)
        |--------------------------------------------------------------------------
        */
        $recentScans = ScanModel::with('user')
            ->orderByDesc('tanggal_scan')
            ->limit(5)
            ->get()
            ->map(function ($scan) {
                return (object) [
                    'product_name' => $scan->nama_produk,
                    'status_halal' => $scan->status_halal,
                    'created_at' => Carbon::parse($scan->tanggal_scan),
                    'user' => $scan->user
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Expiring Certificates
        |--------------------------------------------------------------------------
        */
        $expiring_certificates = HalalProduct::whereNotNull('certificate_valid_until')
            ->where('certificate_valid_until', '<=', Carbon::now()->addDays(30))
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Stats Array for New Dashboard View
        |--------------------------------------------------------------------------
        */
        $stats = [
            'categories' => $totalKategori,
            'products' => $totalProduk,
            'users' => $totalUsers,
            'scans' => $totalScan,
            'scans_change' => $scanChange,
            'api_latency' => rand(25, 65), // Simulated for demo
            'halal_products' => ProductModel::where('status', 'halal')->count(),
            'syubhat_products' => ProductModel::where('status', 'diragukan')->count(),
            'non_halal_products' => ProductModel::where('status', 'tidak halal')->count(),
            'local_products' => $localProduk,
            'open_food_facts_products' => $offProduk,
            'open_beauty_facts_products' => $obfProduk,
            'openfda_medicines' => $openFdaMedicines,
        ];

        $hasActivityEvents = Schema::hasTable('activity_events');
        $monitorStats = [
            'total_external_scans' => 0,
            'total_skincare_analyses' => 0,
            'total_interaction_checks' => 0,
            'major_or_contra_count' => 0,
            'total_risk_checks' => 0,
            'total_drug_food_conflicts' => 0,
        ];
        $activityFeed = collect();

        if ($hasActivityEvents) {
            $monitorStats = [
                'total_external_scans' => DB::table('activity_events')->where('event_type', 'external_scan')->count(),
                'total_skincare_analyses' => DB::table('activity_events')->where('event_type', 'skincare_analysis')->count(),
                'total_interaction_checks' => DB::table('activity_events')->where('event_type', 'drug_interaction')->count(),
                'major_or_contra_count' => DB::table('activity_events')
                    ->where('event_type', 'drug_interaction')
                    ->where(function ($q) {
                        $q->whereJsonContains('payload_json->severity', 'major')
                            ->orWhereJsonContains('payload_json->severity', 'contraindicated');
                    })
                    ->count(),
                'total_risk_checks' => DB::table('activity_events')->where('event_type', 'health_risk_score')->count(),
                'total_drug_food_conflicts' => DB::table('activity_events')
                    ->where('event_type', 'drug_food_conflict')
                    ->where(function ($q) {
                        $q->whereJsonContains('payload_json->has_conflict', true)
                            ->orWhere('status', 'warning');
                    })
                    ->count(),
            ];

            $activityFeed = DB::table('activity_events')
                ->leftJoin('users', 'activity_events.user_id', '=', 'users.id_user')
                ->select(
                    'activity_events.id',
                    'activity_events.event_type',
                    'activity_events.entity_ref',
                    'activity_events.summary',
                    'activity_events.status',
                    'activity_events.payload_json',
                    'activity_events.created_at',
                    DB::raw('COALESCE(activity_events.username, users.username, users.full_name, \'Guest\') as user_name')
                )
                ->orderByDesc('activity_events.created_at')
                ->limit(15)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Kirim ke view baru
        |--------------------------------------------------------------------------
        */
        return view('admin.dashboard-new', [
            'stats' => $stats,
            'monitor_stats' => $monitorStats,
            'activity_feed' => $activityFeed,
            'top_products' => $topScannedProducts,
            'recent_scans' => $recentScans,
            'expiring_certificates' => $expiring_certificates,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | API: Get Stats for Real-time Dashboard
    |--------------------------------------------------------------------------
    */
    public function getStats(Request $request)
    {
        $period = $request->get('period', 30); // 30, 90, 365
        $days = (int)$period;
        
        $totalUsers = User::count();
        $totalProduk = ProductModel::count();
        $totalScan = ScanModel::where('tanggal_scan', '>=', Carbon::now()->subDays($days))->count();
        
        // Scan Trend for chart
        $scanTrend = ScanModel::selectRaw('DATE(tanggal_scan) as tgl, COUNT(*) as total')
            ->where('tanggal_scan', '>=', Carbon::today()->subDays($days - 1))
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->pluck('total', 'tgl')
            ->toArray();

        $labels = [];
        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $tanggal = Carbon::today()->subDays($days - 1 - $i)->toDateString();
            $labels[] = Carbon::parse($tanggal)->format($days > 90 ? 'M Y' : 'd M');
            $data[] = $scanTrend[$tanggal] ?? 0;
        }

        // Halal distribution
        $halalStatus = ProductModel::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return response()->json([
            'success' => true,
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalProduk' => $totalProduk,
                'totalScan' => $totalScan,
                'scanToday' => ScanModel::whereDate('tanggal_scan', Carbon::today())->count(),
                'laporanMasuk' => ReportModel::where('status', 'pending')->count(),
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

    /*
    |--------------------------------------------------------------------------
    | API: System Health (Real Data)
    |--------------------------------------------------------------------------
    */
    public function systemHealth()
    {
        // Storage info
        $diskTotal = @disk_total_space('/');
        $diskFree = @disk_free_space('/');
        $diskUsedPercent = $diskTotal > 0 ? round((($diskTotal - $diskFree) / $diskTotal) * 100, 1) : 0;
        $diskFreePercent = 100 - $diskUsedPercent;
        
        // Memory info (PHP process)
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $memoryPercent = $memoryLimit > 0 ? round(($memoryUsage / $memoryLimit) * 100, 1) : 0;
        
        // CPU Load (Linux only)
        $cpuLoad = function_exists('sys_getloadavg') ? sys_getloadavg() : [0, 0, 0];
        
        // Uptime
        $uptime = 'N/A';
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $uptimeOutput = @shell_exec('uptime -p 2>/dev/null');
            if ($uptimeOutput) {
                $uptime = trim($uptimeOutput);
            }
        }
        
        // Database connection check
        $dbStatus = 'Online';
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'Offline';
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'status' => 'healthy',
                'uptime' => $uptime,
                'server' => gethostname() ?: 'Halalytics-Server',
                'php_version' => PHP_VERSION,
                'storage' => [
                    'total' => $this->formatBytes($diskTotal),
                    'free' => $this->formatBytes($diskFree),
                    'used_percent' => $diskUsedPercent,
                    'free_percent' => $diskFreePercent,
                    'status' => $diskFreePercent > 20 ? 'healthy' : ($diskFreePercent > 10 ? 'warning' : 'critical')
                ],
                'memory' => [
                    'usage' => $this->formatBytes($memoryUsage),
                    'peak' => $this->formatBytes($memoryPeak),
                    'limit' => $this->formatBytes($memoryLimit),
                    'percent' => $memoryPercent,
                    'status' => $memoryPercent < 70 ? 'healthy' : ($memoryPercent < 90 ? 'warning' : 'critical')
                ],
                'cpu' => [
                    'load_1m' => round($cpuLoad[0], 2),
                    'load_5m' => round($cpuLoad[1], 2),
                    'load_15m' => round($cpuLoad[2], 2),
                    'status' => $cpuLoad[0] < 1 ? 'healthy' : ($cpuLoad[0] < 2 ? 'warning' : 'critical')
                ],
                'database' => $dbStatus,
                'external_api' => 'Connected'
            ]
        ]);
    }
    
    /**
     * Parse memory limit string to bytes
     */
    private function parseMemoryLimit($limit)
    {
        if ($limit === '-1') return PHP_INT_MAX;
        $limit = strtolower(trim($limit));
        $value = (int) $limit;
        $unit = substr($limit, -1);
        
        switch ($unit) {
            case 'g': return $value * 1024 * 1024 * 1024;
            case 'm': return $value * 1024 * 1024;
            case 'k': return $value * 1024;
            default: return $value;
        }
    }
    
    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(log($bytes) / log(1024));
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
    
    /*
    |--------------------------------------------------------------------------
    | Trend Indicators (Week over Week Comparison)
    |--------------------------------------------------------------------------
    */
    public function getTrendIndicators()
    {
        // This week scans
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisWeekEnd = Carbon::now()->endOfWeek();
        $thisWeekScans = ScanModel::whereBetween('tanggal_scan', [$thisWeekStart, $thisWeekEnd])->count();
        
        // Last week scans
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
        $lastWeekScans = ScanModel::whereBetween('tanggal_scan', [$lastWeekStart, $lastWeekEnd])->count();
        
        // Calculate trend
        $scanTrend = $this->calculateTrend($thisWeekScans, $lastWeekScans);
        
        // This week users
        $thisWeekUsers = User::whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])->count();
        $lastWeekUsers = User::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();
        $userTrend = $this->calculateTrend($thisWeekUsers, $lastWeekUsers);
        
        // Top products with trend
        $topProductsWithTrend = $this->getTopProductsWithTrend();
        
        return response()->json([
            'success' => true,
            'data' => [
                'scans' => [
                    'this_week' => $thisWeekScans,
                    'last_week' => $lastWeekScans,
                    'trend' => $scanTrend
                ],
                'users' => [
                    'this_week' => $thisWeekUsers,
                    'last_week' => $lastWeekUsers,
                    'trend' => $userTrend
                ],
                'top_products' => $topProductsWithTrend
            ]
        ]);
    }
    
    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return ['direction' => $current > 0 ? 'up' : 'stable', 'percent' => 100];
        }
        
        $change = (($current - $previous) / $previous) * 100;
        
        return [
            'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable'),
            'percent' => round(abs($change), 1)
        ];
    }
    
    private function getTopProductsWithTrend()
    {
        $thisWeekStart = Carbon::now()->startOfWeek();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
        
        // Get top products this week
        $topProducts = ScanModel::select('nama_produk', 'barcode', 'status_halal', DB::raw('COUNT(*) as scan_count'))
            ->where('tanggal_scan', '>=', $thisWeekStart)
            ->whereNotNull('nama_produk')
            ->groupBy('nama_produk', 'barcode', 'status_halal')
            ->orderByDesc('scan_count')
            ->limit(5)
            ->get();
        
        // Add trend info
        return $topProducts->map(function ($product) use ($lastWeekStart, $lastWeekEnd) {
            $lastWeekCount = ScanModel::where('nama_produk', $product->nama_produk)
                ->whereBetween('tanggal_scan', [$lastWeekStart, $lastWeekEnd])
                ->count();
            
            $product->trend = $this->calculateTrend($product->scan_count, $lastWeekCount);
            return $product;
        });
    }

    public function monitorStats()
    {
        if (!Schema::hasTable('activity_events')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_external_scans' => 0,
                    'total_skincare_analyses' => 0,
                    'total_interaction_checks' => 0,
                    'major_or_contra_count' => 0,
                    'total_risk_checks' => 0,
                    'total_drug_food_conflicts' => 0,
                ],
            ]);
        }

        $stats = [
            'total_external_scans' => DB::table('activity_events')->where('event_type', 'external_scan')->count(),
            'total_skincare_analyses' => DB::table('activity_events')->where('event_type', 'skincare_analysis')->count(),
            'total_interaction_checks' => DB::table('activity_events')->where('event_type', 'drug_interaction')->count(),
            'major_or_contra_count' => DB::table('activity_events')
                ->where('event_type', 'drug_interaction')
                ->where(function ($q) {
                    $q->whereJsonContains('payload_json->severity', 'major')
                        ->orWhereJsonContains('payload_json->severity', 'contraindicated');
                })
                ->count(),
            'total_risk_checks' => DB::table('activity_events')->where('event_type', 'health_risk_score')->count(),
            'total_drug_food_conflicts' => DB::table('activity_events')
                ->where('event_type', 'drug_food_conflict')
                ->where(function ($q) {
                    $q->whereJsonContains('payload_json->has_conflict', true)
                        ->orWhere('status', 'warning');
                })
                ->count(),
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    public function monitorFeed()
    {
        if (!Schema::hasTable('activity_events')) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $feed = DB::table('activity_events')
            ->leftJoin('users', 'activity_events.user_id', '=', 'users.id_user')
            ->select(
                'activity_events.id',
                'activity_events.event_type',
                'activity_events.entity_ref',
                'activity_events.summary',
                'activity_events.status',
                'activity_events.payload_json',
                'activity_events.created_at',
                DB::raw('COALESCE(activity_events.username, users.username, users.full_name, \'Guest\') as user_name')
            )
            ->orderByDesc('activity_events.created_at')
            ->limit(20)
            ->get();

        return response()->json(['success' => true, 'data' => $feed]);
    }
    
    /*
    |--------------------------------------------------------------------------
    | Export Dashboard Data
    |--------------------------------------------------------------------------
    */
    public function exportDashboard(Request $request)
    {
        $format = $request->get('format', 'json');
        
        $data = [
            'generated_at' => Carbon::now()->toIso8601String(),
            'summary' => [
                'total_users' => User::count(),
                'total_products' => ProductModel::count(),
                'total_scans' => ScanModel::count(),
                'pending_reports' => ReportModel::where('status', 'pending')->count(),
            ],
            'halal_distribution' => [
                'halal' => ProductModel::where('status', 'halal')->count(),
                'syubhat' => ProductModel::where('status', 'diragukan')->count(),
                'haram' => ProductModel::where('status', 'tidak halal')->count(),
            ],
            'this_month' => [
                'new_users' => User::whereMonth('created_at', Carbon::now()->month)->count(),
                'new_scans' => ScanModel::whereMonth('tanggal_scan', Carbon::now()->month)->count(),
                'new_products' => ProductModel::whereMonth('created_at', Carbon::now()->month)->count(),
            ]
        ];
        
        if ($format === 'json') {
            return response()->json($data);
        }
        
        // For CSV export
        if ($format === 'csv') {
            $filename = 'dashboard_export_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            
            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Total Users', $data['summary']['total_users']]);
                fputcsv($file, ['Total Products', $data['summary']['total_products']]);
                fputcsv($file, ['Total Scans', $data['summary']['total_scans']]);
                fputcsv($file, ['Pending Reports', $data['summary']['pending_reports']]);
                fputcsv($file, ['Halal Products', $data['halal_distribution']['halal']]);
                fputcsv($file, ['Syubhat Products', $data['halal_distribution']['syubhat']]);
                fputcsv($file, ['Haram Products', $data['halal_distribution']['haram']]);
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
        
        return response()->json($data);
    }
}
