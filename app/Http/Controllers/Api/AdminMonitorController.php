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

        $stats = [
            'total_users' => DB::table('users')->where('role', 'user')->count(),
            'scans_today' => DB::table('scan_history')->whereDate('created_at', $today)->count(),
            'meals_analyzed' => DB::table('meal_logs')->whereDate('created_at', $today)->count(),
            'risky_activities' => DB::table('activity_logs')
                                    ->whereDate('created_at', $today)
                                    ->where('is_risk_detected', true)
                                    ->count(),
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    // Live Feed
    public function getActivityFeed()
    {
        $logs = DB::table('activity_logs')
            ->join('users', 'activity_logs.id_user', '=', 'users.id_user')
            ->select('activity_logs.*', 'users.full_name as user_name')
            ->orderBy('activity_logs.created_at', 'desc')
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
