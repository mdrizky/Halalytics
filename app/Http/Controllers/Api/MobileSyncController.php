<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use App\Models\KategoriModel;
use App\Models\ScanModel;
use App\Models\HalalProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MobileSyncController extends Controller
{
    /**
     * Sync scan data from mobile app to admin system
     */
    public function syncScanData(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer|exists:users,id_user',
                'scan_data' => 'required|array',
                'scan_data.*.barcode' => 'required|string',
                'scan_data.*.product_name' => 'required|string',
                'scan_data.*.scan_time' => 'required|date',
                'scan_data.*.halal_status' => 'nullable|string|in:halal,tidak halal,diragukan'
            ]);

            $syncedCount = 0;
            $errors = [];

            foreach ($request->scan_data as $scan) {
                try {
                    // Check if product exists, if not create it
                    $product = ProductModel::where('barcode', $scan['barcode'])->first();
                    
                    if (!$product) {
                        $product = ProductModel::create([
                            'nama_product' => $scan['product_name'],
                            'barcode' => $scan['barcode'],
                            'komposisi' => $scan['komposisi'] ?? null,
                            'info_gizi' => $scan['nutrition_info'] ?? null,
                            'status' => $scan['halal_status'] ?? 'diragukan',
                            'kategori_id' => null
                        ]);
                    }

                    // Create scan record
                    ScanModel::create([
                        'user_id' => $request->user_id,
                        'product_id' => $product->id_product,
                        'nama_produk' => $scan['product_name'],
                        'barcode' => $scan['barcode'],
                        'kategori' => $product->kategori_id ? $product->kategori->nama_kategori : 'Tidak Ada',
                        'status_halal' => $scan['halal_status'] ?? 'diragukan',
                        'status_kesehatan' => 'diragukan',
                        'tanggal_scan' => $scan['scan_time'],
                        'tanggal_expired' => null
                    ]);

                    $syncedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to sync scan with barcode {$scan['barcode']}: " . $e->getMessage();
                    Log::error("Scan sync error: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'errors' => $errors,
                'message' => "Successfully synced {$syncedCount} scan records"
            ]);

        } catch (\Exception $e) {
            Log::error("Bulk scan sync error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync scan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync user data from mobile app
     */
    public function syncUserData(Request $request)
    {
        try {
            $request->validate([
                'users' => 'required|array',
                'users.*.id' => 'required|integer|exists:users,id_user',
                'users.*.name' => 'required|string',
                'users.*.email' => 'required|email',
                'users.*.scan_count' => 'nullable|integer',
                'users.*.last_active' => 'nullable|date'
            ]);

            $syncedCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($request->users as $userData) {
                try {
                    $user = User::find($userData['id']);
                    
                    if ($user) {
                        // Update user statistics - only update fields that exist
                        $updateData = [];
                        if (isset($userData['name'])) {
                            $updateData['full_name'] = $userData['name'];
                        }
                        if (isset($userData['email'])) {
                            $updateData['email'] = $userData['email'];
                        }
                        if (isset($userData['last_active'])) {
                            $updateData['last_login'] = $userData['last_active'];
                        }
                        
                        if (!empty($updateData)) {
                            $user->update($updateData);
                        }
                        
                        $syncedCount++;
                    } else {
                        $errors[] = "User with ID {$userData['id']} not found";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Failed to sync user {$userData['id']}: " . $e->getMessage();
                    Log::error("User sync error: " . $e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'synced_count' => $syncedCount,
                'errors' => $errors,
                'message' => "Successfully synced {$syncedCount} user records"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Bulk user sync error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync user data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products for mobile app
     */
    public function getProducts(Request $request)
    {
        try {
            $query = ProductModel::with('kategori');

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_product', 'LIKE', "%{$search}%")
                      ->orWhere('barcode', 'LIKE', "%{$search}%");
                });
            }

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('kategori_id', $request->category_id);
            }

            // Filter by halal status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Get all products (no pagination for mobile compatibility)
            $products = $query->orderBy('id_product', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ]);

        } catch (\Exception $e) {
            Log::error("Get products error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories for mobile app
     */
    public function getCategories(Request $request)
    {
        try {
            $categories = KategoriModel::withCount('products')
                ->orderBy('nama_kategori')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories->map(function($category) {
                    return [
                        'id' => $category->id_kategori,
                        'name' => $category->nama_kategori,
                        'slug' => \Illuminate\Support\Str::slug($category->nama_kategori, '-'),
                        'product_count' => $category->products_count,
                        'created_at' => $category->created_at,
                        'updated_at' => $category->updated_at
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error("Get categories error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics for admin dashboard
     */
    public function getUserStats(Request $request)
    {
        try {
            $totalUsers = User::count();
            $activeUsers = User::where('last_login', '>=', now()->subDays(7))->count();
            $newUsers = User::where('created_at', '>=', now()->subDays(30))->count();
            
            $topScanners = User::withCount('scans')
                ->orderBy('scans_count', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_users' => $totalUsers,
                    'active_users' => $activeUsers,
                    'new_users' => $newUsers,
                    'top_scanners' => $topScanners
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Get user stats error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get scan statistics for admin dashboard
     */
    public function getScanStats(Request $request)
    {
        try {
            $totalScans = ScanModel::count();
            $todayScans = ScanModel::whereDate('tanggal_scan', today())->count();
            $weekScans = ScanModel::whereBetween('tanggal_scan', [now()->subDays(7), now()])->count();
            
            $halalScans = ScanModel::where('status_halal', 'halal')->count();
            $nonHalalScans = ScanModel::where('status_halal', 'tidak halal')->count();
            $doubtfulScans = ScanModel::where('status_halal', 'diragukan')->count();

            $recentScans = ScanModel::with('user', 'product')
                ->orderBy('tanggal_scan', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_scans' => $totalScans,
                    'today_scans' => $todayScans,
                    'week_scans' => $weekScans,
                    'halal_scans' => $halalScans,
                    'non_halal_scans' => $nonHalalScans,
                    'doubtful_scans' => $doubtfulScans,
                    'recent_scans' => $recentScans
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Get scan stats error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get scan statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
