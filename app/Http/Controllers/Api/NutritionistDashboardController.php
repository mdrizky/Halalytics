<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NutritionConsultation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NutritionistDashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->role !== 'ahli_gizi') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $nutritionistId = $user->id_user;

        $activeConsultations = NutritionConsultation::query()
            ->where('nutritionist_id', $nutritionistId)
            ->where('status', 'open')
            ->count();

        $totalPatients = NutritionConsultation::query()
            ->where('nutritionist_id', $nutritionistId)
            ->distinct()
            ->count('user_id');

        $assignedPatientIds = NutritionConsultation::query()
            ->where('nutritionist_id', $nutritionistId)
            ->distinct()
            ->pluck('user_id');

        $bmiStats = User::query()
            ->whereIn('id_user', $assignedPatientIds)
            ->whereNotNull('bmi')
            ->selectRaw(
                'SUM(CASE WHEN bmi >= 30 THEN 1 ELSE 0 END) as obesity,'.
                'SUM(CASE WHEN bmi < 18.5 THEN 1 ELSE 0 END) as underweight,'.
                'SUM(CASE WHEN bmi >= 18.5 AND bmi < 30 THEN 1 ELSE 0 END) as normal_range'
            )
            ->first();

        return response()->json([
            'success' => true,
            'medical_disclaimer' => 'Data bersifat informatif. Bukan diagnosis medis. Untuk kondisi klinis, rujuk ke fasilitas kesehatan.',
            'data' => [
                'nutritionist_id' => $nutritionistId,
                'active_consultations' => $activeConsultations,
                'total_patients_distinct' => $totalPatients,
                'population_bmi_snapshot' => [
                    'obesity_bmi_gte_30' => (int) ($bmiStats->obesity ?? 0),
                    'underweight_bmi_lt_18_5' => (int) ($bmiStats->underweight ?? 0),
                    'other_recorded_bmi' => (int) ($bmiStats->normal_range ?? 0),
                ],
                'recent_consultations' => NutritionConsultation::query()
                    ->where('nutritionist_id', $nutritionistId)
                    ->with('user:id_user,username,full_name,bmi')
                    ->orderByDesc('updated_at')
                    ->limit(10)
                    ->get()
                    ->map(fn (NutritionConsultation $c) => [
                        'id' => $c->id,
                        'subject' => $c->subject,
                        'user_name' => $c->user?->full_name ?? $c->user?->username,
                        'user_bmi' => $c->user?->bmi,
                    ])
                    ->values(),
            ],
        ]);
    }
}
