<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExpertDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:ahli_gizi']);
    }

    public function index()
    {
        $nutritionistId = auth()->id();
        
        // Mock stats for now, can be updated later with real consultation data
        $stats = [
            'total_patients' => User::where('role', 'user')->count(), // Simplified
            'active_consultations' => 0,
            'completed_consultations' => 0,
            'avg_rating' => 5.0,
        ];
        
        return view('expert.dashboard', compact('stats'));
    }
}
