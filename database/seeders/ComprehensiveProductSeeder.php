<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductModel;
use App\Models\KategoriModel;
use App\Models\Medicine;
use App\Models\Ingredient;
use App\Models\ForbiddenIngredient;

class ComprehensiveProductSeeder extends Seeder
{
    public function run()
    {
        // Create comprehensive categories (10-15 categories)
        $categories = [
            ['nama_kategori' => 'Makanan Kaleng & Kemasan', 'deskripsi' => 'Makanan dalam kemasan kaleng dan botol'],
            ['nama_kategori' => 'Minuman Ringan', 'deskripsi' => 'Minuman soda, jus, dan minuman bersoda'],
            ['nama_kategori' => 'Makanan Ringan', 'deskripsi' => 'Snack, keripik, dan makanan ringan'],
            ['nama_kategori' => 'Bumbu & Saus', 'deskripsi' => 'Bumbu dapur, saus, dan penyedap'],
            ['nama_kategori' => 'Produk Susu', 'deskripsi' => 'Susu, yogurt, dan produk olahan susu'],
            ['nama_kategori' => 'Kosmetik & Perawatan', 'deskripsi' => 'Kosmetik, skincare, dan perawatan tubuh'],
            ['nama_kategori' => 'Obat Bebas', 'deskripsi' => 'Obat-obatan bebas dan suplemen'],
            ['nama_kategori' => 'Makanan Bayi', 'deskripsi' => 'Makanan dan minuman untuk bayi'],
            ['nama_kategori' => 'Makanan Beku', 'deskripsi' => 'Makanan beku dan olahan dingin'],
            ['nama_kategori' => 'Minuman Berenergi', 'deskripsi' => 'Minuman energi dan sport drink'],
            ['nama_kategori' => 'Cokelat & Permen', 'deskripsi' => 'Cokelat, permen, dan makanan manis'],
            ['nama_kategori' => 'Makanan Instan', 'deskripsi' => 'Mie instan, makanan cepat saji'],
            ['nama_kategori' => 'Produk Kebersihan', 'deskripsi' => 'Sabun, shampoo, dan produk kebersihan'],
            ['nama_kategori' => 'Makanan Tradisional', 'deskripsi' => 'Makanan tradisional Indonesia'],
            ['nama_kategori' => 'Minuman Tradisional', 'deskripsi' => 'Minuman tradisional dan herbal'],
        ];

        $categoryIds = [];
        foreach ($categories as $category) {
            $cat = KategoriModel::firstOrCreate(
                ['nama_kategori' => $category['nama_kategori']],
                ['deskripsi' => $category['deskripsi']]
            );
            $categoryIds[$category['nama_kategori']] = $cat->id_kategori;
        }

        // Comprehensive product data with AI-generated details
        $products = [
            // Makanan Kaleng & Kemasan
            [
                'nama_product' => 'Sarden ABC',
                'barcode' => '8991002100001',
                'komposisi' => json_encode(['Ikan Sarden', 'Minyak Kelapa Sawit', 'Garam', 'Pengawet (E211)', 'Antioksidan (E320)', 'Tomato Paste', 'Bawang Putih', 'Merica']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $categoryIds['Makanan Kaleng & Kemasan'],
                'image' => 'https://www.static-src.com/wcsstore/Indrapura/images/catalog/full//93/MTA-3308987/abc_sarden_abc_pedas_425_gr_full01.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 2.5,
                'calories' => 220,
                'halal_certificate' => 'ID00110000000010121',
                'price' => 25000,
                'brand' => 'ABC',
                'packaging' => 'Kaleng 425g',
                'manufacture_date' => '2024-01-15',
                'expiry_date' => '2026-01-15'
            ],
            [
                'nama_product' => 'Kacang Polong Kalengan Del Monte',
                'barcode' => '024000001234',
                'komposisi' => json_encode(['Kacang Polong', 'Air', 'Garam', 'Pengawet (E211)', 'Antioksidan (E300)', 'Gula']),
                'status' => 'halal',
                'active' => true,
                'source' => 'import',
                'kategori_id' => $categoryIds['Makanan Kaleng & Kemasan'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/4/25/12345678-1234-1234-1234-123456789012/del-monte-kacang-polong-kaleng-400g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 3.2,
                'calories' => 85,
                'halal_certificate' => 'ID00220000000020222',
                'price' => 18000,
                'brand' => 'Del Monte',
                'packaging' => 'Kaleng 400g',
                'manufacture_date' => '2024-02-01',
                'expiry_date' => '2026-02-01'
            ],
            [
                'nama_product' => 'Sosis So Good Sapi',
                'barcode' => '8991002300002',
                'komposisi' => json_encode(['Daging Sapi', 'Lemak Babi', 'Tepung Terigu', 'Garam', 'Merica', 'Pengawet (E250)', 'Antioksidan (E316)', 'Penguat Rasa (E621)']),
                'status' => 'haram',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $categoryIds['Makanan Kaleng & Kemasan'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/5/1/12345678-1234-1234-1234-123456789013/so-good-sosis-sapi-500g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 1.8,
                'calories' => 280,
                'halal_certificate' => null,
                'price' => 35000,
                'brand' => 'So Good',
                'packaging' => 'Plastik 500g',
                'manufacture_date' => '2024-01-20',
                'expiry_date' => '2024-07-20'
            ],

            // Minuman Ringan
            [
                'nama_product' => 'Coca Cola',
                'barcode' => '5449000000000',
                'komposisi' => json_encode(['Air Berkarbonasi', 'Gula', 'Karamel (E150d)', 'Asam Fosfat', 'Kafein', 'Pengatur Keasaman (E338)', 'Pengawet (E211)']),
                'status' => 'halal',
                'active' => true,
                'source' => 'import',
                'kategori_id' => $categoryIds['Minuman Ringan'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/6/1/12345678-1234-1234-1234-123456789014/coca-cola-330ml.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 39,
                'calories' => 140,
                'halal_certificate' => 'ID00330000000030333',
                'price' => 8000,
                'brand' => 'Coca Cola',
                'packaging' => 'Botol 330ml',
                'manufacture_date' => '2024-03-01',
                'expiry_date' => '2025-03-01'
            ],
            [
                'nama_product' => 'Sprite',
                'barcode' => '5449000000001',
                'komposisi' => json_encode(['Air Berkarbonasi', 'Gula', 'Asam Sitrat', 'Pengatur Keasaman (E331)', 'Pengawet (E211)', 'Antioksidan (E300)', 'Pemanis Buatan (Aspartam)']),
                'status' => 'halal',
                'active' => true,
                'source' => 'import',
                'kategori_id' => $categoryIds['Minuman Ringan'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/6/2/12345678-1234-1234-1234-123456789015/sprite-330ml.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 0, // Diet version
                'calories' => 0,
                'halal_certificate' => 'ID00440000000040444',
                'price' => 7500,
                'brand' => 'Sprite',
                'packaging' => 'Botol 330ml',
                'manufacture_date' => '2024-03-05',
                'expiry_date' => '2025-03-05'
            ],

            // Makanan Ringan
            [
                'nama_product' => 'Lay\'s Potato Chips Original',
                'barcode' => '028400000000',
                'komposisi' => json_encode(['Kentang', 'Minyak Nabati', 'Garam', 'Penguat Rasa (E621)', 'Antioksidan (E320)', 'Pengawet (E330)']),
                'status' => 'halal',
                'active' => true,
                'source' => 'import',
                'kategori_id' => $categoryIds['Makanan Ringan'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/7/1/12345678-1234-1234-1234-123456789016/lays-potato-chips-150g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 1.2,
                'calories' => 536,
                'halal_certificate' => 'ID00550000000050555',
                'price' => 15000,
                'brand' => 'Lay\'s',
                'packaging' => 'Kantong 150g',
                'manufacture_date' => '2024-02-15',
                'expiry_date' => '2025-02-15'
            ],
            [
                'nama_product' => 'Chitato Lite',
                'barcode' => '8991111000002',
                'komposisi' => json_encode(['Kentang', 'Minyak Kelapa Sawit', 'Garam', 'Bumbu Ayam', 'Penguat Rasa (E621)', 'Antioksidan (E320)', 'Pengawet (E202)']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $categoryIds['Makanan Ringan'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/7/2/12345678-1234-1234-1234-123456789017/chitato-lite-75g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 0.8,
                'calories' => 120,
                'halal_certificate' => 'ID00660000000060666',
                'price' => 8500,
                'brand' => 'Chitato',
                'packaging' => 'Kantong 75g',
                'manufacture_date' => '2024-03-10',
                'expiry_date' => '2025-03-10'
            ],

            // Bumbu & Saus
            [
                'nama_product' => 'Kecap Manis ABC',
                'barcode' => '8991002100003',
                'komposisi' => json_encode(['Gula Merah', 'Air', 'Kecap Asin', 'Garam', 'Pengawet (E211)', 'Antioksidan (E300)', 'Penguat Rasa (E621)']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $categoryIds['Bumbu & Saus'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/8/1/12345678-1234-1234-1234-123456789018/abc-kecap-manis-580ml.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 45,
                'calories' => 180,
                'halal_certificate' => 'ID00770000000070777',
                'price' => 12000,
                'brand' => 'ABC',
                'packaging' => 'Botol 580ml',
                'manufacture_date' => '2024-01-25',
                'expiry_date' => '2026-01-25'
            ],
            [
                'nama_product' => 'Saus Tomat Del Monte',
                'barcode' => '024000001235',
                'komposisi' => json_encode(['Tomat Concentrate', 'Gula', 'Cuka', 'Garam', 'Pengawet (E211)', 'Stabilisator (E415)', 'Antioksidan (E300)']),
                'status' => 'halal',
                'active' => true,
                'source' => 'import',
                'kategori_id' => $categoryIds['Bumbu & Saus'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/8/2/12345678-1234-1234-1234-123456789019/del-monte-saus-tomat-500g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 22,
                'calories' => 85,
                'halal_certificate' => 'ID00880000000080888',
                'price' => 25000,
                'brand' => 'Del Monte',
                'packaging' => 'Botol 500g',
                'manufacture_date' => '2024-02-10',
                'expiry_date' => '2026-02-10'
            ],

            // Produk Susu
            [
                'nama_product' => 'Yogurt Cimory Squeeze Strawberry',
                'barcode' => '8991002100004',
                'komposisi' => json_encode(['Susu Sapi', 'Gula', 'Buah Strawberry', 'Pektin', 'Pengawet (E211)', 'Pengatur Keasaman (E330)', 'Aromatik Identik Alami']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $categoryIds['Produk Susu'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/9/1/12345678-1234-1234-1234-123456789020/cimory-yogurt-squeeze-strawberry-120g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 18,
                'calories' => 95,
                'halal_certificate' => 'ID00990000000090999',
                'price' => 6500,
                'brand' => 'Cimory',
                'packaging' => 'Pouch 120g',
                'manufacture_date' => '2024-03-15',
                'expiry_date' => '2024-09-15'
            ],
            [
                'nama_product' => 'Keju Kraft Singles',
                'barcode' => '021000000000',
                'komposisi' => json_encode(['Susu Sapi', 'Lemak Nabati', 'Garam', 'Pengawet (E200)', 'Antioksidan (E320)', 'Pengatur Keasaman (E330)', 'Warna Makanan (E160b)']),
                'status' => 'halal',
                'active' => true,
                'source' => 'import',
                'kategori_id' => $categoryIds['Produk Susu'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/9/2/12345678-1234-1234-1234-123456789021/kraft-singles-cheese-10-slices.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 1.5,
                'calories' => 70,
                'halal_certificate' => 'ID01010000000101011',
                'price' => 45000,
                'brand' => 'Kraft',
                'packaging' => 'Pack 10 slices',
                'manufacture_date' => '2024-02-20',
                'expiry_date' => '2025-02-20'
            ],

            // Kosmetik & Perawatan
            [
                'nama_product' => 'Wardah Lightening Day Cream',
                'barcode' => '8999777000001',
                'komposisi' => json_encode(['Aqua', 'Glycerin', 'Niacinamide', 'Butylene Glycol', 'Dimethicone', 'Cetyl Alcohol', 'PEG-100 Stearate', 'Glyceryl Stearate', 'Phenonip', 'Parfum']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $categoryIds['Kosmetik & Perawatan'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/10/1/12345678-1234-1234-1234-123456789022/wardah-lightening-day-cream-30g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 0,
                'calories' => 0,
                'halal_certificate' => 'ID01110000000111111',
                'price' => 35000,
                'brand' => 'Wardah',
                'packaging' => 'Tube 30g',
                'manufacture_date' => '2024-01-30',
                'expiry_date' => '2026-01-30'
            ],
            [
                'nama_product' => 'Ponds White Beauty Facial Foam',
                'barcode' => '8850000000001',
                'komposisi' => json_encode(['Aqua', 'Sodium Laureth Sulfate', 'Cocamide DEA', 'Glycerin', 'Sodium Chloride', 'PEG-120 Methyl Glucose Dioleate', 'Acrylates/Steareth-20 Methacrylate Copolymer', 'Phenonip', 'Parfum']),
                'status' => 'halal',
                'active' => true,
                'source' => 'import',
                'kategori_id' => $categoryIds['Kosmetik & Perawatan'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/10/2/12345678-1234-1234-1234-123456789023/ponds-white-beauty-facial-foam-100g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 0,
                'calories' => 0,
                'halal_certificate' => 'ID01210000000121212',
                'price' => 25000,
                'brand' => 'Ponds',
                'packaging' => 'Tube 100g',
                'manufacture_date' => '2024-02-28',
                'expiry_date' => '2026-02-28'
            ],

            // Obat Bebas
            [
                'nama_product' => 'Paracetamol 500mg',
                'barcode' => '8991002100005',
                'komposisi' => json_encode(['Paracetamol 500mg', 'Amylum Maydis', 'Talcum', 'Magnesium Stearate', 'Gelatin', 'FD&C Yellow No. 6', 'FD&C Red No. 40']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $categoryIds['Obat Bebas'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/11/1/12345678-1234-1234-1234-123456789024/paracetamol-500mg-10-tablet.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 0,
                'calories' => 0,
                'halal_certificate' => 'ID01310000000131313',
                'price' => 2500,
                'brand' => 'Kimia Farma',
                'packaging' => 'Strip 10 tablet',
                'manufacture_date' => '2024-03-20',
                'expiry_date' => '2026-03-20'
            ],
            [
                'nama_product' => 'Vitamin C 500mg',
                'barcode' => '8991002100006',
                'komposisi' => json_encode(['Ascorbic Acid 500mg', 'Dicalcium Phosphate', 'Microcrystalline Cellulose', 'Magnesium Stearate', 'Silica Colloidal Anhydrous', 'Hypromellose', 'Titanium Dioxide', 'Macrogol 400']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $categoryIds['Obat Bebas'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/11/2/12345678-1234-1234-1234-123456789025/vitamin-c-500mg-30-kapsul.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 0,
                'calories' => 0,
                'halal_certificate' => 'ID01410000000141414',
                'price' => 15000,
                'brand' => 'Ultra',
                'packaging' => 'Botol 30 kapsul',
                'manufacture_date' => '2024-01-15',
                'expiry_date' => '2026-01-15'
            ],

            // Makanan Bayi
            [
                'nama_product' => 'Susu Formula Bayi Frisian Flag 1',
                'barcode' => '8991002100007',
                'komposisi' => json_encode(['Susu Skim', 'Laktosa', 'Minyak Nabati', 'Whey Protein', 'Kasein', 'Vitamin A', 'Vitamin D3', 'Vitamin E', 'Vitamin K1', 'Vitamin C', 'Vitamin B1', 'Vitamin B2', 'Niacin', 'Vitamin B6', 'Folic Acid', 'Vitamin B12', 'Biotin', 'Pantothenic Acid', 'Sodium', 'Potassium', 'Chloride', 'Calcium', 'Phosphorus', 'Magnesium', 'Iron', 'Zinc', 'Copper', 'Manganese', 'Selenium', 'Iodine']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $categoryIds['Makanan Bayi'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/12/1/12345678-1234-1234-1234-123456789026/frisian-flag-1-400g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 7.5,
                'calories' => 68,
                'halal_certificate' => 'ID01510000000151515',
                'price' => 85000,
                'brand' => 'Frisian Flag',
                'packaging' => 'Kaleng 400g',
                'manufacture_date' => '2024-02-01',
                'expiry_date' => '2025-08-01'
            ],

            // Makanan Beku
            [
                'nama_product' => 'Nugget Ayam So Good',
                'barcode' => '8991002300003',
                'komposisi' => json_encode(['Daging Ayam', 'Tepung Terigu', 'Telur', 'Bawang Putih', 'Garam', 'Merica', 'Penguat Rasa (E621)', 'Antioksidan (E320)', 'Pengawet (E202)']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $categoryIds['Makanan Beku'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2023/12/2/12345678-1234-1234-1234-123456789027/so-good-nugget-ayam-500g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 1.2,
                'calories' => 220,
                'halal_certificate' => 'ID01610000000161616',
                'price' => 45000,
                'brand' => 'So Good',
                'packaging' => 'Plastik 500g',
                'manufacture_date' => '2024-01-10',
                'expiry_date' => '2024-07-10'
            ],

            // Minuman Berenergi
            [
                'nama_product' => 'Red Bull Energy Drink',
                'barcode' => '9040000000000',
                'komposisi' => json_encode(['Air', 'Gula', 'Asam Sitrat', 'Taurin', 'Kafein', 'Vitamin B6', 'Vitamin B12', 'Niacin', 'Panthotenat', 'Inositol', 'Pengatur Keasaman (E331)', 'Pengawet (E211)', 'Aromatik Identik Alami']),
                'status' => 'halal',
                'active' => true,
                'source' => 'import',
                'kategori_id' => $categoryIds['Minuman Berenergi'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/1/1/12345678-1234-1234-1234-123456789028/red-bull-250ml.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 27,
                'calories' => 113,
                'halal_certificate' => 'ID01710000000171717',
                'price' => 12000,
                'brand' => 'Red Bull',
                'packaging' => 'Kaleng 250ml',
                'manufacture_date' => '2024-03-01',
                'expiry_date' => '2025-09-01'
            ],

            // Cokelat & Permen
            [
                'nama_product' => 'Cadbury Dairy Milk',
                'barcode' => '7622300000001',
                'komposisi' => json_encode(['Gula', 'Lemak Kakao', 'Bubuk Kakao', 'Susu Bubuk', 'Lemak Susu', 'Emulsifier (Lesitin Kedelai)', 'Aromatik Identik Alami']),
                'status' => 'halal',
                'active' => true,
                'source' => 'import',
                'kategori_id' => $categoryIds['Cokelat & Permen'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/1/2/12345678-1234-1234-1234-123456789029/cadbury-dairy-milk-45g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 26,
                'calories' => 240,
                'halal_certificate' => 'ID01810000000181818',
                'price' => 15000,
                'brand' => 'Cadbury',
                'packaging' => 'Bar 45g',
                'manufacture_date' => '2024-02-15',
                'expiry_date' => '2025-02-15'
            ],

            // Makanan Instan
            [
                'nama_product' => 'Indomie Kari Ayam',
                'barcode' => '089686010385',
                'komposisi' => json_encode(['Tepung Terigu', 'Minyak Nabati', 'Garam', 'Gula', 'Bawang Putih', 'Bawang Merah', 'Kecap Manis', 'Bumbu Kari', 'Penguat Rasa (E621)', 'Antioksidan (E320)']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $categoryIds['Makanan Instan'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/2/1/12345678-1234-1234-1234-123456789030/indomie-kari-ayam-85g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 4.5,
                'calories' => 380,
                'halal_certificate' => 'ID01910000000191919',
                'price' => 3500,
                'brand' => 'Indomie',
                'packaging' => 'Pack 85g',
                'manufacture_date' => '2024-03-10',
                'expiry_date' => '2025-03-10'
            ],

            // Produk Kebersihan
            [
                'nama_product' => 'Lifebuoy Total 10',
                'barcode' => '8850000000002',
                'komposisi' => json_encode(['Sodium Palmate', 'Sodium Palm Kernelate', 'Aqua', 'Fragrance', 'Sodium Chloride', 'Tetrasodium EDTA', 'Triclocarban', 'CI 11680', 'CI 74260']),
                'status' => 'halal',
                'active' => true,
                'source' => 'import',
                'kategori_id' => $categoryIds['Produk Kebersihan'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/2/2/12345678-1234-1234-1234-123456789031/lifebuoy-total-10-75g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 0,
                'calories' => 0,
                'halal_certificate' => 'ID02010000000202020',
                'price' => 18000,
                'brand' => 'Lifebuoy',
                'packaging' => 'Tube 75g',
                'manufacture_date' => '2024-01-20',
                'expiry_date' => '2026-01-20'
            ],

            // Makanan Tradisional
            [
                'nama_product' => 'Kerupuk Udang',
                'barcode' => '8991002100008',
                'komposisi' => json_encode(['Tepung Tapioka', 'Udang', 'Minyak Kelapa Sawit', 'Garam', 'Bawang Putih', 'Merica', 'Penguat Rasa (E621)']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $categoryIds['Makanan Tradisional'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/3/1/12345678-1234-1234-1234-123456789032/kerupuk-udang-250g.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 1.5,
                'calories' => 450,
                'halal_certificate' => 'ID02110000000212121',
                'price' => 25000,
                'brand' => 'Cap Kerupuk',
                'packaging' => 'Kantong 250g',
                'manufacture_date' => '2024-02-05',
                'expiry_date' => '2025-02-05'
            ],

            // Minuman Tradisional
            [
                'nama_product' => 'Wedang Jahe Instan',
                'barcode' => '8991002100009',
                'komposisi' => json_encode(['Jahe Bubuk', 'Gula', 'Kayu Manis', 'Cengkeh', 'Kapur Sirih', 'Asam Jawa']),
                'status' => 'halal',
                'active' => true,
                'source' => 'local',
                'kategori_id' => $categoryIds['Minuman Tradisional'],
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/3/2/12345678-1234-1234-1234-123456789033/wedang-jahe-instan-20-sachet.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 15,
                'calories' => 60,
                'halal_certificate' => 'ID02210000000222222',
                'price' => 15000,
                'brand' => 'Tolak Angin',
                'packaging' => 'Box 20 sachet',
                'manufacture_date' => '2024-01-30',
                'expiry_date' => '2025-01-30'
            ]
        ];

        foreach ($products as $product) {
            ProductModel::updateOrCreate(
                ['barcode' => $product['barcode']],
                $product
            );
        }

        // Create medicines with proper images
        $medicines = [
            [
                'name' => 'Amoxicillin 500mg',
                'generic_name' => 'Amoxicillin',
                'dosage' => '500mg',
                'form' => 'Kapsul',
                'category' => 'Antibiotik',
                'indication' => 'Infeksi bakteri',
                'contraindication' => 'Alergi penisilin',
                'side_effects' => 'Mual, diare, ruam kulit',
                'manufacturer' => 'Kimia Farma',
                'bpom_number' => 'DBL8112600636A1',
                'halal_status' => 'halal',
                'halal_certificate' => 'ID02310000000232323',
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/4/1/12345678-1234-1234-1234-123456789034/amoxicillin-500mg-10-kapsul.jpg',
                'price' => 5000,
                'stock' => 100
            ],
            [
                'name' => 'Ibuprofen 400mg',
                'generic_name' => 'Ibuprofen',
                'dosage' => '400mg',
                'form' => 'Tablet',
                'category' => 'Analgesik',
                'indication' => 'Nyeri, demam, peradangan',
                'contraindication' => 'Maag, asma',
                'side_effects' => 'Sakit perut, pusing',
                'manufacturer' => 'Sanbe',
                'bpom_number' => 'DBL8112600637A1',
                'halal_status' => 'halal',
                'halal_certificate' => 'ID02410000000242424',
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/4/2/12345678-1234-1234-1234-123456789035/ibuprofen-400mg-10-tablet.jpg',
                'price' => 3000,
                'stock' => 150
            ],
            [
                'name' => 'Omeprazole 20mg',
                'generic_name' => 'Omeprazole',
                'dosage' => '20mg',
                'form' => 'Kapsul',
                'category' => 'Antasida',
                'indication' => 'Maag, GERD',
                'contraindication' => 'Hipersensitivitas',
                'side_effects' => 'Sakit kepala, diare',
                'manufacturer' => 'Dexa Medica',
                'bpom_number' => 'DBL8112600638A1',
                'halal_status' => 'halal',
                'halal_certificate' => 'ID02510000000252525',
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/4/3/12345678-1234-1234-1234-123456789036/omeprazole-20mg-10-kapsul.jpg',
                'price' => 15000,
                'stock' => 80
            ]
        ];

        foreach ($medicines as $medicine) {
            Medicine::updateOrCreate(
                ['bpom_number' => $medicine['bpom_number']],
                $medicine
            );
        }

        // Create ingredients with images
        $ingredients = [
            [
                'name' => 'Monosodium Glutamate (MSG)',
                'indonesian_name' => 'Vetsin',
                'chemical_formula' => 'C5H8NNaO4',
                'category' => 'Penguat Rasa',
                'halal_status' => 'halal',
                'source' => 'sintetis',
                'function' => 'Penguat rasa',
                'max_daily_intake' => 'Tidak terbatas',
                'side_effects' => 'Sakit kepala pada sensitif MSG',
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/5/1/12345678-1234-1234-1234-123456789037/msg-monosodium-glutamate-1kg.jpg',
                'description' => 'Bahan penguat rasa yang umum digunakan dalam masakan'
            ],
            [
                'name' => 'Carrageenan',
                'indonesian_name' => 'Karrageenan',
                'chemical_formula' => 'C24H36O25S2',
                'category' => 'Pengental',
                'halal_status' => 'halal',
                'source' => 'alami (rumput laut)',
                'function' => 'Pengental, stabilizer',
                'max_daily_intake' => 'Tidak terbatas',
                'side_effects' => 'Dapat menyebabkan gangguan pencernaan pada dosis tinggi',
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/5/2/12345678-1234-1234-1234-123456789038/carrageenan-powder-500g.jpg',
                'description' => 'Pengental alami yang diekstrak dari rumput laut merah'
            ],
            [
                'name' => 'Gelatin',
                'indonesian_name' => 'Gelatin',
                'chemical_formula' => 'Protein kompleks',
                'category' => 'Pengental',
                'halal_status' => 'haram (babi)',
                'source' => 'tulang hewan',
                'function' => 'Pengental, stabilizer',
                'max_daily_intake' => 'Tidak terbatas',
                'side_effects' => 'Alergi protein',
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/5/3/12345678-1234-1234-1234-123456789039/gelatin-powder-500g.jpg',
                'description' => 'Protein yang diekstrak dari tulang dan kulit hewan'
            ]
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::updateOrCreate(
                ['name' => $ingredient['name']],
                $ingredient
            );
        }

        // Create forbidden ingredients with images
        $forbiddenIngredients = [
            [
                'name' => 'Ethanol',
                'indonesian_name' => 'Alkohol',
                'category' => 'Pelarut',
                'reason' => 'Intoksikan, haram',
                'alternative' => 'Glycerin, propylene glycol',
                'commonly_found_in' => 'Kosmetik, obat-obatan',
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/6/1/12345678-1234-1234-1234-123456789040/ethanol-96-1-liter.jpg',
                'description' => 'Alkohol yang digunakan sebagai pelarut dan pengawet'
            ],
            [
                'name' => 'Gelatin Babi',
                'indonesian_name' => 'Gelatin Babi',
                'category' => 'Pengental',
                'reason' => 'Haram, najis',
                'alternative' => 'Agar-agar, pectin',
                'commonly_found_in' => 'Permen, kapsul obat',
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/6/2/12345678-1234-1234-1234-123456789041/gelatin-porcine-500g.jpg',
                'description' => 'Gelatin yang berasal dari babi'
            ],
            [
                'name' => 'Lard',
                'indonesian_name' => 'Lemak Babi',
                'category' => 'Lemak',
                'reason' => 'Haram, najis',
                'alternative' => 'Minyak nabati, margarine',
                'commonly_found_in' => 'Makanan olahan, kosmetik',
                'image' => 'https://images.tokopedia.net/img/cache/500-square/VqbcmM/2024/6/3/12345678-1234-1234-1234-123456789042/lard-pig-fat-1kg.jpg',
                'description' => 'Lemak yang diekstrak dari babi'
            ]
        ];

        foreach ($forbiddenIngredients as $ingredient) {
            ForbiddenIngredient::updateOrCreate(
                ['name' => $ingredient['name']],
                $ingredient
            );
        }
    }
}