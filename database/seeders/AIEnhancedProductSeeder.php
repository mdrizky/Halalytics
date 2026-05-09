<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductModel;
use App\Models\KategoriModel;
use App\Models\Ingredient;

class AIEnhancedProductSeeder extends Seeder
{
    public function run()
    {
        // Create comprehensive categories (12 categories as requested)
        $categories = [
            ['nama_kategori' => 'Makanan Kaleng & Kemasan', 'description' => 'Makanan dalam kemasan kaleng, botol, dan vakum'],
            ['nama_kategori' => 'Minuman Ringan', 'description' => 'Minuman soda, jus buah, dan minuman bersoda'],
            ['nama_kategori' => 'Makanan Ringan', 'description' => 'Snack, keripik, biskuit, dan makanan ringan'],
            ['nama_kategori' => 'Bumbu & Saus', 'description' => 'Bumbu dapur, saus, kecap, dan penyedap rasa'],
            ['nama_kategori' => 'Produk Susu', 'description' => 'Susu, yogurt, keju, dan produk olahan susu'],
            ['nama_kategori' => 'Makanan Beku', 'description' => 'Makanan beku seperti nugget, sosis, dan frozen food'],
            ['nama_kategori' => 'Cokelat & Permen', 'description' => 'Cokelat, permen, dan makanan manis'],
            ['nama_kategori' => 'Makanan Instan', 'description' => 'Mie instan, bumbu instan, dan makanan siap saji'],
            ['nama_kategori' => 'Kosmetik & Perawatan', 'description' => 'Kosmetik, skincare, dan produk perawatan'],
            ['nama_kategori' => 'Obat & Suplemen', 'description' => 'Obat-obatan dan suplemen kesehatan'],
            ['nama_kategori' => 'Produk Bayi', 'description' => 'Susu formula, makanan bayi, dan produk perawatan bayi'],
            ['nama_kategori' => 'Bahan Pokok', 'description' => 'Bahan-bahan pokok seperti tepung, gula, dan minyak']
        ];

        foreach ($categories as $category) {
            KategoriModel::updateOrCreate(
                ['nama_kategori' => $category['nama_kategori']],
                $category
            );
        }

        // Create sample products with AI-enhanced data
        $products = [
            [
                'nama_product' => 'Sarden ABC Extra Pedas',
                'barcode' => '8991002100001',
                'komposisi' => json_encode(["Ikan Sarden","Minyak Kelapa Sawit","Garam","Pengawet Natrium Benzoat (E211)","Antioksidan TBHQ (E319)","Pasta Tomat","Bawang Putih Bubuk","Merica Putih","Cabai Merah","Jahe","Kunyit"]),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => 1,
                'image' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/1/1/abc-sarden-extra-pedas-425g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 2.5,
                'calories' => 220,
                'protein_g' => 18.5,
                'fat_g' => 15.2,
                'halal_certificate' => 'ID00110000000010121',
                'price' => 28500,
                'brand' => 'ABC',
                'quantity' => '425g',
                'packaging' => 'Kaleng',
                'labels' => json_encode(['Halal MUI', 'BPOM RI']),
                'halal_analysis' => json_encode([
                    'alcohol_content' => '0%',
                    'animal_derived' => 'Fish (Halal)',
                    'cross_contamination_risk' => 'Low',
                    'certification_body' => 'MUI',
                    'verification_date' => '2024-01-10'
                ]),
            ],
            [
                'nama_product' => 'Coca-Cola Original',
                'barcode' => '8996001440001',
                'komposisi' => json_encode(["Air Berkarbonasi","Gula","Asam Karbonat","Pewarna Karamel","Pengawet Natrium Benzoat","Kafein","Aroma Alami"]),
                'status' => 'halal',
                'active' => true,
                'source' => 'imported',
                'kategori_id' => 2,
                'image' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/1/2/coca-cola-390ml.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 39,
                'calories' => 140,
                'protein_g' => 0,
                'fat_g' => 0,
                'halal_certificate' => 'ID00220000000020222',
                'price' => 8500,
                'brand' => 'Coca-Cola',
                'quantity' => '390ml',
                'packaging' => 'Botol',
                'labels' => json_encode(['Halal MUI', 'BPOM RI']),
                'halal_analysis' => json_encode([
                    'alcohol_content' => '0%',
                    'animal_derived' => 'None',
                    'cross_contamination_risk' => 'Low',
                    'certification_body' => 'MUI',
                    'verification_date' => '2024-02-01'
                ]),
            ]
        ];

        foreach ($products as $product) {
            ProductModel::updateOrCreate(
                ['nama_product' => $product['nama_product']],
                $product
            );
        }

        $this->createIngredientsEncyclopedia();
    }

    private function createIngredientsEncyclopedia()
    {
        $ingredients = [
            [
                'name' => 'Tepung Terigu',
                'halal_status' => 'halal',
                'description' => 'Tepung yang dihasilkan dari biji gandum yang telah digiling halus',
                'sources' => 'Mie, roti, kue',
                'notes' => 'Bahan pokok untuk baking',
                'active' => true,
                'image_url' => 'https://images.tokopedia.net/img/cache/500/VqbcmM/2024/1/1/tepung-terigu.jpg',
                'image' => 'https://images.tokopedia.net/img/cache/500/VqbcmM/2024/1/1/tepung-terigu.jpg',
            ],
            [
                'name' => 'Minyak Kelapa Sawit',
                'halal_status' => 'halal',
                'description' => 'Minyak nabati yang diekstrak dari buah kelapa sawit',
                'sources' => 'Makanan olahan, margarine',
                'notes' => 'Sumber lemak nabati utama',
                'active' => true,
                'image_url' => 'https://images.tokopedia.net/img/cache/500/VqbcmM/2024/1/2/palm-oil.jpg',
                'image' => 'https://images.tokopedia.net/img/cache/500/VqbcmM/2024/1/2/palm-oil.jpg',
            ]
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::updateOrCreate(
                ['name' => $ingredient['name']],
                $ingredient
            );
        }
    }
}