<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceProduct extends Model
{
    protected $fillable = [
        'merchant_id', 'name', 'description', 'price', 'image_path',
        'category', 'is_halal_certified', 'halal_cert_number', 'stock',
    ];

    protected $casts = ['is_halal_certified' => 'boolean'];

    public function merchant() { return $this->belongsTo(Merchant::class); }
}
