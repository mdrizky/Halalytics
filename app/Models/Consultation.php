<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultation extends Model
{
    protected $table = 'consultations';
    protected $primaryKey = 'id_consultation';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'expert_id',
        'status',
        'subject',
        'description',
        'consultation_type',
        'preferred_date',
        'preferred_time',
        'notes',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'completed_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expert_id', 'id_user');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConsultationMessage::class, 'consultation_id', 'id_consultation');
    }

    public function session(): HasMany
    {
        return $this->hasMany(ConsultationSession::class, 'consultation_id', 'id_consultation');
    }
}

class ConsultationMessage extends Model
{
    protected $table = 'consultation_messages';
    protected $primaryKey = 'id_message';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'consultation_id',
        'sender_id',
        'message',
        'message_type',
        'attachment_path',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id', 'id_consultation');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'id_user');
    }
}