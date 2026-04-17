<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\DisplayImageService;

class HalalProduct extends Model
{
    protected $fillable = [
        'product_barcode',
        'product_name',
        'brand',
        'halal_certificate_number',
        'halal_status',
        'certification_body',
        'certificate_valid_until',
        'certificate_data',
        'last_checked_at'
    ];

    protected $casts = [
        'certificate_data' => 'array',
        'certificate_valid_until' => 'date',
        'last_checked_at' => 'datetime'
    ];

    public function getImageUrlAttribute(): string
    {
        return app(DisplayImageService::class)->resolve(null, [
            'name' => $this->product_name,
            'brand' => $this->brand,
            'barcode' => $this->product_barcode,
            'category' => 'halal',
        ], 'product');
    }
}
