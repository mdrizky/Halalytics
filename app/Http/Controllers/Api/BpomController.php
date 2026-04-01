<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BpomData;
use App\Services\BpomService;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BpomController extends Controller
{
    protected $bpomService;
    protected $gemini;

    public function __construct(BpomService $bpomService, GeminiService $gemini)
    {
        $this->bpomService = $bpomService;
        $this->gemini = $gemini;
    }

    /**
     * Cari produk BPOM.
     * Default hanya data resmi/lokal (tanpa fallback AI).
     * Kirim include_ai=true jika ingin fallback AI.
     */
    public function searchBpom(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1',
            'kategori' => 'nullable|string',
        ]);

        $result = $this->bpomService->search(
            $request->input('q'),
            $request->input('kategori')
        );

        return response()->json([
            'success' => true,
            'source' => $result['source'],
            'total' => $result['total'],
            'data' => $result['results'],
            'message' => $result['message'] ?? 'Pencarian BPOM selesai.',
            'session_info' => [
                'sumber' => match ($result['source']) {
                    'database_lokal' => 'Database Lokal Halalytics',
                    'bpom_public_api' => 'BPOM Public API',
                    'bpom_scraping' => 'Situs Resmi BPOM',
                    default => 'Fallback Halalytics',
                },
                'referensi' => 'BPOM RI',
                'disclaimer' => $result['message'] ?? 'Data BPOM ditampilkan dari sumber terbaik yang tersedia saat ini.',
            ],
        ]);
    }

    /**
     * Verifikasi nomor registrasi BPOM (NA/MD/TR/SD/D).
     * Default hanya data resmi/lokal (tanpa fallback AI).
     * Kirim include_ai=true jika ingin fallback AI.
     */
    public function checkRegistration(Request $request)
    {
        $request->validate([
            'code' => 'required|string|min:5',
        ]);
        $result = $this->bpomService->checkRegistration($request->input('code'));

        return response()->json([
            'success' => $result['found'],
            'source' => $result['source'],
            'data' => $result['data'],
            'message' => $result['message'] ?? 'Verifikasi BPOM selesai.',
            'session_info' => [
                'sumber' => match ($result['source']) {
                    'database_lokal' => 'Database Lokal Halalytics',
                    'bpom_public_api' => 'BPOM Public API',
                    'bpom_scraping' => 'Situs Resmi BPOM',
                    default => 'Fallback Halalytics',
                },
                'disclaimer' => $result['message'] ?? 'Data BPOM ditampilkan dari sumber terbaik yang tersedia saat ini.',
            ],
        ], $result['found'] ? 200 : 404);
    }

    public function sync(Request $request)
    {
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:250',
        ]);

        $result = $this->bpomService->syncLatest((int) $request->input('limit', 100));

        return response()->json([
            'success' => true,
            'message' => 'Sinkronisasi BPOM selesai.',
            'data' => $result,
        ]);
    }

    /**
     * Analisis produk lengkap (keamanan + halal + BPOM)
     */
    public function analyzeProduct(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'ingredients_text' => 'nullable|string',
            'category' => 'nullable|string',
            'barcode' => 'nullable|string',
            'image' => 'nullable|string', // base64
            'family_id' => 'nullable|integer',
        ]);

        $productName = $request->product_name;
        $ingredientsText = $request->ingredients_text ?? '';
        $category = $request->category ?? 'umum';
        $familyId = $request->family_id;

        $user = Auth::user();
        $userContext = $this->resolveHealthContext($user, $familyId);

        try {
            $aiResult = $this->gemini->analyzeProductSafety($productName, $ingredientsText, $category, $userContext);

            // Simpan ke database
            $saved = BpomData::updateOrCreate(
                ['nama_produk' => $productName, 'barcode' => $request->barcode],
                [
                    'kategori' => $category,
                    'merk' => $aiResult['nama_produk'] ?? $productName,
                    'ingredients_text' => $ingredientsText,
                    'analisis_kandungan' => json_encode($aiResult),
                    'status_keamanan' => $aiResult['status_keamanan'] ?? 'aman',
                    'skor_keamanan' => $aiResult['skor_keamanan'] ?? null,
                    'status_halal' => $aiResult['status_halal'] ?? 'belum_diverifikasi',
                    'analisis_halal' => json_encode($aiResult['alasan_halal'] ?? null),
                    'barcode' => $request->barcode,
                    'sumber_data' => 'ai',
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $saved,
                'analysis' => $aiResult,
                'session_info' => [
                    'sumber' => 'Analisis AI Halalytics',
                    'referensi' => 'Database Internasional & Standar BPOM',
                    'disclaimer' => 'Status halal resmi harus dikonfirmasi dengan sertifikat MUI/BPJPH.'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Product Analysis Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menganalisis produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle legacy/incorrect include_ai payload (e.g. include_a[], include_ai[]).
     */
    private function normalizeIncludeAi(Request $request): void
    {
        $includeAi = $request->input('include_ai');
        $includeA = $request->input('include_a');

        if (is_array($includeAi)) {
            $includeAi = reset($includeAi);
        }
        if (is_array($includeA)) {
            $includeA = reset($includeA);
        }

        if ($includeAi === null && $includeA !== null) {
            $includeAi = $includeA;
        }

        if ($includeAi !== null) {
            $normalized = filter_var($includeAi, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            $request->merge([
                'include_ai' => $normalized ?? false,
                'include_a' => $normalized ?? false,
            ]);
        }
    }

    private function officialBpomOnlyQuery()
    {
        return BpomData::query()
            ->whereIn('verification_status', ['verified', 'pending'])
            ->whereIn('sumber_data', ['bpom_resmi', 'bpom']);
    }

    /**
     * Deteksi kategori dari pola kode registrasi BPOM
     */
    private function detectKategoriFromCode($code)
    {
        if (preg_match('/^NA/i', $code)) return 'kosmetik';
        if (preg_match('/^NC/i', $code)) return 'kosmetik';
        if (preg_match('/^ND/i', $code)) return 'kosmetik';
        if (preg_match('/^NE/i', $code)) return 'kosmetik';
        if (preg_match('/^MD/i', $code)) return 'pangan';
        if (preg_match('/^ML/i', $code)) return 'pangan';
        if (preg_match('/^D/i', $code))  return 'obat';
        if (preg_match('/^TR/i', $code)) return 'obat_tradisional';
        if (preg_match('/^TI/i', $code)) return 'obat_tradisional';
        if (preg_match('/^HT/i', $code)) return 'obat_tradisional';
        if (preg_match('/^FF/i', $code)) return 'obat_tradisional';
        if (preg_match('/^SD/i', $code)) return 'suplemen';
        if (preg_match('/^SI/i', $code)) return 'suplemen';
        if (preg_match('/^SL/i', $code)) return 'suplemen';
        return 'umum';
    }

    /**
     * Helper to resolve health context for either the main user or a family member
     */
    private function resolveHealthContext($user, $familyId = null)
    {
        if ($familyId) {
            $family = \App\Models\FamilyProfile::where('user_id', $user->id_user)->find($familyId);
            if ($family) {
                return [
                    'name' => $family->name,
                    'is_family_member' => true,
                    'age' => $family->age,
                    'gender' => $family->gender,
                    'medical_history' => $family->medical_history,
                    'allergies' => $family->allergies,
                    'diabetes' => str_contains(strtolower($family->medical_history ?? ''), 'diabetes')
                ];
            }
        }

        return [
            'name' => $user->full_name,
            'is_family_member' => false,
            'age' => $user->age,
            'gender' => $user->gender,
            'medical_history' => $user->medical_history,
            'allergies' => $user->allergy,
            'diabetes' => $user->has_diabetes
        ];
    }

    public function indexCosmetics(Request $request)
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 50);
        $search = $request->query('search');

        $query = BpomData::where('kategori', 'kosmetik');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $cosmetics = $query->latest()->paginate($limit);

        if ($cosmetics->total() === 0 && empty($search)) {
            $fallbackItems = collect($this->fallbackCosmetics())->take($limit)->values();
            return response()->json([
                'success' => true,
                'message' => 'Daftar kosmetik fallback dimuat',
                'data' => $fallbackItems,
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => $fallbackItems->count(),
                    'fallback_mode' => true,
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar kosmetik berhasil dimuat',
            'data' => $cosmetics->items(),
            'meta' => [
                'current_page' => $cosmetics->currentPage(),
                'last_page' => $cosmetics->lastPage(),
                'total' => $cosmetics->total(),
                'fallback_mode' => false,
            ]
        ]);
    }

    public function showCosmetics($id)
    {
        $cosmetic = BpomData::where('kategori', 'kosmetik')->find($id);

        if (!$cosmetic) {
            return response()->json([
                'success' => false,
                'message' => 'Kosmetik tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kosmetik',
            'data' => $cosmetic
        ]);
    }

    private function fallbackCosmetics(): array
    {
        return [
            [
                'id' => null,
                'nama_produk' => 'Hydrating Face Wash',
                'merk' => 'Sample Beauty',
                'kategori' => 'kosmetik',
                'status_keamanan' => 'aman',
                'verification_status' => 'pending',
                'sumber_data' => 'fallback_seed',
            ],
            [
                'id' => null,
                'nama_produk' => 'Daily Sunscreen SPF 50',
                'merk' => 'Sample Beauty',
                'kategori' => 'kosmetik',
                'status_keamanan' => 'aman',
                'verification_status' => 'pending',
                'sumber_data' => 'fallback_seed',
            ],
            [
                'id' => null,
                'nama_produk' => 'Vitamin C Serum',
                'merk' => 'Sample Beauty',
                'kategori' => 'kosmetik',
                'status_keamanan' => 'waspada',
                'verification_status' => 'pending',
                'sumber_data' => 'fallback_seed',
            ],
        ];
    }
}
