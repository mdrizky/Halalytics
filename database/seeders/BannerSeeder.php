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
            'image' => null,
            'is_active' => true,
            'position' => 1
        ]);

        Banner::create([
            'title' => 'Cek Kandungan Nutrisi',
            'description' => 'Halalytics membantu Anda menganalisis bahan-bahan makanan dengan detail.',
            'image' => null,
            'is_active' => true,
            'position' => 2
        ]);
    }
}
