<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BpomData;
use App\Services\ActivityEventService;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SkincareController extends Controller
{
    protected $gemini;
    protected $activityEventService;

    public function __construct(GeminiService $gemini, ActivityEventService $activityEventService)
    {
        $this->gemini = $gemini;
        $this->activityEventService = $activityEventService;
    }

    /**
     * Analisis bahan skincare/kosmetik dari teks OCR atau input manual
     */
    public function analyzeIngredients(Request $request)
    {
        $request->validate([
            'ingredients_text' => 'required_without:image|string',
            'image' => 'required_without:ingredients_text|string', // base64
            'product_name' => 'nullable|string',
            'barcode' => 'nullable|string',
            'family_id' => 'nullable|integer',
        ]);

        $ingredientsText = $request->ingredients_text;

        // Jika input berupa gambar, gunakan OCR AI untuk baca teks
        if (!$ingredientsText && $request->image) {
            try {
                $ocrResult = $this->gemini->generateWithImage(
                    "Baca semua teks bahan/ingredients yang terlihat di gambar ini. Kembalikan hanya daftar bahan dalam format teks biasa, dipisahkan koma. Jangan tambahkan penjelasan lain.",
                    $request->image
                );
                $ingredientsText = $ocrResult['raw_text'] ?? json_encode($ocrResult);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membaca teks dari gambar: ' . $e->getMessage()
                ], 500);
            }
        }

        try {
            $user = Auth::user();
            $familyId = $request->family_id;
            
            // Build health profile for AI context (either User or Family Member)
            $userContext = $this->resolveHealthContext($user, $familyId);

            $analysis = $this->gemini->analyzeSkincareIngredients($ingredientsText, $userContext);
            $ingredientsDetected = $this->buildIngredientIndicators($ingredientsText);
            $statusSafety = $analysis['status_keamanan'] ?? $this->deriveSafetyStatus($ingredientsDetected);
            $statusHalal = $analysis['status_halal'] ?? $this->deriveHalalStatus($ingredientsDetected);
            $scoreSafety = $analysis['skor_keamanan'] ?? $this->deriveSafetyScore($ingredientsDetected);
            $summary = $analysis['ringkasan'] ?? 'Analisis selesai. Periksa detail bahan untuk keamanan dan status halal.';
            $disclaimer = $analysis['disclaimer'] ?? 'Informasi ini bersifat referensi dan bukan pengganti konsultasi dokter/apoteker.';

            // Simpan ke database jika ada nama produk
            if ($request->product_name) {
                BpomData::updateOrCreate(
                    ['nama_produk' => $request->product_name, 'barcode' => $request->barcode],
                    [
                        'kategori' => 'kosmetik',
                        'ingredients_text' => $ingredientsText,
                        'analisis_kandungan' => json_encode($analysis),
                        'status_keamanan' => $statusSafety,
                        'skor_keamanan' => $scoreSafety,
                        'status_halal' => $statusHalal,
                        'analisis_halal' => json_encode($analysis['bahan_syubhat'] ?? []),
                        'barcode' => $request->barcode,
                        'sumber_data' => 'ai',
                    ]
                );
            }

            $this->activityEventService->logEvent(
                eventType: 'skincare_analysis',
                userId: $user?->id_user,
                username: $user?->username,
                entityRef: $request->barcode ?: $request->product_name,
                summary: 'Skincare analysis completed: ' . ($request->product_name ?: 'manual_input'),
                status: 'success',
                payload: [
                    'score_safety' => $scoreSafety,
                    'status_safety' => $statusSafety,
                    'status_halal' => $statusHalal,
                    'ingredients_count' => count($ingredientsDetected),
                ]
            );

            return response()->json([
                'success' => true,
                'ingredients_text' => $ingredientsText,
                'analysis' => $analysis,
                'ingredients_detected' => $ingredientsDetected,
                'score_safety' => $scoreSafety,
                'status_safety' => $statusSafety,
                'status_halal' => $statusHalal,
                'summary' => $summary,
                'disclaimer' => $disclaimer,
                'session_info' => [
                    'sumber' => 'Analisis AI Halalytics',
                    'referensi' => 'Database Bahan Kosmetik Internasional (INCI) & Standar BPOM',
                    'disclaimer' => 'Status halal resmi harus dikonfirmasi dengan sertifikat MUI/BPJPH.'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Skincare Analysis Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => optional(Auth::user())->id_user,
            ]);
            return response()->json([
                'success' => false,
                'error_code' => 'SKINCARE_ANALYSIS_FAILED',
                'message' => 'Gagal menganalisis bahan skincare. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Cek keamanan bahan secara cepat (merkuri, hydroquinone, paraben)
     */
    public function checkSafety(Request $request)
    {
        $request->validate(['ingredients_text' => 'required|string']);

        $ingredientsText = strtolower($request->ingredients_text);

        // Quick check bahan berbahaya yang dilarang BPOM
        $bannedIngredients = [
            'mercury' => 'Merkuri — Logam berat yang merusak ginjal dan sistem saraf',
            'mercuric' => 'Senyawa Merkuri — Sangat beracun',
            'hydroquinone' => 'Hydroquinone — Dilarang BPOM untuk kosmetik bebas (hanya resep dokter)',
            'tretinoin' => 'Tretinoin — Hanya boleh dengan resep dokter',
            'lead' => 'Timbal — Logam berat berbahaya',
            'formaldehyde' => 'Formaldehida — Karsinogen (pemicu kanker)',
            'asbestos' => 'Asbes — Karsinogen',
            'rhodamine' => 'Rhodamine B — Pewarna tekstil berbahaya',
        ];

        $detected = [];
        foreach ($bannedIngredients as $ingredient => $warning) {
            if (str_contains($ingredientsText, $ingredient)) {
                $detected[] = ['bahan' => $ingredient, 'peringatan' => $warning];
            }
        }

        $status = empty($detected) ? 'aman' : 'bahaya';
        $ingredientsDetected = array_map(function ($row) {
            return [
                'name' => $row['bahan'] ?? null,
                'safety_level' => 'danger',
                'halal_status' => 'unknown',
                'reason' => $row['peringatan'] ?? null,
                'color_code' => 'red',
            ];
        }, $detected);

        return response()->json([
            'success' => true,
            'status_keamanan' => $status,
            'ingredients_detected' => $ingredientsDetected,
            'bahan_berbahaya_terdeteksi' => $detected,
            'jumlah_bahaya' => count($detected),
            'pesan' => empty($detected)
                ? 'Tidak terdeteksi bahan berbahaya yang dilarang BPOM dalam daftar ini.'
                : 'PERINGATAN: Terdeteksi ' . count($detected) . ' bahan berbahaya yang dilarang BPOM!',
        ]);
    }

    /**
     * Cek status halal bahan skincare/kosmetik
     */
    public function getHalalStatus(Request $request)
    {
        $request->validate(['ingredients_text' => 'required|string']);

        $ingredientsText = strtolower($request->ingredients_text);

        // Quick check bahan kritis halal
        $criticalIngredients = [
            'glycerin' => ['status' => 'syubhat', 'alasan' => 'Bisa dari nabati atau hewani — perlu konfirmasi sumber'],
            'gelatin' => ['status' => 'syubhat', 'alasan' => 'Umumnya dari hewan — periksa apakah dari sumber halal'],
            'collagen' => ['status' => 'syubhat', 'alasan' => 'Bisa dari ikan (halal) atau babi (haram)'],
            'placenta' => ['status' => 'syubhat', 'alasan' => 'Ekstrak plasenta hewan — status halal perlu diperiksa'],
            'stearic acid' => ['status' => 'syubhat', 'alasan' => 'Bisa dari lemak hewan atau nabati'],
            'lard' => ['status' => 'haram', 'alasan' => 'Lemak babi — HARAM'],
            'carmine' => ['status' => 'haram', 'alasan' => 'Pewarna dari serangga cochineal — mayoritas ulama mengharamkan'],
            'keratin' => ['status' => 'syubhat', 'alasan' => 'Protein dari rambut/kuku hewan — perlu konfirmasi sumber'],
            'squalene' => ['status' => 'syubhat', 'alasan' => 'Bisa dari hati ikan hiu atau zaitun'],
            'lanolin' => ['status' => 'syubhat', 'alasan' => 'Dari lemak bulu domba — umumnya halal tapi perlu sertifikasi'],
            'alcohol' => ['status' => 'syubhat', 'alasan' => 'Ethanol dalam kosmetik — pendapat ulama berbeda'],
        ];

        $detected = [];
        $hasHaram = false;
        $hasSyubhat = false;

        foreach ($criticalIngredients as $ingredient => $info) {
            if (str_contains($ingredientsText, $ingredient)) {
                $detected[] = array_merge(['bahan' => $ingredient], $info);
                if ($info['status'] === 'haram') $hasHaram = true;
                if ($info['status'] === 'syubhat') $hasSyubhat = true;
            }
        }

        $overallStatus = 'halal';
        if ($hasHaram) $overallStatus = 'haram';
        elseif ($hasSyubhat) $overallStatus = 'syubhat';

        $ingredientsDetected = array_map(function ($item) {
            return [
                'name' => $item['bahan'] ?? null,
                'safety_level' => 'warning',
                'halal_status' => $item['status'] ?? 'unknown',
                'reason' => $item['alasan'] ?? null,
                'color_code' => ($item['status'] ?? '') === 'haram' ? 'red' : 'yellow',
            ];
        }, $detected);

        return response()->json([
            'success' => true,
            'status_halal' => $overallStatus,
            'ingredients_detected' => $ingredientsDetected,
            'bahan_kritis' => $detected,
            'jumlah_kritis' => count($detected),
            'pesan' => match ($overallStatus) {
                'halal' => 'Tidak terdeteksi bahan kritis halal dalam daftar ini.',
                'haram' => 'PERINGATAN: Terdeteksi bahan HARAM! Produk ini tidak direkomendasikan.',
                'syubhat' => 'Terdeteksi ' . count($detected) . ' bahan dengan titik kritis halal. Diperlukan konfirmasi sertifikat halal resmi.',
            },
            'disclaimer' => 'Status halal ini berdasarkan analisis bahan. Untuk kepastian hukum, periksa sertifikat halal MUI/BPJPH.',
        ]);
    }

    /**
     * Helper to resolve health context for either the main user or a family member
     */
    private function resolveHealthContext($user, $familyId = null)
    {
        if ($familyId && $user) {
            $family = \App\Models\FamilyProfile::where('user_id', $user->id_user)->find($familyId);
            if ($family) {
                return [
                    'name' => $family->name,
                    'is_family_member' => true,
                    'age' => $family->age,
                    'gender' => $family->gender,
                    'medical_history' => $family->medical_history,
                    'allergies' => $family->allergies,
                    'is_pregnant' => false, // Default for family unless structured differently
                ];
            }
        }

        if ($user) {
            return [
                'name' => $user->full_name,
                'is_family_member' => false,
                'age' => $user->age,
                'gender' => $user->gender,
                'medical_history' => $user->medical_history,
                'allergies' => $user->allergy,
                'is_pregnant' => $user->diet_preference === 'ibu_hamil',
            ];
        }

        return [];
    }

    private function buildIngredientIndicators(string $ingredientsText): array
    {
        $dangerList = $this->bannedIngredients();
        $halalList = $this->criticalIngredients();
        $tokens = array_filter(array_map('trim', preg_split('/,|;|\\n/', strtolower($ingredientsText)) ?: []));

        $result = [];
        foreach ($tokens as $token) {
            $matchedDanger = null;
            foreach ($dangerList as $key => $warning) {
                if (str_contains($token, $key)) {
                    $matchedDanger = ['key' => $key, 'warning' => $warning];
                    break;
                }
            }

            $matchedHalal = null;
            foreach ($halalList as $key => $rule) {
                if (str_contains($token, $key)) {
                    $matchedHalal = array_merge(['key' => $key], $rule);
                    break;
                }
            }

            $safetyLevel = $matchedDanger ? 'danger' : ($matchedHalal ? 'warning' : 'safe');
            $halalStatus = $matchedHalal['status'] ?? 'halal';
            $reason = $matchedDanger['warning'] ?? ($matchedHalal['alasan'] ?? 'Tidak ditemukan indikator risiko utama.');

            $result[] = [
                'name' => $token,
                'safety_level' => $safetyLevel,
                'halal_status' => $halalStatus,
                'reason' => $reason,
                'color_code' => match ($safetyLevel) {
                    'danger' => 'red',
                    'warning' => 'yellow',
                    default => 'green',
                },
            ];
        }

        return array_slice($result, 0, 80);
    }

    private function deriveSafetyStatus(array $items): string
    {
        if (collect($items)->contains(fn ($i) => ($i['safety_level'] ?? '') === 'danger')) {
            return 'bahaya';
        }
        if (collect($items)->contains(fn ($i) => ($i['safety_level'] ?? '') === 'warning')) {
            return 'perlu_perhatian';
        }
        return 'aman';
    }

    private function deriveHalalStatus(array $items): string
    {
        if (collect($items)->contains(fn ($i) => ($i['halal_status'] ?? '') === 'haram')) {
            return 'haram';
        }
        if (collect($items)->contains(fn ($i) => ($i['halal_status'] ?? '') === 'syubhat')) {
            return 'syubhat';
        }
        return 'halal';
    }

    private function deriveSafetyScore(array $items): int
    {
        $score = 100;
        foreach ($items as $item) {
            $score -= match ($item['safety_level'] ?? 'safe') {
                'danger' => 25,
                'warning' => 10,
                default => 0,
            };
        }
        return max(0, $score);
    }

    private function bannedIngredients(): array
    {
        return [
            'mercury' => 'Merkuri — Logam berat yang merusak ginjal dan sistem saraf',
            'mercuric' => 'Senyawa Merkuri — Sangat beracun',
            'hydroquinone' => 'Hydroquinone — Dilarang BPOM untuk kosmetik bebas (hanya resep dokter)',
            'tretinoin' => 'Tretinoin — Hanya boleh dengan resep dokter',
            'lead' => 'Timbal — Logam berat berbahaya',
            'formaldehyde' => 'Formaldehida — Karsinogen (pemicu kanker)',
            'asbestos' => 'Asbes — Karsinogen',
            'rhodamine' => 'Rhodamine B — Pewarna tekstil berbahaya',
        ];
    }

    private function criticalIngredients(): array
    {
        return [
            'glycerin' => ['status' => 'syubhat', 'alasan' => 'Bisa dari nabati atau hewani — perlu konfirmasi sumber'],
            'gelatin' => ['status' => 'syubhat', 'alasan' => 'Umumnya dari hewan — periksa apakah dari sumber halal'],
            'collagen' => ['status' => 'syubhat', 'alasan' => 'Bisa dari ikan (halal) atau babi (haram)'],
            'placenta' => ['status' => 'syubhat', 'alasan' => 'Ekstrak plasenta hewan — status halal perlu diperiksa'],
            'stearic acid' => ['status' => 'syubhat', 'alasan' => 'Bisa dari lemak hewan atau nabati'],
            'lard' => ['status' => 'haram', 'alasan' => 'Lemak babi — HARAM'],
            'carmine' => ['status' => 'haram', 'alasan' => 'Pewarna dari serangga cochineal — mayoritas ulama mengharamkan'],
            'keratin' => ['status' => 'syubhat', 'alasan' => 'Protein dari rambut/kuku hewan — perlu konfirmasi sumber'],
            'squalene' => ['status' => 'syubhat', 'alasan' => 'Bisa dari hati ikan hiu atau zaitun'],
            'lanolin' => ['status' => 'syubhat', 'alasan' => 'Dari lemak bulu domba — umumnya halal tapi perlu sertifikasi'],
            'alcohol' => ['status' => 'syubhat', 'alasan' => 'Ethanol dalam kosmetik — pendapat ulama berbeda'],
        ];
    }
}
