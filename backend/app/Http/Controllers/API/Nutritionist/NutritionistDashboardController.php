<?php

namespace App\Http\Controllers\API\Nutritionist;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class NutritionistDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_patients' => 0,
                'pending_consultations' => 0,
                'ai_reviews_pending' => 0,
            ],
        ]);
    }
}
