<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminMonitorController extends Controller
{
    // Real-time stats for Admin Dashboard
    public function getDashboardStats()
    {
        if (!Schema::hasTable('activity_events')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_users' => DB::table('users')->where('role', 'user')->count(),
                    'scans_today' => 0,
                    'meals_analyzed' => 0,
                    'risky_activities' => 0,
                    'total_external_scans' => 0,
                    'total_skincare_analyses' => 0,
                    'total_interaction_checks' => 0,
                    'major_or_contra_count' => 0,
                    'total_risk_checks' => 0,
                    'total_drug_food_conflicts' => 0,
                ],
            ]);
        }

        $today = now()->format('Y-m-d');
        $todayEvents = DB::table('activity_events')->whereDate('created_at', $today);

        $scanHistoryTable = Schema::hasTable('scan_histories') ? 'scan_histories' : (Schema::hasTable('scan_history') ? 'scan_history' : null);
        $mealLogsTable = Schema::hasTable('meal_logs');
        $activityLogsTable = Schema::hasTable('activity_logs');

        $stats = [
            'total_users' => DB::table('users')->where('role', 'user')->count(),
            'scans_today' => $scanHistoryTable ? DB::table($scanHistoryTable)->whereDate('created_at', $today)->count() : 0,
            'meals_analyzed' => $mealLogsTable ? DB::table('meal_logs')->whereDate('created_at', $today)->count() : 0,
            'risky_activities' => $activityLogsTable
                ? DB::table('activity_logs')
                    ->whereDate('created_at', $today)
                    ->where('is_risk_detected', true)
                    ->count()
                : 0,
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
            'total_risk_checks' => (clone $todayEvents)->where('event_type', 'health_risk_score')->count(),
            'total_drug_food_conflicts' => (clone $todayEvents)
                ->where('event_type', 'drug_food_conflict')
                ->where(function ($q) {
                    $q->whereJsonContains('payload_json->has_conflict', true)
                        ->orWhere('status', 'warning');
                })
                ->count(),
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    // Live Feed
    public function getActivityFeed()
    {
        if (!Schema::hasTable('activity_events')) {
            return response()->json(['success' => true, 'data' => []]);
        }

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
