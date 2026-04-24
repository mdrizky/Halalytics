<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Banner;
use App\Models\ScanModel;
use App\Models\ProductModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminPolishSeeder extends Seeder
{
    public function run()
    {
        /*
        |--------------------------------------------------------------------------
        | 1. HIGH-QUALITY HEALTH ARTICLES (Panjang, Informatif, Asli)
        |--------------------------------------------------------------------------
        */
        $articles = [
            [
                'title' => 'Panduan Lengkap Membaca Label Nutrisi pada Kemasan Produk Makanan',
                'category' => 'nutrition',
                'author' => 'dr. Ahmad Fauzi, M.Gizi',
                'image' => 'https://images.unsplash.com/photo-1506617420156-8e4536971650?w=800&h=400&fit=crop&q=80',
                'content' => 'Membaca label nutrisi pada kemasan produk makanan merupakan kebiasaan penting yang harus dimiliki setiap konsumen cerdas. Informasi Nilai Gizi (ING) yang tercantum pada kemasan produk memberikan gambaran lengkap tentang kandungan nutrisi per sajian, termasuk kalori, lemak total, lemak jenuh, lemak trans, kolesterol, natrium, karbohidrat total, serat pangan, gula total, gula tambahan, protein, serta berbagai vitamin dan mineral. Langkah pertama dalam membaca label nutrisi adalah memperhatikan ukuran sajian (serving size). Semua nilai gizi yang tercantum mengacu pada ukuran sajian ini, bukan keseluruhan isi kemasan. Seringkali satu kemasan berisi lebih dari satu sajian, sehingga jika Anda menghabiskan seluruh isi kemasan, kalori dan nutrisi yang dikonsumsi bisa berlipat ganda. Perhatikan pula Angka Kecukupan Gizi (AKG) atau %Daily Value yang menunjukkan seberapa besar kontribusi nutrisi tersebut terhadap kebutuhan harian Anda berdasarkan diet 2.000 kalori. Secara umum, 5% AKG atau kurang dianggap rendah, sementara 20% AKG atau lebih dianggap tinggi. Untuk nutrisi yang sebaiknya dibatasi seperti lemak jenuh, natrium, dan gula tambahan, pilihlah produk dengan %AKG rendah. Sebaliknya, untuk nutrisi yang bermanfaat seperti serat, vitamin D, kalsium, dan zat besi, pilihlah produk dengan %AKG tinggi. Daftar komposisi bahan juga sangat penting untuk dicermati. Bahan-bahan dicantumkan berdasarkan urutan jumlah terbanyak hingga paling sedikit. Jika gula atau minyak sawit tercantum di urutan pertama, itu berarti produk tersebut mengandung bahan tersebut dalam jumlah dominan. Waspadai juga nama-nama lain gula seperti sukrosa, fruktosa, maltosa, dekstrosa, sirup jagung, dan madu. Di Indonesia, BPOM mewajibkan pencantuman informasi alergen pada kemasan, termasuk susu, telur, kacang tanah, ikan, udang, gandum, kedelai, dan wijen. Bagi penderita alergi, informasi ini sangat krusial untuk menghindari reaksi alergi yang berbahaya. Dengan memahami cara membaca label nutrisi, Anda dapat membuat keputusan pembelian yang lebih cerdas dan mendukung gaya hidup sehat untuk seluruh keluarga.',
                'is_published' => true,
                'views' => 3250,
            ],
            [
                'title' => 'Bahaya Tersembunyi Merkuri dalam Produk Kosmetik Pemutih Wajah',
                'category' => 'beauty',
                'author' => 'Apt. Aisyah Rahmawati, S.Farm',
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&h=400&fit=crop&q=80',
                'content' => 'Merkuri atau air raksa (Hg) merupakan salah satu bahan berbahaya yang masih sering ditemukan dalam produk kosmetik pemutih wajah ilegal di Indonesia. Meskipun BPOM telah berulang kali menarik produk-produk berbahaya ini dari peredaran, permintaan pasar yang tinggi terhadap produk pemutih kulit instan membuat produk ilegal terus bermunculan. Merkuri bekerja dengan menghambat produksi melanin sehingga kulit tampak lebih putih dalam waktu singkat. Namun efek putih ini bersifat sementara dan menyimpan bahaya jangka panjang yang serius. Paparan merkuri secara terus-menerus melalui kulit dapat menyebabkan kerusakan ginjal permanen, gangguan sistem saraf pusat, kerusakan hati, dan bahkan kanker kulit. Gejala awal keracunan merkuri meliputi ruam kulit, iritasi, kulit menghitam saat berhenti pemakaian (rebound effect), dan penipisan kulit. Pada tahap lanjut, merkuri yang terakumulasi dalam tubuh dapat menyebabkan tremor, gangguan memori, insomnia, dan kerusakan organ dalam. Untuk mengenali produk kosmetik yang mengandung merkuri, perhatikan beberapa ciri berikut: produk tidak memiliki nomor registrasi BPOM, memberikan efek putih instan dalam hitungan hari, memiliki bau logam yang khas, dan seringkali dijual tanpa label komposisi yang jelas. Anda dapat memeriksa keamanan produk kosmetik melalui aplikasi Halalytics dengan memindai barcode produk atau memeriksa nomor registrasi BPOM. Pilihlah selalu produk kecantikan yang sudah terdaftar resmi di BPOM dan memiliki sertifikasi halal untuk menjamin keamanan dan kehalalan produk yang Anda gunakan.',
                'is_published' => true,
                'views' => 5120,
            ],
            [
                'title' => 'Mengenal Titik Kritis Kehalalan pada Produk Obat-obatan dan Suplemen',
                'category' => 'medicine',
                'author' => 'Tim Farmasi Halal Halalytics',
                'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=800&h=400&fit=crop&q=80',
                'content' => 'Kehalalan produk obat-obatan dan suplemen kesehatan menjadi perhatian serius bagi umat Muslim di seluruh dunia. Dalam industri farmasi, terdapat beberapa titik kritis kehalalan yang perlu dipahami oleh konsumen dan pelaku industri. Pertama, bahan aktif obat (Active Pharmaceutical Ingredient/API) umumnya diproduksi melalui sintesis kimia dan tidak memiliki masalah kehalalan. Namun, beberapa API yang berasal dari sumber hewani perlu dicermati, seperti heparin yang diekstrak dari mukosa usus babi dan digunakan sebagai antikoagulan. Kedua, bahan eksipien atau bahan tambahan merupakan titik kritis utama. Gelatin yang digunakan untuk cangkang kapsul seringkali berasal dari tulang dan kulit babi. Alternatif halal yang tersedia meliputi gelatin sapi halal, HPMC (Hydroxypropyl Methylcellulose) yang berbasis selulosa nabati, dan pati yang dimodifikasi. Ketiga, laktosa yang digunakan sebagai pengisi tablet bisa menjadi masalah jika diproduksi menggunakan rennet yang berasal dari lambung anak sapi yang tidak disembelih secara syariat Islam. Keempat, gliserin atau gliserol yang banyak digunakan dalam sirup obat dan kosmetik bisa berasal dari lemak babi. Gliserin halal dapat diproduksi dari minyak kelapa sawit atau minyak nabati lainnya. Kelima, alkohol yang digunakan sebagai pelarut dalam obat cair perlu dicermati. Para ulama memiliki perbedaan pendapat mengenai penggunaan alkohol dalam obat, namun mayoritas sepakat bahwa alkohol yang digunakan sebagai pelarut dalam konsentrasi kecil dan bukan untuk tujuan memabukkan masih diperbolehkan jika tidak ada alternatif lain. Halalytics bekerja sama dengan para ahli farmasi dan dewan fatwa untuk terus memperbarui database kehalalan obat-obatan di Indonesia, memudahkan masyarakat Muslim dalam memilih obat yang aman dan halal.',
                'is_published' => true,
                'views' => 2890,
            ],
            [
                'title' => 'Waspada Titanium Dioksida (E171) pada Makanan Olahan: Fakta dan Regulasi',
                'category' => 'nutrition',
                'author' => 'Nutrisionis Sarah Medina, S.Gz',
                'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&h=400&fit=crop&q=80',
                'content' => 'Titanium dioksida dengan kode E171 merupakan zat aditif makanan yang digunakan sebagai pewarna putih dan opacifier dalam berbagai produk makanan olahan. Bahan ini banyak ditemukan dalam permen, cokelat putih, selai, saus salad, dan produk kembang gula lainnya. Pada Mei 2021, Otoritas Keamanan Pangan Eropa (EFSA) menyatakan bahwa E171 tidak lagi dianggap aman sebagai bahan tambahan pangan, terutama karena potensi genotoksisitas dari nanopartikel yang dikandungnya. Berdasarkan penilaian EFSA tersebut, Uni Eropa resmi melarang penggunaan titanium dioksida sebagai bahan tambahan pangan mulai Agustus 2022. Namun di Indonesia, penggunaan E171 masih diperbolehkan dengan batasan tertentu sesuai Peraturan BPOM. Penelitian menunjukkan bahwa paparan jangka panjang terhadap nanopartikel titanium dioksida melalui konsumsi oral dapat menyebabkan kerusakan DNA pada sel-sel usus, perubahan respons imun, dan potensi efek karsinogenik. Meskipun risiko bagi manusia masih memerlukan penelitian lebih lanjut, prinsip kehati-hatian (precautionary principle) menjadi alasan utama banyak negara membatasi atau melarang penggunaannya. Untuk menghindari paparan E171, konsumen dapat memeriksa label komposisi produk dan menghindari produk yang mencantumkan titanium dioksida, CI 77891, atau E171 dalam daftar bahan. Halalytics menyediakan fitur scan barcode yang dapat membantu Anda mengidentifikasi produk-produk yang mengandung bahan aditif kontroversial ini.',
                'is_published' => true,
                'views' => 1840,
            ],
            [
                'title' => 'Sertifikasi Halal MUI: Proses, Manfaat, dan Cara Verifikasi Produk',
                'category' => 'health',
                'author' => 'Redaksi Halalytics',
                'image' => 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=800&h=400&fit=crop&q=80',
                'content' => 'Sertifikasi halal dari Badan Penyelenggara Jaminan Produk Halal (BPJPH) yang bekerja sama dengan Majelis Ulama Indonesia (MUI) merupakan jaminan resmi bahwa suatu produk telah memenuhi standar kehalalan sesuai syariat Islam. Sejak berlakunya UU No. 33 Tahun 2014 tentang Jaminan Produk Halal (JPH), sertifikasi halal menjadi kewajiban bagi seluruh produk yang beredar dan diperdagangkan di Indonesia, baik produk dalam negeri maupun impor. Proses sertifikasi halal meliputi beberapa tahapan: pertama, pelaku usaha mendaftarkan produknya ke BPJPH. Kedua, BPJPH menunjuk Lembaga Pemeriksa Halal (LPH) untuk melakukan pemeriksaan dan pengujian kehalalan produk. LPH akan memeriksa bahan baku, proses produksi, peralatan yang digunakan, sistem penyimpanan, distribusi, dan penyajian. Ketiga, hasil pemeriksaan LPH diserahkan ke MUI untuk penetapan fatwa kehalalan. Keempat, jika dinyatakan halal, BPJPH menerbitkan sertifikat halal yang berlaku selama 4 tahun. Manfaat sertifikasi halal bagi pelaku usaha meliputi peningkatan kepercayaan konsumen, akses ke pasar halal global yang bernilai triliunan dolar, keunggulan kompetitif dibanding produk non-halal, dan kepatuhan terhadap regulasi pemerintah. Bagi konsumen, sertifikasi halal memberikan rasa aman dan nyaman dalam mengonsumsi produk, serta memudahkan identifikasi produk yang sesuai dengan nilai-nilai keagamaan. Untuk memverifikasi keaslian sertifikat halal suatu produk, Anda dapat menggunakan fitur scan barcode di aplikasi Halalytics yang terhubung langsung dengan database BPJPH dan MUI. Pastikan selalu memeriksa logo halal resmi dan nomor sertifikat pada kemasan produk sebelum membeli.',
                'is_published' => true,
                'views' => 4560,
            ],
            [
                'title' => 'Jamu dan Herbal Indonesia: Khasiat Tradisional dalam Perspektif Ilmiah',
                'category' => 'health',
                'author' => 'Prof. dr. Bambang Setiawan, Sp.FK',
                'image' => 'https://images.unsplash.com/photo-1515023115689-589c33041d3c?w=800&h=400&fit=crop&q=80',
                'content' => 'Indonesia memiliki warisan budaya pengobatan tradisional yang sangat kaya, dengan lebih dari 9.600 spesies tanaman obat yang telah diidentifikasi. Jamu, sebagai bagian integral dari budaya kesehatan masyarakat Indonesia, kini mendapatkan pengakuan ilmiah melalui berbagai penelitian farmakologi modern. Temulawak (Curcuma xanthorrhiza) merupakan salah satu tanaman obat unggulan Indonesia. Kurkuminoid yang terkandung di dalamnya terbukti memiliki aktivitas hepatoprotektif yang melindungi hati dari kerusakan, serta sifat anti-inflamasi dan antioksidan yang kuat. Badan POM telah menyetujui penggunaan temulawak sebagai obat herbal terstandar untuk memelihara fungsi hati. Jahe (Zingiber officinale) telah terbukti efektif dalam mengurangi mual dan muntah, termasuk morning sickness pada ibu hamil, melalui mekanisme penghambatan reseptor serotonin 5-HT3. Kunyit (Curcuma longa) dengan kandungan kurkuminnya telah diteliti secara ekstensif dan menunjukkan potensi anti-kanker, anti-inflamasi, dan neuroprotektif. Sambiloto (Andrographis paniculata) dikenal sebagai King of Bitters dan terbukti memiliki aktivitas imunomodulator yang dapat meningkatkan sistem kekebalan tubuh. Namun perlu diingat bahwa penggunaan herbal juga memiliki risiko interaksi obat dan efek samping. Konsultasikan dengan tenaga kesehatan sebelum menggunakan produk herbal, terutama jika Anda sedang mengonsumsi obat-obatan lain. Halalytics menyediakan database lengkap tanaman obat Indonesia beserta interaksinya dengan obat konvensional.',
                'is_published' => true,
                'views' => 3780,
            ],
        ];

        foreach ($articles as $article) {
            $article['slug'] = Str::slug($article['title']);
            $article['excerpt'] = Str::limit(strip_tags($article['content']), 160);
            Article::updateOrCreate(['slug' => $article['slug']], $article);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. CLEAR BROKEN PRODUCT IMAGES
        |--------------------------------------------------------------------------
        */
        ProductModel::where(function ($q) {
            $q->where('image', 'like', '%placeholder%')
              ->orWhere('image', 'like', '%no-image%')
              ->orWhere('image', 'like', '%default-product%');
        })->update(['image' => null]);

        /*
        |--------------------------------------------------------------------------
        | 3. HEALTH-THEMED BANNERS (Unsplash - HD, bebas copyright)
        |--------------------------------------------------------------------------
        */
        Schema::disableForeignKeyConstraints();
        Banner::query()->delete();
        Schema::enableForeignKeyConstraints();

        $banners = [
            [
                'title' => 'Keamanan Produk Kesehatan Indonesia',
                'description' => 'Pastikan produk yang Anda konsumsi sudah terdaftar BPOM dan tersertifikasi halal MUI untuk keamanan keluarga tercinta.',
                'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=1400&h=500&fit=crop&q=85',
                'is_active' => true,
                'position' => 1,
            ],
            [
                'title' => 'Scan & Verifikasi Produk Halal',
                'description' => 'Lebih dari 50.000 produk terverifikasi tersedia. Scan barcode untuk mengetahui status halal dan keamanan produk secara instan.',
                'image' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=1400&h=500&fit=crop&q=85',
                'is_active' => true,
                'position' => 2,
            ],
            [
                'title' => 'Lindungi Keluarga dari Produk Berbahaya',
                'description' => 'Deteksi bahan berbahaya dan produk tidak terdaftar dengan teknologi scan barcode. Keselamatan keluarga adalah prioritas utama.',
                'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=1400&h=500&fit=crop&q=85',
                'is_active' => true,
                'position' => 3,
            ],
            [
                'title' => 'Ensiklopedia Bahan Alami Nusantara',
                'description' => 'Pelajari lebih dari 1.000 bahan herbal dan kandungan produk kesehatan tradisional Indonesia yang telah terbukti manfaatnya.',
                'image' => 'https://images.unsplash.com/photo-1515023115689-589c33041d3c?w=1400&h=500&fit=crop&q=85',
                'is_active' => true,
                'position' => 4,
            ],
            [
                'title' => 'Makanan Sehat, Hidup Berkualitas',
                'description' => 'Temukan produk makanan bergizi dan sehat yang telah terverifikasi untuk mendukung gaya hidup sehat keluarga Indonesia.',
                'image' => 'https://images.unsplash.com/photo-1512069772995-ec65ed45afd6?w=1400&h=500&fit=crop&q=85',
                'is_active' => true,
                'position' => 5,
            ],
            [
                'title' => 'Kosmetik Aman & Bersertifikat BPOM',
                'description' => 'Jangan korbankan kesehatan untuk kecantikan. Temukan kosmetik aman, terdaftar BPOM, dan bebas bahan berbahaya.',
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=1400&h=500&fit=crop&q=85',
                'is_active' => true,
                'position' => 6,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. SEED REALISTIC SCAN HISTORY (for Dashboard charts & feeds)
        |--------------------------------------------------------------------------
        */
        $user = User::first();
        if ($user) {
            $barcodes = [
                ['name' => 'Pocari Sweat 500ml', 'barcode' => '0498703513141', 'halal' => 'halal', 'cat' => 'food'],
                ['name' => 'SilverQueen Cashew 65g', 'barcode' => '8991001001851', 'halal' => 'halal', 'cat' => 'food'],
                ['name' => 'Sari Gandum Sandwich', 'barcode' => '8996001300077', 'halal' => 'halal', 'cat' => 'food'],
                ['name' => 'Indomie Goreng Aceh', 'barcode' => '8998866200333', 'halal' => 'halal', 'cat' => 'food'],
                ['name' => 'Samyang Hot Chicken', 'barcode' => '8801073311534', 'halal' => 'halal', 'cat' => 'food'],
                ['name' => 'Oreo Chocolate 137g', 'barcode' => '8992741911110', 'halal' => 'halal', 'cat' => 'food'],
                ['name' => 'Wardah Lightening Day Cream', 'barcode' => '8993137694103', 'halal' => 'halal', 'cat' => 'cosmetic'],
                ['name' => 'Paracetamol 500mg', 'barcode' => '089686010377', 'halal' => 'halal', 'cat' => 'medicine'],
            ];

            // Only seed if we have fewer than 50 scans
            if (ScanModel::count() < 50) {
                for ($i = 0; $i < 60; $i++) {
                    $item = $barcodes[array_rand($barcodes)];
                    $healthStatuses = ['Sehat', 'Kurang Sehat', 'Waspada'];
                    ScanModel::create([
                        'user_id' => $user->id_user,
                        'nama_produk' => $item['name'],
                        'barcode' => $item['barcode'],
                        'status_halal' => $item['halal'],
                        'status_kesehatan' => $healthStatuses[rand(0, 2)],
                        'tanggal_scan' => now()->subDays(rand(0, 29))->subHours(rand(0, 23)),
                        'kategori' => $item['cat'],
                    ]);
                }
            }
        }
    }
}
