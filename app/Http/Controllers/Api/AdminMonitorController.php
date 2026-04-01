<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineReminder;
use App\Models\Notification;
use App\Models\ScanModel;
use App\Services\FirebaseRealtimeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminMonitorController extends Controller
{
    public function __construct(
        private readonly FirebaseRealtimeService $firebaseRealtimeService
    ) {
    }

    // Real-time stats for Admin Dashboard
    public function getDashboardStats()
    {
        $scanHistoryTable = Schema::hasTable('scan_histories') ? 'scan_histories' : (Schema::hasTable('scan_history') ? 'scan_history' : null);
        $mealLogsTable = Schema::hasTable('meal_logs');
        $activityLogsTable = Schema::hasTable('activity_logs');
        $today = now()->format('Y-m-d');

        if (!Schema::hasTable('activity_events')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'total_users' => DB::table('users')->where('role', 'user')->count(),
                    'scans_today' => $scanHistoryTable ? DB::table($scanHistoryTable)->whereDate('created_at', $today)->count() : ScanModel::whereDate('tanggal_scan', $today)->count(),
                    'meals_analyzed' => $mealLogsTable ? DB::table('meal_logs')->whereDate('created_at', $today)->count() : 0,
                    'risky_activities' => $activityLogsTable
                        ? DB::table('activity_logs')->whereDate('created_at', $today)->where('is_risk_detected', true)->count()
                        : 0,
                    'total_external_scans' => 0,
                    'total_skincare_analyses' => 0,
                    'total_interaction_checks' => 0,
                    'major_or_contra_count' => 0,
                    'total_risk_checks' => 0,
                    'total_drug_food_conflicts' => 0,
                    'fallback_mode' => true,
                ],
            ]);
        }

        $todayEvents = DB::table('activity_events')->whereDate('created_at', $today);

        $stats = [
            'total_users' => DB::table('users')->where('role', 'user')->count(),
            'scans_today' => $scanHistoryTable ? DB::table($scanHistoryTable)->whereDate('created_at', $today)->count() : ScanModel::whereDate('tanggal_scan', $today)->count(),
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
            $fallbackFeed = ScanModel::query()
                ->with('user')
                ->orderByDesc('tanggal_scan')
                ->limit(20)
                ->get()
                ->map(function (ScanModel $scan) {
                    return [
                        'id' => 'legacy_scan_' . $scan->id_scan,
                        'event_type' => 'legacy_scan',
                        'entity_ref' => $scan->barcode,
                        'summary' => 'Scan produk: ' . ($scan->nama_produk ?: 'Unknown product'),
                        'status' => in_array(strtolower((string) $scan->status_halal), ['halal'], true) ? 'success' : 'warning',
                        'payload_json' => null,
                        'created_at' => optional($scan->tanggal_scan ?: $scan->created_at)?->toDateTimeString(),
                        'user_name' => $scan->user->username ?? $scan->user->full_name ?? 'Guest',
                    ];
                })
                ->values();

            return response()->json(['success' => true, 'data' => $fallbackFeed, 'fallback_mode' => true]);
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

        if ($logs->isEmpty()) {
            $logs = ScanModel::query()
                ->with('user')
                ->orderByDesc('tanggal_scan')
                ->limit(20)
                ->get()
                ->map(function (ScanModel $scan) {
                    return (object) [
                        'id' => 'legacy_scan_' . $scan->id_scan,
                        'event_type' => 'legacy_scan',
                        'entity_ref' => $scan->barcode,
                        'summary' => 'Scan produk: ' . ($scan->nama_produk ?: 'Unknown product'),
                        'status' => in_array(strtolower((string) $scan->status_halal), ['halal'], true) ? 'success' : 'warning',
                        'payload_json' => null,
                        'created_at' => optional($scan->tanggal_scan ?: $scan->created_at)?->toDateTimeString(),
                        'user_name' => $scan->user->username ?? $scan->user->full_name ?? 'Guest',
                    ];
                });
        }

        return response()->json(['success' => true, 'data' => $logs]);
    }
    
    // Admin Actions (Control)
    public function updateMedicineStatus(Request $request, $id)
    {
        $request->validate(['halal_status' => 'required|in:halal,haram,syubhat']);

        if (!Schema::hasTable('medicines')) {
            return response()->json([
                'success' => false,
                'message' => 'Tabel medicines tidak tersedia.'
            ], 503);
        }

        $medicine = Medicine::query()
            ->where('id_medicine', $id)
            ->orWhere('id', $id)
            ->first();

        if (!$medicine) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found'
            ], 404);
        }

        $medicine->update([
            'halal_status' => $request->halal_status,
            'is_verified_by_admin' => true,
            'updated_at' => now()
        ]);

        $affectedUserIds = [];
        if (Schema::hasTable('medicine_reminders')) {
            $affectedUserIds = MedicineReminder::query()
                ->where('id_medicine', $medicine->id_medicine)
                ->where('is_active', true)
                ->pluck('id_user')
                ->filter(fn ($idUser) => !empty($idUser))
                ->unique()
                ->values()
                ->all();
        }

        $statusLabel = match ($request->halal_status) {
            'halal' => 'HALAL',
            'haram' => 'HARAM',
            default => 'SYUBHAT',
        };
        $productName = $medicine->name ?: $medicine->generic_name ?: 'Obat';

        $notified = 0;
        foreach ($affectedUserIds as $userId) {
            $notification = Notification::create([
                'user_id' => (int) $userId,
                'title' => 'Update Status Obat',
                'message' => "Status {$productName} diperbarui menjadi {$statusLabel} oleh admin.",
                'type' => 'medicine',
                'action_type' => 'open_health_suite',
                'action_value' => 'health_suite_hub',
                'extra_data' => [
                    'medicine_id' => (int) $medicine->id_medicine,
                    'medicine_name' => $productName,
                    'halal_status' => $request->halal_status,
                ],
                'is_read' => false,
            ]);
            $this->firebaseRealtimeService->syncNotification($notification);
            $notified++;
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated',
            'data' => [
                'medicine_id' => (int) $medicine->id_medicine,
                'halal_status' => $medicine->halal_status,
                'notified_users' => $notified,
            ],
        ]);
    }
}
