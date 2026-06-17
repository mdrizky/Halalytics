<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationSession extends Model
{
    protected $table = 'consultation_sessions';
    protected $primaryKey = 'id_session';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'consultation_id',
        'expert_id',
        'user_id',
        'scheduled_at',
        'started_at',
        'ended_at',
        'duration_minutes',
        'status',
        'meeting_link',
        'notes',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id', 'id_consultation');
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expert_id', 'id_user');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}