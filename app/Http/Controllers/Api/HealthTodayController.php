<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyNutrition;
use App\Models\ScanHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthTodayController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = today()->toDateString();

        $nutrition = DailyNutrition::byUser($user->id_user)
            ->where('scan_date', $today)
            ->first();

        $latestScans = ScanHistory::byUser($user->id_user)
            ->with('ingredients')
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $weeklyNutrition = DailyNutrition::byUser($user->id_user)
            ->whereBetween('scan_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
            ->get();

        $weeklyAvg = [
            'avg_calories' => round($weeklyNutrition->avg('total_calories') ?? 0, 1),
            'avg_protein' => round($weeklyNutrition->avg('total_protein') ?? 0, 1),
            'avg_carbs' => round($weeklyNutrition->avg('total_carbs') ?? 0, 1),
            'avg_fat' => round($weeklyNutrition->avg('total_fat') ?? 0, 1),
            'avg_health_score' => round($weeklyNutrition->avg('health_score_avg') ?? 0, 1),
            'total_weekly_scans' => $weeklyNutrition->sum('total_scans'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $today,
                'today' => $nutrition ? [
                    'total_calories' => (float) $nutrition->total_calories,
                    'total_protein' => (float) $nutrition->total_protein,
                    'total_carbs' => (float) $nutrition->total_carbs,
                    'total_fat' => (float) $nutrition->total_fat,
                    'total_fiber' => (float) $nutrition->total_fiber,
                    'total_sugar' => (float) $nutrition->total_sugar,
                    'total_sodium' => (float) $nutrition->total_sodium,
                    'total_scans' => $nutrition->total_scans,
                    'total_halal' => $nutrition->total_halal,
                    'total_syubhat' => $nutrition->total_syubhat,
                    'total_haram' => $nutrition->total_haram,
                    'health_score_avg' => $nutrition->health_score_avg ? (float) $nutrition->health_score_avg : null,
                ] : [
                    'total_calories' => 0,
                    'total_protein' => 0,
                    'total_carbs' => 0,
                    'total_fat' => 0,
                    'total_fiber' => 0,
                    'total_sugar' => 0,
                    'total_sodium' => 0,
                    'total_scans' => 0,
                    'total_halal' => 0,
                    'total_syubhat' => 0,
                    'total_haram' => 0,
                    'health_score_avg' => null,
                ],
                'latest_scans' => $latestScans,
                'weekly_average' => $weeklyAvg,
            ]
        ]);
    }
}
