<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SystemHealthController extends Controller
{
    /**
     * 🏥 Get system health status
     */
    public function systemHealth()
    {
        // Storage info
        $diskTotal = @disk_total_space('/');
        $diskFree = @disk_free_space('/');
        $diskUsedPercent = $diskTotal > 0 ? round((($diskTotal - $diskFree) / $diskTotal) * 100, 1) : 0;
        $diskFreePercent = 100 - $diskUsedPercent;
        
        // Memory info (PHP process)
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $memoryPercent = $memoryLimit > 0 ? round(($memoryUsage / $memoryLimit) * 100, 1) : 0;
        
        // CPU Load (Linux only)
        $cpuLoad = function_exists('sys_getloadavg') ? sys_getloadavg() : [0, 0, 0];
        
        // Uptime
        $uptime = 'N/A';
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $uptimeOutput = @shell_exec('uptime -p 2>/dev/null');
            if ($uptimeOutput) {
                $uptime = trim($uptimeOutput);
            }
        }
        
        // Database connection check
        $dbStatus = 'Online';
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'Offline';
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'status' => 'healthy',
                'uptime' => $uptime,
                'server' => gethostname() ?: 'Halalytics-Server',
                'php_version' => PHP_VERSION,
                'storage' => [
                    'total' => $this->formatBytes($diskTotal),
                    'free' => $this->formatBytes($diskFree),
                    'used_percent' => $diskUsedPercent,
                    'free_percent' => $diskFreePercent,
                    'status' => $diskFreePercent > 20 ? 'healthy' : ($diskFreePercent > 10 ? 'warning' : 'critical')
                ],
                'memory' => [
                    'usage' => $this->formatBytes($memoryUsage),
                    'peak' => $this->formatBytes($memoryPeak),
                    'limit' => $this->formatBytes($memoryLimit),
                    'usage_percent' => $memoryPercent,
                    'status' => $memoryPercent < 80 ? 'healthy' : ($memoryPercent < 95 ? 'warning' : 'critical')
                ],
                'cpu' => [
                    'load_1min' => round($cpuLoad[0], 2),
                    'load_5min' => round($cpuLoad[1], 2),
                    'load_15min' => round($cpuLoad[2], 2),
                    'status' => $cpuLoad[0] < 2 ? 'healthy' : ($cpuLoad[0] < 4 ? 'warning' : 'critical')
                ],
                'database' => [
                    'status' => $dbStatus,
                    'driver' => config('database.default'),
                    'connection' => config('database.connections.' . config('database.default') . '.host', 'N/A')
                ],
                'timestamp' => now()->toISOString(),
            ]
        ]);
    }

    /**
     * 🧮 Parse memory limit string
     */
    private function parseMemoryLimit($limit): int
    {
        $limit = strtolower(trim($limit));
        
        if ($limit === '-1') {
            return -1; // Unlimited
        }
        
        $unit = preg_replace('/[^a-z]/', '', $limit);
        $value = (int) preg_replace('/[^0-9]/', '', $limit);
        
        switch ($unit) {
            case 'g':
                return $value * 1024 * 1024 * 1024;
            case 'm':
                return $value * 1024 * 1024;
            case 'k':
                return $value * 1024;
            default:
                return (int) $limit;
        }
    }

    /**
     * 💾 Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * 📊 Get detailed performance metrics
     */
    public function getPerformanceMetrics()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'php_info' => [
                    'version' => PHP_VERSION,
                    'memory_limit' => ini_get('memory_limit'),
                    'max_execution_time' => ini_get('max_execution_time'),
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                ],
                'laravel_info' => [
                    'version' => app()->version(),
                    'environment' => config('app.env'),
                    'debug_mode' => config('app.debug'),
                    'cache_driver' => config('cache.default'),
                    'session_driver' => config('session.driver'),
                    'queue_driver' => config('queue.default'),
                ],
                'database_info' => [
                    'driver' => config('database.default'),
                    'host' => config('database.connections.' . config('database.default') . '.host'),
                    'database' => config('database.connections.' . config('database.default') . '.database'),
                    'charset' => config('database.connections.' . config('database.default') . '.charset'),
                ],
            ]
        ]);
    }
}
