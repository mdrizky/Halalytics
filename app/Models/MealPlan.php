<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealPlan extends Model
{
    protected $fillable = [
        'nutritionist_id',
        'user_id',
        'title',
        'description',
        'meals',
        'notes',
        'duration_days',
        'status',
        'start_date',
        'end_date',
        'target_calories',
        'nutritional_targets',
    ];

    protected $casts = [
        'meals' => 'array',
        'nutritional_targets' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function nutritionist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nutritionist_id', 'id_user');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
