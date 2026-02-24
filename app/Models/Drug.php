<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    protected $fillable = [
        'name', 'generic_name', 'manufacturer', 'category',
        'halal_status', 'halal_cert_number', 'halal_cert_expiry',
        'halal_cert_issuer', 'ingredients', 'image_url',
        'description', 'dosage_info', 'side_effects', 'warnings'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'halal_cert_expiry' => 'date'
    ];

    public function pillIdentifications()
    {
        return $this->hasMany(PillIdentification::class);
    }
}
