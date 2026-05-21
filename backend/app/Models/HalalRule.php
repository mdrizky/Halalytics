<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HalalRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'rule_type', 'keyword', 'status', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
