<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CategoryPolishSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        KategoriModel::truncate();
        Schema::enableForeignKeyConstraints();

        $categories = [
            ['nama_kategori' => 'Bumbu Dapur', 'image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Jamu Tradisional', 'image' => 'https://images.unsplash.com/photo-1515023115689-589c33041d3c?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Obat Bebas', 'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Skincare', 'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Makanan Ringan', 'image' => 'https://images.unsplash.com/photo-1599599810769-bcde5a160d32?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Minuman Kemasan', 'image' => 'https://images.unsplash.com/photo-1527661591475-527312dd65f5?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Produk Susu', 'image' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Daging Olahan', 'image' => 'https://images.unsplash.com/photo-1607349913338-fca6f7fc42d0?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Kosmetik Wajah', 'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Perawatan Rambut', 'image' => 'https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Kebutuhan Bayi', 'image' => 'https://images.unsplash.com/photo-1519689680058-324335c77eba?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Bahan Roti & Kue', 'image' => 'https://images.unsplash.com/photo-1556910103-1c02745a87a5?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Suplemen & Vitamin', 'image' => 'https://images.unsplash.com/photo-1550572017-edb1e06e3e57?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Kopi & Teh', 'image' => 'https://images.unsplash.com/photo-1511920170033-f8396924c348?w=400&h=400&fit=crop'],
            ['nama_kategori' => 'Produk Vegan', 'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=400&fit=crop'],
        ];

        foreach ($categories as $cat) {
            KategoriModel::create($cat);
        }
    }
}
