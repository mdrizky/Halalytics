<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForbiddenIngredient extends Model
{
    use HasFactory;

    protected $table = 'forbidden_ingredients';

    protected $fillable = [
        'name',
        'code',
        'aliases',
        'type',
        'risk_level',
        'reason',
        'description',
        'source',
        'is_active'
    ];

    protected $casts = [
        'aliases' => 'array',
        'is_active' => 'boolean',
    ];
}
