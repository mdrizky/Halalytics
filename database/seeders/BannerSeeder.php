<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    public function run()
    {
        Banner::create([
            'title' => 'Scan Produk Halal Sekarang!',
            'description' => 'Gunakan kamera AI kami untuk memverifikasi produk dalam hitungan detik.',
            'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1200&h=500&fit=crop',
            'is_active' => true,
            'position' => 1
        ]);

        Banner::create([
            'title' => 'Cek Kandungan Nutrisi',
            'description' => 'Halalytics membantu Anda menganalisis bahan-bahan makanan dengan detail.',
            'image' => 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=1200&h=500&fit=crop',
            'is_active' => true,
            'position' => 2
        ]);

        Banner::create([
            'title' => 'Konsultasi dengan Ahli Gizi',
            'description' => 'Dapatkan rekomendasi pola makan sehat dari ahli gizi profesional.',
            'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1200&h=500&fit=crop',
            'is_active' => true,
            'position' => 3
        ]);
    }
}
