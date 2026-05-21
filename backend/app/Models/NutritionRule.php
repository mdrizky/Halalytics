<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutritionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'metric', 'threshold', 'unit', 'severity', 'is_active', 'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'threshold' => 'float',
    ];
}
