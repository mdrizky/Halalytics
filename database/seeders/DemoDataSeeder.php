<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;
use App\Models\ProductModel;
use App\Models\HalalProduct;
use App\Models\Notification;
use App\Models\KategoriModel;
use App\Models\User;
use App\Models\ScanModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Categories
        $categories = [
            ['nama_kategori' => 'Makanan', 'description' => 'Produk makanan kemasan'],
            ['nama_kategori' => 'Minuman', 'description' => 'Produk minuman kemasan'],
            ['nama_kategori' => 'Obat', 'description' => 'Produk kesehatan dan obat-obatan'],
            ['nama_kategori' => 'Kosmetik', 'description' => 'Produk kecantikan dan perawatan diri'],
        ];

        foreach ($categories as $cat) {
            KategoriModel::firstOrCreate(['nama_kategori' => $cat['nama_kategori']], $cat);
        }

        $foodId = KategoriModel::where('nama_kategori', 'Makanan')->first()->id_kategori;
        $drinkId = KategoriModel::where('nama_kategori', 'Minuman')->first()->id_kategori;

        // 2. Banners (Campaigns)
        $banners = [
            [
                'title' => 'Promo Ramadan Berkah',
                'description' => 'Dapatkan info produk halal terlengkap selama bulan suci.',
                'image' => 'images/promo/ss-home-1.jpg',
                'is_active' => true,
                'position' => 1
            ],
            [
                'title' => 'Cek BPOM Kini Lebih Mudah',
                'description' => 'Scan barcode dan langsung dapatkan status keamanan BPOM.',
                'image' => 'images/promo/ss-home-2.jpg',
                'is_active' => true,
                'position' => 2
            ],
            [
                'title' => 'Forum Komunitas Halal',
                'description' => 'Tanyakan keaslian produk kepada ribuan kontributor lainnya.',
                'image' => 'images/promo/ss-home-3.jpg',
                'is_active' => true,
                'position' => 3
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(['title' => $banner['title']], $banner);
        }

        // 3. Premium Products with Generated Images
        $premiumProducts = [
            [
                'nama_product' => 'Kecap ABC Manis',
                'barcode' => '8991004000004',
                'komposisi' => 'Gula, air, kacang kedelai, gandum, garam.',
                'status' => 'halal',
                'image' => 'images/products/kecap_abc.png',
                'kategori_id' => $foodId,
                'source' => 'local',
                'active' => true,
                'verification_status' => 'verified'
            ],
            [
                'nama_product' => 'Sari Gandum Sandwich',
                'barcode' => '8996001300077',
                'komposisi' => 'Tepung gandum, gula, minyak nabati, susu bubuk.',
                'status' => 'halal',
                'image' => 'images/products/sari_gandum.png',
                'kategori_id' => $foodId,
                'source' => 'local',
                'active' => true,
                'verification_status' => 'verified'
            ],
            [
                'nama_product' => 'SilverQueen Cashew',
                'barcode' => '8991001001851',
                'komposisi' => 'Gula, kacang mete, lemak kakao, susu bubuk.',
                'status' => 'halal',
                'image' => 'images/products/silverqueen.png',
                'kategori_id' => $foodId,
                'source' => 'local',
                'active' => true,
                'verification_status' => 'verified'
            ],
            [
                'nama_product' => 'Pocari Sweat 500ml',
                'barcode' => '0498703513141',
                'komposisi' => 'Air, gula, pengatur keasaman, natrium klorida.',
                'status' => 'halal',
                'image' => 'https://images.openfoodfacts.org/images/products/049/870/351/3141/front_en.85.400.jpg',
                'kategori_id' => $drinkId,
                'source' => 'local',
                'active' => true,
                'verification_status' => 'verified'
            ],
        ];

        foreach ($premiumProducts as $p) {
            ProductModel::updateOrCreate(['barcode' => $p['barcode']], $p);
        }

        // 4. Halal Products (Database) 
        $halalProducts = [
            [
                'product_barcode' => '8991004000004',
                'product_name' => 'Kecap ABC Manis',
                'brand' => 'ABC',
                'halal_certificate_number' => 'ID00110000012340122',
                'halal_status' => 'halal',
                'certification_body' => 'BPJPH',
                'certificate_valid_until' => Carbon::now()->addYears(2),
            ],
            [
                'product_barcode' => '8996001300077',
                'product_name' => 'Sari Gandum Sandwich',
                'brand' => 'Roma',
                'halal_certificate_number' => 'ID00210000056780222',
                'halal_status' => 'halal',
                'certification_body' => 'MUI',
                'certificate_valid_until' => Carbon::now()->addYear(),
            ],
        ];

        foreach ($halalProducts as $hp) {
            HalalProduct::updateOrCreate(['product_barcode' => $hp['product_barcode']], $hp);
        }

        // 5. Notifications
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $notifications = [
                [
                    'user_id' => $admin->id_user,
                    'title' => 'System Update Complete',
                    'message' => 'Dashboard analytics have been successfully updated.',
                    'type' => 'system',
                    'is_read' => false,
                    'sent_at' => now(),
                ],
                [
                    'user_id' => $admin->id_user,
                    'title' => 'New Product Request',
                    'message' => 'A user has submitted a new product for verification: Indomie Goreng.',
                    'type' => 'request',
                    'is_read' => false,
                    'sent_at' => now()->subHours(2),
                ],
            ];

            foreach ($notifications as $n) {
                Notification::create($n);
            }
        }

        // 6. Sample Scans for Charts
        $user = User::where('role', 'user')->first() ?? $admin;
        if ($user) {
            for ($i = 0; $i < 20; $i++) {
                $p = $premiumProducts[array_rand($premiumProducts)];
                ScanModel::create([
                    'user_id' => $user->id_user,
                    'product_id' => ProductModel::where('barcode', $p['barcode'])->first()->id_product,
                    'nama_produk' => $p['nama_product'],
                    'barcode' => $p['barcode'],
                    'status_halal' => $p['status'],
                    'status_kesehatan' => 'sehat', // Default status health
                    'tanggal_scan' => Carbon::now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }
}
