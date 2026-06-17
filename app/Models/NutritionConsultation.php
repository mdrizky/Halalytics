<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NutritionConsultation extends Model
{
    protected $fillable = [
        'type',
        'user_id',
        'nutritionist_id',
        'admin_id',
        'status',
        'subject',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function nutritionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nutritionist_id', 'id_user');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id', 'id_user');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(NutritionConsultationMessage::class, 'consultation_id');
    }
}
