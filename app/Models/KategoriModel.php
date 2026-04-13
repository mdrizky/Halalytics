<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\DisplayImageService;

class KategoriModel extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';
    public $timestamps = true;

    protected $fillable = [
        'nama_kategori',
        'description',
    ];

    // Relasi ke Produk
    public function products()
    {
        return $this->hasMany(ProductModel::class, 'kategori_id', 'id_kategori');
    }

    public function getThumbnailUrlAttribute(): string
    {
        $product = $this->relationLoaded('products')
            ? $this->products->first()
            : $this->products()->whereNotNull('image')->latest('id_product')->first();

        return app(DisplayImageService::class)->resolve(
            $product?->image,
            [
                'name' => $this->nama_kategori,
                'category' => $this->nama_kategori,
            ],
            'category'
        );
    }
}
