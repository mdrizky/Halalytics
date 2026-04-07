<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'marketplace_products';

    protected $fillable = [
        'merchant_id',
        'name',
        'description',
        'price',
        'image_path',
        'category',
        'is_halal_certified',
        'halal_cert_number',
        'stock',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_halal_certified' => 'boolean',
        'stock' => 'integer',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
