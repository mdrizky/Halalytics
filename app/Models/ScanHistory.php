<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanHistory extends Model
{
    protected $fillable = [
        'user_id',
        'scannable_type', 'scannable_id',
        'product_name', 'product_image', 'barcode', 'halal_status',
        'scan_method', 'source',
        'latitude', 'longitude',
        'confidence_score', 'nutrition_snapshot',
        'firebase_key', 'is_synced'
    ];

    protected $casts = [
        'nutrition_snapshot' => 'array',
        'is_synced' => 'boolean',
    ];

    // Polymorphic relation
    public function scannable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    // Scope by user
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope by date
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
}
