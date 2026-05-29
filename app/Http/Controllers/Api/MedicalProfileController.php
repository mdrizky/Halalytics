<?php

namespace App\Http\Controllers\Api;

use App\Models\MedicalProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\GeminiService;

class MedicalProfileController extends Controller
{
    protected $geminiService;
    protected $thresholdService;

    public function __construct(GeminiService $geminiService, \App\Services\Health\NutritionalThresholdService $thresholdService)
    {
        $this->geminiService = $geminiService;
        $this->thresholdService = $thresholdService;
    }
    /**
     * Ambil profil medis user
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $profile = MedicalProfile::where('id_user', $user->id_user)->first();

        if (!$profile) {
            // FALLBACK: Ambil data dari tabel users jika profile medis belum dibuat
            $weight = ($user->weight ?: $user->weight_kg ?: 0);
            $height = ($user->height ?: 0);
            
            $bmi = 0;
            $bmiCategory = 'unknown';
            
            if ($height > 0) {
                $heightM = $height / 100;
                $bmi = round($weight / ($heightM * $heightM), 1);
                $bmiCategory = $this->getBmiCategoryLabel($bmi);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'weight_kg' => (float) $weight,
                    'height_cm' => (float) $height,
                    'drug_allergies' => [],
                    'food_allergies' => [],
                    'chronic_diseases' => $user->medical_history,
                    'has_gerd' => false,
                    'activity_level' => 'sedentary',
                    'daily_calories_target' => 2000,
                    'daily_sugar_limit_g' => 50.0,
                    'daily_sodium_limit_mg' => 2300,
                    'daily_fat_limit_g' => 67.0,
                    'blood_type' => $user->blood_type,
                    'additional_notes' => null,
                    'bmi' => $bmi,
                    'bmi_category' => $bmiCategory,
                    'updated_at' => $user->updated_at?->toISOString(),
                ],
                'message' => 'Menggunakan data profil dasar user',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => array_merge($profile->toArray(), [
                'bmi' => $profile->bmi,
                'bmi_category' => $profile->bmi_category,
                'updated_at' => $profile->updated_at?->toISOString(),
            ]),
            'message' => 'Profil medis ditemukan',
        ]);
    }

    private function getBmiCategoryLabel($bmi)
    {
        if ($bmi < 18.5) return 'underweight';
        if ($bmi < 23.0) return 'normal';
        if ($bmi < 25.0) return 'overweight';
        return 'obese';
    }

    /**
     * Simpan/update profil medis
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'weight_kg' => 'nullable|numeric|min:10|max:300',
            'height_cm' => 'nullable|numeric|min:50|max:250',
            'drug_allergies' => 'nullable|array',
            'drug_allergies.*' => 'string|max:100',
            'food_allergies' => 'nullable|array',
            'food_allergies.*' => 'string|max:100',
            'chronic_diseases' => 'nullable|string|max:2000',
            'has_gerd' => 'nullable|boolean',
            'activity_level' => 'nullable|string|in:sedentary,light,moderate,active,very_active',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'additional_notes' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();
        
        // Calculate Nutritional Thresholds
        $thresholds = $this->thresholdService->calculateThresholds([
            'weight_kg' => $validated['weight_kg'] ?? ($user->weight ?? 0),
            'height_cm' => $validated['height_cm'] ?? ($user->height ?? 0),
            'age' => $user->age ?? 25,
            'gender' => $user->gender ?? 'male',
            'activity_level' => $validated['activity_level'] ?? 'sedentary',
            'conditions' => $validated['chronic_diseases'] ?? ($user->medical_history ?? ''),
        ]);

        $profile = MedicalProfile::updateOrCreate(
            ['id_user' => $user->id_user],
            array_merge($validated, [
                'daily_calories_target' => $thresholds['calories'],
                'daily_sugar_limit_g' => $thresholds['sugar_g'],
                'daily_sodium_limit_mg' => $thresholds['sodium_mg'],
                'daily_fat_limit_g' => $thresholds['fat_g'],
            ])
        );

        return response()->json([
            'success' => true,
            'message' => 'Informasi medis berhasil disimpan.',
            'data' => [
                'bmi' => $profile->bmi,
                'bmi_category' => $profile->bmi_category,
                'thresholds' => $thresholds,
            ],
        ]);
    }

    /**
     * Hitung BMI
     */
    public function calculateBmi(Request $request)
    {
        $request->validate([
            'weight_kg' => 'required|numeric|min:10|max:300',
            'height_cm' => 'required|numeric|min:50|max:250',
        ]);

        $heightM = $request->height_cm / 100;
        $bmi = round($request->weight_kg / ($heightM * $heightM), 1);

        $category = match (true) {
            $bmi < 17.0 => 'underweight_severe',
            $bmi < 18.5 => 'underweight_mild',
            $bmi < 20.0 => 'normal_thin',
            $bmi < 23.0 => 'normal_ideal',
            $bmi < 25.0 => 'overweight',
            $bmi < 30.0 => 'obese_1',
            default => 'obese_2',
        };

        $descriptions = [
            'underweight_severe' => 'Berat badan Anda sangat kurang. Disarankan untuk berkonsultasi dengan dokter untuk pemeriksaan kesehatan menyeluruh.',
            'underweight_mild' => 'Berat badan Anda kurang (ringan). Tingkatkan asupan nutrisi protein dan karbohidrat kompleks.',
            'normal_thin' => 'BMI Anda normal, namun mendekati batas bawah (kurus). Anda mungkin terlihat sangat ramping; pertimbangkan penambahan massa otot.',
            'normal_ideal' => 'Selamat! BMI Anda berada di rentang ideal yang paling sehat. Pertahankan pola makan dan olahraga saat ini.',
            'overweight' => 'Berat badan Anda berlebih. Batasi asupan gula dan lemak, serta tingkatkan aktivitas fisik harian.',
            'obese_1' => 'BMI menunjukkan Obesitas tingkat 1. Sebaiknya mulai mengatur pola makan rendah kalori dan rutin berolahraga.',
            'obese_2' => 'BMI menunjukkan Obesitas tingkat 2. Sangat disarankan untuk berkonsultasi dengan tenaga medis untuk program penurunan berat badan.',
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'bmi' => $bmi,
                'category' => $category,
                'description' => $descriptions[$category],
                'ranges' => [
                    ['label' => 'Kurus', 'min' => 0, 'max' => 18.4, 'color' => '#3B82F6'],
                    ['label' => 'Normal', 'min' => 18.5, 'max' => 22.9, 'color' => '#10B981'],
                    ['label' => 'Berlebih', 'min' => 23.0, 'max' => 24.9, 'color' => '#F59E0B'],
                    ['label' => 'Obesitas', 'min' => 25.0, 'max' => 50.0, 'color' => '#EF4444'],
                ],
            ],
        ]);
    }

    /**
     * Dapatkan saran kesehatan berbasis AI berdasarkan BMI
     */
    public function getAiBmiAdvice(Request $request)
    {
        $request->validate([
            'weight_kg' => 'required|numeric|min:10|max:300',
            'height_cm' => 'required|numeric|min:50|max:250',
        ]);

        $weight = $request->weight_kg;
        $heightCm = $request->height_cm;
        $heightM = $heightCm / 100;
        $bmi = round($weight / ($heightM * $heightM), 1);
        
        // Target BMI Ideal (21.5)
        $idealWeight = round(21.5 * ($heightM * $heightM));
        $diff = abs($weight - $idealWeight);
        $action = $weight < $idealWeight ? "menaikkan" : "menurunkan";

        $prompt = "Anda adalah pakar kesehatan, ahli gizi, dan personal trainer profesional di aplikasi Halalytics.
                   Berikan analisis kesehatan dan rencana aksi selama 2 bulan ke depan.
                   
                   Data Fisik:
                   - Berat: $weight kg
                   - Tinggi: $heightCm cm
                   - BMI: $bmi
                   - Berat Ideal (target): $idealWeight kg
                   - Tugas: User perlu $action berat sebanyak $diff kg untuk mencapai ideal.

                   Ketentuan Jawaban:
                   1. Gunakan Bahasa Indonesia yang santai, memotivasi, namun tetap edukatif (seperti coach profesional).
                   2. Fokus pada makanan lokal Indonesia yang mudah dicari (halal & thoyyib).
                   3. Berikan rencana olahraga yang realistis dilakukan di rumah atau gym.
                   4. Jawab HANYA dalam format JSON murni (tanpa markdown ```json) dengan struktur:
                   {
                     \"status_fisik\": \"Penjelasan singkat kondisi tubuh saat ini\",
                     \"target_2_bulan\": \"Target realistis dalam 60 hari ke depan\",
                     \"saran_nutrisi\": [\"Poin 1\", \"Poin 2\", \"dst\"],
                     \"saran_olahraga\": [\"Poin 1\", \"Poin 2\", \"dst\"],
                     \"pesan_motivasi\": \"Kalimat penyemangat satu baris\"
                   }";

        try {
            $advice = $this->geminiService->generateCustomContent($prompt);
            $parsed = is_string($advice) ? json_decode($advice, true) : $advice;

            if (!is_array($parsed) || !isset($parsed['status_fisik'])) {
                // Fallback to local advice logic
                $parsed = [
                    'status_fisik' => "Berdasarkan BMI $bmi, status Anda adalah " . ($weight < $idealWeight ? "Kurus" : ($weight > $idealWeight ? "Berlebih" : "Normal")) . ".",
                    'target_2_bulan' => "$action 2-3 kg dalam 60 hari ke depan.",
                    'saran_nutrisi' => ["Konsumsi makanan gizi seimbang", "Perbanyak protein", "Cukup minum air putih"],
                    'saran_olahraga' => ["Jalan santai 30 menit sehari", "Lakukan peregangan"],
                    'pesan_motivasi' => "Kesehatan adalah investasi terbaik Anda!"
                ];
            }
            \Illuminate\Support\Facades\DB::table('ai_medical_reports')->insert([
                'id_user' => $request->user()->id_user,
                'type' => 'bmi',
                'input_data' => json_encode(['weight_kg' => $weight, 'height_cm' => $heightCm, 'bmi' => $bmi]),
                'ai_response' => json_encode($parsed),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $parsed
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan saran AI: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dapatkan riwayat saran AI
     */
    public function getReports(Request $request)
    {
        $reports = \Illuminate\Support\Facades\DB::table('ai_medical_reports')
            ->where('id_user', $request->user()->id_user)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($report) {
                $report->input_data = json_decode($report->input_data, true);
                $report->ai_response = json_decode($report->ai_response, true);
                return $report;
            });

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }
}
