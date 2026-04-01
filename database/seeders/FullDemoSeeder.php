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
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\MedicineReminder;
use App\Models\ProductRequest;
use App\Models\ReportModel;
use App\Models\Ingredient;
use App\Models\ForbiddenIngredient;
use App\Models\BpomData;
use App\Models\PromoBlog;
use App\Models\ActivityEvent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FullDemoSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚀 Memulai seeding data demo lengkap...');

        // ========== 1. CATEGORIES (5+) ==========
        $categories = [];
        $catData = [
            ['nama_kategori' => 'Minuman', 'description' => 'Aneka minuman kemasan'],
            ['nama_kategori' => 'Makanan Ringan', 'description' => 'Camilan dan snack'],
            ['nama_kategori' => 'Bumbu Dapur', 'description' => 'Bumbu masak dan saus'],
            ['nama_kategori' => 'Kesehatan', 'description' => 'Obat-obatan dan suplemen'],
            ['nama_kategori' => 'Kosmetik', 'description' => 'Produk perawatan kulit dan tubuh'],
            ['nama_kategori' => 'Dairy', 'description' => 'Susu dan produk olahan susu'],
            ['nama_kategori' => 'Makanan Beku', 'description' => 'Produk frozen food siap masak'],
            ['nama_kategori' => 'Sereal & Sarapan', 'description' => 'Produk sarapan siap saji'],
            ['nama_kategori' => 'Bayi & Anak', 'description' => 'Produk nutrisi anak dan bayi'],
            ['nama_kategori' => 'Saus & Dressing', 'description' => 'Saus, mayo, dan dressing'],
            ['nama_kategori' => 'Roti & Bakery', 'description' => 'Roti, cake, pastry'],
            ['nama_kategori' => 'Seafood Olahan', 'description' => 'Produk seafood kemasan'],
            ['nama_kategori' => 'Herbal & Jamu', 'description' => 'Produk herbal tradisional'],
            ['nama_kategori' => 'Frozen Snack', 'description' => 'Camilan beku siap goreng'],
        ];
        foreach ($catData as $c) {
            $categories[] = KategoriModel::firstOrCreate(
                ['nama_kategori' => $c['nama_kategori']],
                $c
            );
        }
        $this->command->info('✅ Kategori: ' . count($categories) . ' records');

        // ========== 2. USERS (keep existing, add test if not exists) ==========
        $admin = User::firstOrCreate(
            ['email' => 'admin@halalytics.com'],
            [
                'username' => 'admin',
                'full_name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '08123456789',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'daffa@example.com'],
            [
                'username' => 'daffa',
                'full_name' => 'Daffa Rizky',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '08987654321',
                'blood_type' => 'O',
                'allergy' => 'Kacang',
                'medical_history' => 'Asma ringan',
            ]
        );

        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'username' => 'testuser',
                'full_name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '08111222333',
                'blood_type' => 'A',
                'allergy' => 'Seafood',
                'medical_history' => 'Diabetes Tipe 2',
            ]
        );
        $rhavel = User::firstOrCreate(
            ['email' => 'rhavel@example.com'],
            [
                'username' => 'rhavel',
                'full_name' => 'Rhavel Testing',
                'password' => Hash::make('password'),
                'role' => 'user',
                'phone' => '081298765432',
                'active' => true,
            ]
        );
        $this->command->info('✅ Users: 4 records (admin, daffa, testuser, rhavel)');

        // ========== 3. PRODUCTS (8+) ==========
        $products = [];
        $prodData = [
            [
                'nama_product' => 'Indomie Goreng Special',
                'barcode' => '089686010384',
                'komposisi' => json_encode(['Tepung terigu', 'Minyak nabati', 'Garam', 'Gula', 'Bawang putih']),
                'status' => 'halal',
                'source' => 'local',
                'image' => 'https://www.indomie.com/uploads/product/indomie-mi-goreng-special_detail_095627771.png',
                'verification_status' => 'verified',
                'sugar_g' => 5, 'calories' => 380,
                'halal_certificate' => 'ID00110000000010121',
            ],
            [
                'nama_product' => 'Pocari Sweat 500ml',
                'barcode' => '4987035131411',
                'komposisi' => json_encode(['Air', 'Gula', 'Natrium klorida', 'Kalium klorida']),
                'status' => 'halal',
                'source' => 'local',
                'image' => 'https://pocarisweat.id/assets/img/product/product-500ml.png',
                'verification_status' => 'verified',
                'sugar_g' => 25, 'calories' => 120,
                'halal_certificate' => 'ID00110000000020121',
            ],
            [
                'nama_product' => 'Samyang Buldak Hot Chicken',
                'barcode' => '8801073311534',
                'komposisi' => json_encode(['Tepung terigu', 'Minyak kelapa sawit', 'Pati kentang', 'Saus pedas']),
                'status' => 'halal',
                'source' => 'openfoodfacts',
                'image' => 'https://images.openfoodfacts.org/images/products/880/107/331/1534/front_en.111.400.jpg',
                'verification_status' => 'verified',
                'sugar_g' => 4, 'calories' => 530,
                'halal_certificate' => 'KR00010000000030221',
            ],
            [
                'nama_product' => 'Tango Wafer Coklat',
                'barcode' => '089686040029',
                'komposisi' => json_encode(['Gula', 'Tepung terigu', 'Minyak nabati', 'Bubuk kakao', 'Susu bubuk']),
                'status' => 'halal',
                'source' => 'local',
                'verification_status' => 'verified',
                'sugar_g' => 18, 'calories' => 150,
                'halal_certificate' => 'ID00110000000040121',
            ],
            [
                'nama_product' => 'Ultra Milk 250ml Coklat',
                'barcode' => '8888166328006',
                'komposisi' => json_encode(['Susu segar', 'Gula', 'Bubuk kakao', 'Stabilizer']),
                'status' => 'halal',
                'source' => 'local',
                'verification_status' => 'verified',
                'sugar_g' => 22, 'calories' => 145,
                'halal_certificate' => 'ID00110000000050121',
            ],
            [
                'nama_product' => 'Haribo Goldbären Gummy',
                'barcode' => '4001686301074',
                'komposisi' => json_encode(['Gelatin', 'Gula', 'Sirup glukosa', 'Pengasam', 'Pewarna']),
                'status' => 'haram',
                'source' => 'openfoodfacts',
                'verification_status' => 'verified',
                'sugar_g' => 46, 'calories' => 343,
            ],
            [
                'nama_product' => 'SilverQueen Cashew Nut',
                'barcode' => '089686015020',
                'komposisi' => json_encode(['Gula', 'Kakao', 'Susu bubuk', 'Kacang mete', 'Lesitin kedelai']),
                'status' => 'halal',
                'source' => 'local',
                'verification_status' => 'verified',
                'sugar_g' => 30, 'calories' => 560,
                'halal_certificate' => 'ID00110000000070121',
            ],
            [
                'nama_product' => 'Kecap ABC Manis',
                'barcode' => '089686010377',
                'komposisi' => json_encode(['Kedelai', 'Gula aren', 'Garam', 'Air']),
                'status' => 'halal',
                'source' => 'local',
                'verification_status' => 'verified',
                'sugar_g' => 12, 'calories' => 80,
            ],
        ];

        foreach ($prodData as $i => $pd) {
            $pd['active'] = true;
            $pd['kategori_id'] = $categories[$i % count($categories)]->id_kategori;
            $products[] = ProductModel::firstOrCreate(
                ['barcode' => $pd['barcode']],
                $pd
            );
        }
        $this->command->info('✅ Products: ' . count($products) . ' records');

        // ========== 4. BANNERS (5) ==========
        $banners = [
            ['title' => 'Ramadhan Sehat with Halalytics', 'description' => 'Cek kehalalan takjilmu dengan fitur scan terbaru kami!', 'image' => 'https://img.freepik.com/free-vector/ramadan-kareem-sale-banner-template_23-2148873752.jpg', 'position' => 1, 'is_active' => true],
            ['title' => 'AI Meal Scanner', 'description' => 'Foto makananmu dan dapatkan analisis gizi & halal instan!', 'image' => 'https://img.freepik.com/free-vector/organic-food-banner-template_23-2148922282.jpg', 'position' => 2, 'is_active' => true],
            ['title' => 'Skincare Halal Checker', 'description' => 'Analisis kandungan skincare dengan database OpenBeautyFacts.', 'image' => 'https://img.freepik.com/free-vector/beauty-cosmetics-sale-banner_23-2148966287.jpg', 'position' => 3, 'is_active' => true],
            ['title' => 'Medicine Reminder', 'description' => 'Jangan lupa minum obat! Aktifkan pengingat AI sekarang.', 'image' => 'https://img.freepik.com/free-vector/health-care-banner-design_23-2149611253.jpg', 'position' => 4, 'is_active' => true],
            ['title' => 'Smart Report', 'description' => 'Lihat laporan kesehatan AI-mu di halaman Smart Report.', 'image' => 'https://img.freepik.com/free-vector/business-data-analysis-banner_23-2148944834.jpg', 'position' => 5, 'is_active' => true],
        ];
        foreach ($banners as $b) {
            Banner::firstOrCreate(['title' => $b['title']], $b);
        }
        $this->command->info('✅ Banners: ' . count($banners) . ' records');

        // ========== 5. SCAN HISTORY & SCAN MODEL (6) ==========
        $scanData = [
            ['product' => 0, 'method' => 'barcode', 'hours_ago' => 2],
            ['product' => 1, 'method' => 'barcode', 'hours_ago' => 5],
            ['product' => 2, 'method' => 'barcode', 'hours_ago' => 12],
            ['product' => 3, 'method' => 'barcode', 'hours_ago' => 24],
            ['product' => 4, 'method' => 'barcode', 'hours_ago' => 48],
            ['product' => 5, 'method' => 'barcode', 'hours_ago' => 72],
        ];
        foreach ($scanData as $sd) {
            $p = $products[$sd['product']];
            ScanHistory::firstOrCreate(
                ['user_id' => $user->id_user, 'scannable_id' => $p->id_product, 'scannable_type' => 'App\\Models\\ProductModel'],
                [
                    'product_name' => $p->nama_product,
                    'product_image' => $p->image,
                    'barcode' => $p->barcode,
                    'halal_status' => $p->status,
                    'scan_method' => $sd['method'],
                    'source' => 'local',
                    'created_at' => now()->subHours($sd['hours_ago']),
                ]
            );
            ScanModel::firstOrCreate(
                ['user_id' => $user->id_user, 'product_id' => $p->id_product],
                [
                    'nama_produk' => $p->nama_product,
                    'barcode' => $p->barcode,
                    'kategori' => $categories[$sd['product'] % count($categories)]->nama_kategori,
                    'status_halal' => $p->status,
                    'status_kesehatan' => 'sehat',
                    'tanggal_scan' => now()->subHours($sd['hours_ago']),
                ]
            );
        }

        // Additional timeline scans for 30/90/365 dashboard periods
        $timelineDays = [1, 2, 4, 6, 9, 12, 16, 21, 27, 33, 41, 55, 67, 84, 96, 121, 153, 189, 224, 266, 301, 333, 359];
        foreach ($timelineDays as $index => $day) {
            $p = $products[$index % count($products)];
            ScanModel::firstOrCreate(
                [
                    'user_id' => $rhavel->id_user,
                    'product_id' => $p->id_product,
                    'tanggal_scan' => now()->subDays($day)->startOfDay(),
                ],
                [
                    'nama_produk' => $p->nama_product,
                    'barcode' => $p->barcode,
                    'kategori' => optional($p->kategori)->nama_kategori ?? 'Umum',
                    'status_halal' => $p->status,
                    'status_kesehatan' => 'sehat',
                ]
            );
        }
        $this->command->info('✅ Scan Histories: ' . count($scanData) . ' records');

        // ========== 6. NOTIFICATIONS (8) ==========
        $notifs = [
            ['title' => '🎉 Selamat Datang di Halalytics!', 'message' => 'Mulai scan produk pertamamu dan temukan status halal instan.', 'type' => 'welcome'],
            ['title' => '⚠️ Produk Haram Terdeteksi', 'message' => 'Haribo Goldbären mengandung gelatin babi. Hindari konsumsi.', 'type' => 'alert', 'related_product_id' => $products[5]->id_product ?? null],
            ['title' => '💊 Pengingat Obat', 'message' => 'Waktunya minum Paracetamol 500mg. Jangan lupa ya!', 'type' => 'reminder'],
            ['title' => '📊 Laporan Mingguan', 'message' => 'Kamu sudah scan 6 produk minggu ini. 5 halal, 1 haram.', 'type' => 'report'],
            ['title' => '🔬 Hasil Lab AI', 'message' => 'Hasil analisis lab terbarumu sudah siap ditinjau.', 'type' => 'lab_result'],
            ['title' => '🏥 Tips Kesehatan', 'message' => 'Puasa sehat: pastikan sahur dengan makanan bergizi seimbang.', 'type' => 'health_tip'],
            ['title' => '✅ Verifikasi Produk Selesai', 'message' => 'Kecap ABC Manis telah diverifikasi sebagai halal oleh tim kami.', 'type' => 'verification'],
            ['title' => '🛡️ Keamanan Skincare', 'message' => 'Analisis skincare Wardah UV Shield menunjukkan skor aman 92/100.', 'type' => 'skincare_alert'],
        ];
        foreach ($notifs as $i => $n) {
            $n['user_id'] = $user->id_user;
            $n['is_read'] = $i < 3; // first 3 already read
            $n['created_at'] = now()->subHours($i * 6);
            Notification::firstOrCreate(
                ['user_id' => $n['user_id'], 'title' => $n['title']],
                $n
            );
        }
        $this->command->info('✅ Notifications: ' . count($notifs) . ' records');

        // ========== 7. MEDICAL RECORDS (5) ==========
        if (Schema::hasTable('medical_records')) {
            $medRecords = [
                ['title' => 'Pemeriksaan Umum', 'record_type' => 'Diagnosis', 'description' => 'Hasil pemeriksaan umum tahunan. Tekanan darah 120/80, gula darah normal.', 'hospital_name' => 'RS Cipto Mangunkusumo', 'doctor_name' => 'dr. Ahmad Fauzi', 'record_date' => now()->subDays(30)],
                ['title' => 'Tes Darah Lengkap', 'record_type' => 'Lab', 'description' => 'Hemoglobin 14.2, Leukosit 7500, Trombosit 250000.', 'hospital_name' => 'Lab Prodia', 'doctor_name' => 'dr. Siti Aminah', 'record_date' => now()->subDays(45)],
                ['title' => 'Resep Obat Asma', 'record_type' => 'Resep', 'description' => 'Salbutamol inhaler 100mcg, 2 puff saat sesak.', 'hospital_name' => 'RS Hermina', 'doctor_name' => 'dr. Rina Wati, Sp.P', 'record_date' => now()->subDays(15)],
                ['title' => 'Vaksin COVID-19 Booster', 'record_type' => 'Vaksinasi', 'description' => 'Dosis booster ke-2, Moderna. Tidak ada efek samping.', 'hospital_name' => 'Puskesmas Kecamatan', 'doctor_name' => 'dr. Hasan M', 'record_date' => now()->subDays(60)],
                ['title' => 'Operasi Usus Buntu', 'record_type' => 'Operasi', 'description' => 'Appendectomy laparoskopi berhasil. Pemulihan 7 hari.', 'hospital_name' => 'RS Pertamina', 'doctor_name' => 'dr. Budi S, Sp.B', 'record_date' => now()->subDays(90)],
            ];
            foreach ($medRecords as $mr) {
                $mr['id_user'] = $user->id_user;
                $mr['is_archived'] = false;
                MedicalRecord::firstOrCreate(
                    ['id_user' => $mr['id_user'], 'title' => $mr['title']],
                    $mr
                );
            }
            $this->command->info('✅ Medical Records: ' . count($medRecords) . ' records');
        }

        // ========== 8. MEDICINES (seeded by MedicineSeeder already, but ensure some exist) ==========
        $meds = Medicine::count();
        if ($meds < 5) {
            $medData = [
                ['name' => 'Paracetamol 500mg', 'generic_name' => 'Paracetamol', 'manufacturer' => 'Kimia Farma', 'halal_status' => 'halal', 'category' => 'Analgesik', 'dosage_form' => 'Tablet', 'description' => 'Obat pereda nyeri dan demam.'],
                ['name' => 'Amoxicillin 500mg', 'generic_name' => 'Amoxicillin', 'manufacturer' => 'Dexa Medica', 'halal_status' => 'halal', 'category' => 'Antibiotik', 'dosage_form' => 'Kapsul', 'description' => 'Antibiotik untuk infeksi bakteri.'],
                ['name' => 'OBH Combi', 'generic_name' => 'Dextromethorphan', 'manufacturer' => 'Combiphar', 'halal_status' => 'halal', 'category' => 'Batuk', 'dosage_form' => 'Sirup', 'description' => 'Obat batuk dan flu.'],
                ['name' => 'Antangin JRG', 'generic_name' => 'Herbal', 'manufacturer' => 'Deltomed', 'halal_status' => 'halal', 'category' => 'Herbal', 'dosage_form' => 'Cair', 'description' => 'Jamu untuk masuk angin.'],
                ['name' => 'Ibuprofen 400mg', 'generic_name' => 'Ibuprofen', 'manufacturer' => 'Indo Farma', 'halal_status' => 'syubhat', 'category' => 'Anti-inflamasi', 'dosage_form' => 'Tablet', 'description' => 'Anti-inflamasi non-steroid (NSAID).'],
            ];
            foreach ($medData as $md) {
                Medicine::firstOrCreate(['name' => $md['name']], $md);
            }
            $this->command->info('✅ Medicines: 5 records ditambahkan');
        } else {
            $this->command->info('✅ Medicines: sudah ada ' . $meds . ' records');
        }

        // ========== 9. MEDICINE REMINDERS (3) ==========
        if (Schema::hasTable('medicine_reminders')) {
            $firstMed = Medicine::first();
            if ($firstMed) {
                $reminders = [
                    ['id_medicine' => $firstMed->id_medicine, 'dosage' => '500mg', 'frequency_per_day' => 3, 'schedule_times' => json_encode(['08:00', '14:00', '20:00']), 'start_date' => now()->subDays(2), 'end_date' => now()->addDays(5), 'is_active' => true, 'notes' => 'Diminum setelah makan - demam ringan'],
                    ['id_medicine' => $firstMed->id_medicine, 'dosage' => '250mg', 'frequency_per_day' => 2, 'schedule_times' => json_encode(['07:00', '19:00']), 'start_date' => now(), 'end_date' => now()->addDays(3), 'is_active' => true, 'notes' => 'Sakit kepala, hubungi dokter jika berlanjut'],
                    ['id_medicine' => $firstMed->id_medicine, 'dosage' => '500mg', 'frequency_per_day' => 3, 'schedule_times' => json_encode(['06:00', '12:00', '18:00']), 'start_date' => now()->subDays(7), 'end_date' => now()->subDays(1), 'is_active' => false, 'notes' => 'Sudah selesai - flu'],
                ];
                foreach ($reminders as $r) {
                    $r['id_user'] = $user->id_user;
                    MedicineReminder::firstOrCreate(
                        ['id_user' => $r['id_user'], 'notes' => $r['notes']],
                        $r
                    );
                }
                $this->command->info('✅ Medicine Reminders: ' . count($reminders) . ' records');
            }
        }

        // ========== 11. PRODUCT REQUESTS (testing) ==========
        if (Schema::hasTable('product_requests')) {
            $requestSamples = [
                ['barcode' => '8991234500011', 'product_name' => 'Mie Kuah Pedas Nusantara'],
                ['barcode' => '8991234500012', 'product_name' => 'Susu Oat Vanilla Delight'],
                ['barcode' => '8991234500013', 'product_name' => 'Sarden Saus Tomat Premium'],
                ['barcode' => '8991234500014', 'product_name' => 'Kopi Susu Gula Aren Ready-to-Drink'],
                ['barcode' => '8991234500015', 'product_name' => 'Biskuit Gandum Coklat'],
                ['barcode' => '8991234500016', 'product_name' => 'Minyak Zaitun Extra Virgin'],
            ];
            foreach ($requestSamples as $sample) {
                ProductRequest::firstOrCreate(
                    [
                        'user_id' => $rhavel->id_user,
                        'barcode' => $sample['barcode'],
                    ],
                    [
                        'product_name' => $sample['product_name'],
                        'image_front' => 'images/placeholders/product-placeholder.svg',
                        'image_back' => 'images/placeholders/product-placeholder.svg',
                        'ocr_text' => 'Komposisi uji: air, gula, perisa alami, pengawet, pewarna makanan.',
                        'status' => 'pending',
                    ]
                );
            }
            $this->command->info('✅ Product Requests: fallback test data ready');
        }

        // ========== 12. REPORTS (testing + smart verification) ==========
        if (Schema::hasTable('reports')) {
            $reportPayloads = [
                ['reason' => 'incorrect_status', 'laporan' => 'Status halal tidak sesuai komposisi terbaru', 'status' => 'pending'],
                ['reason' => 'expired_cert', 'laporan' => 'Sertifikat halal diduga sudah kedaluwarsa', 'status' => 'pending'],
                ['reason' => 'fake_forgery', 'laporan' => 'Produk ini terindikasi memakai label halal palsu', 'status' => 'pending'],
                ['reason' => 'incorrect_status', 'laporan' => 'Perlu verifikasi ulang status karena perubahan resep', 'status' => 'approved'],
                ['reason' => 'other', 'laporan' => 'Kemasan tidak mencantumkan data halal secara jelas', 'status' => 'rejected'],
            ];
            foreach ($reportPayloads as $idx => $payload) {
                $product = $products[$idx % count($products)];
                ReportModel::firstOrCreate(
                    [
                        'user_id' => $rhavel->id_user,
                        'product_id' => $product->id_product,
                        'reason' => $payload['reason'],
                        'laporan' => $payload['laporan'],
                    ],
                    [
                        'status' => $payload['status'],
                        'evidence_image' => null,
                        'admin_notes' => $payload['status'] === 'pending' ? null : 'Auto seed notes',
                    ]
                );
            }
            $this->command->info('✅ Reports: test data ready');
        }

        // ========== 13. BPOM / COSMETICS BULK FALLBACK ==========
        if (Schema::hasTable('bpom_data')) {
            $bpomSamples = [
                ['nomor_reg' => 'NA18240100031', 'kategori' => 'kosmetik', 'nama_produk' => 'Brightening Day Cream', 'merk' => 'Lumina', 'status_keamanan' => 'aman', 'sumber_data' => 'open_beauty_facts'],
                ['nomor_reg' => 'NA18240100032', 'kategori' => 'kosmetik', 'nama_produk' => 'Acne Spot Gel', 'merk' => 'DermaPlus', 'status_keamanan' => 'waspada', 'sumber_data' => 'open_beauty_facts'],
                ['nomor_reg' => 'MD22450100033', 'kategori' => 'pangan', 'nama_produk' => 'Yoghurt Drink Strawberry', 'merk' => 'MilkyOne', 'status_keamanan' => 'aman', 'sumber_data' => 'open_food_facts'],
                ['nomor_reg' => 'MD22450100034', 'kategori' => 'pangan', 'nama_produk' => 'Potato Chips Sea Salt', 'merk' => 'CrunchIt', 'status_keamanan' => 'aman', 'sumber_data' => 'open_food_facts'],
                ['nomor_reg' => 'TR21240100035', 'kategori' => 'obat', 'nama_produk' => 'Antacid Tablet', 'merk' => 'MediLife', 'status_keamanan' => 'aman', 'sumber_data' => 'fallback_seed'],
                ['nomor_reg' => 'SI22450100036', 'kategori' => 'suplemen', 'nama_produk' => 'Omega 3 Fish Oil', 'merk' => 'NutraCore', 'status_keamanan' => 'waspada', 'sumber_data' => 'fallback_seed'],
            ];
            foreach ($bpomSamples as $sample) {
                BpomData::firstOrCreate(
                    ['nomor_reg' => $sample['nomor_reg']],
                    array_merge($sample, [
                        'verification_status' => 'pending',
                        'status_halal' => 'belum_diverifikasi',
                        'image_url' => 'images/placeholders/product-placeholder.svg',
                    ])
                );
            }
            $this->command->info('✅ BPOM: additional testing rows ready');
        }

        // ========== 14. INGREDIENTS + FORBIDDEN ==========
        if (Schema::hasTable('ingredients')) {
            $ingredientRows = [
                ['name' => 'Gelatin', 'e_number' => null, 'halal_status' => 'syubhat', 'health_risk' => 'low_risk'],
                ['name' => 'L-Cysteine', 'e_number' => 'E920', 'halal_status' => 'syubhat', 'health_risk' => 'low_risk'],
                ['name' => 'MSG', 'e_number' => 'E621', 'halal_status' => 'halal', 'health_risk' => 'safe'],
                ['name' => 'Titanium Dioxide', 'e_number' => 'E171', 'halal_status' => 'halal', 'health_risk' => 'high_risk'],
                ['name' => 'Carmine', 'e_number' => 'E120', 'halal_status' => 'haram', 'health_risk' => 'high_risk'],
            ];
            foreach ($ingredientRows as $row) {
                Ingredient::firstOrCreate(
                    ['name' => $row['name']],
                    array_merge($row, [
                        'description' => 'Seeded ingredient for admin testing.',
                        'sources' => 'Seed',
                        'notes' => null,
                        'active' => true,
                    ])
                );
            }
        }
        if (Schema::hasTable('forbidden_ingredients')) {
            $forbiddenRows = [
                ['name' => 'Lard', 'code' => null, 'type' => 'halal_haram', 'risk_level' => 'high'],
                ['name' => 'Porcine Gelatin', 'code' => null, 'type' => 'halal_haram', 'risk_level' => 'high'],
                ['name' => 'Sudan Red', 'code' => null, 'type' => 'health_hazard', 'risk_level' => 'high'],
                ['name' => 'Borax', 'code' => null, 'type' => 'health_hazard', 'risk_level' => 'high'],
            ];
            foreach ($forbiddenRows as $row) {
                ForbiddenIngredient::firstOrCreate(
                    ['name' => $row['name']],
                    array_merge($row, [
                        'aliases' => [],
                        'reason' => 'Seeded for admin testing',
                        'description' => 'Forbidden ingredient seeded data',
                        'source' => 'Seeder',
                        'is_active' => true,
                    ])
                );
            }
        }
        $this->command->info('✅ Ingredient encyclopedia + forbidden database ready');

        // ========== 15. PROMO BLOG ARTICLES ==========
        if (Schema::hasTable('promo_blogs')) {
            $blogRows = [
                ['title' => 'Cara Cek Status Halal Produk Secara Cepat', 'category' => 'halal-education'],
                ['title' => 'Memahami Label BPOM dan Artinya', 'category' => 'bpom'],
                ['title' => 'Tips Aman Memilih Kosmetik Harian', 'category' => 'beauty'],
                ['title' => 'Panduan Membaca Komposisi Obat untuk Muslim', 'category' => 'medicine'],
            ];
            foreach ($blogRows as $row) {
                $slug = Str::slug($row['title']);
                PromoBlog::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'title' => $row['title'],
                        'excerpt' => 'Artikel edukasi untuk membantu pengguna memahami status halal dan keamanan produk.',
                        'content' => 'Konten demo artikel untuk pengujian halaman blog admin dan user.',
                        'image' => null,
                        'category' => $row['category'],
                        'status' => 'published',
                        'views' => rand(25, 200),
                    ]
                );
            }
            $this->command->info('✅ Articles: published demo content ready');
        }

        // ========== 16. REALTIME ACTIVITY EVENTS ==========
        if (Schema::hasTable('activity_events')) {
            $events = [
                ['external_scan', 'Scan external barcode berhasil', 'success'],
                ['skincare_analysis', 'Analisis skincare selesai', 'success'],
                ['drug_interaction', 'Ditemukan interaksi level major', 'warning'],
                ['health_risk_score', 'Risk score dihitung', 'success'],
                ['drug_food_conflict', 'Konflik obat-makanan ditemukan', 'warning'],
            ];
            foreach (range(1, 20) as $index) {
                $def = $events[$index % count($events)];
                ActivityEvent::create([
                    'event_type' => $def[0],
                    'user_id' => $rhavel->id_user,
                    'username' => $rhavel->username,
                    'entity_ref' => 'seed-' . $index,
                    'summary' => $def[1],
                    'status' => $def[2],
                    'payload_json' => [
                        'severity' => $def[0] === 'drug_interaction' ? 'major' : 'low',
                        'has_conflict' => $def[0] === 'drug_food_conflict',
                    ],
                    'created_at' => now()->subMinutes($index * 7),
                ]);
            }
            $this->command->info('✅ Realtime activity feed data generated');
        }

        // ========== 10. Update User Stats ==========
        $user->update([
            'total_scans' => ScanModel::where('user_id', $user->id_user)->count() + ScanHistory::where('user_id', $user->id_user)->count(),
            'halal_products_count' => ScanModel::where('user_id', $user->id_user)->where('status_halal', 'halal')->count(),
        ]);

        $this->command->info('');
        $this->command->info('🎉 FullDemoSeeder selesai! Semua data testing sudah tersedia.');
        $this->command->info('   Login: admin@halalytics.com / password');
        $this->command->info('   Login: daffa@example.com / password');
    }
}
