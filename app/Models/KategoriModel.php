<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\DisplayImageService;
use Illuminate\Support\Str;

class KategoriModel extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'id_kategori';
    public $timestamps = true;

    protected $fillable = [
        'nama_kategori',
        'description',
        'image',
    ];

    // Relasi ke Produk
    public function products()
    {
        return $this->hasMany(ProductModel::class, 'kategori_id', 'id_kategori');
    }

    public function getThumbnailUrlAttribute(): string
    {
        // Prioritize the category's own image if it exists
        if ($this->image) {
            return app(DisplayImageService::class)->resolve(
                $this->getRawOriginal('image') ?? $this->image,
                [
                    'name' => $this->nama_kategori,
                    'category' => $this->nama_kategori,
                ],
                'category'
            );
        }

        // Direct Unsplash URL based on category name
        $searchTerm = match (Str::lower($this->nama_kategori ?? '')) {
            'makanan' => 'food',
            'minuman' => 'beverage',
            'kosmetik' => 'cosmetics',
            'obat' => 'medicine',
            default => blank($this->nama_kategori) ? 'category' : Str::slug($this->nama_kategori, '+'),
        };

        return "https://source.unsplash.com/400x400/?{$searchTerm}";
    }
}
