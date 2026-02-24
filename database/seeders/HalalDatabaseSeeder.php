<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HalalDatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Admin User
        // 1. Create Admin User (Idempotent)
        DB::table('users')->updateOrInsert(
            ['username' => 'admin'],
            [
                'full_name' => 'Administrator',
                'email' => 'admin@halalytics.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. Create Sample Categories
        $categories = [
            ['nama_kategori' => 'Makanan'],
            ['nama_kategori' => 'Minuman'],
            ['nama_kategori' => 'Snack'],
            ['nama_kategori' => 'Bumbu Masak'],
            ['nama_kategori' => 'Produk Susu'],
            ['nama_kategori' => 'Daging'],
            ['nama_kategori' => 'Sayuran'],
            ['nama_kategori' => 'Buah-buahan'],
        ];

        foreach ($categories as $cat) {
            DB::table('kategori')->updateOrInsert(['nama_kategori' => $cat['nama_kategori']], $cat);
        }

        // 3. Create Sample Products (Idempotent)
        $products = [
            [
                'nama_product' => 'Indomie Mie Goreng Original',
                'barcode' => '0896860100101',
                'komposisi' => 'Tepung terigu, minyak sawit, garam, perisa, bumbu, gula',
                'status' => 'halal',
                'info_gizi' => 'Energi: 380kkal, Protein: 8g, Lemak: 17g, Karbohidrat: 50g',
                'kategori_id' => 1,
            ],
            [
                'nama_product' => 'Coca Cola 330ml',
                'barcode' => '5449000000996',
                'komposisi' => 'Air, gula, karamel E150d, asam fosfat, kafein, perisa alami',
                'status' => 'syubhat',
                'info_gizi' => 'Energi: 139kkal, Gula: 39g',
                'kategori_id' => 2,
            ],
            [
                'nama_product' => 'Susu Ultra Milk 1L',
                'barcode' => '0896888810018',
                'komposisi' => 'Susu sapi segar, vitamin D, vitamin A',
                'status' => 'halal',
                'info_gizi' => 'Energi: 64kkal, Protein: 3.2g, Lemak: 3.5g, Karbohidrat: 4.9g',
                'kategori_id' => 5,
            ],
            [
                'nama_product' => 'Taro Net 60g',
                'barcode' => '0896860110263',
                'komposisi' => 'Kentang, minyak nabati, garam, perisa',
                'status' => 'halal',
                'info_gizi' => 'Energi: 320kkal, Lemak: 20g, Karbohidrat: 32g',
                'kategori_id' => 3,
            ],
            [
                'nama_product' => 'Kecap Manis Bango 450ml',
                'barcode' => '0896860111006',
                'komposisi' => 'Kedelai, gula, garam, air, asam sitrat',
                'status' => 'halal',
                'info_gizi' => 'Energi: 60kkal per sdm',
                'kategori_id' => 4,
            ],
        ];

        foreach ($products as $prod) {
            DB::table('products')->updateOrInsert(['barcode' => $prod['barcode']], $prod);
        }

        // 4. Create Halal Certificates
        $certificates = [
            [
                'certificate_number' => 'MUI-1234567890',
                'product_name' => 'Indomie Mie Goreng Original',
                'manufacturer' => 'PT Indofood CBP Sukses Makmur Tbk',
                'certifying_body' => 'MUI',
                'issue_date' => '2023-01-15',
                'expiry_date' => '2026-01-14',
                'status' => 'active',
                'notes' => 'Sertifikat halal untuk produk mie instan',
            ],
            [
                'certificate_number' => 'BPJPH-0987654321',
                'product_name' => 'Susu Ultra Milk 1L',
                'manufacturer' => 'PT Ultrajaya Milk Industry',
                'certifying_body' => 'BPJPH',
                'issue_date' => '2023-03-20',
                'expiry_date' => '2026-03-19',
                'status' => 'active',
                'notes' => 'Sertifikat halal untuk produk susu',
            ],
        ];

        foreach ($certificates as $cert) {
            DB::table('halal_certificates')->updateOrInsert(['certificate_number' => $cert['certificate_number']], $cert);
        }

        // 6. Ingredient Seeding REMOVED - Handled by IngredientSeeder class
        // $ingredients = [...];

        // 6. Create Allergens
        $allergens = [
            ['name' => 'Susu', 'code' => 'milk', 'severity' => 'medium'],
            ['name' => 'Telur', 'code' => 'egg', 'severity' => 'medium'],
            ['name' => 'Kacang Tanah', 'code' => 'peanut', 'severity' => 'severe'],
            ['name' => 'Kacang Pohon', 'code' => 'tree_nut', 'severity' => 'severe'],
            ['name' => 'Kedelai', 'code' => 'soy', 'severity' => 'medium'],
            ['name' => 'Gandum', 'code' => 'wheat', 'severity' => 'medium'],
            ['name' => 'Udang', 'code' => 'shrimp', 'severity' => 'severe'],
            ['name' => 'Ikan', 'code' => 'fish', 'severity' => 'medium'],
        ];

        DB::table('allergens')->insert($allergens);

        // 7. Link Products to Halal Status
        $productHalalStatus = [
            ['product_id' => 1, 'halal_status' => 'halal', 'certificate_id' => 1, 'verified_by' => 1, 'verified_at' => now()],
            ['product_id' => 2, 'halal_status' => 'syubhat', 'certificate_id' => null, 'verified_by' => 1, 'verified_at' => now()],
            ['product_id' => 3, 'halal_status' => 'halal', 'certificate_id' => 2, 'verified_by' => 1, 'verified_at' => now()],
            ['product_id' => 4, 'halal_status' => 'halal', 'certificate_id' => null, 'verified_by' => 1, 'verified_at' => now()],
            ['product_id' => 5, 'halal_status' => 'halal', 'certificate_id' => null, 'verified_by' => 1, 'verified_at' => now()],
        ];

        DB::table('product_halal_status')->insert($productHalalStatus);

        // 8. Create Health Scores
        $healthScores = [
            ['product_id' => 1, 'overall_score' => 65, 'sugar_score' => 40, 'fat_score' => 60, 'salt_score' => 80, 'additive_score' => 70, 'grade' => 'C'],
            ['product_id' => 2, 'overall_score' => 25, 'sugar_score' => 10, 'fat_score' => 80, 'salt_score' => 90, 'additive_score' => 50, 'grade' => 'E'],
            ['product_id' => 3, 'overall_score' => 85, 'sugar_score' => 80, 'fat_score' => 70, 'salt_score' => 90, 'additive_score' => 95, 'grade' => 'B'],
            ['product_id' => 4, 'overall_score' => 45, 'sugar_score' => 60, 'fat_score' => 30, 'salt_score' => 70, 'additive_score' => 40, 'grade' => 'D'],
            ['product_id' => 5, 'overall_score' => 70, 'sugar_score' => 30, 'fat_score' => 80, 'salt_score' => 60, 'additive_score' => 85, 'grade' => 'C'],
        ];

        DB::table('health_scores')->insert($healthScores);

        $this->command->info('✅ Halal database seeded successfully!');
        $this->command->info('👤 Admin login: admin@halalytics.com / admin123');
    }
}
