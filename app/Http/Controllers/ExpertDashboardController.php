<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProductModel;
use App\Models\ActivityEvent;
use Illuminate\Support\Facades\DB;

class ExpertDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:ahli_gizi']);
    }

    public function index()
    {
        // Data statistik spesifik Ahli Gizi
        $stats = [
            'total_patients' => User::where('role', 'user')->count(),
            'active_consultations' => ActivityEvent::where('event_type', 'ai_health_assistant')->count(),
            'pending_verifications' => ProductModel::where('verification_status', 'needs_review')->count(),
            'avg_rating' => 4.8,
            'completed_consultations' => rand(15, 45),
        ];

        // Aktivitas pasien terbaru (Health Activities) menggunakan Eloquent untuk relasi yang lebih bersih
        $patientActivities = ActivityEvent::with('user')
            ->whereIn('event_type', ['external_scan', 'skincare_analysis', 'health_risk_score'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                // Pastikan user_full_name tersedia untuk view
                $activity->user_full_name = $activity->user ? $activity->user->full_name : 'Guest';
                return $activity;
            });
        
        return view('expert.dashboard', compact('stats', 'patientActivities'));
    }
}
