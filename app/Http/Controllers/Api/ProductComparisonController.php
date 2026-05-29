<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\ProductModel;
use App\Models\MedicalProfile;
use App\Services\UniversalProductService;
use Illuminate\Support\Facades\Auth;

class ProductComparisonController extends Controller
{
    protected $geminiService;
    protected $universalService;

    public function __construct(GeminiService $geminiService, UniversalProductService $universalService)
    {
        $this->geminiService = $geminiService;
        $this->universalService = $universalService;
    }

    /**
     * Bandingkan beberapa produk berdasarkan barcode
     */
    public function compare(Request $request)
    {
        $request->validate([
            'barcodes' => 'required|array|min:2|max:3',
            'barcodes.*' => 'string'
        ]);

        $barcodes = $request->barcodes;
        $user = Auth::user();
        $medicalProfile = MedicalProfile::where('id_user', $user->id_user)->first();

        $productsData = [];
        foreach ($barcodes as $barcode) {
            $result = $this->universalService->findProduct($barcode);
            if ($result['found']) {
                $productsData[] = array_merge($result['standardized'], [
                    'source' => $result['source'],
                    'verification_status' => $result['data']->verification_status ?? 'unknown'
                ]);
            } else {
                $productsData[] = [
                    'barcode' => $barcode,
                    'name' => 'Produk tidak ditemukan',
                    'found' => false
                ];
            }
        }

        $userContext = [
            'name' => $user->full_name,
            'medical_history' => $user->medical_history,
            'allergies' => $user->allergy,
            'thresholds' => $medicalProfile ? $medicalProfile->thresholds : null,
        ];

        // Advanced AI Prompt for Comparison (Aligned with Android ComparisonModels.kt)
        $prompt = "Bandingkan produk-produk berikut secara Head-to-Head untuk aplikasi Halalytics.\n"
                . "User Context: " . json_encode($userContext) . "\n"
                . "Produk Data: " . json_encode($productsData) . "\n\n"
                . "Ketentuan Jawaban:\n"
                . "1. Berikan skor (0-100) untuk: Status Halal, Safety/Keamanan.\n"
                . "2. Berikan kesimpulan akhir (better_choice, reason, summary).\n"
                . "3. Gunakan Bahasa Indonesia.\n"
                . "4. Jawab dalam JSON dengan struktur EXACT:\n"
                . "{\n"
                . "  \"summary\": \"Ringkasan singkat\",\n"
                . "  \"better_choice\": \"Nama Produk Terbaik\",\n"
                . "  \"reason\": \"Alasan mendalam\",\n"
                . "  \"similarities\": [\"Kesamaan 1\"],\n"
                . "  \"comparison\": [\n"
                . "    {\n"
                . "      \"product_name\": \"Nama Produk\",\n"
                . "      \"halal_score\": 100,\n"
                . "      \"safety_score\": 90,\n"
                . "      \"pros\": [\"Keunggulan\"],\n"
                . "      \"cons\": [\"Kekurangan\"],\n"
                . "      \"suitability_notes\": \"Catatan kecocokan\"\n"
                . "    }\n"
                . "  ]\n"
                . "}";

        try {
            $comparison = $this->geminiService->generateCustomContent($prompt);
            $parsed = is_string($comparison) ? json_decode($comparison, true) : $comparison;

            return response()->json([
                'success' => true,
                'data' => $parsed,
                'products' => $productsData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membandingkan produk: ' . $e->getMessage()
            ], 500);
        }
    }
}
