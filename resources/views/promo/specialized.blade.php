@extends('promo.layout')
@section('title', $data['title'] . ' - ' . ($settings['site_name'] ?? 'Halalytics'))

@section('styles')
<style>
    .spec-hero { padding-top: 120px; padding-bottom: 80px; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #fff 100%); }
    .stat-card { background: white; border-radius: 20px; padding: 24px; border: 1px solid #e5e7eb; text-align: center; }
    .stat-num { font-size: 2rem; font-weight: 900; color: #004D40; }
    .med-card { background: white; border-radius: 20px; padding: 20px; border: 1px solid #e5e7eb; transition: all .3s; }
    .med-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,77,64,.12); border-color: #10b981; }
    .step-num { width: 48px; height: 48px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.2rem; flex-shrink: 0; }
    .faq-item { border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden; }
    .faq-item summary { padding: 20px; font-weight: 700; cursor: pointer; list-style: none; display: flex; justify-content: space-between; align-items: center; }
    .faq-item summary::after { content: '+'; font-size: 1.5rem; color: #10b981; }
    .faq-item[open] summary::after { content: '−'; }
    .faq-item .faq-body { padding: 0 20px 20px; color: #6b7280; line-height: 1.8; font-size: 0.9rem; }
</style>
@endsection

@php
$specData = [
    'diabetes' => [
        'heroImg' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&q=80&w=800',
        'stats' => [['num'=>'2.4M','label'=>'Pasien Diabetes di RI'],['num'=>'24/7','label'=>'Monitoring Gula Darah'],['num'=>'150+','label'=>'Dokter Endokrin']],
        'features' => [
            ['icon'=>'🩸','title'=>'Monitor Gula Darah','desc'=>'Catat dan pantau kadar gula darah harian Anda dengan grafik tren mingguan dan bulanan.'],
            ['icon'=>'💊','title'=>'Paket Obat Langganan','desc'=>'Insulin, Metformin, dan obat diabetes lainnya dikirim rutin ke rumah setiap bulan.'],
            ['icon'=>'🥗','title'=>'Diet Plan Diabetes','desc'=>'Rencana makan rendah glikemik yang disesuaikan dengan kebutuhan kalori harian Anda.'],
            ['icon'=>'🧑‍⚕️','title'=>'Konsultasi Endokrin','desc'=>'Jadwalkan sesi video call dengan dokter spesialis penyakit dalam/endokrin.'],
            ['icon'=>'📊','title'=>'HbA1c Tracker','desc'=>'Pantau kadar HbA1c tiga bulanan untuk evaluasi kontrol gula darah jangka panjang.'],
            ['icon'=>'🚨','title'=>'Alert Hipoglikemia','desc'=>'Notifikasi darurat jika kadar gula darah turun di bawah batas aman.'],
        ],
        'medicines' => [
            ['name'=>'Metformin 500mg','desc'=>'Obat lini pertama untuk diabetes tipe 2. Membantu menurunkan produksi glukosa di hati.','price'=>'Rp 25.000'],
            ['name'=>'Glimepiride 2mg','desc'=>'Sulfonilurea yang merangsang pankreas untuk memproduksi lebih banyak insulin.','price'=>'Rp 35.000'],
            ['name'=>'Insulin Glargine','desc'=>'Insulin basal long-acting untuk kontrol gula darah 24 jam.','price'=>'Rp 180.000'],
        ],
        'faqs' => [
            ['q'=>'Apa perbedaan diabetes tipe 1 dan tipe 2?','a'=>'Diabetes tipe 1 adalah kondisi autoimun di mana tubuh tidak memproduksi insulin. Tipe 2 adalah kondisi di mana tubuh menjadi resisten terhadap insulin. Tipe 2 lebih umum (90% kasus) dan sering dikaitkan dengan gaya hidup.'],
            ['q'=>'Berapa kadar gula darah normal?','a'=>'Gula darah puasa normal: 70-100 mg/dL. Gula darah 2 jam setelah makan: di bawah 140 mg/dL. HbA1c normal: di bawah 5.7%.'],
            ['q'=>'Apakah diabetes bisa disembuhkan?','a'=>'Diabetes tipe 2 dapat dikelola dan bahkan dimasukkan ke remisi dengan perubahan gaya hidup drastis (diet, olahraga, penurunan berat badan). Namun secara medis, belum ada obat yang menyembuhkan total.'],
        ],
    ],
    'heart' => [
        'heroImg' => 'https://images.unsplash.com/photo-1628348070889-cb656235b4eb?auto=format&fit=crop&q=80&w=800',
        'stats' => [['num'=>'#1','label'=>'Penyebab Kematian Global'],['num'=>'36%','label'=>'Kematian di Indonesia'],['num'=>'200+','label'=>'Dokter Kardiologi']],
        'features' => [
            ['icon'=>'💓','title'=>'Monitor Tekanan Darah','desc'=>'Catat tekanan darah sistolik/diastolik harian dan lihat tren kesehatan jantung Anda.'],
            ['icon'=>'🧪','title'=>'Cek Kolesterol Homecare','desc'=>'Pesan tes profil lipid lengkap (LDL, HDL, Trigliserida) langsung dari rumah.'],
            ['icon'=>'🥦','title'=>'Diet Jantung Sehat','desc'=>'Rencana diet DASH dan Mediterranean yang terbukti menurunkan risiko penyakit jantung.'],
            ['icon'=>'🏃','title'=>'Program Olahraga','desc'=>'Panduan latihan kardio yang aman dan efektif sesuai kondisi jantung Anda.'],
            ['icon'=>'🧑‍⚕️','title'=>'Konsultasi Kardiologi','desc'=>'Akses langsung ke dokter spesialis jantung dan pembuluh darah.'],
            ['icon'=>'📈','title'=>'Risk Score Calculator','desc'=>'Hitung skor risiko penyakit kardiovaskular 10 tahun ke depan berdasarkan data Anda.'],
        ],
        'medicines' => [
            ['name'=>'Amlodipine 5mg','desc'=>'Penghambat kanal kalsium untuk menurunkan tekanan darah tinggi.','price'=>'Rp 15.000'],
            ['name'=>'Simvastatin 20mg','desc'=>'Statin untuk menurunkan kolesterol LDL dan risiko serangan jantung.','price'=>'Rp 30.000'],
            ['name'=>'Aspirin 100mg','desc'=>'Antiplatelet dosis rendah untuk pencegahan pembekuan darah.','price'=>'Rp 12.000'],
        ],
        'faqs' => [
            ['q'=>'Apa saja tanda serangan jantung?','a'=>'Nyeri dada seperti tertekan benda berat, nyeri menjalar ke lengan kiri/rahang, sesak napas, keringat dingin, mual, dan pusing. Segera hubungi 119 jika mengalami gejala ini.'],
            ['q'=>'Berapa tekanan darah normal?','a'=>'Tekanan darah normal: di bawah 120/80 mmHg. Prehipertensi: 120-139/80-89 mmHg. Hipertensi tahap 1: 140-159/90-99 mmHg.'],
            ['q'=>'Bagaimana cara menurunkan kolesterol secara alami?','a'=>'Konsumsi serat larut (oat, kacang), perbanyak omega-3 (ikan salmon), olahraga rutin 30 menit/hari, kurangi lemak jenuh, dan hentikan merokok.'],
        ],
    ],
    'mental' => [
        'heroImg' => 'https://images.unsplash.com/photo-1544027993-37dbfe43562a?auto=format&fit=crop&q=80&w=800',
        'stats' => [['num'=>'26M','label'=>'Orang Terdampak di RI'],['num'=>'100%','label'=>'Privasi Terjamin'],['num'=>'80+','label'=>'Psikolog Klinis']],
        'features' => [
            ['icon'=>'🧘','title'=>'Skrining Mental Gratis','desc'=>'Tes PHQ-9 dan GAD-7 terstandar WHO untuk deteksi dini depresi dan kecemasan.'],
            ['icon'=>'💬','title'=>'Sesi Konseling Privat','desc'=>'Video call 1-on-1 dengan psikolog klinis berlisensi, dijamin kerahasiaannya.'],
            ['icon'=>'📓','title'=>'Mood Journal','desc'=>'Catatan suasana hati harian dengan analisis pola emosi mingguan.'],
            ['icon'=>'😌','title'=>'Guided Meditation','desc'=>'Sesi meditasi terpandu berbahasa Indonesia untuk relaksasi dan mindfulness.'],
            ['icon'=>'🆘','title'=>'Crisis Hotline','desc'=>'Tombol darurat langsung ke hotline kesehatan jiwa 119 ext 8.'],
            ['icon'=>'📚','title'=>'Self-Help Library','desc'=>'Koleksi artikel dan video CBT (Cognitive Behavioral Therapy) dari pakar.'],
        ],
        'medicines' => [
            ['name'=>'Sertraline 50mg','desc'=>'Antidepresan golongan SSRI untuk gangguan depresi mayor dan kecemasan.','price'=>'Rp 45.000'],
            ['name'=>'Alprazolam 0.5mg','desc'=>'Benzodiazepin untuk gangguan kecemasan akut (hanya dengan resep dokter).','price'=>'Rp 35.000'],
            ['name'=>'Melatonin 3mg','desc'=>'Suplemen alami untuk membantu memperbaiki kualitas tidur.','price'=>'Rp 55.000'],
        ],
        'faqs' => [
            ['q'=>'Kapan harus ke psikolog?','a'=>'Jika gejala (sedih berkepanjangan, cemas berlebihan, gangguan tidur, kehilangan minat) berlangsung lebih dari 2 minggu dan mengganggu aktivitas sehari-hari. Jangan ragu untuk mencari bantuan — itu tanda kekuatan.'],
            ['q'=>'Apa bedanya psikolog dan psikiater?','a'=>'Psikolog memberikan terapi bicara (konseling/psikoterapi). Psikiater adalah dokter yang bisa meresepkan obat. Keduanya saling melengkapi untuk penanganan komprehensif.'],
            ['q'=>'Apakah konsultasi online efektif?','a'=>'Ya! Penelitian menunjukkan teleterapi memiliki efektivitas setara dengan sesi tatap muka untuk sebagian besar gangguan ringan-sedang, terutama depresi dan kecemasan.'],
        ],
    ],
    'skin' => [
        'heroImg' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&q=80&w=800',
        'stats' => [['num'=>'85%','label'=>'Akurasi Analisis AI'],['num'=>'50+','label'=>'Dermatolog Online'],['num'=>'1000+','label'=>'Produk Halal']],
        'features' => [
            ['icon'=>'📸','title'=>'Skin Analysis AI','desc'=>'Foto wajah Anda dan dapatkan analisis jenis kulit, masalah, dan rekomendasi produk instan.'],
            ['icon'=>'🧑‍⚕️','title'=>'Konsultasi Dermatologi','desc'=>'Chat atau video call dengan dokter spesialis kulit dan kelamin.'],
            ['icon'=>'🧴','title'=>'Skincare Halal','desc'=>'Rekomendasi produk perawatan kulit yang terverifikasi halal dan BPOM.'],
            ['icon'=>'📋','title'=>'Rutinitas Harian','desc'=>'Panduan skincare routine pagi dan malam yang dipersonalisasi.'],
            ['icon'=>'🔬','title'=>'Ingredient Checker','desc'=>'Cek kandungan produk skincare Anda apakah aman dan halal.'],
            ['icon'=>'📈','title'=>'Progress Tracker','desc'=>'Foto berkala dan lihat perkembangan kondisi kulit Anda dari waktu ke waktu.'],
        ],
        'medicines' => [
            ['name'=>'Tretinoin 0.025%','desc'=>'Retinoid topikal untuk jerawat dan anti-aging (dengan resep dokter).','price'=>'Rp 65.000'],
            ['name'=>'Niacinamide Serum 10%','desc'=>'Serum untuk mencerahkan, mengecilkan pori, dan mengontrol minyak berlebih.','price'=>'Rp 85.000'],
            ['name'=>'Sunscreen SPF 50+','desc'=>'Tabir surya broad spectrum untuk perlindungan UVA/UVB setiap hari.','price'=>'Rp 95.000'],
        ],
        'faqs' => [
            ['q'=>'Bagaimana urutan skincare yang benar?','a'=>'Pagi: Cleanser → Toner → Serum → Moisturizer → Sunscreen. Malam: Double cleanse → Toner → Treatment (retinol/AHA) → Moisturizer.'],
            ['q'=>'Apakah produk skincare halal berbeda?','a'=>'Produk skincare halal tidak mengandung bahan turunan hewan non-halal (seperti kolagen babi) dan bebas alkohol. Kualitasnya setara atau bahkan lebih baik karena menggunakan bahan alami.'],
            ['q'=>'Kapan harus ke dokter kulit?','a'=>'Jika mengalami jerawat parah yang tidak membaik dalam 3 bulan, ruam yang tidak hilang, perubahan bentuk/warna tahi lalat, atau masalah kulit yang mengganggu kepercayaan diri.'],
        ],
    ],
];
$spec = $specData[$data['slug'] ?? ''] ?? $specData['diabetes'];
@endphp

@section('content')
<!-- HERO -->
<section class="spec-hero px-6">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <span class="inline-block bg-{{ $data['color'] }}-100 text-{{ $data['color'] }}-700 text-sm font-bold px-4 py-2 rounded-full mb-6">Program Perawatan Khusus</span>
                <h1 class="text-5xl font-black text-gray-900 leading-tight mb-6">{{ $data['title'] }}</h1>
                <p class="text-xl text-gray-600 mb-10 leading-relaxed">{{ $data['desc'] }}</p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('download') }}" class="bg-emerald-600 text-white px-8 py-4 rounded-2xl font-black text-lg hover:bg-emerald-700 transition-all shadow-xl">Mulai Konsultasi</a>
                    <a href="#features" class="bg-white text-gray-700 border border-gray-200 px-8 py-4 rounded-2xl font-black text-lg hover:bg-gray-50">Pelajari Program</a>
                </div>
            </div>
            <div><img src="{{ $spec['heroImg'] }}" class="rounded-[40px] shadow-2xl" alt="{{ $data['title'] }}"></div>
        </div>

        <div class="grid grid-cols-3 gap-6 mt-16">
            @foreach($spec['stats'] as $st)
            <div class="stat-card"><p class="stat-num">{{ $st['num'] }}</p><p class="text-sm text-gray-500 font-bold mt-2">{{ $st['label'] }}</p></div>
            @endforeach
        </div>
    </div>
</section>

<!-- FEATURES -->
<section id="features" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-black text-gray-900 mb-4">Fitur Program {{ $data['title'] }}</h2>
        <p class="text-gray-500 mb-12 max-w-2xl">Layanan komprehensif yang dirancang khusus untuk kebutuhan Anda.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($spec['features'] as $f)
            <div class="med-card">
                <span class="text-4xl mb-4 block">{{ $f['icon'] }}</span>
                <h3 class="text-lg font-black text-gray-900 mb-3">{{ $f['title'] }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- MEDICINES -->
<section class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-black text-gray-900 mb-4">Obat & Vitamin Terkait</h2>
        <p class="text-gray-500 mb-12">Produk terverifikasi halal yang sering diresepkan untuk program ini.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($spec['medicines'] as $med)
            <div class="med-card">
                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-3xl mb-4">💊</div>
                <h4 class="font-black text-gray-900 mb-2">{{ $med['name'] }}</h4>
                <p class="text-sm text-gray-500 mb-4 leading-relaxed">{{ $med['desc'] }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-emerald-600 font-black">{{ $med['price'] }}</span>
                    <span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold">Halal ✓</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-black text-gray-900 mb-12 text-center">Cara Memulai</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            @php $steps = [['n'=>'1','t'=>'Download Aplikasi','d'=>'Unduh Halalytics dari Google Play Store.','c'=>'emerald'],['n'=>'2','t'=>'Pilih Program','d'=>'Pilih program '.$data['title'].' di menu Perawatan Khusus.','c'=>'blue'],['n'=>'3','t'=>'Konsultasi Dokter','d'=>'Chat atau video call dengan dokter spesialis.','c'=>'purple'],['n'=>'4','t'=>'Pantau Progres','d'=>'Gunakan alat monitor untuk pantau kesehatan harian.','c'=>'amber']]; @endphp
            @foreach($steps as $s)
            <div class="text-center">
                <div class="step-num bg-{{ $s['c'] }}-100 text-{{ $s['c'] }}-700 mx-auto mb-4">{{ $s['n'] }}</div>
                <h4 class="font-black text-gray-900 mb-2">{{ $s['t'] }}</h4>
                <p class="text-sm text-gray-500">{{ $s['d'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-black text-gray-900 mb-12 text-center">Pertanyaan Umum</h2>
        <div class="max-w-3xl mx-auto space-y-4">
            @foreach($spec['faqs'] as $faq)
            <details class="faq-item bg-white">
                <summary>{{ $faq['q'] }}</summary>
                <div class="faq-body">{{ $faq['a'] }}</div>
            </details>
            @endforeach
        </div>
    </div>
</section>

<!-- RELATED ARTICLES -->
@if($relatedArticles->count() > 0)
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-black text-gray-900 mb-12">Artikel Terkait</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedArticles as $article)
            <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 group cursor-pointer" onclick="location.href='{{ route('blog.show', $article->slug) }}'">
                <div class="w-full h-48 bg-emerald-50 flex items-center justify-center text-6xl">📝</div>
                <div class="p-6">
                    <h4 class="font-black text-gray-900 mb-2 leading-tight">{{ $article->title }}</h4>
                    <p class="text-sm text-gray-500 line-clamp-2">{{ $article->excerpt }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA -->
<section class="py-24 bg-emerald-600">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-4xl font-black text-white mb-6">Mulai Program {{ $data['title'] }} Sekarang</h2>
        <p class="text-emerald-100 text-lg mb-10">Download aplikasi Halalytics dan dapatkan konsultasi pertama Anda secara gratis.</p>
        <a href="{{ route('download') }}" class="bg-white text-emerald-700 px-10 py-5 rounded-2xl font-black text-xl hover:bg-emerald-50 transition-all shadow-2xl inline-block">Download Gratis</a>
    </div>
</section>
@endsection
