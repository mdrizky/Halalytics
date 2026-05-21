<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class UserDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'weekly_health_score' => 72,
                'recent_scans' => [],
                'recommended_products' => [],
            ],
        ]);
    }
}
