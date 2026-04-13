<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\GeminiService;

class MealAiController extends Controller
{
    private $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function analyzeMeal(Request $request)
    {
        $request->validate([
            'image' => 'required|string', // Base64 string
        ]);

        $base64Image = $request->input('image');
        // Remove header if present (data:image/jpeg;base64,)
        if (str_contains($base64Image, ',')) {
            $base64Image = explode(',', $base64Image)[1];
        }

        try {
            $userId = $request->user()?->id_user ?? $request->user()?->id;

            // Step 1: Use centralized GeminiService for Visual AI Analysis
            $analysis = $this->geminiService->analyzeMealImage($base64Image);

            if (!$analysis) {
                return response()->json(['success' => false, 'message' => 'AI Service Unavailable. Please try again later.'], 502);
            }

            // Step 2: Log to Database
            $logId = DB::table('meal_logs')->insertGetId([
                'user_id' => $userId,
                'meal_name' => $analysis['food_name'] ?? 'Unknown Meal',
                'calories' => $analysis['nutrition']['calories'] ?? 0,
                'protein' => $analysis['nutrition']['protein'] ?? 0,
                'fat' => $analysis['nutrition']['fat'] ?? 0,
                'carbs' => $analysis['nutrition']['carbs'] ?? 0,
                'raw_ai_response' => json_encode($analysis),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Auto-update Activity Log with Risk Detection
            $isRisk = ($analysis['halal_analysis']['status'] === 'haram') || ($analysis['halal_analysis']['status'] === 'syubhat');
            DB::table('activity_logs')->insert([
                'user_id' => $userId,
                'action' => 'SCAN_MEAL',
                'description' => "Visual Scan: {$analysis['food_name']} ({$analysis['halal_analysis']['status']}).",
                'is_risk_detected' => $isRisk,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $analysis
            ]);

        } catch (\Exception $e) {
            Log::error('Meal Analysis Server Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}
