<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriModel;
use App\Models\ProductModel;
use App\Models\User;
use App\Models\ScanHistory;
use App\Models\Banner;
use App\Models\Notification;
use App\Models\ScanModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // 1. CLEAR TABLES
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        KategoriModel::truncate();
        ProductModel::truncate();
        User::truncate();
        ScanHistory::truncate();
        ScanModel::truncate();
        Banner::truncate();
        Notification::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. CREATE CATEGORIES
        $cat1 = KategoriModel::create(['nama_kategori' => 'Minuman', 'description' => 'Aneka minuman kemasan']);
        $cat2 = KategoriModel::create(['nama_kategori' => 'Makanan Ringan', 'description' => 'Camilan dan snack']);
        $cat3 = KategoriModel::create(['nama_kategori' => 'Bumbu Dapur', 'description' => 'Bumbu masak dan saus']);
        $cat4 = KategoriModel::create(['nama_kategori' => 'Kesehatan', 'description' => 'Obat-obatan dan suplemen']);

        // 3. CREATE USERS
        $admin = User::create([
            'username' => 'admin',
            'full_name' => 'Super Admin',
            'email' => 'admin@halalytics.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '08123456789'
        ]);

        $user = User::create([
            'username' => 'daffa',
            'full_name' => 'Daffa Rizky',
            'email' => 'daffa@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone' => '08987654321',
            'blood_type' => 'O',
            'allergy' => 'None',
            'medical_history' => 'Healthy'
        ]);

        // 4. CREATE PRODUCTS
        $p1 = ProductModel::create([
            'nama_product' => 'Indomie Goreng Special',
            'barcode' => '089686010384',
            'komposisi' => json_encode(['Tepung terigu', 'Minyak nabati', 'Garam', 'Gula', 'Bawang putih', 'Bawang merah']),
            'status' => 'halal',
            'active' => true,
            'source' => 'local',
            'kategori_id' => $cat2->id_kategori,
            'image' => 'https://www.indomie.com/uploads/product/indomie-mi-goreng-special_detail_095627771.png',
            'verification_status' => 'verified',
            'sugar_g' => 5,
            'calories' => 380,
            'halal_certificate' => 'ID00110000000010121'
        ]);

        $p2 = ProductModel::create([
            'nama_product' => 'Pocari Sweat 500ml',
            'barcode' => '4987035131411',
            'komposisi' => json_encode(['Air', 'Gula', 'Pengatur keasaman', 'Natrium klorida', 'Kalium klorida']),
            'status' => 'halal',
            'active' => true,
            'source' => 'local',
            'kategori_id' => $cat1->id_kategori,
            'image' => 'https://pocarisweat.id/assets/img/product/product-500ml.png',
            'verification_status' => 'verified',
            'sugar_g' => 25,
            'calories' => 120,
            'halal_certificate' => 'ID00110000000020121'
        ]);

        // 5. CREATE BANNERS
        Banner::create([
            'title' => 'Ramadhan Sehat with Halalytics',
            'description' => 'Cek kehalalan takjilmu dengan fitur scan terbaru kami!',
            'image' => 'https://img.freepik.com/free-vector/ramadan-kareem-sale-banner-template_23-2148873752.jpg',
            'position' => 1,
            'is_active' => true
        ]);

        // 6. CREATE SCAN HISTORIES & SCAN MODELS (Dual Sync)
        $h1 = ScanHistory::create([
            'user_id' => $user->id_user,
            'scannable_type' => 'App\Models\ProductModel',
            'scannable_id' => $p1->id_product,
            'product_name' => $p1->nama_product,
            'product_image' => $p1->image,
            'barcode' => $p1->barcode,
            'halal_status' => $p1->status,
            'scan_method' => 'barcode',
            'source' => 'local',
            'created_at' => now()->subHours(2)
        ]);

        ScanModel::create([
            'user_id' => $user->id_user,
            'product_id' => $p1->id_product,
            'nama_produk' => $p1->nama_product,
            'barcode' => $p1->barcode,
            'kategori' => 'Sembako',
            'status_halal' => 'halal',
            'status_kesehatan' => 'sehat',
            'tanggal_scan' => now()->subHours(2),
        ]);

        $this->command->info('Demo Data Seeded Successfully with Correct Schema!');
    }
}
