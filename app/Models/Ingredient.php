<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\DisplayImageService;

class Ingredient extends Model
{
    use HasFactory;

    protected $table = 'ingredients';
    protected $primaryKey = 'id_ingredient';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'e_number',
        'halal_status',
        'health_risk',
        'description',
        'sources',
        'notes',
        'active',
        'image_url'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Scope for active ingredients
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope by halal status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('halal_status', $status);
    }

    public function getImageUrlAttribute(): string
    {
        return app(DisplayImageService::class)->resolve($this->attributes['image_url'] ?? null, [
            'name' => $this->name,
            'category' => 'ingredient',
            'e_number' => $this->e_number,
        ], 'ingredient');
    }
}
