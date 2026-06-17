<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProductModel;
use App\Models\ProductAnalysisResult;
use App\Models\ActivityEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpertDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:ahli_gizi']);
    }

    public function index()
    {
        $stats = [
            'total_patients' => User::where('role', 'user')->count(),
            'active_consultations' => ActivityEvent::where('event_type', 'ai_health_assistant')->count(),
            'pending_verifications' => ProductAnalysisResult::where('is_verified_by_expert', false)->count(),
            'avg_rating' => 0,
            'completed_consultations' => ActivityEvent::where('event_type', 'ai_health_assistant')
                ->whereNotNull('completed_at')
                ->count(),
        ];

        $patientActivities = ActivityEvent::with('user')
            ->whereIn('event_type', ['external_scan', 'skincare_analysis', 'health_risk_score'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                $activity->user_full_name = $activity->user ? $activity->user->full_name : 'Guest';
                return $activity;
            });

        return view('expert.dashboard', compact('stats', 'patientActivities'));
    }

    public function patients()
    {
        $patients = User::where('role', 'user')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('expert.patients', compact('patients'));
    }

    public function consultations()
    {
        $consultations = ActivityEvent::with('user')
            ->where('event_type', 'ai_health_assistant')
            ->latest()
            ->paginate(20);

        return view('expert.consultations', compact('consultations'));
    }

    public function mealPlans()
    {
        return view('expert.meal-plans');
    }

    public function verifications()
    {
        $results = ProductAnalysisResult::with(['product', 'user', 'expert'])
            ->orderBy('is_verified_by_expert', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending' => ProductAnalysisResult::where('is_verified_by_expert', false)->count(),
            'verified' => ProductAnalysisResult::where('is_verified_by_expert', true)->count(),
            'total' => ProductAnalysisResult::count(),
            'unique_products' => ProductAnalysisResult::distinct('product_id')->count('product_id'),
        ];

        return view('expert.verifications', compact('results', 'stats'));
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'halal_verdict' => 'required|string',
            'health_verdict' => 'nullable|string',
            'nutri_score' => 'nullable|string',
            'expert_notes' => 'nullable|string|max:1000',
        ]);

        $result = ProductAnalysisResult::findOrFail($id);

        $result->update([
            'halal_verdict' => $request->halal_verdict,
            'health_verdict' => $request->health_verdict ?: $result->health_verdict,
            'nutri_score' => $request->nutri_score ?: $result->nutri_score,
            'expert_notes' => $request->expert_notes,
            'is_verified_by_expert' => true,
            'expert_id' => Auth::user()->id_user,
        ]);

        return redirect()->back()->with('success', 'Analisis berhasil diverifikasi!');
    }
}
