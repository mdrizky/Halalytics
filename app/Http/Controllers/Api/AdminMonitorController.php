<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMonitorController extends Controller
{
    // Real-time stats for Admin Dashboard
    public function getDashboardStats()
    {
        $today = now()->format('Y-m-d');
        $todayEvents = DB::table('activity_events')->whereDate('created_at', $today);

        $stats = [
            'total_users' => DB::table('users')->where('role', 'user')->count(),
            'scans_today' => DB::table('scan_history')->whereDate('created_at', $today)->count(),
            'meals_analyzed' => DB::table('meal_logs')->whereDate('created_at', $today)->count(),
            'risky_activities' => DB::table('activity_logs')
                                    ->whereDate('created_at', $today)
                                    ->where('is_risk_detected', true)
                                    ->count(),
            'total_external_scans' => (clone $todayEvents)->where('event_type', 'external_scan')->count(),
            'total_skincare_analyses' => (clone $todayEvents)->where('event_type', 'skincare_analysis')->count(),
            'total_interaction_checks' => (clone $todayEvents)->where('event_type', 'drug_interaction')->count(),
            'major_or_contra_count' => (clone $todayEvents)
                ->where('event_type', 'drug_interaction')
                ->where(function ($q) {
                    $q->whereJsonContains('payload_json->severity', 'major')
                        ->orWhereJsonContains('payload_json->severity', 'contraindicated');
                })
                ->count(),
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    // Live Feed
    public function getActivityFeed()
    {
        $logs = DB::table('activity_events')
            ->leftJoin('users', 'activity_events.user_id', '=', 'users.id_user')
            ->select(
                'activity_events.id',
                'activity_events.event_type',
                'activity_events.entity_ref',
                'activity_events.summary',
                'activity_events.status',
                'activity_events.payload_json',
                'activity_events.created_at',
                DB::raw('COALESCE(activity_events.username, users.username, users.full_name, \'Guest\') as user_name')
            )
            ->orderBy('activity_events.created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json(['success' => true, 'data' => $logs]);
    }
    
    // Admin Actions (Control)
    public function updateMedicineStatus(Request $request, $id)
    {
        $request->validate(['halal_status' => 'required|in:halal,haram,syubhat']);
        
        DB::table('medicines')->where('id', $id)->update([
            'halal_status' => $request->halal_status,
            'is_verified_by_admin' => true,
            'updated_at' => now()
        ]);
        
        // TODO: Trigger Notification to users using this med
        
        return response()->json(['success' => true, 'message' => 'Status updated']);
    }
}
