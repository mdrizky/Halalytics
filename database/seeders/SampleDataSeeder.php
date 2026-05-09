<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProductModel;
use App\Models\KategoriModel;
use App\Models\ScanModel;
use App\Models\ReportModel;
use App\Models\OCRProduct;
use App\Models\ForbiddenIngredient;
use App\Models\HalalProduct;
use App\Models\Medicine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    /**
     * 🌱 Run the database seeds with sample data for testing
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting sample data seeding...');
        
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $this->seedUsers();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedScans();
        $this->seedReports();
        $this->seedOCRProducts();
        $this->seedForbiddenIngredients();
        $this->seedHalalProducts();
        $this->seedMedicines();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('✅ Sample data seeding completed!');
        $this->displaySummary();
    }

    /**
     * 👥 Seed sample users
     */
    private function seedUsers(): void
    {
        $this->command->info('👥 Seeding users...');
        
        // Clear existing users (except admin)
        User::where('role', 'user')->delete();
        
        $users = [
            [
                'username' => 'john_doe',
                'full_name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '+62812345678',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'active' => true,
                'email_verified_at' => now(),
                'onboarding_points' => 50,
                'onboarding_level' => 'Explorer',
                'onboarding_progress' => json_encode([
                    'profile_complete' => ['completed' => true, 'completed_at' => now()->toISOString()],
                    'first_scan' => ['completed' => true, 'completed_at' => now()->toISOString()],
                    'add_favorite' => ['completed' => true, 'completed_at' => now()->toISOString()],
                ]),
            ],
            [
                'username' => 'jane_smith',
                'full_name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '+62823456789',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'active' => true,
                'email_verified_at' => now(),
                'onboarding_points' => 120,
                'onboarding_level' => 'Expert',
                'onboarding_progress' => json_encode([
                    'profile_complete' => ['completed' => true, 'completed_at' => now()->toISOString()],
                    'first_scan' => ['completed' => true, 'completed_at' => now()->toISOString()],
                    'add_favorite' => ['completed' => true, 'completed_at' => now()->toISOString()],
                    'explore_categories' => ['completed' => true, 'completed_at' => now()->toISOString()],
                    'read_article' => ['completed' => true, 'completed_at' => now()->toISOString()],
                ]),
            ],
            [
                'username' => 'ahmad_rizki',
                'full_name' => 'Ahmad Rizki',
                'email' => 'ahmad@example.com',
                'phone' => '+62834567890',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'active' => true,
                'email_verified_at' => now(),
                'onboarding_points' => 25,
                'onboarding_level' => 'Beginner',
                'onboarding_progress' => json_encode([
                    'profile_complete' => ['completed' => true, 'completed_at' => now()->toISOString()],
                ]),
            ],
            [
                'username' => 'siti_nurhaliza',
                'full_name' => 'Siti Nurhaliza',
                'email' => 'siti@example.com',
                'phone' => '+62845678901',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'active' => false, // Inactive user for testing
                'email_verified_at' => null,
            ],
        ];

        foreach ($users as $userData) {
            User::create(array_merge($userData, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * 🏷️ Seed product categories
     */
    private function seedCategories(): void
    {
        $this->command->info('🏷️ Seeding categories...');
        
        KategoriModel::truncate();
        
        $categories = [
            ['nama_kategori' => 'Makanan', 'description' => 'Berbagai jenis makanan halal'],
            ['nama_kategori' => 'Minuman', 'description' => 'Minuman segar dan kemasan'],
            ['nama_kategori' => 'Snack', 'description' => 'Makanan ringan dan camilan'],
            ['nama_kategori' => 'Kosmetik', 'description' => 'Produk kecantikan dan perawatan'],
            ['nama_kategori' => 'Obat-obatan', 'description' => 'Obat dan suplemen kesehatan'],
            ['nama_kategori' => 'Dairy', 'description' => 'Produk susu dan turunannya'],
            ['nama_kategori' => 'Bakery', 'description' => 'Roti, kue, dan produk bakery'],
            ['nama_kategori' => 'Frozen Food', 'description' => 'Makanan beku'],
        ];

        foreach ($categories as $category) {
            KategoriModel::create(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * 📦 Seed sample products
     */
    private function seedProducts(): void
    {
        $this->command->info('📦 Seeding products...');
        
        ProductModel::truncate();
        
        $categories = KategoriModel::pluck('id_kategori', 'nama_kategori');
        
        $products = [
            [
                'barcode' => '8888891000017',
                'nama_product' => 'Indomie Goreng Original',
                'kategori_id' => $categories['Makanan'],
                'status' => 'halal',
                'source' => 'local',
                'image' => 'indomie_goreng.jpg',
                'komposisi' => 'Mie instant goreng rasa original',
            ],
            [
                'barcode' => '8888891000024',
                'nama_product' => 'Aqua Mineral Water 600ml',
                'kategori_id' => $categories['Minuman'],
                'status' => 'halal',
                'source' => 'local',
                'image' => 'aqua_600ml.jpg',
                'komposisi' => 'Air mineral murni kemasan 600ml',
            ],
            [
                'barcode' => '8888891000031',
                'nama_product' => 'Ultra Milk Full Cream 1L',
                'kategori_id' => $categories['Dairy'],
                'status' => 'halal',
                'source' => 'local',
                'image' => 'ultra_milk.jpg',
                'komposisi' => 'Susu murni full cream 1 liter',
            ],
            [
                'barcode' => '8888891000048',
                'nama_product' => 'Chitato Potato Chips 75g',
                'kategori_id' => $categories['Snack'],
                'status' => 'diragukan',
                'source' => 'local',
                'image' => 'chitato_chips.jpg',
                'komposisi' => 'Keripik kentang rasa BBQ',
            ],
            [
                'barcode' => '8888891000055',
                'nama_product' => 'Ponds White Beauty Day Cream',
                'kategori_id' => $categories['Kosmetik'],
                'status' => 'diragukan',
                'source' => 'local',
                'image' => 'ponds_day_cream.jpg',
                'komposisi' => 'Krim siang pemutih wajah',
            ],
            [
                'barcode' => '8888891000062',
                'nama_product' => 'Panadol Extra 500mg',
                'kategori_id' => $categories['Obat-obatan'],
                'status' => 'diragukan',
                'source' => 'local',
                'image' => 'panadol_extra.jpg',
                'komposisi' => 'Obat sakit kepala',
            ],
            [
                'barcode' => '8888891000079',
                'nama_product' => 'Sari Roti Tawar',
                'kategori_id' => $categories['Bakery'],
                'status' => 'halal',
                'source' => 'local',
                'image' => 'sari_roti.jpg',
                'komposisi' => 'Roti tawar lembut',
            ],
            [
                'barcode' => '8888891000086',
                'nama_product' => 'Frozen Chicken Nugget 500g',
                'kategori_id' => $categories['Frozen Food'],
                'status' => 'halal',
                'source' => 'local',
                'image' => 'chicken_nugget.jpg',
                'komposisi' => 'Nugget ayam beku',
            ],
        ];

        foreach ($products as $product) {
            ProductModel::create(array_merge($product, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * 📊 Seed scan history
     */
    private function seedScans(): void
    {
        $this->command->info('📊 Seeding scan history...');
        
        ScanModel::truncate();
        
        $users = User::where('active', true)->get();
        $products = ProductModel::all();
        
        foreach ($users as $user) {
            // Each user has 5-15 scans
            $scanCount = rand(5, 15);
            
            for ($i = 0; $i < $scanCount; $i++) {
                $product = $products->random();
                $scanDate = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23));
                
                ScanModel::create([
                    'user_id' => $user->id_user,
                    'product_id' => $product->id_product,
                    'barcode' => $product->barcode,
                    'nama_produk' => $product->nama_product,
                    'kategori' => optional($product->kategori)->nama_kategori ?? 'Makanan',
                    'status_halal' => $product->status,
                    'status_kesehatan' => 'aman',
                    'tanggal_scan' => $scanDate,
                    'created_at' => $scanDate,
                    'updated_at' => $scanDate,
                ]);
            }
        }
    }

    /**
     * 📝 Seed reports
     */
    private function seedReports(): void
    {
        $this->command->info('📝 Seeding reports...');
        
        ReportModel::truncate();
        
        $users = User::where('active', true)->get();
        $products = ProductModel::all();
        
        $reportTypes = ['wrong_info', 'missing_product', 'halal_status', 'other'];
        $statuses = ['pending', 'resolved', 'rejected'];
        
        foreach ($users as $user) {
            // Each user has 1-3 reports
            $reportCount = rand(1, 3);
            
            for ($i = 0; $i < $reportCount; $i++) {
                $product = $products->random();
                $reportDate = Carbon::now()->subDays(rand(0, 60));
                
                ReportModel::create([
                    'user_id' => $user->id_user,
                    'product_id' => $product->id_product,
                    'reason' => $reportTypes[array_rand($reportTypes)],
                    'laporan' => "Report for {$product->nama_product} - " . $this->getRandomReportDescription(),
                    'status' => $statuses[array_rand($statuses)],
                    'created_at' => $reportDate,
                    'updated_at' => $reportDate,
                ]);
            }
        }
    }

    /**
     * 📷 Seed OCR products
     */
    private function seedOCRProducts(): void
    {
        $this->command->info('📷 Seeding OCR products...');
        
        OCRProduct::truncate();
        
        $users = User::where('active', true)->get();
        
        $ocrProducts = [
            [
                'product_name' => 'Teh Botol Sosro',
                'brand' => 'Sosro',
                'ingredients_raw' => 'Air, gula, teh, asam sitrat, perisa alami',
                'halal_status' => 'halal',
                'status' => 'approved',
            ],
            [
                'product_name' => 'Coca-Cola',
                'brand' => 'Coca-Cola',
                'ingredients_raw' => 'Air karbonasi, gula, karamel, asam fosfat, kafein',
                'halal_status' => 'diragukan',
                'status' => 'pending_admin_review',
            ],
            [
                'product_name' => 'Milo Activ-Go',
                'brand' => 'Nestlé',
                'ingredients_raw' => 'Susu bubuk, maltodekstrin, gula, minyak sayur, kalsium',
                'halal_status' => 'halal',
                'status' => 'approved',
            ],
        ];
        
        foreach ($users as $user) {
            // Each user has 1-2 OCR products
            $ocrCount = rand(1, 2);
            
            for ($i = 0; $i < $ocrCount; $i++) {
                $ocrData = $ocrProducts[array_rand($ocrProducts)];
                $ocrDate = Carbon::now()->subDays(rand(0, 45));
                
                OCRProduct::create([
                    'user_id' => $user->id_user,
                    'product_name' => $ocrData['product_name'],
                    'brand' => $ocrData['brand'],
                    'country' => 'Indonesia',
                    'ingredients_raw' => $ocrData['ingredients_raw'],
                    'ingredients_parsed' => json_encode([
                        ['name' => 'Air', 'status' => 'halal', 'risk_level' => 'low'],
                        ['name' => 'Gula', 'status' => 'halal', 'risk_level' => 'low'],
                        ['name' => 'Teh', 'status' => 'halal', 'risk_level' => 'low'],
                    ]),
                    'confidence_level' => rand(75, 95),
                    'halal_status' => $ocrData['halal_status'],
                    'status' => $ocrData['status'],
                    'source' => 'ocr_mobile',
                    'front_image_path' => 'ocr_samples/front_' . uniqid() . '.jpg',
                    'back_image_path' => 'ocr_samples/back_' . uniqid() . '.jpg',
                    'language' => 'id',
                    'ai_analysis' => json_encode([
                        'overall_status' => $ocrData['halal_status'],
                        'confidence' => rand(70, 90),
                        'recommendation' => $this->getHalalRecommendation($ocrData['halal_status']),
                    ]),
                    'created_at' => $ocrDate,
                    'updated_at' => $ocrDate,
                ]);
            }
        }
    }

    /**
     * 🚫 Seed forbidden ingredients
     */
    private function seedForbiddenIngredients(): void
    {
        $this->command->info('🚫 Seeding forbidden ingredients...');
        
        ForbiddenIngredient::truncate();
        
        $ingredients = [
            ['name' => 'Pork', 'code' => 'E631', 'aliases' => 'Babi, Pork', 'type' => 'animal', 'description' => 'Daging babi', 'risk_level' => 'high', 'reason' => 'Haram karena daging babi'],
            ['name' => 'Gelatin', 'code' => 'E441', 'aliases' => 'Gelatin, Gelatine', 'type' => 'animal', 'description' => 'Gelatin non-halal', 'risk_level' => 'high', 'reason' => 'Berasal dari hewan non-halal'],
            ['name' => 'Alcohol', 'code' => 'E1510', 'aliases' => 'Alkohol, Ethanol', 'type' => 'chemical', 'description' => 'Kandungan alkohol', 'risk_level' => 'high', 'reason' => 'Mengandung alkohol'],
            ['name' => 'Lard', 'code' => 'E471', 'aliases' => 'Lemak babi, Lard', 'type' => 'animal', 'description' => 'Lemak babi', 'risk_level' => 'high', 'reason' => 'Lemak dari babi'],
            ['name' => 'Enzyme', 'code' => 'E1100', 'aliases' => 'Enzyme, Enzim', 'type' => 'enzyme', 'description' => 'Enzim non-halal', 'risk_level' => 'medium', 'reason' => 'Perlu verifikasi sumber'],
            ['name' => 'Carrageenan', 'code' => 'E407', 'aliases' => 'Carrageenan, Karagenan', 'type' => 'additive', 'description' => 'Perlu verifikasi', 'risk_level' => 'medium', 'reason' => 'Beberapa sumber non-halal'],
            ['name' => 'Rennet', 'code' => 'E904', 'aliases' => 'Rennet, Renin', 'type' => 'enzyme', 'description' => 'Enzim dari hewan', 'risk_level' => 'medium', 'reason' => 'Berasal dari hewan'],
            ['name' => 'Lecithin', 'code' => 'E322', 'aliases' => 'Lecithin, Lesitin', 'type' => 'additive', 'description' => 'Perlu verifikasi sumber', 'risk_level' => 'medium', 'reason' => 'Bisa dari sumber non-halal'],
        ];
        
        foreach ($ingredients as $ingredient) {
            ForbiddenIngredient::create(array_merge($ingredient, [
                'is_active' => true,
                'source' => 'MUI',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * 🕌 Seed halal products with certificates
     */
    private function seedHalalProducts(): void
    {
        $this->command->info('🕌 Seeding halal products...');
        
        HalalProduct::truncate();
        
        $products = ProductModel::where('status', 'halal')->get();
        
        foreach ($products as $product) {
            HalalProduct::create([
                'product_barcode' => $product->barcode,
                'product_name' => $product->nama_product,
                'brand' => 'Local Brand',
                'halal_certificate_number' => 'LPPOM-MUI-' . strtoupper(uniqid()),
                'halal_status' => 'halal',
                'certification_body' => 'LPPOM MUI',
                'certificate_valid_until' => Carbon::now()->addYears(rand(1, 3)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * 💊 Seed medicines
     */
    private function seedMedicines(): void
    {
        $this->command->info('💊 Seeding medicines...');
        
        Medicine::truncate();
        
        $medicines = [
            [
                'name' => 'Paracetamol 500mg',
                'generic_name' => 'Paracetamol',
                'manufacturer' => 'Kimia Farma',
                'dosage_form' => 'Tablet',
                'source' => 'openfda',
                'halal_status' => 'diragukan',
            ],
            [
                'name' => 'Vitamin C 1000mg',
                'generic_name' => 'Ascorbic Acid',
                'manufacturer' => 'Kalbe Farma',
                'dosage_form' => 'Capsule',
                'source' => 'openfda',
                'halal_status' => 'diragukan',
            ],
            [
                'name' => 'Antasida DOEN',
                'generic_name' => 'Magaldrate',
                'manufacturer' => 'Dexa Medica',
                'dosage_form' => 'Suspension',
                'source' => 'openfda',
                'halal_status' => 'halal',
            ],
        ];
        
        foreach ($medicines as $medicine) {
            Medicine::create(array_merge($medicine, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * 📝 Get random report description
     */
    private function getRandomReportDescription(): string
    {
        $descriptions = [
            'Product information is incorrect',
            'Missing halal certificate',
            'Wrong halal status',
            'Product image not available',
            'Barcode not working',
            'Product details incomplete',
            'Need to update ingredient list',
        ];
        
        return $descriptions[array_rand($descriptions)];
    }

    /**
     * 💡 Get halal recommendation
     */
    private function getHalalRecommendation(string $status): string
    {
        switch ($status) {
            case 'halal':
                return '✅ Produk ini tampaknya halal berdasarkan analisis bahan.';
            case 'haram':
                return '⚠️ Produk ini mengandung bahan haram. Tidak disarankan.';
            default:
                return '❓ Tidak dapat memastikan status halal. Perlu verifikasi.';
        }
    }

    /**
     * 📊 Display seeding summary
     */
    private function displaySummary(): void
    {
        $this->command->info("\n📊 === SEEDING SUMMARY ===");
        $this->command->info("👥 Users: " . User::count());
        $this->command->info("🏷️ Categories: " . KategoriModel::count());
        $this->command->info("📦 Products: " . ProductModel::count());
        $this->command->info("📊 Scans: " . ScanModel::count());
        $this->command->info("📝 Reports: " . ReportModel::count());
        $this->command->info("📷 OCR Products: " . OCRProduct::count());
        $this->command->info("🚫 Forbidden Ingredients: " . ForbiddenIngredient::count());
        $this->command->info("🕌 Halal Products: " . HalalProduct::count());
        $this->command->info("💊 Medicines: " . Medicine::count());
        $this->command->info("🎉 Sample data ready for testing!");
    }
}
