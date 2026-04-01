<?php

namespace App\Http\Controllers\Api;

use App\Models\MedicalProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MedicalProfileController extends Controller
{
    /**
     * Ambil profil medis user
     */
    public function show(Request $request)
    {
        $profile = MedicalProfile::where('id_user', $request->user()->id_user)->first();

        if (!$profile) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Belum ada informasi medis. Silakan lengkapi.',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'weight_kg' => $profile->weight_kg,
                'height_cm' => $profile->height_cm,
                'drug_allergies' => $profile->drug_allergies ?? [],
                'chronic_diseases' => $profile->chronic_diseases,
                'has_gerd' => $profile->has_gerd,
                'blood_type' => $profile->blood_type,
                'additional_notes' => $profile->additional_notes,
                'bmi' => $profile->bmi,
                'bmi_category' => $profile->bmi_category,
                'updated_at' => $profile->updated_at?->toISOString(),
            ],
        ]);
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
            'chronic_diseases' => 'nullable|string|max:2000',
            'has_gerd' => 'nullable|boolean',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'additional_notes' => 'nullable|string|max:2000',
        ]);

        $profile = MedicalProfile::updateOrCreate(
            ['id_user' => $request->user()->id_user],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Informasi medis berhasil disimpan.',
            'data' => [
                'bmi' => $profile->bmi,
                'bmi_category' => $profile->bmi_category,
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
            $bmi < 18.5 => 'underweight',
            $bmi < 23.0 => 'normal',
            $bmi < 25.0 => 'overweight',
            default => 'obese',
        };

        $descriptions = [
            'underweight' => 'Berat badan Anda di bawah normal. Konsultasikan dengan dokter atau ahli gizi untuk pola makan sehat.',
            'normal' => 'Selamat! BMI Anda dalam rentang normal. Pertahankan pola hidup sehat.',
            'overweight' => 'Berat badan Anda sedikit berlebih. Pertimbangkan diet seimbang dan olahraga teratur.',
            'obese' => 'BMI Anda menunjukkan obesitas. Segera konsultasikan dengan dokter untuk penanganan yang tepat.',
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
}
