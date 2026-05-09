<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use App\Models\User;
use App\Models\ProductModel;
use App\Models\ScanModel;
use App\Models\ReportModel;
use App\Models\KategoriModel;
use App\Models\HalalProduct;
use App\Models\Medicine;
use App\Models\NotificationCampaign;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardViewController extends Controller
{
    private $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * 🏠 Main dashboard view
     */
    public function index()
    {
        $periodDays = $this->parsePeriodInput(request()->get('period', 30));

        // 🚀 Fetch stats from CacheService
        $cachedStats = $this->cacheService->getDashboardStats();
        
        // Detailed counts for specific sources
        $localProduk = ProductModel::where('source', 'local')->count();
        $offProduk = ProductModel::whereIn('source', ['open_food_facts', 'openfoodfacts', 'off_api'])->count();
        $obfProduk = ProductModel::whereIn('source', ['open_beauty_facts', 'openbeautyfacts', 'obf_api'])->count();
        $openFdaMedicines = Medicine::whereIn('source', ['openfda', 'open_fda'])->count();

        // Trend Calculation
        $scanLast30 = ScanModel::where('tanggal_scan', '>=', Carbon::now()->subDays(30))->count();
        $scanPrev30 = ScanModel::whereBetween('tanggal_scan', [Carbon::now()->subDays(60), Carbon::now()->subDays(30)])->count();
        $scanChange = $scanPrev30 > 0 ? round((($scanLast30 - $scanPrev30) / $scanPrev30) * 100, 1) : 0;

        // Top Scanned Products
        $topScannedProducts = collect($this->cacheService->getTopScannedProducts(5))
            ->map(function ($product) {
                $imageService = app(\App\Services\DisplayImageService::class);
                $resolvedImage = $imageService->resolve(
                    $product['image'] ?? null,
                    [
                        'name' => $product['product_name'],
                        'barcode' => $product['barcode'],
                        'category' => $product['category_name'] ?? 'food',
                    ],
                    'product'
                );

                return (object) [
                    'product_name' => $product['product_name'],
                    'barcode' => $product['barcode'],
                    'halal_status' => $product['halal_status'],
                    'image' => $resolvedImage,
                    'category_name' => $product['category_name'],
                    'scan_count' => $product['scan_count'],
                ];
            });

        // Recent Scans
        $recentScans = ScanModel::with(['user', 'product'])
            ->orderByDesc('tanggal_scan')
            ->limit(5)
            ->get()
            ->map(function ($scan) {
                $imageService = app(\App\Services\DisplayImageService::class);
                $resolvedImage = $imageService->resolve(
                    $scan->product?->image ?? null,
                    [
                        'name' => $scan->nama_produk ?? $scan->product?->nama_product,
                        'barcode' => $scan->barcode,
                        'category' => 'food',
                    ],
                    'product'
                );

                return (object) [
                    'product_name' => $scan->nama_produk ?? $scan->product?->nama_product,
                    'status_halal' => $scan->product?->status ?? $scan->status_halal,
                    'created_at' => Carbon::parse($scan->tanggal_scan),
                    'user' => $scan->user,
                    'image' => $resolvedImage,
                ];
            });

        // Expiring Certificates
        $expiring_certificates = HalalProduct::whereNotNull('certificate_valid_until')
            ->where('certificate_valid_until', '<=', Carbon::now()->addDays(30))
            ->orderBy('certificate_valid_until')
            ->take(5)
            ->get();

        // 📦 Stats Array mapped to view expectations
        $stats = [
            'users' => $cachedStats['total_users'],
            'local_products' => $localProduk,
            'open_food_facts_products' => $offProduk,
            'open_beauty_facts_products' => $obfProduk,
            'openfda_medicines' => $openFdaMedicines,
            'total_kategori' => KategoriModel::count(),
            'api_latency' => rand(38, 52), // Sample metric
        ];

        // 📈 Analytics Array mapped to view expectations
        $analytics = [
            'overview' => [
                'total_scans' => $cachedStats['total_scans'],
                'new_users_today' => $cachedStats['new_users_today'] ?? User::whereDate('created_at', Carbon::today())->count(),
                'campaigns_sent' => NotificationCampaign::where('status', 'sent')->sum('sent_count'),
            ],
            'article_stats' => [
                'published' => Article::where('is_published', true)->count(),
            ],
            'user_growth' => $cachedStats['user_growth'] ?? [],
            'scan_activity' => collect($cachedStats['scan_activity'] ?? [])->map(function($item) {
                return [
                    'date' => $item['date'],
                    'halal' => $item['halal'] ?? 0,
                    'syubhat' => $item['syubhat'] ?? 0,
                    'haram' => $item['haram'] ?? 0,
                ];
            })->toArray(),
            'halal_stats_detailed' => [
                'halal' => $cachedStats['halal_count'] ?? 0,
                'haram' => $cachedStats['haram_count'] ?? 0,
                'syubhat' => $cachedStats['syubhat_count'] ?? 0,
            ],
            'health_trends' => [
                ['metric_type' => 'Calorie Check', 'count' => rand(100, 500)],
                ['metric_type' => 'Allergen Scan', 'count' => rand(100, 500)],
                ['metric_type' => 'Sugar Level', 'count' => rand(100, 500)],
                ['metric_type' => 'Fat Content', 'count' => rand(100, 500)],
                ['metric_type' => 'Sodium Check', 'count' => rand(100, 500)],
            ]
        ];

        // Monitor Stats
        $monitorStats = [
            'total_external_scans' => ScanModel::whereNotNull('barcode')->count(),
            'total_skincare_analyses' => ProductModel::where('kategori_id', KategoriModel::where('nama_kategori', 'Kosmetik')->value('id_kategori'))->count(),
            'total_interaction_checks' => rand(120, 450), // Mocked for now until interaction tracking is built
            'major_or_contra_count' => rand(5, 25),      // Mocked
            'total_risk_checks' => ScanModel::count(),
            'total_drug_food_conflicts' => rand(2, 10),  // Mocked
        ];

        // Activity Feed
        $activityFeed = ScanModel::query()
            ->with('user')
            ->latest('tanggal_scan')
            ->limit(15)
            ->get()
            ->map(function ($scan) {
                return (object) [
                    'id' => $scan->id,
                    'event_type' => 'scan',
                    'entity_ref' => $scan->barcode,
                    'summary' => "Scanned " . ($scan->nama_produk ?: 'Product'),
                    'status' => 'success',
                    'user_name' => $scan->user->username ?? $scan->user->full_name ?? 'Guest',
                    'created_at' => $scan->tanggal_scan,
                ];
            });

        return view('admin.dashboard', [
            'stats' => $stats,
            'analytics' => $analytics,
            'top_products' => $topScannedProducts,
            'recent_scans' => $recentScans,
            'expiring_certificates' => $expiring_certificates,
            'monitor_stats' => $monitorStats,
            'activity_feed' => $activityFeed,
            'period_days' => $periodDays
        ]);
    }

    private function parsePeriodInput($period): int
    {
        $periodMap = ['7' => 7, '30' => 30, '90' => 90, '365' => 365];
        return $periodMap[$period] ?? 30;
    }
}
