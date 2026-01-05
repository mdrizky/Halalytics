<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'id_product';
    public $timestamps = true;

    protected $fillable = [
        'nama_product',
        'barcode',
        'komposisi',
        'status',
        'info_gizi',
        'kategori_id'
    ];

    // Relasi ke Kategori
    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'id_kategori');
    }

    // Relasi ke Scan
    public function scans()
    {
        return $this->hasMany(ScanModel::class, 'product_id', 'id_product');
    }

    // Relasi ke Report
    public function reports()
    {
        return $this->hasMany(ReportModel::class, 'product_id', 'id_product');
    }
}
