<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFoodBehavior extends Model
{
    protected $table = 'user_food_behavior';

    protected $fillable = [
        'user_id', 'product_category', 'scan_count', 'high_sugar_count',
        'high_sodium_count', 'junk_food_count', 'ultra_processed_count',
        'week_start', 'last_scan', 'consumption_risk',
    ];

    protected $casts = [
        'week_start' => 'date',
        'last_scan' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
