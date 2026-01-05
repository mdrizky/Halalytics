<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportModel extends Model
{
    use HasFactory;

    protected $table = 'reports';
    protected $primaryKey = 'id_report';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'product_id',
        'laporan',
        'status'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    // Relasi ke Produk
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'id_product');
    }
}
