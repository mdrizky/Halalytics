<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => 0,
                'total_scans' => 0,
                'total_donations' => 0,
                'ai_requests_today' => 0,
                'ai_error_rate' => 0,
            ],
        ]);
    }
}
