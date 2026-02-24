<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ScanModel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminUserController extends Controller
{
    // Menampilkan semua user dengan statistik
    public function admin_user(Request $request)
    {
        // Stats for new view
        $totalUsers = User::count();
        $activeUsers = User::where('active', 1)->count();
        $totalScans = ScanModel::count();
        
        // Calculate user growth
        $usersLastMonth = User::where('created_at', '>=', Carbon::now()->subMonth())->count();
        $usersPrevMonth = User::whereBetween('created_at', [Carbon::now()->subMonths(2), Carbon::now()->subMonth()])->count();
        $userChange = $usersPrevMonth > 0 ? round((($usersLastMonth - $usersPrevMonth) / $usersPrevMonth) * 100) : 0;
        
        $stats = [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'total_scans' => $totalScans,
            'user_change' => abs($userChange)
        ];
        
        // Users with scan count
        $query = User::withCount('scans');
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('id_user', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->where('active', 1);
            } elseif ($request->status === 'blocked') {
                $query->where('active', 0);
            }
        }
        
        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $users = $query->paginate(10)->withQueryString();
        
        // Scan trends by category
        $scanTrends = ScanModel::selectRaw("
            CASE 
                WHEN kategori LIKE '%dairy%' OR kategori LIKE '%susu%' THEN 'Dairy & Poultry'
                WHEN kategori LIKE '%snack%' THEN 'Processed Snacks'
                WHEN kategori LIKE '%cosmetic%' OR kategori LIKE '%beauty%' THEN 'Beauty & Cosmetics'
                ELSE 'Others'
            END as category,
            COUNT(*) as count
        ")
        ->groupBy('category')
        ->orderByDesc('count')
        ->limit(4)
        ->get();

        return view('admin.user-new', compact('users', 'stats', 'scanTrends'));
    }

    // Edit user form
    public function edit($id_user)
    {
        $user = User::withCount('scans')->findOrFail($id_user);
        $userScans = ScanModel::where('user_id', $id_user)->orderByDesc('tanggal_scan')->limit(10)->get();
        return view('admin.user_edit', compact('user', 'userScans'));
    }

    // Update user
    public function update(Request $request, $id_user)
    {
        $user = User::findOrFail($id_user);

        $user->update($request->only(['full_name', 'username', 'email', 'role', 'active', 'phone']));

        return redirect()->route('admin.user.index')->with('success', 'User berhasil diperbarui');
    }

    // Hapus user
    public function hapus($id_user)
    {
        $user = User::findOrFail($id_user);

        // Jangan izinkan hapus admin
        if ($user->role === 'admin') {
            return redirect()->route('admin.user.index')->with('error', 'Akun Administrator tidak dapat dihapus!');
        }

        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus');
    }

    // Create user form
    public function create()
    {
        return view('admin.user_create');
    }

    // Store new user
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user',
        ]);

        User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'active' => 1,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan');
    }

    // Toggle status active/non-active
    public function toggleStatus($id_user)
    {
        $user = User::findOrFail($id_user);

        // Jangan izinkan blokir admin
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Administrator tidak dapat diblokir!'
            ], 403);
        }

        $user->active = $user->active == 1 ? 0 : 1;
        $user->save();

        return response()->json([
            'success' => true,
            'status' => $user->active ? 'active' : 'blocked',
            'message' => 'Status akun berhasil diubah!'
        ]);
    }

    // Change user role (admin/user)
    public function changeRole(Request $request, $id_user)
    {
        $user = User::findOrFail($id_user);

        // Protect last admin from demotion
        if ($user->role === 'admin' && $request->input('role') !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menurunkan admin terakhir!'
                ], 403);
            }
        }

        $newRole = $request->input('role', 'user');
        if (!in_array($newRole, ['admin', 'user'])) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak valid!'
            ], 400);
        }

        $user->role = $newRole;
        $user->save();

        return response()->json([
            'success' => true,
            'role' => $user->role,
            'message' => 'Role berhasil diubah menjadi ' . strtoupper($newRole) . '!'
        ]);
    }
}
