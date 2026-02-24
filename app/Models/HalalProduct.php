<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
