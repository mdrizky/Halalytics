<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductModel;
use App\Models\KategoriModel;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Ensure categories exist or get IDs
        $snackId = KategoriModel::firstOrCreate(['nama_kategori' => 'Makanan Ringan'])->id_kategori;
        $drinkId = KategoriModel::firstOrCreate(['nama_kategori' => 'Minuman'])->id_kategori;
        $condimentId = KategoriModel::firstOrCreate(['nama_kategori' => 'Bumbu Dapur'])->id_kategori;

        $products = [
            [
                'nama_product' => 'Indomie Goreng Original',
                'barcode' => '089686010384',
                'komposisi' => json_encode(['Tepung Terigu', 'Minyak Nabati', 'Garam', 'Gula', 'Bawang Putih', 'Bawang Merah', 'Kecap Manis', 'Saus Cabai']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $snackId,
                'image' => 'https://assets.klikindomaret.com/products/10003517/10003517_1.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 5,
                'calories' => 380,
                'halal_certificate' => 'ID00110000000010121'
            ],
            [
                'nama_product' => 'Chitato Sapi Panggang',
                'barcode' => '8991111000001',
                'komposisi' => json_encode(['Kentang', 'Minyak Kelapa Sawit', 'Bumbu Sapi Panggang', 'Penguat Rasa (E621)', 'Garam', 'Gula']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $snackId,
                'image' => 'https://image.cermati.com/q_70,w_1200,h_800,c_fit/v1/page/h1k7z8z8z8z8/chitato-sapi-panggang.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 2,
                'calories' => 150,
                'halal_certificate' => 'ID00220000000020222'
            ],
            [
                'nama_product' => 'Oreo Original',
                'barcode' => '7622300000000',
                'komposisi' => json_encode(['Tepung Terigu', 'Gula', 'Minyak Nabati', 'Kakao Bubuk', 'Sirup Fruktosa', 'Pengembang', 'Garam', 'Pengemulsi (Lesitin Kedelai)']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $snackId,
                'image' => 'https://www.oreo.com/media/catalog/product/o/r/oreo_original_1.png',
                'verification_status' => 'verified',
                'sugar_g' => 12,
                'calories' => 140,
                'halal_certificate' => 'ID00330000000030333'
            ],
            [
                'nama_product' => 'SilverQueen Cashew',
                'barcode' => '8991001000001',
                'komposisi' => json_encode(['Gula', 'Kacang Mede', 'Susu Bubuk', 'Kakao Massa', 'Lemak Kakao', 'Pengemulsi (Lesitin Kedelai)', 'Perisa Vanili']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $snackId,
                'image' => 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//96/MTA-2775604/silverqueen_silverqueen-cashew-65-gr_full02.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 20,
                'calories' => 350,
                'halal_certificate' => 'ID00440000000040444'
            ],
            [
                'nama_product' => 'Ultra Milk Chocolate 250ml',
                'barcode' => '8991002000002',
                'komposisi' => json_encode(['Susu Sapi Segar', 'Gula', 'Bubuk Cokelat', 'Penstabil', 'Perisa Cokelat']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $drinkId,
                'image' => 'https://assets.klikindomaret.com/products/20002621/20002621_1.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 18,
                'calories' => 160,
                'halal_certificate' => 'ID00550000000050555'
            ],
            [
                'nama_product' => 'Teh Botol Sosro',
                'barcode' => '8991003000003',
                'komposisi' => json_encode(['Air', 'Gula', 'Ekstrak Teh Melati']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $drinkId,
                'image' => 'https://pkh.s3.ap-southeast-1.amazonaws.com/images/products/202102/teh-botol-sosro-original-pet-450ml.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 20,
                'calories' => 80,
                'halal_certificate' => 'ID00660000000060666'
            ],
            [
                'nama_product' => 'Bango Kecap Manis',
                'barcode' => '8991004000004',
                'komposisi' => json_encode(['Gula', 'Sari Kedelai Hitam', 'Air', 'Garam']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $condimentId,
                'image' => 'https://assets.klikindomaret.com/products/20002081/20002081_1.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 10,
                'calories' => 50,
                'halal_certificate' => 'ID00770000000070777'
            ],
            [
                'nama_product' => 'ABC Sambal Asli',
                'barcode' => '8991005000005',
                'komposisi' => json_encode(['Cabai', 'Air', 'Gula', 'Garam', 'Bawang Putih', 'Pati', 'Cuka', 'Pengawet (Natrium Benzoat E211)']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $condimentId,
                'image' => 'https://assets.klikindomaret.com/products/10000108/10000108_1.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 5,
                'calories' => 20,
                'halal_certificate' => 'ID00880000000080888'
            ],
            [
                'nama_product' => 'Bear Brand Milk',
                'barcode' => '8992001000001',
                'komposisi' => json_encode(['100% Susu Sapi Murni']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $drinkId,
                'image' => 'https://assets.klikindomaret.com/products/10003050/10003050_1.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 9,
                'calories' => 120,
                'halal_certificate' => 'ID00990000000090999'
            ],
            [
                'nama_product' => 'Pringles Original (Import)',
                'barcode' => '8888000000111',
                'komposisi' => json_encode(['Kentang Kering', 'Minyak Sayur', 'Tepung Beras', 'Pati Gandum', 'Emulsifier (E471 from plant source)', 'Maltodextrin', 'Garam', 'Dextrose']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $snackId,
                'image' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2021/6/15/4876203a-0e9e-4e42-9988-8198f1614717.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 0,
                'calories' => 150,
                'halal_certificate' => 'ID010101010101'
            ],
             [
                'nama_product' => 'Gummy Bears (Non-Halal Example)',
                'barcode' => '9999111122223',
                'komposisi' => json_encode(['Glucose Syrup', 'Sugar', 'Gelatin (Pork)', 'Dextrose', 'Fruit Juice', 'Citric Acid', 'Flavoring']),
                'status' => 'haram', // Pork Gelatin
                'active' => true,
                'source' => 'local',
                'kategori_id' => $snackId,
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/a/a6/Gummy_bears.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 45,
                'calories' => 300,
                'halal_certificate' => null
            ]
        ];

        foreach ($products as $data) {
            ProductModel::updateOrCreate(
                ['barcode' => $data['barcode']],
                $data
            );
        }
    }
}
