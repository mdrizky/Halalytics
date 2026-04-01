<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiHealthLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'api_name', 'status', 'latency_ms', 'http_status',
        'error_details', 'checked_at',
    ];

    protected $casts = [
        'latency_ms'  => 'float',
        'http_status' => 'integer',
        'checked_at'  => 'datetime',
    ];

    public function scopeForApi($query, string $apiName)
    {
        return $query->where('api_name', $apiName);
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('checked_at', '>=', now()->subHours($hours));
    }

    /** Get latest status for each API. */
    public static function latestPerApi(): array
    {
        $apis = ['gemini', 'openfoodfacts', 'fda', 'bpom', 'openbeautyfacts', 'firebase_fcm'];
        $result = [];

        foreach ($apis as $api) {
            $latest = static::forApi($api)->orderByDesc('checked_at')->first();
            $result[$api] = $latest ? [
                'status'     => $latest->status,
                'latency_ms' => $latest->latency_ms,
                'checked_at' => $latest->checked_at->toIso8601String(),
            ] : ['status' => 'unknown', 'latency_ms' => null, 'checked_at' => null];
        }

        return $result;
    }

    /** Get uptime percentage for an API over given days. */
    public static function uptimePercent(string $apiName, int $days = 30): float
    {
        $total = static::forApi($apiName)
            ->where('checked_at', '>=', now()->subDays($days))
            ->count();

        if ($total === 0) return 100.0;

        $up = static::forApi($apiName)
            ->where('checked_at', '>=', now()->subDays($days))
            ->where('status', 'up')
            ->count();

        return round(($up / $total) * 100, 2);
    }
}
