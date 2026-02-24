<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Carbon;
use App\Models\ProductModel;
use App\Models\KategoriModel;
use App\Models\ReportModel;
use App\Models\ScanModel;
use App\Models\User;

class AdminApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    private function ensureAdmin()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            abort(response()->json([
                'response_code' => 403,
                'message' => 'Akses ditolak: khusus admin'
            ], 403));
        }
    }

    // PRODUCTS (ADMIN)
    public function indexProducts()
    {
        $this->ensureAdmin();
        try {
            $products = ProductModel::with('kategori')->orderBy('id_product', 'desc')->get();
            return response()->json([
                'response_code' => 200,
                'message' => 'Daftar produk (admin)',
                'content' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' => 500,
                'message' => 'Error retrieving products: ' . $e->getMessage(),
                'content' => []
            ], 500);
        }
    }

    public function storeProduct(Request $request)
    {
        $this->ensureAdmin();
        $request->validate([
            'nama_product' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode',
            'komposisi' => 'nullable|string',
            'status' => 'required|string|in:halal,tidak halal,diragukan',
            'info_gizi' => 'nullable|string',
            'kategori_id' => 'nullable|exists:kategori,id_kategori',
        ]);

        $product = ProductModel::create($request->all());
        return response()->json([
            'response_code' => 201,
            'message' => 'Produk dibuat',
            'content' => $product
        ], 201);
    }

    public function updateProduct(Request $request, $id)
    {
        $this->ensureAdmin();
        $product = ProductModel::find($id);
        if (!$product) {
            return response()->json(['response_code' => 404, 'message' => 'Produk tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_product' => 'sometimes|string|max:255',
            'barcode' => 'sometimes|string|unique:products,barcode,' . $id . ',id_product',
            'komposisi' => 'nullable|string',
            'status' => 'sometimes|string|in:halal,tidak halal,diragukan',
            'info_gizi' => 'nullable|string',
            'kategori_id' => 'nullable|exists:kategori,id_kategori',
        ]);

        $product->update($request->all());
        return response()->json([
            'response_code' => 200,
            'message' => 'Produk diperbarui',
            'content' => $product
        ]);
    }

    public function destroyProduct($id)
    {
        $this->ensureAdmin();
        $product = ProductModel::find($id);
        if (!$product) {
            return response()->json(['response_code' => 404, 'message' => 'Produk tidak ditemukan'], 404);
        }
        $product->delete();
        return response()->json([
            'response_code' => 200,
            'message' => 'Produk dihapus'
        ]);
    }

    // KATEGORI (ADMIN)
    public function indexKategori()
    {
        $this->ensureAdmin();
        try {
            $kategori = KategoriModel::withCount('products')->orderBy('id_kategori', 'desc')->get();
            return response()->json([
                'response_code' => 200,
                'message' => 'Daftar kategori (admin)',
                'content' => $kategori
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' => 500,
                'message' => 'Error retrieving categories: ' . $e->getMessage(),
                'content' => []
            ], 500);
        }
    }

    public function storeKategori(Request $request)
    {
        $this->ensureAdmin();
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);
        $kategori = KategoriModel::create($request->all());
        return response()->json([
            'response_code' => 201,
            'message' => 'Kategori dibuat',
            'content' => $kategori
        ], 201);
    }

    public function updateKategori(Request $request, $id)
    {
        $this->ensureAdmin();
        $kategori = KategoriModel::find($id);
        if (!$kategori) {
            return response()->json(['response_code' => 404, 'message' => 'Kategori tidak ditemukan'], 404);
        }
        $request->validate([
            'nama_kategori' => 'sometimes|string|max:255',
        ]);
        $kategori->update($request->all());
        return response()->json([
            'response_code' => 200,
            'message' => 'Kategori diperbarui',
            'content' => $kategori
        ]);
    }

    public function destroyKategori($id)
    {
        $this->ensureAdmin();
        $kategori = KategoriModel::find($id);
        if (!$kategori) {
            return response()->json(['response_code' => 404, 'message' => 'Kategori tidak ditemukan'], 404);
        }
        $kategori->delete();
        return response()->json([
            'response_code' => 200,
            'message' => 'Kategori dihapus'
        ]);
    }

    // REPORTS REVIEW (ADMIN)
    public function indexReports()
    {
        $this->ensureAdmin();
        try {
            $reports = ReportModel::with('product', 'user')->orderBy('created_at', 'desc')->get();
            return response()->json([
                'response_code' => 200,
                'message' => 'Daftar laporan (admin)',
                'content' => $reports
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' => 500,
                'message' => 'Error retrieving reports: ' . $e->getMessage(),
                'content' => []
            ], 500);
        }
    }

    public function updateReportStatus(Request $request, $id)
    {
        $this->ensureAdmin();
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected,reviewed',
        ]);
        $report = ReportModel::find($id);
        if (!$report) {
            return response()->json(['response_code' => 404, 'message' => 'Laporan tidak ditemukan'], 404);
        }
        $report->status = $request->status;
        $report->save();
        return response()->json([
            'response_code' => 200,
            'message' => 'Status laporan diperbarui',
            'content' => $report
        ]);
    }

    // ANDROID SYNC ENDPOINTS
    public function syncScanData(Request $request)
    {
        $this->ensureAdmin();
        $request->validate([
            'user_id' => 'required|integer',
            'barcode' => 'required|string',
            'product_name' => 'required|string',
            'halal_status' => 'required|string|in:halal,haram,syubhat,non_halal,not_verified',
            'scan_date' => 'required|date',
            'device_info' => 'nullable|string',
            'location' => 'nullable|string'
        ]);

        // Simpan data scan ke database
        $scanData = [
            'user_id' => $request->user_id,
            'barcode' => $request->barcode,
            'product_name' => $request->product_name,
            'halal_status' => $request->halal_status,
            'tanggal_scan' => $request->scan_date,
            'device_info' => $request->device_info,
            'location' => $request->location,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('scans')->insert($scanData);

        // Update atau buat entri product_halal_status
        $existingProduct = DB::table('product_halal_status')
            ->where('barcode', $request->barcode)
            ->first();

        if (!$existingProduct) {
            DB::table('product_halal_status')->insert([
                'barcode' => $request->barcode,
                'product_name' => $request->product_name,
                'halal_status' => $request->halal_status,
                'last_checked' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            DB::table('product_halal_status')
                ->where('barcode', $request->barcode)
                ->update([
                    'halal_status' => $request->halal_status,
                    'last_checked' => now(),
                    'updated_at' => now()
                ]);
        }

        return response()->json([
            'response_code' => 200,
            'message' => 'Data scan berhasil disinkronkan',
            'data' => $scanData
        ]);
    }

    public function syncUserData(Request $request)
    {
        $this->ensureAdmin();
        $request->validate([
            'user_id' => 'required|integer',
            'full_name' => 'required|string',
            'email' => 'required|email',
            'bmi' => 'nullable|numeric',
            'activity_level' => 'nullable|string',
            'last_login' => 'nullable|date',
            'device_info' => 'nullable|string'
        ]);

        // Update data user
        DB::table('users')
            ->where('id', $request->user_id)
            ->update([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'bmi' => $request->bmi,
                'activity_level' => $request->activity_level,
                'last_login' => $request->last_login,
                'device_info' => $request->device_info,
                'updated_at' => now()
            ]);

        return response()->json([
            'response_code' => 200,
            'message' => 'Data user berhasil disinkronkan',
            'data' => $request->all()
        ]);
    }

    public function getRealtimeStats()
    {
        $this->ensureAdmin();
        
        try {
            // Get real-time statistics with error handling for missing tables/data
            $stats = [
                'total_scans_today' => $this->safeCount('scans', 'tanggal_scan', Carbon::today()),
                'total_users_active' => $this->safeCount('users', 'last_login', Carbon::today()),
                'total_categories' => $this->safeCount('kategori'),
                'total_products' => $this->safeCount('products'),
                'total_users' => $this->safeCount('users'),
                'recent_scans' => $this->safeGetRecentScans(),
                'halal_stats' => $this->safeGetHalalStats(),
                'user_activity' => $this->safeGetUserActivity()
            ];

            return response()->json([
                'response_code' => 200,
                'message' => 'Real-time stats retrieved',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' => 500,
                'message' => 'Error retrieving stats: ' . $e->getMessage(),
                'data' => [
                    'total_scans_today' => 0,
                    'total_users_active' => 0,
                    'total_categories' => 0,
                    'total_products' => 0,
                    'total_users' => 0,
                    'recent_scans' => [],
                    'halal_stats' => [
                        'halal' => 0,
                        'haram' => 0,
                        'syubhat' => 0,
                        'non_halal' => 0,
                        'not_verified' => 0
                    ],
                    'user_activity' => []
                ]
            ], 500);
        }
    }

    // Helper method to safely count records
    private function safeCount($table, $dateColumn = null, $date = null)
    {
        try {
            $query = DB::table($table);
            if ($dateColumn && $date) {
                $query->whereDate($dateColumn, $date);
            }
            return $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    // Helper method to safely get recent scans
    private function safeGetRecentScans()
    {
        try {
            return DB::table('scans')
                ->orderBy('tanggal_scan', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    // Helper method to safely get halal stats
    private function safeGetHalalStats()
    {
        try {
            // Try to get from product_halal_status table first
            if (DB::getSchemaBuilder()->hasTable('product_halal_status')) {
                return [
                    'halal' => DB::table('product_halal_status')->where('halal_status', 'halal')->count(),
                    'haram' => DB::table('product_halal_status')->where('halal_status', 'haram')->count(),
                    'syubhat' => DB::table('product_halal_status')->where('halal_status', 'syubhat')->count(),
                    'non_halal' => DB::table('product_halal_status')->where('halal_status', 'non_halal')->count(),
                    'not_verified' => DB::table('product_halal_status')->where('halal_status', 'not_verified')->count()
                ];
            }
            
            // Fallback to products table
            if (DB::getSchemaBuilder()->hasTable('products')) {
                return [
                    'halal' => DB::table('products')->where('status', 'halal')->count(),
                    'haram' => DB::table('products')->where('status', 'tidak halal')->count(),
                    'syubhat' => DB::table('products')->where('status', 'diragukan')->count(),
                    'non_halal' => DB::table('products')->where('status', 'tidak halal')->count(),
                    'not_verified' => DB::table('products')->where('status', 'diragukan')->count()
                ];
            }
        } catch (\Exception $e) {
            // Return default values if both tables don't exist
        }
        
        return [
            'halal' => 0,
            'haram' => 0,
            'syubhat' => 0,
            'non_halal' => 0,
            'not_verified' => 0
        ];
    }

    // Helper method to safely get user activity
    private function safeGetUserActivity()
    {
        try {
            // Check if activity_level column exists
            if (DB::getSchemaBuilder()->hasColumn('users', 'activity_level')) {
                return DB::table('users')
                    ->select('activity_level', DB::raw('COUNT(*) as count'))
                    ->groupBy('activity_level')
                    ->get();
            }
            
            // Fallback to basic user stats
            return [
                ['activity_level' => 'active', 'count' => $this->safeCount('users')],
                ['activity_level' => 'inactive', 'count' => 0]
            ];
        } catch (\Exception $e) {
            return collect([]);
        }
    }
}


