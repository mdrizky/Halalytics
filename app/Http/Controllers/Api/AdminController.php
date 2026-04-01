<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OCRProduct;
use App\Models\ScanHistory;
use App\Models\ScanModel;
use App\Models\User;
use App\Models\ProductModel;
use App\Models\FavoriteProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('admin');
    }

    /**
     * Get dashboard statistics
     */
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        $totalProducts = ProductModel::count();
        $pendingProducts = ProductModel::where(function ($query) {
            $query->where('approval_status', 'pending')
                ->orWhere('verification_status', 'pending')
                ->orWhere('verification_status', 'needs_review');
        })
            ->count();
        $useRealtimeScanTable = Schema::hasTable('scan_histories');
        
        $stats = [
            'total_products' => $totalProducts,
            'pending_approval' => $pendingProducts,
            'total_users' => User::where('role', 'user')->count(),
            'total_scans_today' => $useRealtimeScanTable
                ? ScanHistory::whereDate('created_at', today())->count()
                : ScanModel::whereDate('tanggal_scan', today())->count(),
            
            'total_ocr_products' => OCRProduct::count(),
            'pending_review' => OCRProduct::where('status', 'pending_admin_review')->count(),
            
            'recent_scans' => $this->getRecentScans(10),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get Pending Products (ProductModel)
     */
    public function getPendingProducts()
    {
        $products = ProductModel::where(function ($query) {
                $query->where('approval_status', 'pending')
                    ->orWhere('verification_status', 'pending')
                    ->orWhere('verification_status', 'needs_review');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Approve Product
     */
    public function approveProduct(Request $request, $id)
    {
        $product = ProductModel::where('id_product', $id)->orWhere('id', $id)->first();
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update([
            'approval_status' => 'approved',
            'approved_by' => Auth::id(), // Use user id
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product approved successfully',
        ]);
    }

    /**
     * Reject Product
     */
    public function rejectProduct(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);
        
        $product = ProductModel::where('id_product', $id)->orWhere('id', $id)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product rejected',
        ]);
    }

    /**
     * Get all OCR products with filters
     */
    public function getOCRProducts(Request $request)
    {
        $query = OCRProduct::with(['user', 'ingredients', 'verifier']);

        // Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->halal_status) {
            $query->where('halal_status', $request->halal_status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->search . '%')
                  ->orWhere('brand', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Get OCR product details for admin review
     */
    public function getOCRProductDetail($id)
    {
        $product = OCRProduct::with([
            'user',
            'ingredients',
            'scanHistories',
            'favorites',
            'verifier'
        ])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Verify OCR product
     */
    public function verifyOCRProduct(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|string',
            'brand' => 'nullable|string',
            'country' => 'nullable|string',
            'halal_status' => 'required|in:halal,haram,syubhat,unknown',
            'status' => 'required|in:verified,rejected',
            'admin_notes' => 'nullable|string',
            'ingredients' => 'nullable|array',
            'ingredients.*.id' => 'required|integer',
            'ingredients.*.status' => 'required|in:halal,haram,syubhat,unknown',
            'ingredients.*.risk_level' => 'required|in:low,medium,high'
        ]);

        $product = OCRProduct::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Update product
        $product->update([
            'product_name' => $request->product_name,
            'brand' => $request->brand,
            'country' => $request->country,
            'halal_status' => $request->halal_status,
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'verified_by' => Auth::id(),
            'verified_at' => now()
        ]);

        // Update ingredients if provided
        if ($request->ingredients) {
            foreach ($request->ingredients as $ingredient) {
                $product->ingredients()->updateExistingPivot($ingredient['id'], [
                    'status' => $ingredient['status'],
                    'risk_level' => $ingredient['risk_level']
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $product->load(['ingredients', 'verifier']),
            'message' => 'Product verified successfully'
        ]);
    }

    /**
     * Reject OCR product
     */
    public function rejectOCRProduct(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $product = OCRProduct::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $product->update([
            'status' => 'rejected',
            'admin_notes' => $request->rejection_reason,
            'verified_by' => Auth::id(),
            'verified_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product rejected successfully'
        ]);
    }

    /**
     * Get scan history
     */
    public function getScanHistory(Request $request)
    {
        if (Schema::hasTable('scan_histories')) {
            $query = ScanHistory::with(['user']);

            if ($request->product_type) {
                $query->where('scannable_type', 'like', '%' . $request->product_type . '%');
            }

            if ($request->status) {
                $query->where('halal_status', $request->status);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $scans = $query->orderBy('created_at', 'desc')
                ->paginate($request->per_page ?? 50);

            return response()->json([
                'success' => true,
                'data' => $scans
            ]);
        }

        $legacyQuery = ScanModel::with(['user']);

        if ($request->status) {
            $legacyQuery->where('status_halal', $request->status);
        }
        if ($request->user_id) {
            $legacyQuery->where('user_id', $request->user_id);
        }
        if ($request->date_from) {
            $legacyQuery->whereDate('tanggal_scan', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $legacyQuery->whereDate('tanggal_scan', '<=', $request->date_to);
        }

        $scans = $legacyQuery->orderByDesc('tanggal_scan')->paginate($request->per_page ?? 50);
        $scans->getCollection()->transform(function (ScanModel $scan) {
            return [
                'id' => $scan->id_scan,
                'user_id' => $scan->user_id,
                'product_name' => $scan->nama_produk,
                'barcode' => $scan->barcode,
                'halal_status' => $scan->status_halal,
                'source' => 'local',
                'scan_method' => 'legacy',
                'created_at' => optional($scan->tanggal_scan ?: $scan->created_at)?->toDateTimeString(),
                'user' => $scan->user,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $scans,
            'message' => 'Menampilkan riwayat scan dari tabel legacy.'
        ]);
    }

    /**
     * Get users list
     */
    public function getUsers(Request $request)
    {
        $query = User::query()->withCount('scans');

        if (Schema::hasTable('favorite_products')) {
            $query->addSelect([
                'favorites_count' => FavoriteProduct::query()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('favorite_products.user_id', 'users.id_user')
            ]);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%')
                  ->orWhere('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Get user details
     */
    public function getUserDetail($id)
    {
        $user = User::query()
            ->where('id_user', $id)
            ->orWhere('id', $id)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $recentScans = Schema::hasTable('scan_histories')
            ? ScanHistory::query()
                ->where('user_id', $user->id_user)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get()
            : ScanModel::query()
                ->where('user_id', $user->id_user)
                ->orderByDesc('tanggal_scan')
                ->limit(20)
                ->get()
                ->map(function (ScanModel $scan) {
                    return [
                        'id' => $scan->id_scan,
                        'product_name' => $scan->nama_produk,
                        'barcode' => $scan->barcode,
                        'halal_status' => $scan->status_halal,
                        'created_at' => optional($scan->tanggal_scan ?: $scan->created_at)?->toDateTimeString(),
                    ];
                });

        $favorites = Schema::hasTable('favorite_products')
            ? FavoriteProduct::query()
                ->where('user_id', $user->id_user)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get()
            : collect();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'recent_scans' => $recentScans,
                'favorites' => $favorites,
            ]
        ]);
    }

    /**
     * Get system statistics
     */
    public function getSystemStats()
    {
        $productsByStatus = Schema::hasTable('ocr_products')
            ? OCRProduct::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
            : ProductModel::selectRaw('verification_status as status, COUNT(*) as count')
                ->groupBy('verification_status')
                ->pluck('count', 'status');

        $productsByHalalStatus = Schema::hasTable('ocr_products')
            ? OCRProduct::selectRaw('halal_status, COUNT(*) as count')
                ->groupBy('halal_status')
                ->pluck('count', 'halal_status')
            : ProductModel::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

        $scansByMonth = Schema::hasTable('scan_histories')
            ? ScanHistory::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->take(12)
                ->pluck('count', 'month')
            : ScanModel::selectRaw('DATE_FORMAT(COALESCE(tanggal_scan, created_at), "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->take(12)
                ->pluck('count', 'month');

        $stats = [
            'products_by_status' => $productsByStatus,
            'products_by_halal_status' => $productsByHalalStatus,
            'scans_by_month' => $scansByMonth,
            'top_scanners' => User::withCount('scans')
                ->orderBy('scans_count', 'desc')
                ->take(10)
                ->get(['id_user', 'username', 'full_name', 'email']),
            'recent_activity' => $this->getRecentScans(20),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    private function getRecentScans(int $limit = 10)
    {
        if (Schema::hasTable('scan_histories')) {
            return ScanHistory::with('user')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();
        }

        return ScanModel::with('user')
            ->orderByDesc('tanggal_scan')
            ->limit($limit)
            ->get()
            ->map(function (ScanModel $scan) {
                return [
                    'id' => $scan->id_scan,
                    'product_name' => $scan->nama_produk,
                    'barcode' => $scan->barcode,
                    'halal_status' => $scan->status_halal,
                    'scan_method' => 'legacy',
                    'source' => 'local',
                    'created_at' => optional($scan->tanggal_scan ?: $scan->created_at)?->toDateTimeString(),
                    'user' => $scan->user,
                ];
            });
    }

    /**
     * Export data
     */
    public function exportData(Request $request)
    {
        $request->validate([
            'type' => 'required|in:ocr_products,scan_history,users',
            'format' => 'required|in:csv,xlsx',
            'filters' => 'nullable|array'
        ]);

        if ($request->format !== 'csv') {
            return response()->json([
                'success' => false,
                'message' => 'Format xlsx belum tersedia. Gunakan csv.'
            ], 422);
        }

        $type = (string) $request->type;
        $filters = (array) $request->input('filters', []);
        $now = now()->format('Ymd_His');
        $fileName = "{$type}_{$now}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($type, $filters) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fwrite($out, "\xEF\xBB\xBF");

            if ($type === 'ocr_products') {
                fputcsv($out, ['id', 'product_name', 'brand', 'country', 'halal_status', 'status', 'created_at']);
                $query = OCRProduct::query();
                if (!empty($filters['status'])) {
                    $query->where('status', $filters['status']);
                }
                if (!empty($filters['halal_status'])) {
                    $query->where('halal_status', $filters['halal_status']);
                }
                $query->orderByDesc('created_at')
                    ->chunk(500, function ($rows) use ($out) {
                        foreach ($rows as $row) {
                            fputcsv($out, [
                                $row->id,
                                $row->product_name,
                                $row->brand,
                                $row->country,
                                $row->halal_status,
                                $row->status,
                                optional($row->created_at)?->toDateTimeString(),
                            ]);
                        }
                    });
            } elseif ($type === 'users') {
                fputcsv($out, ['id_user', 'username', 'full_name', 'email', 'role', 'active', 'created_at']);
                $query = User::query();
                if (!empty($filters['role'])) {
                    $query->where('role', $filters['role']);
                }
                if (array_key_exists('active', $filters)) {
                    $query->where('active', (bool) $filters['active']);
                }
                $query->orderByDesc('created_at')
                    ->chunk(500, function ($rows) use ($out) {
                        foreach ($rows as $row) {
                            fputcsv($out, [
                                $row->id_user ?? $row->id,
                                $row->username,
                                $row->full_name ?? $row->name,
                                $row->email,
                                $row->role,
                                (int) ($row->active ?? $row->is_active ?? 0),
                                optional($row->created_at)?->toDateTimeString(),
                            ]);
                        }
                    });
            } else {
                fputcsv($out, ['id', 'user_id', 'product_name', 'barcode', 'halal_status', 'source', 'scan_method', 'created_at']);
                $isRealtimeTable = Schema::hasTable('scan_histories');
                if ($isRealtimeTable) {
                    $query = ScanHistory::query();
                    if (!empty($filters['user_id'])) {
                        $query->where('user_id', (int) $filters['user_id']);
                    }
                    if (!empty($filters['halal_status'])) {
                        $query->where('halal_status', $filters['halal_status']);
                    }
                    if (!empty($filters['source'])) {
                        $query->where('source', $filters['source']);
                    }
                    $query->orderByDesc('created_at')
                        ->chunk(500, function ($rows) use ($out) {
                            foreach ($rows as $row) {
                                fputcsv($out, [
                                    $row->id,
                                    $row->user_id,
                                    $row->product_name,
                                    $row->barcode,
                                    $row->halal_status,
                                    $row->source,
                                    $row->scan_method,
                                    optional($row->created_at)?->toDateTimeString(),
                                ]);
                            }
                        });
                } else {
                    $query = ScanModel::query();
                    if (!empty($filters['user_id'])) {
                        $query->where('user_id', (int) $filters['user_id']);
                    }
                    if (!empty($filters['halal_status'])) {
                        $query->where('status_halal', $filters['halal_status']);
                    }
                    $query->orderByDesc('created_at')
                        ->chunk(500, function ($rows) use ($out) {
                            foreach ($rows as $row) {
                                fputcsv($out, [
                                    $row->id_scan,
                                    $row->user_id,
                                    $row->nama_produk,
                                    $row->barcode,
                                    $row->status_halal,
                                    'local',
                                    'legacy',
                                    optional($row->tanggal_scan ?: $row->created_at)?->toDateTimeString(),
                                ]);
                            }
                        });
                }
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
