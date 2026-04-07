<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HaramIngredient extends Model
{
    protected $fillable = [
        'name', 'aliases', 'category', 'severity', 'description', 'is_active',
    ];

    protected $casts = [
        'aliases'   => 'array',
        'is_active' => 'boolean',
    ];
}
