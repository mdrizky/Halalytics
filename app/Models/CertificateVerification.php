<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'certificate_number',
        'product_name',
        'manufacturer',
        'expiry_date',
        'status',
        'issuer',
        'raw_data'
    ];

    protected $casts = [
        'raw_data' => 'array',
        'expiry_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
