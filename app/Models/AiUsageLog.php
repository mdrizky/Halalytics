<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    protected $fillable = [
        'user_id', 'feature', 'model', 'input_tokens',
        'output_tokens', 'response_time_ms', 'status', 'error_message',
    ];

    protected $casts = [
        'input_tokens'     => 'integer',
        'output_tokens'    => 'integer',
        'response_time_ms' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function scopeForFeature($query, string $feature)
    {
        return $query->where('feature', $feature);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeErrors($query)
    {
        return $query->where('status', 'error');
    }

    /** Log an AI usage event and return the model. */
    public static function logUsage(array $data): self
    {
        return static::create($data);
    }

    /** Get today's total request count. */
    public static function todayCount(?int $userId = null): int
    {
        $q = static::today();
        if ($userId) $q->where('user_id', $userId);
        return $q->count();
    }

    /** Average response time today (ms). */
    public static function avgResponseTimeToday(): float
    {
        return (float) static::today()->successful()->avg('response_time_ms');
    }
}
