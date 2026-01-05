<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanModel extends Model
{
    use HasFactory;

    protected $table = 'scans';
    protected $primaryKey = 'id_scan';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'product_id',
        'nama_produk',
        'barcode',
        'kategori',
        'status_halal',
        'status_kesehatan',
        'tanggal_expired',
        'tanggal_scan'
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
