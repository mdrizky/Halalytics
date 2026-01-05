<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriModel extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';
    public $timestamps = true;

    protected $fillable = [
        'nama_kategori',
    ];

    // Relasi ke Produk
    public function products()
    {
        return $this->hasMany(ProductModel::class, 'kategori_id', 'id_kategori');
    }
}
