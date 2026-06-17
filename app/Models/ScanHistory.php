<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\DisplayImageService;

class ScanHistory extends Model
{
    protected $fillable = [
        'user_id',
        'scannable_type', 'scannable_id',
        'product_name', 'product_image', 'barcode', 'halal_status',
        'health_score',
        'calories', 'protein', 'carbs', 'fat', 'fiber', 'sugar', 'sodium',
        'nova_group', 'nutri_score',
        'ai_recommendation',
        'short_term_effects', 'long_term_effects',
        'scan_date',
        'scan_method', 'source',
        'latitude', 'longitude',
        'confidence_score', 'nutrition_snapshot',
        'firebase_key', 'is_synced'
    ];

    protected $casts = [
        'nutrition_snapshot' => 'array',
        'short_term_effects' => 'array',
        'long_term_effects' => 'array',
        'is_synced' => 'boolean',
        'scan_date' => 'date',
    ];

    public function scannable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function ingredients()
    {
        return $this->hasMany(ScanIngredient::class);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month);
    }

    public function getProductImageAttribute($value): string
    {
        return app(DisplayImageService::class)->resolve($value, [
            'name' => $this->product_name,
            'barcode' => $this->barcode,
            'category' => 'scan-history',
        ], 'product');
    }
}
