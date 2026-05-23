<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ScanModel;
use App\Models\ScanHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    // Menampilkan semua user dengan statistik
    public function index(Request $request)
    {
        // Stats for new view
        $totalUsers = User::count();
        $activeUsers = User::where('active', 1)->count();
        $totalScans = ScanModel::count();
        if (Schema::hasTable('scan_histories')) {
            $totalScans += ScanHistory::count();
        }
        
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
        $query = $this->buildUsersQuery($request);
        
        $users = $query->paginate(10)->withQueryString();
        
        // Scan trends by category
        $scanTrendsRaw = ScanModel::selectRaw("
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

        $totalTrendScans = max(1, (int) $scanTrendsRaw->sum('count'));
        $colors = ['#00bbc2', '#f59e0b', '#6366f1', '#10b981'];
        $scanTrends = $scanTrendsRaw->values()->map(function ($row, $index) use ($totalTrendScans, $colors) {
            return [
                'category' => $row->category,
                'count' => (int) $row->count,
                'percentage' => (int) round(($row->count / $totalTrendScans) * 100),
                'color' => $colors[$index % count($colors)],
            ];
        })->toArray();

        $topContributors = User::query()
            ->withCount('scans')
            ->withCount('scanHistories')
            ->orderByRaw('(COALESCE(scans_count,0) + COALESCE(scan_histories_count,0)) DESC')
            ->limit(3)
            ->get();

        return view('admin.user', [
            'users' => $users,
            'stats' => $stats,
            'scanTrends' => $scanTrends,
            'scan_trends' => $scanTrends,
            'topContributors' => $topContributors,
        ]);
    }

    // Edit user form
    public function edit($id_user)
    {
        $user = User::withCount('scans')->findOrFail($id_user);
        $userScans = ScanModel::where('user_id', $id_user)->orderByDesc('tanggal_scan')->limit(10)->get();
        return view('admin.user_edit', compact('user', 'userScans'));
    }

    // Detail user profile
    public function show($id_user)
    {
        $user = User::withCount('scans')->withCount('scanHistories')->findOrFail($id_user);
        
        // Latest scans from both legacy and new systems
        $scans = ScanModel::where('user_id', $id_user)->orderByDesc('tanggal_scan')->limit(10)->get();
        $scanHistories = collect();
        if (Schema::hasTable('scan_histories')) {
            $scanHistories = ScanHistory::where('user_id', $id_user)->orderByDesc('created_at')->limit(20)->get();
        }

        // Stats summary
        $stats = [
            'total_scans' => (int) ($user->scans_count ?? 0) + (int) ($user->scan_histories_count ?? 0),
            'halal_scans' => ScanModel::where('user_id', $id_user)->where('status_halal', 'halal')->count(),
            'haram_scans' => ScanModel::where('user_id', $id_user)->where('status_halal', 'tidak halal')->count(),
        ];

        return view('admin.user_show', compact('user', 'scans', 'scanHistories', 'stats'));
    }

    // Update user
    public function update(Request $request, $id_user)
    {
        $user = User::findOrFail($id_user);
        $this->normalizeUserRequest($request);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id_user . ',id_user',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id_user . ',id_user',
            'phone' => 'nullable|string|max:20',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-,A,B,AB,O',
            'allergy' => 'nullable|string|max:1000',
            'medical_history' => 'nullable|string|max:2000',
            'weight' => 'nullable|numeric|min:1|max:500',
            'height' => 'nullable|numeric|min:1|max:300',
            'role' => 'required|in:admin,user,nutritionist',
            'active' => 'required|boolean',
        ]);

        $validated['username'] = $this->generateUsername(
            $validated['username'] ?? null,
            $validated['full_name'],
            $validated['email'],
            $user->id_user
        );

        if (
            $user->role === 'admin' &&
            $validated['role'] !== 'admin' &&
            User::where('role', 'admin')->count() <= 1
        ) {
            return redirect()
                ->route('admin.user.edit', $user->id_user)
                ->withErrors(['role' => 'Admin terakhir tidak dapat diturunkan menjadi user biasa.'])
                ->withInput();
        }

        // Calculate BMI
        if ($validated['weight'] && $validated['height']) {
            $heightInMeters = $validated['height'] / 100;
            $validated['bmi'] = round($validated['weight'] / ($heightInMeters * $heightInMeters), 1);
        } else {
            $validated['bmi'] = null;
        }

        $user->update($validated);

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

    public function export(Request $request)
    {
        $users = $this->buildUsersQuery($request)->get();
        $fileName = 'halalytics-users-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ];

        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID',
                'Full Name',
                'Username',
                'Email',
                'Phone',
                'Blood Type',
                'Role',
                'Status',
                'Total Scans',
                'Created At',
            ]);

            foreach ($users as $user) {
                $totalScans = (int) ($user->scans_count ?? 0) + (int) ($user->scan_histories_count ?? 0);

                fputcsv($handle, [
                    $user->id_user,
                    $user->full_name,
                    $user->username,
                    $user->email,
                    $user->phone,
                    $user->blood_type,
                    $user->role,
                    (int) ($user->active ?? 1) === 1 ? 'Active' : 'Blocked',
                    $totalScans,
                    optional($user->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $fileName, $headers);
    }

    // Store new user
    public function store(Request $request)
    {
        $this->normalizeUserRequest($request);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user,nutritionist,ahli_gizi',
            'phone' => 'nullable|string|max:20',
            'blood_type' => 'nullable|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-,A,B,AB,O',
            'allergy' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'weight' => 'nullable|numeric|min:1|max:500',
            'height' => 'nullable|numeric|min:1|max:300',
            'active' => 'nullable|boolean',
        ]);

        $validated['username'] = $this->generateUsername(
            $validated['username'] ?? null,
            $validated['full_name'],
            $validated['email']
        );

        User::create([
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'blood_type' => $validated['blood_type'] ?? null,
            'allergy' => $validated['allergy'] ?? null,
            'medical_history' => $validated['medical_history'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'height' => $validated['height'] ?? null,
            'bmi' => (isset($validated['weight']) && isset($validated['height']) && $validated['height'] > 0) 
                ? round($validated['weight'] / (pow($validated['height'] / 100, 2)), 1) 
                : null,
            'active' => array_key_exists('active', $validated) ? (bool) $validated['active'] : 1,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan');
    }

    // Toggle status active/non-active
    public function toggleStatus(Request $request, $id_user)
    {
        $user = User::findOrFail($id_user);

        // Jangan izinkan blokir admin
        if ($user->role === 'admin') {
            $response = [
                'success' => false,
                'message' => 'Akun Administrator tidak dapat diblokir!'
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 403);
            }

            return redirect()->route('admin.user.index')->with('error', $response['message']);
        }

        $user->active = $user->active == 1 ? 0 : 1;
        $user->save();

        $message = 'Status akun berhasil diubah!';
        $response = [
            'success' => true,
            'status' => $user->active ? 'active' : 'blocked',
            'message' => $message
        ];

        if ($request->expectsJson()) {
            return response()->json($response);
        }

        return redirect()->route('admin.user.index')->with('success', $message);
    }

    // Change user role (admin/user)
    public function changeRole(Request $request, $id_user)
    {
        $user = User::findOrFail($id_user);

        // Protect last admin from demotion
        if ($user->role === 'admin' && $request->input('role') !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                $response = [
                    'success' => false,
                    'message' => 'Tidak dapat menurunkan admin terakhir!'
                ];

                if ($request->expectsJson()) {
                    return response()->json($response, 403);
                }

                return redirect()->route('admin.user.index')->with('error', $response['message']);
            }
        }

        $newRole = $request->input('role', 'user');
        if (!in_array($newRole, ['admin', 'user'])) {
            $response = [
                'success' => false,
                'message' => 'Role tidak valid!'
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 400);
            }

            return redirect()->route('admin.user.index')->with('error', $response['message']);
        }

        $user->role = $newRole;
        $user->save();

        $message = 'Role berhasil diubah menjadi ' . strtoupper($newRole) . '!';
        $response = [
            'success' => true,
            'role' => $user->role,
            'message' => $message
        ];

        if ($request->expectsJson()) {
            return response()->json($response);
        }

        return redirect()->route('admin.user.index')->with('success', $message);
    }

    private function normalizeUserRequest(Request $request): void
    {
        $request->merge([
            'full_name' => trim((string) ($request->input('full_name') ?? $request->input('name') ?? '')),
            'email' => trim((string) $request->input('email', '')),
            'phone' => trim((string) ($request->input('phone') ?? $request->input('phone_number') ?? '')),
            'blood_type' => $this->normalizeBloodType($request->input('blood_type')),
            'allergy' => $this->normalizeTextField($request->input('allergy', $request->input('allergies'))),
            'medical_history' => $this->normalizeTextField($request->input('medical_history')),
            'weight' => $request->filled('weight') ? floatval($request->input('weight')) : null,
            'height' => $request->filled('height') ? floatval($request->input('height')) : null,
        ]);
    }

    private function normalizeBloodType(mixed $bloodType): ?string
    {
        $value = trim((string) ($bloodType ?? ''));

        if ($value === '' || Str::lower($value) === 'tidak tahu' || Str::lower($value) === 'unknown') {
            return null;
        }

        return strtoupper($value);
    }

    private function normalizeTextField(mixed $value): ?string
    {
        if (is_array($value)) {
            $value = implode(', ', array_filter(array_map('trim', $value)));
        }

        $text = trim((string) ($value ?? ''));

        return $text !== '' ? $text : null;
    }

    private function buildUsersQuery(Request $request)
    {
        $query = User::withCount('scans')->withCount('scanHistories');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('id_user', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->where('active', 1);
            } elseif ($request->status === 'blocked') {
                $query->where('active', 0);
            }
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = strtolower((string) $request->get('order', 'desc'));
        $allowedSort = ['created_at', 'username', 'scans_count'];

        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'created_at';
        }
        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        if ($sortBy === 'scans_count') {
            $query->orderByRaw('(COALESCE(scans_count,0) + COALESCE(scan_histories_count,0)) ' . $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }

    private function generateUsername(?string $username, string $fullName, string $email, ?int $ignoreUserId = null): string
    {
        $candidate = Str::of($username ?: Str::before($email, '@') ?: $fullName)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9_]+/', '_')
            ->trim('_')
            ->value();

        $candidate = $candidate !== '' ? $candidate : 'user';
        $base = Str::limit($candidate, 40, '');
        $suffix = 0;

        while (
            User::query()
                ->when($ignoreUserId, fn ($query) => $query->where('id_user', '!=', $ignoreUserId))
                ->where('username', $candidate)
                ->exists()
        ) {
            $suffix++;
            $candidate = Str::limit($base, max(1, 40 - strlen((string) $suffix) - 1), '') . '_' . $suffix;
        }

        return $candidate;
    }
}
