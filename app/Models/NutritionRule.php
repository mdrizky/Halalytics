<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionRule extends Model
{
    protected $fillable = [
        'rule_key', 'label', 'threshold_value', 'unit', 'comparison', 'is_active',
    ];

    protected $casts = [
        'threshold_value' => 'float',
        'is_active' => 'boolean',
    ];
}
