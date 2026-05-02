<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\DisplayImageService;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banners';
    protected $fillable = ['title', 'description', 'image', 'is_active', 'position'];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getImageUrlAttribute(): string
    {
        return app(DisplayImageService::class)->resolve($this->attributes['image'] ?? null, [
            'name' => $this->title,
            'category' => 'banner',
        ], 'banner');
    }
}
