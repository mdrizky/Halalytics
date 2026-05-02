<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImageFallback extends Model
{
    protected $fillable = [
        'category_key',
        'label',
        'image_path',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
