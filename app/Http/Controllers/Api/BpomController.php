<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BpomData;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BpomController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Cari produk BPOM — cek database lokal dulu, kalau kosong tanya AI
     */
    public function searchBpom(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);
        $query = $request->q;

        // 1. Cek database lokal dulu (termasuk hasil AI sebelumnya)
        $localResults = BpomData::where(function ($q) use ($query) {
                $q->where('nama_produk', 'LIKE', "%{$query}%")
                  ->orWhere('nomor_reg', 'LIKE', "%{$query}%")
                  ->orWhere('merk', 'LIKE', "%{$query}%");
            })
            ->orderBy('nama_produk')
            ->limit(20)
            ->get();

        if ($localResults->count() >= 5) {
            return response()->json([
                'success' => true,
                'source' => 'database_lokal',
                'total' => $localResults->count(),
                'data' => $localResults,
                'session_info' => [
                    'sumber' => 'Database Lokal Halalytics',
                    'referensi' => 'Data Publik BPOM RI',
                    'disclaimer' => 'Untuk validitas hukum, periksa situs resmi cekbpom.pom.go.id'
                ]
            ]);
        }

        // 2. Kalau lokal kurang dari 5, tanya AI untuk data tambahan
        try {
            $aiResult = $this->gemini->analyzeBpomProduct($query);

            // Simpan hasil AI ke database lokal (auto-populate)
            if (isset($aiResult['found']) && $aiResult['found']) {
                $saved = BpomData::updateOrCreate(
                    ['nomor_reg' => $aiResult['nomor_reg'] ?? null, 'nama_produk' => $aiResult['nama_produk'] ?? $query],
                    [
                        'kategori' => $aiResult['kategori'] ?? 'umum',
                        'nama_produk' => $aiResult['nama_produk'] ?? $query,
                        'merk' => $aiResult['merk'] ?? null,
                        'pendaftar' => $aiResult['pendaftar'] ?? null,
                        'alamat_produsen' => $aiResult['alamat_produsen'] ?? null,
                        'bentuk_sediaan' => $aiResult['bentuk_sediaan'] ?? null,
                        'status_keamanan' => $aiResult['status_keamanan'] ?? 'aman',
                        'status_halal' => 'belum_diverifikasi',
                        'analisis_halal' => json_encode($aiResult['analisis_halal'] ?? null),
                        'sumber_data' => 'ai',
                    ]
                );

                $localResults = $localResults->push($saved);
            }

            return response()->json([
                'success' => true,
                'source' => 'hybrid_ai',
                'total' => $localResults->count(),
                'data' => $localResults,
                'ai_analysis' => $aiResult,
                'session_info' => [
                    'sumber' => 'Analisis AI + Database Publik BPOM RI',
                    'referensi' => 'Metadata Publik BPOM',
                    'disclaimer' => 'Data ditampilkan berdasarkan informasi publik. Untuk validitas hukum, periksa situs resmi BPOM.'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('BPOM AI Search Error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'source' => 'database_lokal',
                'total' => $localResults->count(),
                'data' => $localResults,
                'session_info' => [
                    'sumber' => 'Database Lokal',
                    'disclaimer' => 'AI sedang tidak tersedia, menampilkan data lokal.'
                ]
            ]);
        }
    }

    /**
     * Verifikasi nomor registrasi BPOM (NA/MD/TR/SD/D)
     */
    public function checkRegistration(Request $request)
    {
        $request->validate(['code' => 'required|string|min:5']);
        $code = strtoupper(trim($request->code));

        // Deteksi kategori dari pola kode
        $kategori = $this->detectKategoriFromCode($code);

        // Cek database lokal
        $existing = BpomData::where('nomor_reg', $code)->first();
        if ($existing) {
            return response()->json([
                'success' => true,
                'source' => 'database_lokal',
                'data' => $existing,
                'session_info' => [
                    'sumber' => 'Database Terverifikasi BPOM RI',
                    'status' => 'Data ditemukan di database lokal',
                ]
            ]);
        }

        // Tanya AI
        try {
            $aiResult = $this->gemini->analyzeBpomProduct($code);

            if (isset($aiResult['found']) && $aiResult['found']) {
                $nomorReg = $aiResult['nomor_reg'] ?? $code;
                $saved = BpomData::updateOrCreate(
                    ['nomor_reg' => $nomorReg],
                    [
                        'kategori' => $aiResult['kategori'] ?? $kategori,
                        'nama_produk' => $aiResult['nama_produk'] ?? 'Tidak diketahui',
                        'merk' => $aiResult['merk'] ?? null,
                        'pendaftar' => $aiResult['pendaftar'] ?? null,
                        'alamat_produsen' => $aiResult['alamat_produsen'] ?? null,
                        'bentuk_sediaan' => $aiResult['bentuk_sediaan'] ?? null,
                        'status_keamanan' => $aiResult['status_keamanan'] ?? 'aman',
                        'analisis_halal' => json_encode($aiResult['analisis_halal'] ?? null),
                        'sumber_data' => 'ai',
                    ]
                );

                return response()->json([
                    'success' => true,
                    'source' => 'hybrid_ai',
                    'data' => $saved,
                    'ai_analysis' => $aiResult,
                    'session_info' => [
                        'sumber' => 'Analisis AI (Referensi BPOM RI)',
                        'disclaimer' => $aiResult['disclaimer'] ?? 'Data berdasarkan informasi publik.',
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Nomor registrasi tidak ditemukan dalam database BPOM.',
                'kategori_terdeteksi' => $kategori,
                'ai_analysis' => $aiResult,
            ]);
        } catch (\Exception $e) {
            Log::error('BPOM Check Registration Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi nomor registrasi. Silakan coba lagi.',
            ], 500);
        }
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
}
