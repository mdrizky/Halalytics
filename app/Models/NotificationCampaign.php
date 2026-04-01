<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationCampaign extends Model
{
    protected $fillable = [
        'name', 'title', 'body', 'image_url', 'action_url',
        'target_segment', 'target_count', 'sent_count', 'opened_count',
        'status', 'scheduled_at', 'sent_at', 'created_by',
    ];

    protected $casts = [
        'target_segment' => 'array',
        'target_count'   => 'integer',
        'sent_count'     => 'integer',
        'opened_count'   => 'integer',
        'scheduled_at'   => 'datetime',
        'sent_at'        => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    public function scopeDraft($query)     { return $query->where('status', 'draft'); }
    public function scopeScheduled($query) { return $query->where('status', 'scheduled'); }
    public function scopeSent($query)      { return $query->where('status', 'sent'); }

    public function getOpenRateAttribute(): float
    {
        return $this->sent_count > 0
            ? round(($this->opened_count / $this->sent_count) * 100, 1)
            : 0.0;
    }

    public function markSending(): void
    {
        $this->update(['status' => 'sending']);
    }

    public function markSent(int $sentCount): void
    {
        $this->update([
            'status'     => 'sent',
            'sent_count' => $sentCount,
            'sent_at'    => now(),
        ]);
    }
}
