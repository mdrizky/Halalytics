<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer',
            'resource_type' => 'nullable|string',
            'action' => 'nullable|string',
            'result' => 'nullable|in:success,denied',
            'days' => 'nullable|integer|min:1|max:90',
        ]);

        $query = AccessLog::query();

        if ($validated['user_id'] ?? null) {
            $query->where('user_id', $validated['user_id']);
        }

        if ($validated['resource_type'] ?? null) {
            $query->where('resource_type', $validated['resource_type']);
        }

        if ($validated['action'] ?? null) {
            $query->where('action', 'like', '%' . $validated['action'] . '%');
        }

        if ($validated['result'] ?? null) {
            $query->where('result', $validated['result']);
        }

        $days = $validated['days'] ?? 30;
        $query->where('created_at', '>=', now()->subDays($days));

        $logs = $query
            ->with('user:id_user,username,full_name,role')
            ->orderByDesc('created_at')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $log = AccessLog::with('user:id_user,username,full_name,role')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $log,
        ]);
    }

    public function statistics(Request $request): JsonResponse
    {
        $days = $request->input('days', 30);
        $startDate = now()->subDays($days);

        $stats = [
            'total_accesses' => AccessLog::where('created_at', '>=', $startDate)->count(),
            'successful_accesses' => AccessLog::where('result', 'success')
                ->where('created_at', '>=', $startDate)->count(),
            'denied_accesses' => AccessLog::where('result', 'denied')
                ->where('created_at', '>=', $startDate)->count(),
            'by_resource_type' => AccessLog::where('created_at', '>=', $startDate)
                ->groupBy('resource_type')
                ->selectRaw('resource_type, COUNT(*) as count')
                ->get()
                ->keyBy('resource_type')
                ->map(fn($item) => $item->count),
            'by_user_role' => AccessLog::where('created_at', '>=', $startDate)
                ->join('users', 'access_logs.user_id', '=', 'users.id_user')
                ->groupBy('users.role')
                ->selectRaw('users.role, COUNT(*) as count')
                ->get()
                ->keyBy('role')
                ->map(fn($item) => $item->count),
            'recent_denied' => AccessLog::where('result', 'denied')
                ->where('created_at', '>=', $startDate)
                ->with('user:id_user,username,full_name')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
