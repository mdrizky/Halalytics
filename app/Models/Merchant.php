<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    protected $fillable = [
        'name', 'type', 'address', 'latitude', 'longitude', 'phone',
        'website', 'affiliate_link', 'is_verified', 'google_place_id',
        'opening_hours', 'image_path',
    ];

    protected $casts = [
        'is_verified'   => 'boolean',
        'opening_hours' => 'array',
        'latitude'      => 'double',
        'longitude'     => 'double',
    ];

    public function products()
    {
        return $this->hasMany(MarketplaceProduct::class);
    }
}
