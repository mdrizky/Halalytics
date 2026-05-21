<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiPrompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'prompt', 'version', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
