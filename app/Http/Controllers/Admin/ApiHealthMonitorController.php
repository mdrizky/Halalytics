<?php

namespace App\Http\Controllers\Admin;

use App\Models\ApiHealthLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiHealthMonitorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $apis = ['gemini', 'openfoodfacts', 'fda', 'openbeautyfacts'];

        $current = [];
        foreach ($apis as $api) {
            $latest = ApiHealthLog::where('api_name', $api)
                ->orderByDesc('checked_at')
                ->first();

            $uptime24h = $this->calculateUptime($api, 24);
            $uptime7d = $this->calculateUptime($api, 168);
            $avgLatency = ApiHealthLog::where('api_name', $api)
                ->where('status', 'up')
                ->where('checked_at', '>=', now()->subDay())
                ->avg('latency_ms') ?? 0;

            $current[$api] = [
                'status' => $latest->status ?? 'unknown',
                'latency_ms' => $latest->latency_ms ?? null,
                'last_check' => $latest->checked_at ?? null,
                'http_status' => $latest->http_status ?? null,
                'uptime_24h' => round($uptime24h, 1),
                'uptime_7d' => round($uptime7d, 1),
                'avg_latency' => round($avgLatency, 0),
            ];
        }

        // History for chart (last 24 hours, every 5 minutes = 288 points)
        $history = ApiHealthLog::where('checked_at', '>=', now()->subHours(24))
            ->orderBy('checked_at')
            ->get()
            ->groupBy('api_name');

        return view('admin.api-monitor.index', compact('current', 'history', 'apis'));
    }

    public function history(Request $request, $apiName)
    {
        $days = $request->get('days', 7);

        $logs = ApiHealthLog::where('api_name', $apiName)
            ->where('checked_at', '>=', now()->subDays($days))
            ->orderByDesc('checked_at')
            ->paginate(50);

        $uptime = $this->calculateUptime($apiName, $days * 24);

        return view('admin.api-monitor.history', compact('logs', 'apiName', 'uptime', 'days'));
    }

    /**
     * Manual health check trigger
     */
    public function check()
    {
        dispatch(new \App\Jobs\ApiHealthCheckJob());
        return back()->with('success', 'Health check dipicu. Hasil akan muncul dalam beberapa detik.');
    }

    private function calculateUptime(string $apiName, int $hours): float
    {
        $total = ApiHealthLog::where('api_name', $apiName)
            ->where('checked_at', '>=', now()->subHours($hours))
            ->count();

        if ($total === 0) return 0;

        $up = ApiHealthLog::where('api_name', $apiName)
            ->where('checked_at', '>=', now()->subHours($hours))
            ->whereIn('status', ['up', 'degraded'])
            ->count();

        return ($up / $total) * 100;
    }
}
