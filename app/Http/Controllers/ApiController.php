<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\ProductModel;
use App\Models\KategoriModel;
use App\Models\ScanModel;
use App\Models\ReportModel;

class ApiController extends Controller
{
    // ==========================================================
    // 🔑 AUTENTIKASI (REGISTER, LOGIN, LOGOUT, PROFILE)
    // ==========================================================

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username',
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'response_code' => 422,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'active' => 1,
        ]);

        $token = $user->createToken('auth_token_android')->plainTextToken;

        return response()->json([
            'response_code' => 200,
            'message' => 'Registrasi berhasil!',
            'content' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'response_code' => 401,
                'message' => 'Username atau password salah!'
            ], 401);
        }

        $user = Auth::user();
        $user->update(['last_login' => now()]);
        $token = $user->createToken('auth_token_android')->plainTextToken;

        return response()->json([
            'response_code' => 200,
            'message' => 'Login berhasil!',
            'content' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    public function logout(Request $request)
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'response_code' => 200,
            'message' => 'Logout berhasil!'
        ], 200);
    }

    public function profile()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['response_code' => 401, 'message' => 'Unauthorized'], 401);
        }

        // Hitung total scan
        $totalScans = ScanModel::where('user_id', $user->id_user)->count();
        
        // Dapatkan 5 scan terakhir
        $recentScans = ScanModel::where('user_id', $user->id_user)
            ->orderBy('tanggal_scan', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'response_code' => 200,
            'message' => 'Data profil user login',
            'content' => [
                'user' => $user,
                'stats' => [
                    'total_scans' => $totalScans,
                    'recent_scans' => $recentScans
                ]
            ]
        ], 200);
    }
    //Update data profile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['response_code' => 401, 'message' => 'Unauthorized'], 401);
        }

        $rules = [
            'full_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|unique:users,email,' . $user->id_user . ',id_user',
            'phone' => 'nullable|string|max:20',
            'blood_type' => 'nullable|string|max:5',
            'allergy' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'goal' => 'nullable|string',
            'diet_preference' => 'nullable|string',
            'activity_level' => 'nullable|string',
            'address' => 'nullable|string',
            'language' => 'nullable|string',
            'age' => 'nullable|integer',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'bmi' => 'nullable|numeric',
            'notif_enabled' => 'nullable|boolean',
            'dark_mode' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];

        $request->validate($rules);

        // Upload foto profil baru
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/profile_images', $filename);

            // Hapus foto lama jika ada
            if ($user->image) {
                $old = str_replace('/storage/', 'public/', $user->image);
                if (Storage::exists($old)) {
                    Storage::delete($old);
                }
            }

            $user->image = '/storage/profile_images/' . $filename;
        }

        // Update kolom lain
        $user->fill($request->only([
            'full_name', 'email', 'phone', 'blood_type', 'allergy', 'medical_history',
            'goal', 'diet_preference', 'activity_level', 'address', 'language',
            'age', 'height', 'weight', 'bmi', 'notif_enabled', 'dark_mode'
        ]));

        $user->save();

        return response()->json([
            'response_code' => 200,
            'message' => 'Profil berhasil diperbarui',
            'content' => $user
        ], 200);
    }
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'response_code' => 422,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'response_code' => 400,
                'message' => 'Password saat ini tidak sesuai'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'response_code' => 200,
            'message' => 'Password berhasil diupdate'
        ], 200);
    }

    // ==========================================================
    // 🧾 SCAN PRODUK (KHUSUS USER LOGIN)
    // ==========================================================
    public function storeScan(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id_product',
            'nama_produk' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'status_halal' => 'nullable|in:halal,haram,syubhat',
            'status_kesehatan' => 'nullable|in:sehat,tidak_sehat,perlu_riset',
        ]);

        $scan = ScanModel::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'nama_produk' => $request->nama_produk,
            'barcode' => $request->barcode,
            'kategori' => $request->kategori,
            'status_halal' => $request->status_halal ?? 'syubhat',
            'status_kesehatan' => $request->status_kesehatan ?? 'perlu_riset',
            'tanggal_scan' => now(),
        ]);

        return response()->json([
            'response_code' => 201,
            'message' => 'Scan berhasil dicatat',
            'content' => $scan
        ], 201);
    }

    public function indexMyScans()
    {
        $scans = ScanModel::where('user_id', Auth::id())
            ->with('product')
            ->orderBy('tanggal_scan', 'desc')
            ->get();

        return response()->json([
            'response_code' => 200,
            'message' => 'Riwayat scan Anda',
            'content' => $scans
        ], 200);
    }

    // ==========================================================
    // 📝 REPORT (LAPOR PRODUK)
    // ==========================================================
    public function storeReport(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id_product',
            'laporan' => 'required|string',
        ]);

        $report = ReportModel::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'laporan' => $request->laporan,
            'status' => 'pending',
        ]);

        return response()->json([
            'response_code' => 201,
            'message' => 'Laporan berhasil dikirim',
            'content' => $report
        ], 201);
    }

    public function indexMyReports()
    {
        $reports = ReportModel::where('user_id', Auth::id())
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'response_code' => 200,
            'message' => 'Riwayat laporan Anda',
            'content' => $reports
        ], 200);
    }

    // ==========================================================
    // 🔎 PRODUK & KATEGORI
    // ==========================================================
    public function indexProduct()
    {
        $products = ProductModel::with('kategori')->get();

        return response()->json([
            'response_code' => 200,
            'message' => 'Daftar semua produk',
            'content' => $products
        ], 200);
    }

    public function showProduct($id)
    {
        $product = ProductModel::with('kategori')->find($id);

        if (!$product) {
            return response()->json(['response_code' => 404, 'message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json([
            'response_code' => 200,
            'message' => 'Detail produk',
            'content' => $product
        ], 200);
    }

    public function searchProduct(Request $request)
    {
        $search = $request->query('q');
        if (!$search) {
            return response()->json(['response_code' => 400, 'message' => 'Parameter q wajib diisi.'], 400);
        }

        $products = ProductModel::where('nama_product', 'like', "%$search%")
            ->orWhere('barcode', 'like', "%$search%")
            ->get();

        return response()->json([
            'response_code' => 200,
            'message' => "Hasil pencarian: $search",
            'content' => $products
        ], 200);
    }

    public function scanProductByBarcode($barcode)
    {
        $product = ProductModel::where('barcode', $barcode)->first();

        if (!$product) {
            return response()->json(['response_code' => 404, 'message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json([
            'response_code' => 200,
            'message' => 'Hasil scan barcode',
            'content' => $product
        ], 200);
    }

    public function indexKategori()
    {
        $kategori = KategoriModel::all();

        return response()->json([
            'response_code' => 200,
            'message' => 'Daftar kategori',
            'content' => $kategori
        ], 200);
    }
    // ==========================================================
    // History
    // ==========================================================
    public function getScanHistory(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $status = $request->query('status');
        
        $query = ScanModel::where('user_id', Auth::id())
            ->with('product')
            ->orderBy('tanggal_scan', 'desc');
        
        if ($status) {
            $query->where('status_halal', $status);
        }
        
        $scans = $query->paginate($perPage);
        
        return response()->json([
            'response_code' => 200,
            'message' => 'Riwayat scan',
            'content' => $scans
        ], 200);
    }


    // ==========================================================
    // Status Pengguna
    // ==========================================================
    public function getUserStats()
    {
        $user = Auth::user();
        
        // Hitung total scan
        $totalScans = ScanModel::where('user_id', $user->id_user)->count();
        
        // Hitung scan berdasarkan status halal
        $halalScans = ScanModel::where('user_id', $user->id_user)
            ->where('status_halal', 'halal')
            ->count();
            
        $syubhatScans = ScanModel::where('user_id', $user->id_user)
            ->where('status_halal', 'syubhat')
            ->count();
            
        $haramScans = ScanModel::where('user_id', $user->id_user)
            ->where('status_halal', 'haram')
            ->count();
        
        // Hitung total laporan
        $totalReports = ReportModel::where('user_id', $user->id_user)->count();
        
        return response()->json([
            'response_code' => 200,
            'message' => 'Statistik pengguna',
            'content' => [
                'total_scans' => $totalScans,
                'halal_scans' => $halalScans,
                'syubhat_scans' => $syubhatScans,
                'haram_scans' => $haramScans,
                'total_reports' => $totalReports,
                'user_data' => $user
            ]
        ], 200);
    }
    // ==========================================================
    // Notifikasi
    // ==========================================================
    public function getNotifications()
    {
        $user = Auth::user();
        
        // Contoh notifikasi
        $notifications = [
            [
                'id' => 1,
                'title' => 'Scan Produk Baru',
                'message' => 'Anda baru saja melakukan scan produk "Indomie Goreng"',
                'type' => 'scan',
                'read' => false,
                'created_at' => now()->format('Y-m-d H:i:s')
            ],
            // Tambahkan notifikasi lainnya
        ];
        
        return response()->json([
            'response_code' => 200,
            'message' => 'Daftar notifikasi',
            'content' => $notifications
        ], 200);
    }
}