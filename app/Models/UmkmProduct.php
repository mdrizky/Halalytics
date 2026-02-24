<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UmkmProduct extends Model
{
    protected $fillable = [
        'umkm_name', 'umkm_owner', 'umkm_phone', 'umkm_address',
        'latitude', 'longitude',
        'product_name', 'product_description', 'product_category',
        'halal_status', 'halal_cert_number', 'halal_cert_expiry', 'halal_cert_image',
        'nutrition_info', 'ingredients',
        'qr_code_unique_id', 'qr_code_image_path',
        'is_verified', 'is_active', 'verified_at', 'verified_by',
        'scan_count', 'last_scanned_at'
    ];

    protected $casts = [
        'nutrition_info' => 'array',
        'ingredients' => 'array',
        'halal_cert_expiry' => 'date',
        'verified_at' => 'datetime',
        'last_scanned_at' => 'datetime',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->qr_code_unique_id) {
                $model->qr_code_unique_id = 'UMKM-' . strtoupper(Str::random(10));
            }
        });
    }

    public function recordScan()
    {
        $this->increment('scan_count');
        $this->update(['last_scanned_at' => now()]);
    }
}
