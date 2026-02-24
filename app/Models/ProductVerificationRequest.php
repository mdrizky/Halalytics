<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVerificationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'product_name',
        'user_id',
        'notes',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
