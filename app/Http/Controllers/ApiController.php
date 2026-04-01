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
use App\Models\UmkmProduct;
use App\Models\ProductVerificationRequest;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    protected $universalService;

    public function __construct(\App\Services\UniversalProductService $universalService)
    {
        $this->universalService = $universalService;
    }

    // ... (rest of methods) ...

    public function scanProductByBarcode($barcode)
    {
        // Use Universal Service
        $result = $this->universalService->findProduct($barcode);

        if ($result['found']) {
            $productData = $result['standardized'];
            $source = $result['source'];

            // Determine ID
            $model = $result['data'];
            $productId = 0;
            if ($model instanceof \App\Models\BpomData) {
                $productId = $model->id;
            } elseif ($model instanceof \App\Models\ProductModel) {
                $productId = $model->id_product;
            }

            // Construct Halal Info
            $halalInfo = [
                'halal_status' => $productData['status_halal'] ?? 'unknown',
                'halal_certificate_number' => $productData['halal_certificate'],
                'certification_body' => null,
                'certificate_valid_until' => null,
                'last_checked_at' => now(), // Mock
                'source' => $source
            ];

            return response()->json([
                'success' => true, // Standard format
                'response_code' => 200, // Legacy support
                'message' => 'Produk ditemukan',
                'data' => [
                    'product' => [
                        'id' => $productId,
                        'barcode' => $productData['barcode'],
                        'name' => $productData['name'],
                        'brand' => $productData['brand'],
                        'image' => $productData['image_url'],
                        'image_front_url' => $productData['image_url'],
                        'ingredients_text' => $productData['ingredients_text'],
                        'category' => $productData['category'],
                        'nutriscore' => $productData['nutriscore'] ?? null,
                        'additives' => $productData['additives'] ?? [],
                        'allergens' => $productData['allergens'] ?? []
                    ],
                    'halal_info' => $halalInfo,
                    'halal_source' => $source
                ],
                // Legacy content for older apps (optional, but good for safety)
                'content' => $result['data'] 
            ], 200);
        }

        return response()->json([
            'success' => false,
            'response_code' => 404,
            'message' => 'Produk tidak ditemukan'
        ], 404);
    }

    // ==========================================================
    // 🔑 AUTENTIKASI (REGISTER, LOGIN, LOGOUT, PROFILE)
    // ==========================================================

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username',
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
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

        // Check if user exists first
        $user = \App\Models\User::where('username', $credentials['username'])->first();
        
        if (!$user) {
            return response()->json([
                'response_code' => 404,
                'message' => 'User tidak ditemukan!'
            ], 404);
        }

        // Check if user is active
        if (!$user->active) {
            return response()->json([
                'response_code' => 403,
                'message' => 'Akun tidak aktif!'
            ], 403);
        }

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
                    'recent_scans' => $recentScans,
                    'streak' => [
                        'current' => $user->current_streak,
                        'longest' => $user->longest_streak,
                        'last_active' => $user->last_active_date
                    ]
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
            
            // New profile fields
            'avatar_url' => 'nullable|url',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:500',
            'dietary_preferences' => 'nullable|array',
            'allergies' => 'nullable|array',
            'notifications_enabled' => 'nullable|boolean',
            'profile_visibility' => 'nullable|in:public,private,friends',
            'show_health_tips' => 'nullable|boolean',
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
            'age', 'height', 'weight', 'bmi', 'notif_enabled', 'dark_mode',
            'avatar_url', 'birth_date', 'gender', 'bio', 'dietary_preferences',
            'allergies', 'notifications_enabled', 'profile_visibility', 'show_health_tips'
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
            'product_id' => 'nullable|integer|exists:products,id_product',
            'nama_produk' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'status_halal' => 'nullable|in:halal,haram,syubhat',
            'status_kesehatan' => 'nullable|in:sehat,tidak_sehat,perlu_riset',
        ]);

        $scan = ScanModel::create([
            'user_id' => Auth::user()->id_user,
            'product_id' => $request->product_id ?: null,
            'nama_produk' => $request->nama_produk,
            'barcode' => $request->barcode,
            'kategori' => $request->kategori,
            'status_halal' => $request->status_halal ?? 'syubhat',
            'status_kesehatan' => $request->status_kesehatan ?? 'perlu_riset',
            'tanggal_scan' => now(),
        ]);

        // Create Admin Notification for Real-time Visibility
        try {
            \App\Http\Controllers\Admin\AdminNotificationController::createNotification(
                'scan',
                'Scan Produk Baru (Legacy)',
                "User " . Auth::user()->username . " melakukan scan produk: " . $request->nama_produk,
                ['scan_id' => $scan->id, 'product_name' => $request->nama_produk]
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create admin notification for legacy scan: ' . $e->getMessage());
        }

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
            'reason' => 'required|string',
            'laporan' => 'nullable|string',
            'evidence_image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('evidence_image')) {
            $imagePath = $request->file('evidence_image')->store('reports', 'public');
        }

        $report = ReportModel::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'reason' => $request->reason,
            'laporan' => $request->laporan,
            'evidence_image' => $imagePath,
            'status' => 'pending',
        ]);

        // Crowd-Sourced Safety Check
        $pendingReportsCount = ReportModel::where('product_id', $request->product_id)
            ->where('status', 'pending')
            ->count();

        if ($pendingReportsCount >= 5) {
            $product = ProductModel::find($request->product_id);
            if ($product && $product->verification_status != 'forgery_confirmed') {
                $product->update([
                    'verification_status' => 'suspicious',
                    'needs_manual_review' => true
                ]);
            }
        }

        // Create Admin Notification
        try {
            \App\Http\Controllers\Admin\AdminNotificationController::createNotification(
                'report',
                'Laporan Kejanggalan Produk',
                "User " . Auth::user()->username . " melaporkan kejanggalan pada produk ID " . $request->product_id . " (" . $request->reason . ")",
                ['report_id' => $report->id_report, 'product_id' => $request->product_id]
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal membuat notifikasi admin: ' . $e->getMessage());
        }

        return response()->json([
            'response_code' => 201,
            'message' => 'Laporan berhasil dikirim. Tim kami akan segera meninjau.',
            'content' => $report,
            'is_suspicious' => $pendingReportsCount >= 5
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

        // Hybrid Search (Local + BPOM + External)
        $products = $this->universalService->search($search);

        return response()->json([
            'response_code' => 200,
            'message' => "Hasil pencarian: $search",
            'content' => $products
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

    /**
     * Add new scan history
     */
    public function addScanHistory(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['response_code' => 401, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'barcode' => 'nullable|string',
            'product_name' => 'required|string',
            'brand' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'image_url' => 'nullable|url',
            'scan_type' => 'required|in:camera,manual,search',
            'halal_status' => 'required|in:halal,haram,syubhat,unknown',
            'confidence_score' => 'nullable|integer|min:0|max:100',
            'suspicious_ingredients' => 'nullable|array',
            'calories' => 'nullable|integer',
            'protein' => 'nullable|numeric',
            'carbs' => 'nullable|numeric',
            'fat' => 'nullable|numeric',
            'sugar' => 'nullable|numeric',
            'health_score' => 'nullable|integer|min:0|max:100',
            'source' => 'nullable|string',
            'raw_data' => 'nullable|array',
        ]);

        // Create scan history record
        $scanHistory = ScanHistory::create([
            'user_id' => $user->id_user,
            'scannable_type' => 'App\\Models\\ProductModel', // Default to ProductModel
            'scannable_id' => 0, // Will be updated if product exists
            'product_name' => $validated['product_name'],
            'product_image' => $validated['image_url'] ?? null,
            'barcode' => $validated['barcode'],
            'halal_status' => $validated['halal_status'],
            'scan_method' => $validated['scan_type'],
            'source' => $validated['source'] ?? 'open_food_facts',
            'confidence_score' => $validated['confidence_score'],
            'nutrition_snapshot' => [
                'calories' => $validated['calories'],
                'protein' => $validated['protein'],
                'carbs' => $validated['carbs'],
                'fat' => $validated['fat'],
                'sugar' => $validated['sugar'],
            ],
            'suspicious_ingredients' => $validated['suspicious_ingredients'] ?? [],
            'health_score' => $validated['health_score'],
            'raw_data' => $validated['raw_data'] ?? [],
        ]);

        // Update user statistics
        $user->increment('total_scans');
        if ($validated['halal_status'] === 'halal') {
            $user->increment('halal_products_count');
        }
        $user->save(); // Ensure persistent update

        // Create Admin Notification for Real-time Visibility
        try {
            \App\Http\Controllers\Admin\AdminNotificationController::createNotification(
                'scan',
                'Scan Produk Baru (Manual/Search)',
                "User " . $user->username . " melakukan scan/cek produk: " . $scanHistory->product_name,
                ['scan_id' => $scanHistory->id, 'product_name' => $scanHistory->product_name]
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create admin notification for manual scan: ' . $e->getMessage());
        }

        return response()->json([
            'response_code' => 201,
            'message' => 'Scan history added successfully',
            'content' => $scanHistory
        ], 201);
    }

    // ==========================================================
    // Status Pengguna
    // ==========================================================
    public function getUserStats()
    {
        $user = Auth::user();
        $userId = $user->id_user;
        
        // Unify Statistics: Combine Legacy (ScanModel) and Realtime (ScanHistory)
        $legacyScans = ScanModel::where('user_id', $userId)->count();
        $realtimeScans = \App\Models\ScanHistory::where('user_id', $userId)->count();
        $totalScans = $legacyScans + $realtimeScans;
        
        // Hitung scan berdasarkan status halal
        $legacyHalal = ScanModel::where('user_id', $userId)->where('status_halal', 'halal')->count();
        $realtimeHalal = \App\Models\ScanHistory::where('user_id', $userId)->where('halal_status', 'halal')->count();
        $halalScans = $legacyHalal + $realtimeHalal;
            
        $legacySyubhat = ScanModel::where('user_id', $userId)->where('status_halal', 'syubhat')->count();
        $realtimeSyubhat = \App\Models\ScanHistory::where('user_id', $userId)->where('halal_status', 'syubhat')->count();
        $syubhatScans = $legacySyubhat + $realtimeSyubhat;
            
        $legacyHaram = ScanModel::where('user_id', $userId)->where('status_halal', 'haram')->count();
        $realtimeHaram = \App\Models\ScanHistory::where('user_id', $userId)->where('halal_status', 'haram')->count();
        $haramScans = $legacyHaram + $realtimeHaram;
        
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
                'streak' => [
                    'current' => $user->current_streak,
                    'longest' => $user->longest_streak,
                    'last_active' => $user->last_active_date
                ],
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
    public function getBanners()
    {
        $banners = \App\Models\Banner::where('is_active', true)
            ->orderBy('position', 'asc')
            ->get();

        return response()->json([
            'response_code' => 200,
            'message' => 'Daftar banner slider',
            'content' => $banners
        ], 200);
    }

    // ==========================================================
    // 🛡️ PREMIUM FEATURES (ALLERGY, MAPS, STATS, SUBSTITUTION)
    // ==========================================================

    /**
     * Update user allergies / medical history
     */
    public function updateAllergies(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['response_code' => 401, 'message' => 'Unauthorized'], 401);

        $user->update([
            'allergy' => $request->allergy,
            'medical_history' => $request->medical_history
        ]);

        return response()->json([
            'response_code' => 200,
            'message' => 'Data alergi diperbarui!',
            'content' => $user
        ], 200);
    }

    /**
     * Get nearby UMKM products based on location
     */
    public function getNearbyUmkm(Request $request)
    {
        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->radius ?? 5; // km

        if (!$lat || !$lng) {
            return response()->json(['response_code' => 400, 'message' => 'Lat & Lng required'], 400);
        }

        // Haversine formula
        $umkms = UmkmProduct::select('*')
            ->selectRaw("(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$lat, $lng, $lat])
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->get();

        return response()->json([
            'response_code' => 200,
            'message' => 'UMKM terdekat ditemukan',
            'content' => $umkms
        ], 200);
    }

    /**
     * Submit request for product verification
     */
    public function requestVerification(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['response_code' => 401, 'message' => 'Unauthorized'], 401);

        $validator = Validator::make($request->all(), [
            'barcode' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['response_code' => 422, 'message' => 'Barcode wajib diisi'], 422);
        }

        $verificationRequest = ProductVerificationRequest::create([
            'barcode' => $request->barcode,
            'product_name' => $request->product_name,
            'user_id' => $user->id_user,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return response()->json([
            'response_code' => 200,
            'message' => 'Permintaan verifikasi dikirim!',
            'content' => $verificationRequest
        ], 200);
    }

    /**
     * Get weekly scan statistics for current user
     */
    public function getWeeklyStats(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['response_code' => 401, 'message' => 'Unauthorized'], 401);

        $stats = ScanModel::where('user_id', $user->id_user)
            ->where('tanggal_scan', '>=', now()->subDays(7))
            ->selectRaw('DATE(tanggal_scan) as date, status, COUNT(*) as count')
            ->groupBy('date', 'status')
            ->get();

        return response()->json([
            'response_code' => 200,
            'message' => 'Statistik mingguan',
            'content' => $stats
        ], 200);
    }

    /**
     * Get halal alternatives for a specific product category
     */
    public function getRecommendations(Request $request)
    {
        $category = $request->category;
        
        if (!$category) {
            return response()->json(['response_code' => 400, 'message' => 'Category required'], 400);
        }

        $recommendations = ProductModel::where('kategori', 'like', "%$category%")
            ->where('status', 'halal')
            ->where('is_verified', 1)
            ->limit(5)
            ->get();

        return response()->json([
            'response_code' => 200,
            'message' => 'Rekomendasi produk halal',
            'content' => $recommendations
        ], 200);
    }
}
