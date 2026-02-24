<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UmkmProduct;
use App\Models\ProductModel;
use Illuminate\Support\Str;

class HalalyticsFeatureSeeder extends Seeder
{
    public function run()
    {
        $this->seedUmkmProducts();
        $this->seedPendingVerificationProducts();
    }

    /**
     * Seed UMKM Products with QR codes for demonstration
     */
    protected function seedUmkmProducts()
    {
        $umkmProducts = [
            [
                'umkm_name' => 'Warung Makan Bu Siti',
                'umkm_owner' => 'Siti Nurhaliza',
                'umkm_phone' => '081234567890',
                'umkm_address' => 'Jl. Pasar Minggu No. 10, Jakarta Selatan',
                'product_name' => 'Nasi Padang Komplit',
                'product_description' => 'Nasi dengan lauk rendang, ayam pop, telur dadar, dan sayur nangka',
                'product_category' => 'Makanan Berat',
                'halal_status' => 'self_declared',
                'ingredients' => ['beras', 'daging sapi', 'ayam', 'telur', 'santan', 'rempah-rempah', 'nangka muda'],
                'nutrition_info' => [
                    'kalori' => 650,
                    'protein' => 35,
                    'karbohidrat' => 80,
                    'lemak' => 25
                ],
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'umkm_name' => 'Kedai Bakso Pak Joko',
                'umkm_owner' => 'Joko Widodo',
                'umkm_phone' => '082345678901',
                'umkm_address' => 'Jl. Raya Solo No. 25, Surakarta',
                'product_name' => 'Bakso Beranak Jumbo',
                'product_description' => 'Bakso super besar dengan isian telur dan bakso kecil di dalam',
                'product_category' => 'Makanan Berat',
                'halal_status' => 'halal_mui',
                'halal_cert_number' => 'LPPOM-00123456789',
                'halal_cert_expiry' => '2027-12-31',
                'ingredients' => ['daging sapi', 'tepung tapioka', 'telur', 'bawang putih', 'merica', 'garam'],
                'nutrition_info' => [
                    'kalori' => 450,
                    'protein' => 28,
                    'karbohidrat' => 45,
                    'lemak' => 18
                ],
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'umkm_name' => 'Es Cendol Manis Madu',
                'umkm_owner' => 'Ahmad Dahlan',
                'umkm_phone' => '083456789012',
                'umkm_address' => 'Jl. Malioboro No. 50, Yogyakarta',
                'product_name' => 'Es Cendol Durian',
                'product_description' => 'Es cendol dengan topping daging durian segar dan gula aren',
                'product_category' => 'Minuman',
                'halal_status' => 'self_declared',
                'ingredients' => ['tepung beras', 'santan', 'gula aren', 'durian', 'es batu'],
                'nutrition_info' => [
                    'kalori' => 280,
                    'protein' => 4,
                    'karbohidrat' => 55,
                    'lemak' => 8
                ],
                'is_verified' => false,
                'is_active' => true,
            ],
        ];

        foreach ($umkmProducts as $data) {
            $data['qr_code_unique_id'] = 'UMKM-' . strtoupper(Str::random(10));
            UmkmProduct::firstOrCreate(
                ['umkm_name' => $data['umkm_name'], 'product_name' => $data['product_name']],
                $data
            );
        }

        $this->command->info('✅ UMKM products seeded successfully!');
    }

    /**
     * Seed products that need manual verification (for Smart Verification feature)
     */
    protected function seedPendingVerificationProducts()
    {
        $pendingProducts = [
            [
                'nama_product' => 'Chips Ahoy Cookies',
                'barcode' => '7622210449283',
                'source' => 'open_food_facts',
                'verification_status' => 'needs_review',
                'needs_manual_review' => true,
                'status' => 'syubhat',
                'komposisi' => json_encode(['tepung terigu', 'gula', 'margarin', 'cokelat chips', 'pengemulsi E471', 'perisa vanila']),
                'data_completeness_score' => 75,
                'is_imported_from_off' => true,
                'auto_imported_at' => now(),
            ],
            [
                'nama_product' => 'Haribo Goldbären',
                'barcode' => '4001686301098',
                'source' => 'open_food_facts',
                'verification_status' => 'needs_review',
                'needs_manual_review' => true,
                'status' => 'syubhat',
                'komposisi' => json_encode(['sirup glukosa', 'gula', 'gelatin', 'asam sitrat', 'konsentrat jus buah']),
                'data_completeness_score' => 60,
                'is_imported_from_off' => true,
                'auto_imported_at' => now()->subHours(2),
            ],
            [
                'nama_product' => 'M&M\'s Peanut',
                'barcode' => '5000159304306',
                'source' => 'open_food_facts',
                'verification_status' => 'needs_review',
                'needs_manual_review' => true,
                'status' => 'syubhat',
                'komposisi' => json_encode(['kacang tanah', 'gula', 'kakao', 'susu bubuk', 'pewarna makanan E102', 'E110', 'E129']),
                'data_completeness_score' => 82,
                'is_imported_from_off' => true,
                'auto_imported_at' => now()->subDay(),
            ],
            [
                'nama_product' => 'Pringles Original',
                'barcode' => '5053990101573',
                'source' => 'open_food_facts',
                'verification_status' => 'needs_review',
                'needs_manual_review' => true,
                'status' => 'halal',
                'komposisi' => json_encode(['kentang dehidrasi', 'minyak sayur', 'tepung beras', 'garam', 'maltodextrin']),
                'data_completeness_score' => 90,
                'is_imported_from_off' => true,
                'auto_imported_at' => now()->subDays(2),
            ],
            [
                'nama_product' => 'Oreo Double Stuf',
                'barcode' => '7622210100023',
                'source' => 'open_food_facts',
                'verification_status' => 'needs_review',
                'needs_manual_review' => true,
                'status' => 'syubhat',
                'komposisi' => json_encode(['tepung terigu', 'gula', 'minyak kelapa sawit', 'kakao', 'sirup glukosa', 'pengemulsi lesitin kedelai']),
                'data_completeness_score' => 85,
                'is_imported_from_off' => true,
                'auto_imported_at' => now()->subHours(5),
            ],
        ];

        foreach ($pendingProducts as $data) {
            ProductModel::firstOrCreate(
                ['barcode' => $data['barcode']],
                $data
            );
        }

        $this->command->info('✅ Pending verification products seeded successfully!');
    }
}
