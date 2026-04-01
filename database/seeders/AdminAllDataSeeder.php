<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductModel;
use App\Models\BpomData;
use App\Models\Article;
use App\Models\KategoriModel;
use App\Models\Medicine;
use App\Models\User;
use App\Models\ReportModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminAllDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚀 AdminAllDataSeeder: Populating all admin data...');

        // ========== 1. MORE CATEGORIES ==========
        $newCats = [
            ['nama_kategori' => 'Mie Instan', 'description' => 'Mie dan pasta instan siap saji'],
            ['nama_kategori' => 'Roti & Kue', 'description' => 'Produk bakery dan pastry'],
            ['nama_kategori' => 'Suplemen', 'description' => 'Vitamin dan suplemen kesehatan'],
            ['nama_kategori' => 'Baby Food', 'description' => 'Makanan dan susu bayi'],
            ['nama_kategori' => 'Daging Olahan', 'description' => 'Sosis, nugget, kornet, dll'],
            ['nama_kategori' => 'Kopi & Teh', 'description' => 'Minuman kopi dan teh kemasan'],
            ['nama_kategori' => 'Saus & Sambal', 'description' => 'Saus, sambal, dan bumbu cair'],
            ['nama_kategori' => 'Skincare', 'description' => 'Produk perawatan kulit wajah dan tubuh'],
            ['nama_kategori' => 'Makanan Kaleng', 'description' => 'Ikan kaleng, buah kaleng, dll'],
        ];
        foreach ($newCats as $c) {
            KategoriModel::firstOrCreate(['nama_kategori' => $c['nama_kategori']], $c);
        }
        $this->command->info('✅ Kategori tambahan: ' . count($newCats) . ' records');

        // ========== 2. PRODUCTS WITH EXTERNAL SOURCES ==========
        $externalProducts = [
            ['nama_product' => 'Nutella Hazelnut Spread', 'barcode' => '3017620422003', 'source' => 'openfoodfacts', 'status' => 'syubhat', 'verification_status' => 'verified', 'image' => 'https://images.openfoodfacts.org/images/products/301/762/042/2003/front_en.428.400.jpg', 'komposisi' => json_encode(['Sugar', 'Palm Oil', 'Hazelnuts 13%', 'Cocoa', 'Skim Milk Powder']), 'sugar_g' => 56, 'calories' => 539],
            ['nama_product' => 'Coca-Cola Classic 330ml', 'barcode' => '5449000000996', 'source' => 'openfoodfacts', 'status' => 'halal', 'verification_status' => 'verified', 'image' => 'https://images.openfoodfacts.org/images/products/544/900/000/0996/front_en.551.400.jpg', 'komposisi' => json_encode(['Carbonated Water', 'Sugar', 'Caramel Color', 'Phosphoric Acid', 'Natural Flavors']), 'sugar_g' => 35, 'calories' => 139],
            ['nama_product' => 'KitKat 4 Fingers', 'barcode' => '3800020435106', 'source' => 'openfoodfacts', 'status' => 'halal', 'verification_status' => 'verified', 'image' => 'https://images.openfoodfacts.org/images/products/380/002/043/5106/front_en.7.400.jpg', 'komposisi' => json_encode(['Sugar', 'Wheat Flour', 'Cocoa Butter', 'Milk']), 'sugar_g' => 48, 'calories' => 518],
            ['nama_product' => 'Oreo Original', 'barcode' => '7622210100610', 'source' => 'openfoodfacts', 'status' => 'halal', 'verification_status' => 'verified', 'image' => 'https://images.openfoodfacts.org/images/products/762/221/010/0610/front_en.6.400.jpg', 'komposisi' => json_encode(['Wheat Flour', 'Sugar', 'Palm Oil', 'Cocoa Powder']), 'sugar_g' => 38, 'calories' => 470],
            ['nama_product' => 'Pringles Original', 'barcode' => '5053990101573', 'source' => 'openfoodfacts', 'status' => 'halal', 'verification_status' => 'verified', 'image' => 'https://images.openfoodfacts.org/images/products/505/399/010/1573/front_en.7.400.jpg', 'komposisi' => json_encode(['Dehydrated Potatoes', 'Vegetable Oils', 'Rice Flour', 'Wheat Starch', 'Salt']), 'sugar_g' => 3, 'calories' => 503],
            ['nama_product' => 'Lays Classic Potato Chips', 'barcode' => '0028400028905', 'source' => 'openfoodfacts', 'status' => 'halal', 'verification_status' => 'verified', 'image' => 'https://images.openfoodfacts.org/images/products/002/840/002/8905/front_en.21.400.jpg', 'komposisi' => json_encode(['Potatoes', 'Vegetable Oil', 'Salt']), 'sugar_g' => 1, 'calories' => 536],
        ];
        $catIds = KategoriModel::pluck('id_kategori')->toArray();
        foreach ($externalProducts as $ep) {
            $ep['active'] = true;
            $ep['kategori_id'] = $catIds[array_rand($catIds)];
            ProductModel::firstOrCreate(['barcode' => $ep['barcode']], $ep);
        }
        $this->command->info('✅ External products: ' . count($externalProducts) . ' records');

        // ========== 3. BPOM DATA — MORE KOSMETIK ==========
        $kosmetikData = [
            ['nomor_reg' => 'NA18201700012', 'nama_produk' => 'Wardah UV Shield Essential Sunscreen SPF30', 'merk' => 'Wardah', 'kategori' => 'kosmetik', 'status_keamanan' => 'aman', 'status_halal' => 'halal', 'sumber_data' => 'bpom_ri', 'ingredients_text' => 'Aqua, Ethylhexyl Methoxycinnamate, Titanium Dioxide, Glycerin'],
            ['nomor_reg' => 'NA18201700014', 'nama_produk' => 'Emina Bright Stuff Moisturizing Cream', 'merk' => 'Emina', 'kategori' => 'kosmetik', 'status_keamanan' => 'aman', 'status_halal' => 'halal', 'sumber_data' => 'bpom_ri', 'ingredients_text' => 'Aqua, Niacinamide, Glycerin, Dimethicone'],
            ['nomor_reg' => 'NA18201700016', 'nama_produk' => 'Somethinc Niacinamide Moisture Sabi Blemish Serum', 'merk' => 'Somethinc', 'kategori' => 'kosmetik', 'status_keamanan' => 'aman', 'status_halal' => 'halal', 'sumber_data' => 'open_beauty_facts', 'ingredients_text' => 'Water, Niacinamide, Butylene Glycol, Sodium Hyaluronate'],
            ['nomor_reg' => 'NA18201700018', 'nama_produk' => 'Skintific 5X Ceramide Barrier Moisturize Gel', 'merk' => 'Skintific', 'kategori' => 'kosmetik', 'status_keamanan' => 'aman', 'status_halal' => 'halal', 'sumber_data' => 'open_beauty_facts', 'ingredients_text' => 'Aqua, Ceramide NP, Ceramide AP, Ceramide EOP, Squalane'],
            ['nomor_reg' => 'NA18201700020', 'nama_produk' => 'Cetaphil Gentle Skin Cleanser 125ml', 'merk' => 'Cetaphil', 'kategori' => 'kosmetik', 'status_keamanan' => 'aman', 'status_halal' => 'syubhat', 'sumber_data' => 'open_beauty_facts', 'ingredients_text' => 'Aqua, Cetyl Alcohol, Propylene Glycol, Sodium Lauryl Sulfate, Stearyl Alcohol'],
            ['nomor_reg' => 'NA18201700022', 'nama_produk' => 'PIXY White Aqua Gel Cream Night Cream', 'merk' => 'PIXY', 'kategori' => 'kosmetik', 'status_keamanan' => 'aman', 'status_halal' => 'halal', 'sumber_data' => 'bpom_ri', 'ingredients_text' => 'Aqua, Glycerin, Niacinamide, Hyaluronic Acid'],
            ['nomor_reg' => 'DITARIK001', 'nama_produk' => 'Cream Pemutih XL Super White (BERBAHAYA)', 'merk' => 'XL Beauty', 'kategori' => 'kosmetik', 'status_keamanan' => 'bahaya', 'status_halal' => 'haram', 'sumber_data' => 'bpom_ri', 'ingredients_text' => 'Mercury, Hydroquinone, Tretinoin (BAHAN TERLARANG)'],
            ['nomor_reg' => 'NA18201700024', 'nama_produk' => 'Safi White Natural Brightening Cream', 'merk' => 'Safi', 'kategori' => 'kosmetik', 'status_keamanan' => 'aman', 'status_halal' => 'halal', 'sumber_data' => 'bpom_ri', 'ingredients_text' => 'Water, Stearic Acid, Niacinamide, Glycerin, Tocopherol'],
            ['nomor_reg' => 'NA18201700026', 'nama_produk' => 'The Ordinary Niacinamide 10% + Zinc 1%', 'merk' => 'The Ordinary', 'kategori' => 'kosmetik', 'status_keamanan' => 'aman', 'status_halal' => 'syubhat', 'sumber_data' => 'open_beauty_facts', 'ingredients_text' => 'Aqua, Niacinamide, Pentylene Glycol, Zinc PCA'],
            ['nomor_reg' => 'NA18201700028', 'nama_produk' => 'Vaseline Healthy White UV Lotion', 'merk' => 'Vaseline', 'kategori' => 'kosmetik', 'status_keamanan' => 'aman', 'status_halal' => 'halal', 'sumber_data' => 'bpom_ri', 'ingredients_text' => 'Water, Glycerin, Stearic Acid, Niacinamide, Titanium Dioxide'],
        ];
        // Also add more BPOM data (obat/makanan)
        $bpomObat = [
            ['nomor_reg' => 'DKL1234567890A1', 'nama_produk' => 'Panadol Extra 500mg', 'merk' => 'Panadol', 'kategori' => 'obat', 'status_keamanan' => 'aman', 'status_halal' => 'halal', 'sumber_data' => 'bpom_ri', 'ingredients_text' => 'Paracetamol 500mg, Caffeine 65mg'],
            ['nomor_reg' => 'DKL1234567891A1', 'nama_produk' => 'Betadine Solution 30ml', 'merk' => 'Betadine', 'kategori' => 'obat', 'status_keamanan' => 'aman', 'status_halal' => 'halal', 'sumber_data' => 'bpom_ri', 'ingredients_text' => 'Povidone-Iodine 10%'],
            ['nomor_reg' => 'DKL1234567892A1', 'nama_produk' => 'Komix Herbal Original', 'merk' => 'Komix', 'kategori' => 'obat', 'status_keamanan' => 'aman', 'status_halal' => 'halal', 'sumber_data' => 'bpom_ri', 'ingredients_text' => 'Dextromethorphan HBr, Phenylpropanolamine HCl'],
            ['nomor_reg' => 'MD1234567893A1', 'nama_produk' => 'Indomilk Susu Kental Manis', 'merk' => 'Indomilk', 'kategori' => 'makanan', 'status_keamanan' => 'aman', 'status_halal' => 'halal', 'sumber_data' => 'bpom_ri', 'ingredients_text' => 'Susu Sapi, Gula, Laktosa'],
            ['nomor_reg' => 'MD1234567894A1', 'nama_produk' => 'Sari Roti Tawar', 'merk' => 'Sari Roti', 'kategori' => 'makanan', 'status_keamanan' => 'aman', 'status_halal' => 'halal', 'sumber_data' => 'bpom_ri', 'ingredients_text' => 'Tepung Terigu, Air, Gula, Ragi, Garam'],
        ];
        foreach (array_merge($kosmetikData, $bpomObat) as $bd) {
            BpomData::firstOrCreate(['nomor_reg' => $bd['nomor_reg']], $bd);
        }
        $this->command->info('✅ BPOM Data: ' . count($kosmetikData) . ' kosmetik + ' . count($bpomObat) . ' lainnya');

        // ========== 4. ARTICLES ==========
        if (Schema::hasTable('articles')) {
            $articles = [
                ['title' => 'Panduan Lengkap Membaca Label Halal BPJPH', 'slug' => 'panduan-label-halal-bpjph', 'excerpt' => 'Memahami cara membaca label halal resmi dari BPJPH dan perbedaan dengan label halal lama MUI.', 'content' => '<h2>Apa itu Label Halal BPJPH?</h2><p>Sejak 2019, sertifikasi halal di Indonesia dikelola oleh Badan Penyelenggara Jaminan Produk Halal (BPJPH). Label halal baru memiliki desain yang berbeda dari label MUI sebelumnya.</p><h3>Cara Membaca Label Halal Baru</h3><p>Label halal BPJPH berbentuk lingkaran dengan tulisan "HALAL" dalam bahasa Arab dan Latin. Di bawahnya terdapat nomor registrasi yang bisa dicek keasliannya.</p><h3>Perbedaan dengan Label Lama</h3><p>Label MUI lama berbentuk lingkaran hijau dengan tulisan MUI di tengah. Label ini masih berlaku hingga masa berlakunya habis.</p>', 'category' => 'halal', 'source' => 'local', 'author' => 'Dr. Ahmad Halal', 'image' => 'https://img.freepik.com/free-vector/halal-certified-badge-design_23-2148938834.jpg'],
                ['title' => 'Tips Sahur Sehat untuk Puasa Ramadhan', 'slug' => 'tips-sahur-sehat-ramadhan', 'excerpt' => 'Panduan nutrisi cerdas untuk sahur agar tetap berenergi sepanjang hari selama puasa Ramadhan.', 'content' => '<h2>Pentingnya Sahur yang Tepat</h2><p>Sahur bukan sekadar makan sebelum imsak. Pemilihan makanan yang tepat menentukan kualitas puasa kita sepanjang hari.</p><h3>Menu Sahur Ideal</h3><ul><li>Karbohidrat kompleks (nasi merah, oatmeal)</li><li>Protein (telur, ikan, tahu)</li><li>Serat (sayur, buah)</li><li>Air putih minimal 2 gelas</li></ul><h3>Makanan yang Dihindari</h3><p>Hindari makanan berminyak, terlalu asin, dan kafein berlebihan saat sahur.</p>', 'category' => 'health', 'source' => 'local', 'author' => 'Nutritionist Halalytics', 'image' => 'https://img.freepik.com/free-photo/healthy-food-background_23-2147867691.jpg'],
                ['title' => 'Mengenal Gelatin: Halal atau Haram?', 'slug' => 'mengenal-gelatin-halal-haram', 'excerpt' => 'Gelatin adalah bahan kontroversial dalam industri makanan. Pelajari sumbernya dan bagaimana menentukan kehalalan.', 'content' => '<h2>Apa itu Gelatin?</h2><p>Gelatin adalah protein yang diekstrak dari kulit, tulang, dan jaringan ikat hewan. Banyak digunakan sebagai pengental dalam permen, marshmallow, dan kapsul obat.</p><h3>Sumber Gelatin</h3><ul><li><strong>Babi:</strong> HARAM - Gelatin dari babi dilarang dalam Islam</li><li><strong>Sapi:</strong> Halal jika penyembelihan sesuai syariat</li><li><strong>Ikan:</strong> Umumnya halal</li><li><strong>Nabati:</strong> Alternatif halal (agar-agar, karagenan)</li></ul>', 'category' => 'halal', 'source' => 'local', 'author' => 'Tim Riset Halalytics', 'image' => 'https://img.freepik.com/free-photo/gummy-bears-colorful_23-2147680267.jpg'],
                ['title' => 'Cara Cek Obat Halal di Indonesia', 'slug' => 'cara-cek-obat-halal', 'excerpt' => 'Panduan lengkap mengecek status kehalalan obat-obatan menggunakan database BPOM dan LPPOM MUI.', 'content' => '<h2>Pentingnya Obat Halal</h2><p>Bagi umat Muslim, mengonsumsi obat halal adalah kewajiban. Namun, tidak semua obat memiliki sertifikat halal.</p><h3>Langkah Pengecekan</h3><ol><li>Cek label kemasan obat</li><li>Gunakan aplikasi Halalytics untuk scan barcode</li><li>Cek database BPOM online</li><li>Konsultasi dengan apoteker</li></ol>', 'category' => 'medicine', 'source' => 'local', 'author' => 'Apt. Siti Halimah', 'image' => 'https://img.freepik.com/free-photo/medical-pills-tablets_23-2148971722.jpg'],
                ['title' => 'Dampak Merkuri pada Kosmetik Ilegal', 'slug' => 'dampak-merkuri-kosmetik-ilegal', 'excerpt' => 'BPOM telah menarik ratusan produk kosmetik mengandung merkuri. Kenali bahayanya dan cara mengenalinya.', 'content' => '<h2>Bahaya Merkuri dalam Kosmetik</h2><p>Merkuri (air raksa) sering ditemukan dalam krim pemutih ilegal. Efek jangka pendek memang mencerahkan kulit, tetapi dampak jangka panjangnya sangat berbahaya.</p><h3>Dampak Kesehatan</h3><ul><li>Kerusakan ginjal</li><li>Gangguan saraf</li><li>Iritasi kulit parah</li><li>Keracunan kronis</li></ul>', 'category' => 'cosmetic', 'source' => 'local', 'author' => 'Tim BPOM Halalytics', 'image' => 'https://img.freepik.com/free-photo/cosmetics-products-table_23-2147893247.jpg'],
                ['title' => 'Pola Makan Sehat ala Rasulullah SAW', 'slug' => 'pola-makan-sehat-rasulullah', 'excerpt' => 'Mempelajari kebiasaan makan Nabi Muhammad SAW yang ternyata sangat selaras dengan ilmu gizi modern.', 'content' => '<h2>Prinsip Makan Rasulullah</h2><p>Rasulullah mengajarkan prinsip makan yang sederhana namun sangat kaya manfaat kesehatan.</p><h3>Kebiasaan Makan</h3><ul><li>Makan saat lapar, berhenti sebelum kenyang</li><li>Sepertiga makanan, sepertiga air, sepertiga udara</li><li>Berdoa sebelum dan sesudah makan</li><li>Makan dengan tangan kanan</li></ul>', 'category' => 'health', 'source' => 'local', 'author' => 'Ustaz Health Editor', 'image' => 'https://img.freepik.com/free-photo/top-view-delicious-food-arrangement_23-2149564946.jpg'],
                ['title' => 'Daftar E-Code Aditif Makanan yang Haram', 'slug' => 'daftar-ecode-aditif-haram', 'excerpt' => 'Kenali kode E-number pada kemasan makanan yang mengindikasikan bahan haram atau syubhat.', 'content' => '<h2>Apa itu E-Code?</h2><p>E-number adalah kode standar Eropa untuk zat aditif makanan. Beberapa di antaranya berasal dari sumber hewan yang tidak halal.</p><h3>E-Code yang Harus Diwaspadai</h3><ul><li>E120 (Carmine) - dari serangga</li><li>E441 (Gelatin) - mungkin dari babi</li><li>E904 (Shellac) - dari serangga</li><li>E471 (Mono/Diglycerides) - mungkin dari lemak hewan</li></ul>', 'category' => 'halal', 'source' => 'local', 'author' => 'Food Scientist Halalytics', 'image' => 'https://img.freepik.com/free-vector/food-additives-concept_23-2148898312.jpg'],
                ['title' => 'Review: 10 Skincare Halal Terbaik 2026', 'slug' => 'review-skincare-halal-terbaik-2026', 'excerpt' => 'Rekomendasi produk skincare halal terbaik tahun ini berdasarkan sertifikasi resmi dan review pengguna.', 'content' => '<h2>Skincare Halal Pilihan</h2><p>Semakin banyak brand skincare yang mendapatkan sertifikasi halal. Berikut rekomendasi terbaik kami.</p><h3>Top 5 Brand Halal</h3><ol><li>Wardah - Pioneer skincare halal Indonesia</li><li>Emina - Halal dan affordable</li><li>Safi - Research skin experts</li><li>Somethinc - Clean beauty halal</li><li>Avoskin - Natural ingredients</li></ol>', 'category' => 'cosmetic', 'source' => 'local', 'author' => 'Beauty Editor Halalytics', 'image' => 'https://img.freepik.com/free-photo/skincare-products-arrangement_23-2149214445.jpg'],
            ];
            foreach ($articles as $a) {
                Article::firstOrCreate(['slug' => $a['slug']], array_merge($a, ['is_published' => true, 'views' => rand(50, 500)]));
            }
            $this->command->info('✅ Articles: ' . count($articles) . ' records');
        }

        // ========== 5. MORE FORBIDDEN INGREDIENTS ==========
        if (Schema::hasTable('forbidden_ingredients')) {
            $forbidden = [
                ['name' => 'Alkohol (Ethanol)', 'code' => 'E1510', 'type' => 'Pelarut', 'risk_level' => 'high', 'reason' => 'Haram kecuali dalam jumlah sangat kecil yang tidak memabukkan', 'description' => 'Alkohol yang digunakan sebagai pelarut dalam makanan dan obat.', 'source' => 'LPPOM MUI'],
                ['name' => 'Gelatin Babi (Porcine Gelatin)', 'code' => 'E441-P', 'type' => 'Pengental', 'risk_level' => 'critical', 'reason' => 'Berasal dari babi, haram mutlak', 'description' => 'Protein dari kulit/tulang babi untuk permen, marshmallow, kapsul obat.', 'source' => 'LPPOM MUI'],
                ['name' => 'Shortening (Non-Halal)', 'code' => 'SHORT-NH', 'type' => 'Lemak', 'risk_level' => 'medium', 'reason' => 'Mungkin berasal dari lemak babi', 'description' => 'Lemak padat yang bisa berasal dari lemak babi dalam roti dan kue.', 'source' => 'BPJPH'],
                ['name' => 'L-Cysteine (E920)', 'code' => 'E920', 'type' => 'Aditif', 'risk_level' => 'medium', 'reason' => 'Bisa diproduksi dari rambut manusia atau bulu unggas', 'description' => 'Asam amino untuk pengembang roti.', 'source' => 'EFSA'],
                ['name' => 'Rennet (Non-Halal)', 'code' => 'RENNET-NH', 'type' => 'Enzim', 'risk_level' => 'high', 'reason' => 'Enzim dari perut anak sapi yang tidak disembelih syariat', 'description' => 'Enzim untuk pembuatan keju.', 'source' => 'LPPOM MUI'],
                ['name' => 'Carmine (E120)', 'code' => 'E120', 'type' => 'Pewarna', 'risk_level' => 'medium', 'reason' => 'Pewarna dari serangga cochineal, status diperdebatkan', 'description' => 'Pewarna merah alami dari serangga.', 'source' => 'EFSA'],
                ['name' => 'Mono & Digliserida (E471)', 'code' => 'E471', 'type' => 'Pengemulsi', 'risk_level' => 'medium', 'reason' => 'Bisa dari lemak hewan/babi', 'description' => 'Pengemulsi makanan yang sumbernya sulit ditentukan.', 'source' => 'LPPOM MUI'],
                ['name' => 'Stearic Acid (Animal)', 'code' => 'STEARIC-A', 'type' => 'Aditif', 'risk_level' => 'medium', 'reason' => 'Bisa berasal dari lemak babi', 'description' => 'Asam lemak dalam kosmetik dan obat.', 'source' => 'BPJPH'],
            ];
            foreach ($forbidden as $f) {
                DB::table('forbidden_ingredients')->updateOrInsert(
                    ['name' => $f['name']],
                    array_merge($f, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
                );
            }
            $this->command->info('✅ Forbidden Ingredients tambahan: ' . count($forbidden) . ' records');
        }

        // ========== 6. PRODUCT REPORTS (User Product Reports) ==========
        $user = User::where('email', 'daffa@example.com')->first();
        if ($user) {
            $products = ProductModel::take(6)->get();
            $reports = [
                ['reason' => 'produk_baru', 'laporan' => 'Menemukan produk Mie Sedaap belum ada di database. Mohon ditambahkan.', 'status' => 'pending'],
                ['reason' => 'tanya_kehalalan', 'laporan' => 'Apakah Es Krim Walls Magnum mengandung gelatin babi? Label tidak jelas.', 'status' => 'pending'],
                ['reason' => 'data_salah', 'laporan' => 'Status halal Kopi Good Day di app berbeda dengan kemasan. Mohon dicek.', 'status' => 'reviewing'],
                ['reason' => 'sertifikat_expired', 'laporan' => 'Sertifikat halal Roti Sari Gandum expired Desember 2025. Mohon update.', 'status' => 'resolved'],
                ['reason' => 'tanya_kehalalan', 'laporan' => 'Sabun Dove mengandung sodium tallowate (lemak sapi). Sumber sapinya halal?', 'status' => 'pending'],
                ['reason' => 'bahan_mencurigakan', 'laporan' => 'Permen Yupi mengandung gelatin tapi tidak tertulis sumbernya.', 'status' => 'pending'],
            ];
            foreach ($reports as $i => $r) {
                $pid = isset($products[$i]) ? $products[$i]->id_product : null;
                ReportModel::firstOrCreate(
                    ['user_id' => $user->id_user, 'laporan' => $r['laporan']],
                    array_merge($r, [
                        'user_id' => $user->id_user,
                        'product_id' => $pid,
                        'created_at' => now()->subDays($i * 3),
                    ])
                );
            }
            $this->command->info('✅ Product Reports: ' . count($reports) . ' records');
        }

        // ========== 7. MEDICINES from OpenFDA source ==========
        $fdaMeds = [
            ['name' => 'Ibuprofen 200mg', 'generic_name' => 'Ibuprofen', 'brand_name' => 'Advil', 'manufacturer' => 'Pfizer', 'halal_status' => 'syubhat', 'source' => 'openfda', 'category' => 'Anti-inflamasi', 'dosage_form' => 'Tablet', 'description' => 'NSAID untuk nyeri dan demam.', 'indications' => 'Nyeri ringan-sedang, demam, arthritis', 'side_effects' => 'Sakit perut, mual, pusing'],
            ['name' => 'Amoxicillin 250mg', 'generic_name' => 'Amoxicillin', 'brand_name' => 'Amoxil', 'manufacturer' => 'GSK', 'halal_status' => 'halal', 'source' => 'openfda', 'category' => 'Antibiotik', 'dosage_form' => 'Kapsul', 'description' => 'Antibiotik spektrum luas.', 'indications' => 'Infeksi bakteri saluran pernapasan, telinga, saluran kemih', 'side_effects' => 'Diare, mual, ruam kulit'],
            ['name' => 'Cetirizine 10mg', 'generic_name' => 'Cetirizine HCl', 'brand_name' => 'Zyrtec', 'manufacturer' => 'Johnson & Johnson', 'halal_status' => 'halal', 'source' => 'openfda', 'category' => 'Antihistamin', 'dosage_form' => 'Tablet', 'description' => 'Antihistamin non-sedatif.', 'indications' => 'Alergi, rhinitis, gatal-gatal', 'side_effects' => 'Mengantuk ringan, mulut kering'],
            ['name' => 'Omeprazole 20mg', 'generic_name' => 'Omeprazole', 'brand_name' => 'Prilosec', 'manufacturer' => 'AstraZeneca', 'halal_status' => 'syubhat', 'source' => 'openfda', 'category' => 'PPI', 'dosage_form' => 'Kapsul', 'description' => 'Penghambat pompa proton untuk asam lambung.', 'indications' => 'GERD, tukak lambung, heartburn', 'side_effects' => 'Sakit kepala, diare, mual'],
            ['name' => 'Metformin 500mg', 'generic_name' => 'Metformin HCl', 'brand_name' => 'Glucophage', 'manufacturer' => 'Merck', 'halal_status' => 'halal', 'source' => 'openfda', 'category' => 'Antidiabetes', 'dosage_form' => 'Tablet', 'description' => 'Obat diabetes tipe 2.', 'indications' => 'Diabetes melitus tipe 2', 'side_effects' => 'Mual, diare, nyeri perut'],
        ];
        foreach ($fdaMeds as $fm) {
            Medicine::firstOrCreate(['name' => $fm['name']], $fm);
        }
        $this->command->info('✅ OpenFDA Medicines: ' . count($fdaMeds) . ' records');

        $this->command->info('');
        $this->command->info('🎉 AdminAllDataSeeder selesai! Semua data admin telah dipopulasi.');
    }
}
