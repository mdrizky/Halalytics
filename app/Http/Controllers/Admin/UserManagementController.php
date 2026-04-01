<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScanHistory;
use App\Models\ScanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = User::query()->orderByDesc('created_at');

        if ($this->hasRealtimeScanHistories()) {
            $query->withCount('scanHistories');
        } else {
            $query->withCount('scans');
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        if ($request->filled('active')) {
            $query->where('active', $request->get('active') === 'true');
        }

        $users = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        $scans = $this->userScansQuery($user)
            ->orderByDesc($this->hasRealtimeScanHistories() ? 'created_at' : 'tanggal_scan')
            ->limit(50)
            ->get();

        $stats = [
            'total_scans' => $this->userScansQuery($user)->count(),
            'scans_this_month' => $this->filterThisMonth($this->userScansQuery($user))->count(),
            'scans_this_week' => $this->filterThisWeek($this->userScansQuery($user))->count(),
            'halal_scans' => $this->userScansQuery($user)->where($this->scanStatusColumn(), 'halal')->count(),
            'haram_scans' => $this->userScansQuery($user)->where($this->scanStatusColumn(), 'haram')->count(),
            'syubhat_scans' => $this->userScansQuery($user)
                ->whereIn($this->scanStatusColumn(), ['syubhat', 'diragukan'])
                ->count(),
            'most_scanned_categories' => $this->mostScannedCategories($user),
            'scan_methods' => $this->scanMethodsDistribution($user),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'scans' => $scans,
                'stats' => $stats,
            ],
        ]);
    }

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
            'data' => $user->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin users',
            ], 403);
        }

        DB::transaction(function () use ($user) {
            $this->userScansQuery($user)->delete();
            $user->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    public function scanHistory(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $query = $this->userScansQuery($user);

        if ($request->filled('date_from')) {
            $query->whereDate($this->scanDateColumn(), '>=', $request->get('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate($this->scanDateColumn(), '<=', $request->get('date_to'));
        }
        if ($request->filled('halal_status')) {
            $query->where($this->scanStatusColumn(), $request->get('halal_status'));
        }
        if ($request->filled('scan_method') && $this->hasRealtimeScanHistories()) {
            $query->where('scan_method', $request->get('scan_method'));
        }

        $scans = $query->orderByDesc($this->scanDateColumn())->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $scans,
        ]);
    }

    public function dashboard(Request $request)
    {
        $scanBaseQuery = $this->hasRealtimeScanHistories() ? ScanHistory::query() : ScanModel::query();
        $scanStatusColumn = $this->scanStatusColumn();
        $scanDateColumn = $this->scanDateColumn();

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('active', true)->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_scans' => (clone $scanBaseQuery)->count(),
            'scans_today' => (clone $scanBaseQuery)->whereDate($scanDateColumn, today())->count(),
            'scans_this_week' => (clone $scanBaseQuery)
                ->whereBetween($scanDateColumn, [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'scans_this_month' => (clone $scanBaseQuery)
                ->whereMonth($scanDateColumn, now()->month)
                ->whereYear($scanDateColumn, now()->year)
                ->count(),
            'top_users_by_scans' => $this->topUsersByScans(),
            'scan_status_distribution' => [
                'halal' => (clone $scanBaseQuery)->where($scanStatusColumn, 'halal')->count(),
                'haram' => (clone $scanBaseQuery)->whereIn($scanStatusColumn, ['haram', 'tidak halal'])->count(),
                'syubhat' => (clone $scanBaseQuery)->whereIn($scanStatusColumn, ['syubhat', 'diragukan'])->count(),
                'unknown' => (clone $scanBaseQuery)->where($scanStatusColumn, 'unknown')->count(),
            ],
            'scan_methods_distribution' => $this->scanMethodsDistribution(),
            'using_realtime_table' => $this->hasRealtimeScanHistories(),
        ];

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        }

        return view('admin.users.dashboard', compact('stats'));
    }

    public function export($id)
    {
        $user = User::findOrFail($id);
        $scanHistory = $this->userScansQuery($user)
            ->orderByDesc($this->scanDateColumn())
            ->get();

        $exportData = [
            'user' => $user->toArray(),
            'scan_history' => $scanHistory->toArray(),
            'exported_at' => now()->toIso8601String(),
        ];

        return response()->json($exportData, 200, [
            'Content-Disposition' => 'attachment; filename="user_' . $user->id_user . '_export.json"',
        ]);
    }

    private function hasRealtimeScanHistories(): bool
    {
        return Schema::hasTable('scan_histories');
    }

    private function userScansQuery(User $user)
    {
        return $this->hasRealtimeScanHistories()
            ? $user->scanHistories()
            : $user->scans();
    }

    private function scanDateColumn(): string
    {
        return $this->hasRealtimeScanHistories() ? 'created_at' : 'tanggal_scan';
    }

    private function scanStatusColumn(): string
    {
        return $this->hasRealtimeScanHistories() ? 'halal_status' : 'status_halal';
    }

    private function filterThisMonth($query)
    {
        $column = $this->scanDateColumn();
        return $query->whereMonth($column, now()->month)->whereYear($column, now()->year);
    }

    private function filterThisWeek($query)
    {
        $column = $this->scanDateColumn();
        return $query->whereBetween($column, [now()->startOfWeek(), now()->endOfWeek()]);
    }

    private function topUsersByScans()
    {
        if ($this->hasRealtimeScanHistories()) {
            return User::withCount('scanHistories')
                ->orderByDesc('scan_histories_count')
                ->limit(10)
                ->get();
        }

        return User::withCount('scans')
            ->orderByDesc('scans_count')
            ->limit(10)
            ->get();
    }

    private function mostScannedCategories(User $user)
    {
        if (!$this->hasRealtimeScanHistories()) {
            return $user->scans()
                ->join('products', 'scans.product_id', '=', 'products.id_product')
                ->leftJoin('kategori', 'products.kategori_id', '=', 'kategori.id_kategori')
                ->select(
                    DB::raw("COALESCE(kategori.nama_kategori, 'Uncategorized') as nama_kategori"),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('nama_kategori')
                ->orderByDesc('count')
                ->limit(5)
                ->get();
        }

        return collect();
    }

    private function scanMethodsDistribution(?User $user = null): array
    {
        if (!$this->hasRealtimeScanHistories()) {
            return [
                'camera' => 0,
                'manual' => 0,
                'search' => 0,
                'barcode' => $user ? $user->scans()->count() : ScanModel::count(),
            ];
        }

        $query = $user ? $user->scanHistories() : ScanHistory::query();

        return [
            'camera' => (clone $query)->where('scan_method', 'photo')->count(),
            'manual' => (clone $query)->where('scan_method', 'manual')->count(),
            'search' => (clone $query)->where('scan_method', 'text_search')->count(),
            'barcode' => (clone $query)->where('scan_method', 'barcode')->count(),
        ];
    }
}
