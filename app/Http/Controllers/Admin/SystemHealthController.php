<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemHealthController extends Controller
{
    public function systemHealth(Request $request): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'gemini_api' => $this->checkGeminiApi(),
            'fcm' => $this->checkFCM(),
        ];

        $allHealthy = collect($checks)->every(fn($check) => $check['status'] === 'healthy');

        return response()->json([
            'success' => true,
            'overall_status' => $allHealthy ? 'healthy' : 'warning',
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function getPerformanceMetrics(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'metrics' => [
                'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
                'peak_memory' => round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB',
                'uptime' => $this->getServerUptime(),
                'database_connections' => $this->getDatabaseConnectionCount(),
                'cache_hit_rate' => $this->getCacheHitRate(),
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    private function checkDatabase(): array
    {
        try {
            \DB::connection()->getPdo();
            return [
                'status' => 'healthy',
                'message' => 'Database connection OK',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }
    }

    private function checkCache(): array
    {
        try {
            \Cache::put('health_check', true, 60);
            $value = \Cache::get('health_check');
            \Cache::forget('health_check');
            
            return [
                'status' => $value ? 'healthy' : 'unhealthy',
                'message' => $value ? 'Cache OK' : 'Cache write/read failed',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Cache error: ' . $e->getMessage(),
            ];
        }
    }

    private function checkStorage(): array
    {
        try {
            $path = storage_path('health_check.txt');
            file_put_contents($path, 'ok');
            $content = file_get_contents($path);
            unlink($path);
            
            return [
                'status' => $content === 'ok' ? 'healthy' : 'unhealthy',
                'message' => 'Storage OK',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Storage error: ' . $e->getMessage(),
            ];
        }
    }

    private function checkGeminiApi(): array
    {
        $apiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY');
        
        if (empty($apiKey)) {
            return [
                'status' => 'warning',
                'message' => 'GEMINI_API_KEY not configured',
                'configured' => false,
            ];
        }

        if (!preg_match('/^AIza[0-9a-zA-Z_-]*$/', $apiKey)) {
            return [
                'status' => 'warning',
                'message' => 'GEMINI_API_KEY format invalid',
                'configured' => false,
            ];
        }

        return [
            'status' => 'healthy',
            'message' => 'GEMINI_API_KEY configured',
            'configured' => true,
            'key_preview' => substr($apiKey, 0, 10) . '...' . substr($apiKey, -4),
        ];
    }

    private function checkFCM(): array
    {
        $configPath = config_path('firebase.php');
        
        if (!file_exists($configPath)) {
            return [
                'status' => 'warning',
                'message' => 'Firebase config not found',
            ];
        }

        return [
            'status' => 'healthy',
            'message' => 'Firebase configured',
        ];
    }

    private function getServerUptime(): string
    {
        try {
            if (php_uname('s') === 'Linux') {
                $uptime = shell_exec('uptime -p');
                return trim($uptime) ?: 'unknown';
            }
            return 'N/A';
        } catch (\Exception $e) {
            return 'unknown';
        }
    }

    private function getDatabaseConnectionCount(): int
    {
        try {
            $result = \DB::select("SHOW PROCESSLIST");
            return count($result);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getCacheHitRate(): string
    {
        try {
            $stats = \Cache::getStore()->getRedis()->info('stats');
            if (isset($stats['hits']) && isset($stats['misses'])) {
                $total = $stats['hits'] + $stats['misses'];
                $rate = $total > 0 ? round(($stats['hits'] / $total) * 100, 2) : 0;
                return $rate . '%';
            }
            return 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}
