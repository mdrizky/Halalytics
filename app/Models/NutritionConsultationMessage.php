<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutritionConsultationMessage extends Model
{
    protected $fillable = [
        'consultation_id',
        'sender_role',
        'sender_user_id',
        'body',
        'attachment_path',
        'metadata',
        'read_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'read_at' => 'datetime',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(NutritionConsultation::class, 'consultation_id');
    }
}
