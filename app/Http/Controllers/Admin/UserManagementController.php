<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ScanHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'admin']);
    }

    /**
     * Display all users with their statistics
     */
    public function index(Request $request)
    {
        $query = User::withCount('scanHistories')
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->get('role'));
        }

        // Filter by status
        if ($request->has('active')) {
            $query->where('active', $request->get('active') === 'true');
        }

        $users = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Show specific user with detailed information
     */
    public function show($id)
    {
        $user = User::with(['scanHistories' => function($query) {
                $query->with('scannable')
                  ->orderBy('created_at', 'desc')
                  ->limit(50);
            }])
            ->findOrFail($id);

        // Calculate user statistics
        $stats = [
            'total_scans' => $user->scanHistories()->count(),
            'scans_this_month' => $user->scanHistories()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'scans_this_week' => $user->scanHistories()
                ->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])
                ->count(),
            'halal_scans' => $user->scanHistories()
                ->where('halal_status', 'halal')
                ->count(),
            'haram_scans' => $user->scanHistories()
                ->where('halal_status', 'haram')
                ->count(),
            'syubhat_scans' => $user->scanHistories()
                ->where('halal_status', 'syubhat')
                ->count(),
            'most_scanned_categories' => $user->scanHistories()
                ->join('products', 'scan_histories.scannable_id', '=', 'products.id_product')
                ->join('kategori', 'products.id_kategori', '=', 'kategori.id_kategori')
                ->select('kategori.nama_kategori', DB::raw('COUNT(*) as count'))
                ->groupBy('kategori.id_kategori', 'kategori.nama_kategori')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
            'scan_methods' => [
                'camera' => $user->scanHistories()->where('scan_method', 'camera')->count(),
                'manual' => $user->scanHistories()->where('scan_method', 'manual')->count(),
                'search' => $user->scanHistories()->where('scan_method', 'text_search')->count(),
                'barcode' => $user->scanHistories()->where('scan_method', 'barcode')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Update user information
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id . ',id_user',
            'phone' => 'sometimes|string|max:20',
            'role' => 'sometimes|in:admin,user',
            'active' => 'sometimes|boolean',
            'avatar_url' => 'sometimes|url|nullable',
            'birth_date' => 'sometimes|date|nullable',
            'gender' => 'sometimes|in:male,female,other|nullable',
            'bio' => 'sometimes|string|max:500|nullable',
            'dietary_preferences' => 'sometimes|array|nullable',
            'allergies' => 'sometimes|array|nullable',
            'notifications_enabled' => 'sometimes|boolean',
            'profile_visibility' => 'sometimes|in:public,private,friends',
            'show_health_tips' => 'sometimes|boolean',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user->fresh()
        ]);
    }

    /**
     * Delete user and all related data
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deletion of admin users
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin users'
            ], 403);
        }

        // Delete user and related scan histories
        DB::transaction(function() use ($user) {
            $user->scanHistories()->delete();
            $user->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Get user scan history with filters
     */
    public function scanHistory(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        $query = $user->scanHistories()->with('scannable');

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Filter by halal status
        if ($request->has('halal_status')) {
            $query->where('halal_status', $request->get('halal_status'));
        }

        // Filter by scan method
        if ($request->has('scan_method')) {
            $query->where('scan_method', $request->get('scan_method'));
        }

        $scans = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $scans
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('active', true)->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_scans' => ScanHistory::count(),
            'scans_today' => ScanHistory::whereDate('created_at', today())->count(),
            'scans_this_week' => ScanHistory::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'scans_this_month' => ScanHistory::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'top_users_by_scans' => User::withCount('scanHistories')
                ->orderBy('scan_histories_count', 'desc')
                ->limit(10)
                ->get(),
            'scan_status_distribution' => [
                'halal' => ScanHistory::where('halal_status', 'halal')->count(),
                'haram' => ScanHistory::where('halal_status', 'haram')->count(),
                'syubhat' => ScanHistory::where('halal_status', 'syubhat')->count(),
                'unknown' => ScanHistory::where('halal_status', 'unknown')->count(),
            ],
            'scan_methods_distribution' => [
                'camera' => ScanHistory::where('scan_method', 'camera')->count(),
                'manual' => ScanHistory::where('scan_method', 'manual')->count(),
                'search' => ScanHistory::where('scan_method', 'text_search')->count(),
                'barcode' => ScanHistory::where('scan_method', 'barcode')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Export user data
     */
    public function export($id)
    {
        $user = User::findOrFail($id);
        $scanHistory = $user->scanHistories()
            ->with('scannable')
            ->orderBy('created_at', 'desc')
            ->get();

        $exportData = [
            'user' => $user->toArray(),
            'scan_history' => $scanHistory->toArray(),
            'exported_at' => now()->toIso8601String()
        ];

        return response()->json($exportData, 200, [
            'Content-Disposition' => 'attachment; filename="user_' . $user->id_user . '_export.json"'
        ]);
    }
}
