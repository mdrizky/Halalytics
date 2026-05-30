@extends('promo.layout')

@section('title', 'Halalytics - Solusi Kesehatan Dengan Kepastian Halal')

@section('content')
<!-- Hero Section -->
<section class="relative min-h-screen flex items-center pt-20 overflow-hidden bg-white">
    <div class="container mx-auto px-4 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 text-emerald-600 text-xs font-black tracking-widest uppercase animate-fade-in">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Halalytics AI v2.0 - Kini Lebih Cerdas & Responsif
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-black text-slate-900 leading-[1.1] tracking-tight">
                    Skrining Kesehatan <br>
                    <span class="text-emerald-600">Dengan Kepastian Halal.</span>
                </h1>
                
                <p class="text-lg text-slate-500 font-medium max-w-xl leading-relaxed">
                    Asisten pintar kesehatan terintegrasi pertama yang menghubungkan kecerdasan buatan AI dengan basis data obat-obatan BPOM serta sertifikasi halal resmi. Cepat, akurat, dan aman.
                </p>

                <div class="flex flex-wrap gap-4 items-center">
                    <a href="{{ route('download') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-2xl font-black shadow-xl shadow-emerald-900/20 transition-all transform hover:-translate-y-1 flex items-center gap-3">
                        Download Aplikasi 
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="#health-check" class="bg-white border-2 border-slate-100 hover:border-emerald-100 text-slate-600 px-8 py-4 rounded-2xl font-black transition-all">
                        Kalkulator Medis
                    </a>
                </div>

                <!-- App Value Proposition -->
                <div class="bg-emerald-50/50 border border-emerald-100 p-6 rounded-3xl max-w-xl">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-2xl shadow-sm">📱</div>
                        <div>
                            <h4 class="font-black text-slate-900">Ekosistem Halal Terlengkap</h4>
                            <p class="text-sm text-slate-500 mt-1">Satu aplikasi untuk semua kebutuhan gaya hidup halal & sehat Anda. Tersedia gratis di Play Store.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Device Preview -->
            <div class="relative hidden lg:block">
                <div class="relative z-10 animate-float flex justify-center">
                    <div class="relative w-[320px] h-[640px] bg-slate-900 rounded-[3rem] border-[8px] border-slate-800 shadow-2xl overflow-hidden">
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-slate-800 rounded-b-2xl z-20"></div>
                        <img src="{{ asset('images/promo/ss-home-1.png') }}" alt="Halalytics App" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div>
                    </div>
                </div>
                
                <!-- Floating Info Cards -->
                <div class="absolute top-10 -left-10 bg-white p-5 rounded-3xl shadow-xl border border-emerald-50 w-64 z-20 animate-float-delayed">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500 flex items-center justify-center text-white">
                            <span class="material-icons-round">auto_awesome</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase">AI Halalytics (HILDA)</p>
                            <p class="text-xs font-extrabold text-slate-800">"Apakah kandungan gelatin babi ada pada obat A?"</p>
                        </div>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-2xl">
                        <p class="text-[10px] leading-relaxed text-slate-500 italic">"Hasil penelusuran: Obat A terdaftar BPOM dan menggunakan gelatin sapi bersertifikat halal."</p>
                    </div>
                </div>

                <!-- Live Status Badge -->
                <div class="absolute -bottom-4 right-0 bg-white p-5 rounded-3xl shadow-xl border border-emerald-50 w-60 z-20">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Komunitas Halalytics</p>
                    <div class="flex items-center gap-3">
                        <div class="w-3.5 h-3.5 rounded-full bg-emerald-500 animate-ping absolute"></div>
                        <div class="w-3.5 h-3.5 rounded-full bg-emerald-500 relative"></div>
                        <p class="font-extrabold text-sm text-slate-800">{{ number_format($stats['total_users'] ?? 0) }} Pengguna Aktif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Partner Marquee -->
<div class="py-12 bg-white border-y border-slate-50 overflow-hidden">
    <div class="container mx-auto px-4">
        <p class="text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-8">Didukung Oleh Data & Teknologi Dari</p>
        <div class="swiper partner-swiper">
            <div class="swiper-wrapper flex items-center">
                @php
                $partners = [
                    ['name' => 'BPOM RI', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/d/d0/Logo_BPOM.png'],
                    ['name' => 'Kemenkes', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/b/be/Logo_Kementerian_Kesehatan_Republik_Indonesia.png'],
                    ['name' => 'MUI', 'logo' => 'https://upload.wikimedia.org/wikipedia/id/thumb/a/a2/Logo_MUI.svg/1200px-Logo_MUI.svg.png'],
                    ['name' => 'Gemini AI', 'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Google_Gemini_logo.svg/2560px-Google_Gemini_logo.svg.png'],
                    ['name' => 'Open Food Facts', 'logo' => 'https://world.openfoodfacts.org/images/misc/openfoodfacts-logo-en-600x600.png'],
                    ['name' => 'OpenFDA', 'logo' => 'https://open.fda.gov/img/openfda-logo.svg'],
                ];
                @endphp
                @foreach($partners as $p)
                <div class="swiper-slide !w-auto px-8">
                    <img src="{{ $p['logo'] }}" alt="{{ $p['name'] }}" class="h-10 md:h-12 w-auto grayscale opacity-40 hover:grayscale-0 hover:opacity-100 transition-all duration-500 object-contain">
                </div>
                @endforeach
                <!-- Duplicate for seamless loop -->
                @foreach($partners as $p)
                <div class="swiper-slide !w-auto px-8">
                    <img src="{{ $p['logo'] }}" alt="{{ $p['name'] }}" class="h-10 md:h-12 w-auto grayscale opacity-40 hover:grayscale-0 hover:opacity-100 transition-all duration-500 object-contain">
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Latest Blogs Section -->
<section class="py-24 bg-slate-50">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest mb-4">
                    <span class="material-icons-round text-xs">auto_stories</span>
                    Edukasi Halal & Sehat
                </div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tight">Wawasan Halal & Sehat</h2>
                <p class="text-slate-500 mt-2 font-medium">Informasi terkini dari pakar nutrisi dan kesehatan Halalytics.</p>
            </div>
            <a href="{{ route('blog.index') }}" class="group bg-white border border-slate-100 px-6 py-3 rounded-2xl text-emerald-600 font-black flex items-center gap-2 hover:bg-emerald-50 transition-all shadow-sm">
                Lihat Semua Artikel 
                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($latestBlogs as $blog)
            <div class="group bg-white rounded-[2.5rem] overflow-hidden border border-slate-100 shadow-sm hover:shadow-2xl hover:shadow-emerald-900/5 transition-all duration-500 transform hover:-translate-y-2">
                <div class="aspect-[16/10] overflow-hidden relative bg-slate-100">
                    <img src="{{ $blog->image_url }}" 
                         alt="{{ $blog->title }}" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                         onerror="this.src='https://picsum.photos/seed/{{ $blog->id }}/800/500'">
                    <div class="absolute top-4 left-4">
                        <span class="bg-white/90 backdrop-blur-md text-emerald-600 text-[10px] font-black px-3 py-1.5 rounded-full shadow-sm">
                            {{ $blog->category ?? 'Edukasi' }}
                        </span>
                    </div>
                </div>
                <div class="p-8">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">{{ $blog->formatted_date }}</p>
                    <h3 class="text-xl font-black text-slate-900 group-hover:text-emerald-600 transition-colors leading-tight mb-4">
                        <a href="{{ route('blog.show', $blog->slug) }}">{{ $blog->title }}</a>
                    </h3>
                    <p class="text-sm text-slate-500 leading-relaxed line-clamp-2 mb-6 font-medium">{{ $blog->excerpt }}</p>
                    <a href="{{ route('blog.show', $blog->slug) }}" class="inline-flex items-center gap-2 text-xs font-black text-slate-900 group-hover:text-emerald-600 transition-colors">
                        BACA SELENGKAPNYA 
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Health Tools / Calculator Section -->
<section class="py-24 bg-white" id="health-check">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black tracking-widest uppercase mb-6">
                    Self-Screening Tools
                </div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tight leading-tight mb-6">
                    Pantau Kesehatan Anda <br>
                    <span class="text-emerald-600">Secara Mandiri & Presisi.</span>
                </h2>
                <p class="text-slate-500 font-medium leading-relaxed mb-8">
                    Gunakan kalkulator medis berbasis standar internasional untuk mengetahui profil kesehatan Anda. Data Anda aman dan diproses secara privat oleh sistem AI kami.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-6 rounded-3xl border border-slate-100 bg-slate-50/50 hover:border-emerald-200 transition-all group">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">⚖️</div>
                        <h4 class="font-black text-slate-900 mb-1">Kalkulator BMI</h4>
                        <p class="text-xs text-slate-500">Cek indeks massa tubuh dan berat badan ideal Anda.</p>
                    </div>
                    <div onclick="openWaterModal()" class="p-6 rounded-3xl border border-slate-100 bg-slate-50/50 hover:border-emerald-200 transition-all group cursor-pointer">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">💧</div>
                        <h4 class="font-black text-slate-900 mb-1">Target Air Minum</h4>
                        <p class="text-xs text-slate-500">Hitung kebutuhan hidrasi harian berdasarkan berat badan.</p>
                    </div>
                    <div onclick="openHPLModal()" class="p-6 rounded-3xl border border-slate-100 bg-slate-50/50 hover:border-emerald-200 transition-all group cursor-pointer">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">🤰</div>
                        <h4 class="font-black text-slate-900 mb-1">Estimasi HPL</h4>
                        <p class="text-xs text-slate-500">Hitung hari perkiraan lahir untuk ibu hamil.</p>
                    </div>
                    <div onclick="openEyeModal()" class="p-6 rounded-3xl border border-slate-100 bg-slate-50/50 hover:border-emerald-200 transition-all group cursor-pointer">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">👁️</div>
                        <h4 class="font-black text-slate-900 mb-1">Skrining Mata</h4>
                        <p class="text-xs text-slate-500">Tes ketajaman mata sederhana (Snellen Chart).</p>
                    </div>
                </div>

                <div class="mt-10 p-6 bg-emerald-600 rounded-[2.5rem] text-white flex items-center justify-between gap-6">
                    <div>
                        <p class="text-xs font-black opacity-80 uppercase tracking-widest mb-1">Ingin Hasil Lebih Detail?</p>
                        <p class="text-sm font-bold">Download aplikasi untuk analisis AI lengkap!</p>
                    </div>
                    <a href="{{ route('download') }}" class="px-6 py-3 bg-white text-emerald-600 rounded-2xl font-black text-xs hover:bg-emerald-50 transition-all whitespace-nowrap">
                        Download Now
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -inset-4 bg-emerald-100/50 rounded-[3rem] blur-3xl -z-10"></div>
                <div class="bg-white p-8 md:p-12 rounded-[3rem] border border-slate-100 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8">
                        <span class="material-icons-round text-slate-100 text-8xl">calculate</span>
                    </div>
                    
                    <div id="bmi-calculator" class="relative z-10">
                        <h3 class="text-2xl font-black text-slate-900 mb-8 flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center">
                                <span class="material-icons-round text-xl">monitor_weight</span>
                            </span>
                            BMI Quick Check
                        </h3>

                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Berat Badan (kg)</label>
                                    <input type="number" id="bmi-weight" placeholder="Contoh: 65" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-emerald-500 transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Tinggi Badan (cm)</label>
                                    <input type="number" id="bmi-height" placeholder="Contoh: 170" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-emerald-500 transition-all">
                                </div>
                            </div>

                            <button onclick="calculateBMI()" class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/20 flex items-center justify-center gap-3">
                                Hitung Skor BMI
                                <span class="material-icons-round">bolt</span>
                            </button>

                            <div id="bmi-result" class="hidden animate-fade-in">
                                <div class="p-6 rounded-3xl bg-emerald-50 border border-emerald-100">
                                    <div class="flex justify-between items-end mb-4">
                                        <div>
                                            <div class="flex items-center gap-1.5 mb-1">
                                                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Hasil Analisis</p>
                                                <span class="px-1.5 py-0.5 rounded-md bg-emerald-600 text-[8px] font-black text-white flex items-center gap-0.5">
                                                    <span class="material-icons-round text-[10px]">auto_awesome</span>
                                                    AI POWERED
                                                </span>
                                            </div>
                                            <h4 id="bmi-status" class="text-xl font-black text-slate-900">Normal</h4>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Skor BMI</p>
                                            <p id="bmi-score" class="text-3xl font-black text-emerald-600">22.4</p>
                                        </div>
                                    </div>
                                    <div id="bmi-advice" class="text-xs text-slate-600 leading-relaxed font-medium"></div>
                                    <button onclick="resetBMI()" class="mt-6 w-full py-3 rounded-xl border border-emerald-200 text-emerald-600 text-[10px] font-black uppercase tracking-widest hover:bg-emerald-100 transition-all">
                                        Cek Ulang Lagi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Services Section -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-4xl font-black text-slate-900 tracking-tight">Layanan Unggulan Kami</h2>
            <p class="text-slate-500 mt-4 font-medium">Ekosistem lengkap untuk mendukung gaya hidup halal dan sehat keluarga Anda setiap hari.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
            $mainServices = [
                ['icon' => '🔍', 'name' => 'Unified Scanner', 'desc' => 'Scan barcode produk untuk cek halal & nutrisi instan.', 'color' => '#f0fdf4', 'link' => route('download')],
                ['icon' => '🛡️', 'name' => 'AI Ingredients', 'desc' => 'Analisis komposisi bahan kimia (E-numbers) secara otomatis.', 'color' => '#eff6ff', 'link' => route('download')],
                ['icon' => '📦', 'name' => 'Family Box', 'desc' => 'Satu scan untuk cek keamanan seluruh anggota keluarga.', 'color' => '#fff7ed', 'link' => route('download')],
                ['icon' => '💊', 'name' => 'Medicine Reminder', 'desc' => 'Pengingat minum obat pintar dengan jadwal makan.', 'color' => '#fef2f2', 'link' => route('download')],
                ['icon' => '🥗', 'name' => 'Recipe AI', 'desc' => 'Cari resep sehat & substitusi bahan halal otomatis.', 'color' => '#faf5ff', 'link' => route('download')],
                ['icon' => '🩸', 'name' => 'Blood Donation', 'desc' => 'Pantau stok darah & daftar donor dengan mudah.', 'color' => '#fff1f2', 'link' => route('download')],
                ['icon' => '📈', 'name' => 'Health Tracker', 'desc' => 'Monitoring asupan Gula, Natrium & Kalori harian.', 'color' => '#f0f9ff', 'link' => route('download')],
                ['icon' => '🩺', 'name' => 'Chat Ahli Gizi', 'desc' => 'Konsultasi gizi & diet dengan pakar profesional.', 'color' => '#f5f3ff', 'link' => route('download')]
            ];
            @endphp

            @foreach($mainServices as $service)
            <a href="{{ $service['link'] }}" class="block group p-8 rounded-[2.5rem] transition-all duration-500 hover:shadow-2xl hover:shadow-emerald-900/5 hover:-translate-y-1" style="background-color: {{ $service['color'] }};">
                <div class="text-4xl mb-6 transform transition-transform group-hover:scale-110 duration-500">{{ $service['icon'] }}</div>
                <h4 class="text-lg font-black text-slate-900 mb-2">{{ $service['name'] }}</h4>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">{{ $service['desc'] }}</p>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Health Dictionary Section -->
<section class="py-24 bg-slate-50 overflow-hidden" id="dictionary">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row justify-between items-end mb-16 gap-8">
            <div class="max-w-2xl">
                <h2 class="text-4xl font-black text-slate-900 tracking-tight">Kamus Kesehatan A-Z</h2>
                <p class="text-slate-500 mt-4 font-medium leading-relaxed">
                    Ensiklopedia kesehatan terlengkap yang membahas penyakit, obat-obatan, dan istilah medis dari perspektif kesehatan umum serta tinjauan titik kritis kehalalan.
                </p>
            </div>
            
            <div class="flex flex-wrap gap-2 justify-center lg:justify-end max-w-xl">
                @foreach(range('A', 'Z') as $char)
                <button onclick="filterAlphabet('{{ $char }}')" 
                        class="char-btn w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-xs font-black text-slate-400 hover:border-emerald-300 hover:text-emerald-600 transition-all {{ $char === 'A' ? 'active bg-emerald-600 border-emerald-600 text-white' : '' }}">
                    {{ $char }}
                </button>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="dictionary-grid">
            @foreach($diseases ?? [] as $d)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-emerald-300 hover:shadow-md transition-all cursor-pointer group" 
                 data-disease='@json($d)'
                 onclick="openDictionaryDetail(JSON.parse(this.dataset.disease))">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-black text-gray-900 group-hover:text-emerald-700 transition-colors">{{ $d->title ?? ($d['title'] ?? 'Penyakit') }}</h4>
                    <span class="text-xs font-extrabold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">{{ $d->alphabet ?? ($d['alphabet'] ?? 'A') }}</span>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed line-clamp-2">{{ $d->summary ?? ($d['summary'] ?? '') }}</p>
                <div class="mt-4 flex items-center text-xs font-extrabold text-emerald-600 gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    Lihat Selengkapnya <span>&rarr;</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Dictionary Detail Modal -->
<div id="dict-modal" class="fixed inset-0 z-[2000] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-300">
    <div id="dict-modal-card" class="bg-white w-full max-w-3xl rounded-[3rem] shadow-2xl overflow-hidden transform scale-90 transition-all duration-300">
        <div class="p-8 md:p-12 max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex justify-between items-start mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-3xl">🩺</div>
                    <div>
                        <h3 id="dict-title" class="text-3xl font-black text-slate-900 leading-tight">Detail Penyakit</h3>
                        <p class="text-xs font-black text-emerald-600 tracking-widest uppercase mt-1">Informasi Medis & Halal</p>
                    </div>
                </div>
                <button onclick="closeDictionaryDetail()" class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all">
                    <span class="material-icons-round">close</span>
                </button>
            </div>

            <div class="space-y-8">
                <div>
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span> Ringkasan
                    </h4>
                    <p id="dict-desc" class="text-slate-600 leading-relaxed font-medium"></p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-slate-50 p-6 rounded-3xl">
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-3 flex items-center gap-2">Penyebab</h4>
                        <p id="dict-causes" class="text-xs text-slate-500 leading-relaxed"></p>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-3xl">
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-3 flex items-center gap-2">Gejala</h4>
                        <p id="dict-symptoms" class="text-xs text-slate-500 leading-relaxed"></p>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span> Penanganan & Pengobatan
                    </h4>
                    <p id="dict-treatments" class="text-slate-600 leading-relaxed font-medium"></p>
                </div>

                <div class="bg-emerald-600 p-8 rounded-[2.5rem] text-white relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 text-white/10 text-8xl font-black">HALAL</div>
                    <h4 class="text-xs font-black uppercase tracking-widest mb-3 opacity-80 flex items-center gap-2">Tinjauan Titik Kritis Halal</h4>
                    <p id="dict-halal" class="text-sm font-bold leading-relaxed relative z-10"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Hilda Floating Button & Panel -->
<div id="ai-fab" onclick="toggleAI()" class="fixed bottom-8 right-8 z-[1500] group flex items-center gap-4 cursor-pointer">
    <div class="bg-white px-5 py-3 rounded-2xl shadow-xl border border-emerald-50 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0">
        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Tanya Hilda AI</p>
    </div>
    <div class="w-16 h-16 rounded-[1.5rem] bg-emerald-600 text-white flex items-center justify-center shadow-2xl shadow-emerald-900/40 hover:bg-emerald-700 transition-all transform hover:scale-110">
        <span class="material-icons-round text-3xl">auto_awesome</span>
    </div>
</div>

<div id="ai-panel" class="fixed bottom-8 right-8 z-[1600] w-[400px] h-[600px] bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 hidden flex-col overflow-hidden transform transition-all duration-300">
    <!-- Header -->
    <div class="p-6 bg-emerald-600 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center">
                <span class="material-icons-round">smart_toy</span>
            </div>
            <div>
                <h4 class="font-black text-sm">Hilda AI Assistant</h4>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                    <span class="text-[10px] font-bold opacity-80">Online & Siap Membantu</span>
                </div>
            </div>
        </div>
        <button onclick="toggleAI()" class="hover:bg-white/20 p-2 rounded-xl transition-all">
            <span class="material-icons-round">close</span>
        </button>
    </div>

    <!-- Chat Messages -->
    <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar bg-slate-50/30">
        <div class="flex justify-start mb-4">
            <div class="flex flex-col gap-1 max-w-[85%]">
                <span class="text-[10px] font-black text-slate-400 ml-1">HILDA AI</span>
                <div class="bg-white border border-emerald-50 text-slate-800 p-4 rounded-3xl rounded-tl-none text-sm shadow-sm">
                    Halo! Saya <b>AI Halalytics</b> 🤖 Asisten cerdas kesehatan, gizi, diet, obat, dan produk halal Anda. Silakan tanyakan apa saja!
                </div>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-2 pt-2">
            <button onclick="askChip('Beri saya tips diet sehat bergizi')" class="px-4 py-2 rounded-xl bg-white border border-emerald-100 text-emerald-600 text-[10px] font-black hover:bg-emerald-600 hover:text-white transition-all shadow-sm">Tips Diet Sehat</button>
            <button onclick="askChip('Apa saja gejala diabetes dan cara mencegahnya?')" class="px-4 py-2 rounded-xl bg-white border border-emerald-100 text-emerald-600 text-[10px] font-black hover:bg-emerald-600 hover:text-white transition-all shadow-sm">Gejala Diabetes</button>
            <button onclick="askChip('Bagaimana cara mengetahui produk kosmetik aman dan halal?')" class="px-4 py-2 rounded-xl bg-white border border-emerald-100 text-emerald-600 text-[10px] font-black hover:bg-emerald-600 hover:text-white transition-all shadow-sm">Skincare Halal</button>
        </div>
    </div>

    <!-- Input Area -->
    <div class="p-4 bg-white border-t border-slate-100">
        <div class="relative flex items-end gap-2 bg-slate-50 rounded-3xl p-2 border border-slate-200 focus-within:border-emerald-400 transition-all">
            <textarea id="chat-input" rows="1" placeholder="Tulis pertanyaan Anda..." class="flex-1 bg-transparent border-none focus:ring-0 text-sm font-medium p-3 resize-none max-h-32" onkeypress="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendAIMessage()}"></textarea>
            <button onclick="sendAIMessage()" class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center hover:bg-emerald-700 transition-all shadow-lg flex-shrink-0">
                <span class="material-icons-round">send</span>
            </button>
        </div>
    </div>
</div>

<!-- Screening Tool Modals -->
<div id="water-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-[3rem] p-8 shadow-2xl scale-95 transition-transform duration-300 overflow-hidden relative">
        <button onclick="closeWaterModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <span class="material-icons-round">close</span>
        </button>
        <h3 class="text-2xl font-black text-slate-900 mb-2 flex items-center gap-2">💧 Target Air Minum</h3>
        <p class="text-xs text-slate-500 font-medium mb-8">Hitung kebutuhan air harian Anda sesuai berat badan.</p>
        
        <div class="space-y-6">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Berat Badan Anda (kg)</label>
                <input type="number" id="water-weight" placeholder="Contoh: 60" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-emerald-500 transition-all">
            </div>
            <button onclick="calculateWater()" class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-black hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/20">
                Hitung Target
            </button>
            <div id="water-result" class="hidden p-6 rounded-3xl bg-blue-50 border border-blue-100 text-center animate-fade-in">
                <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">Target Harian Anda</p>
                <p id="water-score" class="text-3xl font-black text-blue-600 mb-2">2.1 Liter</p>
                <p class="text-[10px] text-blue-500 font-bold leading-relaxed mb-4">Setara dengan sekitar 8-9 gelas air per hari.</p>
                <button onclick="resetWater()" class="w-full py-2 rounded-xl border border-blue-200 text-blue-600 text-[10px] font-black uppercase tracking-widest hover:bg-blue-100 transition-all">
                    Hitung Ulang
                </button>
            </div>
        </div>
    </div>
</div>

<div id="hpl-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-[3rem] p-8 shadow-2xl scale-95 transition-transform duration-300 overflow-hidden relative">
        <button onclick="closeHPLModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <span class="material-icons-round">close</span>
        </button>
        <h3 class="text-2xl font-black text-slate-900 mb-2 flex items-center gap-2">🤰 Estimasi HPL</h3>
        <p class="text-xs text-slate-500 font-medium mb-8">Hitung Hari Perkiraan Lahir berdasarkan hari pertama haid terakhir.</p>
        
        <div class="space-y-6">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Hari Pertama Haid Terakhir (HPHT)</label>
                <input type="date" id="hpl-date" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold focus:ring-2 focus:ring-emerald-500 transition-all">
            </div>
            <button onclick="calculateHPL()" class="w-full bg-rose-500 text-white py-4 rounded-2xl font-black hover:bg-rose-600 transition-all shadow-lg shadow-rose-500/20">
                Hitung Estimasi
            </button>
            <div id="hpl-result" class="hidden p-6 rounded-3xl bg-rose-50 border border-rose-100 text-center animate-fade-in">
                <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-1">Estimasi Kelahiran</p>
                <p id="hpl-score" class="text-2xl font-black text-rose-600 mb-1">-</p>
                <p class="text-[10px] text-rose-400 font-bold leading-relaxed">Estimasi ini menggunakan aturan Naegele (280 hari).</p>
            </div>
        </div>
    </div>
</div>

<div id="eye-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white w-full max-w-lg rounded-[3rem] p-8 shadow-2xl scale-95 transition-transform duration-300 overflow-hidden relative">
        <button onclick="closeEyeModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <span class="material-icons-round">close</span>
        </button>
        <h3 class="text-2xl font-black text-slate-900 mb-2 flex items-center gap-2">👁️ Skrining Mata</h3>
        <p class="text-xs text-slate-500 font-medium mb-8">Posisikan layar 1 meter dari mata Anda dan coba baca huruf terkecil.</p>
        
        <div class="bg-slate-50 p-8 rounded-[2rem] flex flex-col items-center gap-6 select-none">
            <div class="text-[60px] font-black text-slate-900 leading-none">E</div>
            <div class="text-[40px] font-black text-slate-900 leading-none flex gap-4"><span>F</span> <span>P</span></div>
            <div class="text-[25px] font-black text-slate-900 leading-none flex gap-4"><span>T</span> <span>O</span> <span>Z</span></div>
            <div class="text-[15px] font-black text-slate-900 leading-none flex gap-4"><span>L</span> <span>P</span> <span>E</span> <span>D</span></div>
            <div class="text-[10px] font-black text-slate-700 leading-none flex gap-4 italic">Bisa baca baris ini? Penglihatan Anda sangat tajam!</div>
        </div>
        
        <div class="mt-8 p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
            <p class="text-[10px] text-emerald-700 font-bold leading-relaxed">
                <strong>Catatan:</strong> Ini adalah skrining dasar. Jika Anda merasa penglihatan kabur, segera konsultasikan ke dokter spesialis mata.
            </p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Initialize Swiper.js Partner Marquee
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.partner-swiper')) {
        new Swiper('.partner-swiper', {
            loop: true,
            autoplay: {
                delay: 0,
                disableOnInteraction: false,
            },
            speed: 5000,
            slidesPerView: 'auto',
            spaceBetween: 24,
            allowTouchMove: false,
        });
    }
});

// BMI Reset
function resetBMI() {
    document.getElementById('bmi-result').classList.add('hidden');
    document.getElementById('bmi-weight').value = '';
    document.getElementById('bmi-height').value = '';
    document.getElementById('bmi-weight').focus();
}

// Water Intake Logic
function openWaterModal() {
    document.getElementById('water-modal').style.display = 'flex';
}
function closeWaterModal() {
    document.getElementById('water-modal').style.display = 'none';
}
function resetWater() {
    document.getElementById('water-result').classList.add('hidden');
    document.getElementById('water-weight').value = '';
    document.getElementById('water-weight').focus();
}
function calculateWater() {
    const w = parseFloat(document.getElementById('water-weight').value);
    if (!w) return;
    const result = (w * 0.035).toFixed(1);
    document.getElementById('water-score').innerText = result + ' Liter';
    document.getElementById('water-result').classList.remove('hidden');
}

// HPL Logic
function openHPLModal() {
    document.getElementById('hpl-modal').style.display = 'flex';
}
function closeHPLModal() {
    document.getElementById('hpl-modal').style.display = 'none';
}
function calculateHPL() {
    const d = document.getElementById('hpl-date').value;
    if (!d) return;
    let date = new Date(d);
    date.setDate(date.getDate() + 7);
    date.setMonth(date.getMonth() - 3);
    date.setFullYear(date.getFullYear() + 1);
    
    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    document.getElementById('hpl-score').innerText = date.toLocaleDateString('id-ID', options);
    document.getElementById('hpl-result').classList.remove('hidden');
}

// Eye Logic
function openEyeModal() {
    document.getElementById('eye-modal').style.display = 'flex';
}
function closeEyeModal() {
    document.getElementById('eye-modal').style.display = 'none';
}

function calculateBMI() {
    const w = parseFloat(document.getElementById('bmi-weight').value);
    const h = parseFloat(document.getElementById('bmi-height').value);
    
    if (!w || !h) {
        alert('Mohon isi berat dan tinggi badan dengan benar.');
        return;
    }

    const hMeter = h / 100;
    const bmi = (w / (hMeter * hMeter)).toFixed(1);
    const res = document.getElementById('bmi-result');
    const score = document.getElementById('bmi-score');
    const status = document.getElementById('bmi-status');
    const advice = document.getElementById('bmi-advice');

    score.innerText = bmi;
    res.classList.remove('hidden');
    advice.innerHTML = '<div class="flex items-center gap-2 text-emerald-600 font-bold"><div class="animate-spin rounded-full h-4 w-4 border-2 border-emerald-600 border-t-transparent"></div> Menganalisis dengan AI...</div>';

    // Call AI for better advice
    fetch("{{ route('promo.bmi_ai_advice') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        body: JSON.stringify({ weight: w, height: h })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success && res.data) {
            const data = res.data;
            status.innerText = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            
            // Set color based on status
            const statusMap = {
                'underweight': 'text-amber-600',
                'normal': 'text-emerald-600',
                'overweight': 'text-orange-600',
                'obesitas': 'text-rose-600'
            };
            status.className = `text-xl font-black ${statusMap[data.status] || 'text-slate-900'}`;
            
            // Format recommendations as a nice list
            let adviceHtml = '<ul class="space-y-2 mt-2">';
            data.recommendations.forEach(rec => {
                adviceHtml += `<li class="flex items-start gap-2">
                    <span class="material-icons-round text-emerald-500 text-sm mt-0.5">check_circle</span>
                    <span>${rec}</span>
                </li>`;
            });
            adviceHtml += '</ul>';
            
            if (data.risks && data.risks.length > 0) {
                adviceHtml += '<div class="mt-4 p-3 bg-rose-50 rounded-2xl border border-rose-100">';
                adviceHtml += '<p class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-1">Risiko Kesehatan</p>';
                adviceHtml += `<p class="text-xs text-rose-700 font-medium">${data.risks[0]}</p>`;
                adviceHtml += '</div>';
            }
            
            advice.innerHTML = adviceHtml;
        } else {
            // Fallback to basic logic if AI fails
            setBasicBMIAdvice(bmi, status, advice);
        }
    })
    .catch(() => {
        setBasicBMIAdvice(bmi, status, advice);
    });
}

function setBasicBMIAdvice(bmi, status, advice) {
    if (bmi < 18.5) {
        status.innerText = 'Kekurangan Berat Badan';
        status.className = 'text-xl font-black text-amber-600';
        advice.innerText = 'Anda berada dalam kategori underweight. Disarankan untuk meningkatkan asupan nutrisi seimbang dan konsultasi dengan ahli gizi.';
    } else if (bmi < 25) {
        status.innerText = 'Normal (Ideal)';
        status.className = 'text-xl font-black text-emerald-600';
        advice.innerText = 'Bagus! Berat badan Anda ideal. Pertahankan pola makan sehat dan rutin berolahraga ya.';
    } else if (bmi < 30) {
        status.innerText = 'Kelebihan Berat Badan';
        status.className = 'text-xl font-black text-orange-600';
        advice.innerText = 'Anda masuk kategori overweight. Mulailah kurangi konsumsi gula berlebih dan tingkatkan aktivitas fisik harian.';
    } else {
        status.innerText = 'Obesitas';
        status.className = 'text-xl font-black text-rose-600';
        advice.innerText = 'Peringatan: Kategori obesitas memiliki risiko kesehatan tinggi. Sangat disarankan untuk memulai program diet sehat dan olahraga teratur.';
    }
}

// Server Data Passing
</script>
<script id="server-data" type="application/json">
    @json([
        'diseases' => $diseases ?? [],
        'stats' => $stats ?? []
    ])
</script>
<script>
const serverData = JSON.parse(document.getElementById('server-data').textContent);

// Health Dictionary Interactive Controls
function filterAlphabet(letter) {
    const letterValue = letter.trim();
    document.querySelectorAll('.char-btn').forEach(btn => {
        if (btn.innerText.trim() === letterValue) btn.classList.add('active', 'bg-emerald-600', 'border-emerald-600', 'text-white');
        else btn.classList.remove('active', 'bg-emerald-600', 'border-emerald-600', 'text-white');
    });

    const grid = document.getElementById('dictionary-grid');
    grid.innerHTML = '<div class="col-span-full py-12 text-center"><div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-emerald-600 border-t-transparent"></div></div>';

    fetch(`/api/health-encyclopedia?alphabet=${letterValue}`)
        .then(r => r.json())
        .then(res => {
            grid.innerHTML = '';
            if (res.success && res.data && res.data.length > 0) {
                res.data.forEach(d => {
                    const card = document.createElement('div');
                    card.className = 'bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-emerald-300 hover:shadow-md transition-all cursor-pointer group';
                    card.onclick = function() { openDictionaryDetail(d); };
                    card.innerHTML = `
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-black text-gray-900 group-hover:text-emerald-700 transition-colors">${d.title || d.name}</h4>
                            <span class="text-xs font-extrabold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">${d.alphabet || letterValue}</span>
                        </div>
                        <p class="text-sm text-gray-500 leading-relaxed line-clamp-2">${d.summary || d.desc || ''}</p>
                        <div class="mt-4 flex items-center text-xs font-extrabold text-emerald-600 gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            Lihat Selengkapnya <span>&rarr;</span>
                        </div>
                    `;
                    grid.appendChild(card);
                });
            } else {
                grid.innerHTML = `<div class="col-span-full py-20 text-center"><p class="text-slate-400 font-black">Data untuk huruf ${letterValue} belum tersedia.</p></div>`;
            }
        })
        .catch(err => {
            grid.innerHTML = '<div class="col-span-full py-20 text-center"><p class="text-rose-500 font-black">Gagal memuat data. Silakan coba lagi.</p></div>';
        });
}

function openDictionaryDetail(data) {
    document.getElementById('dict-title').innerText = data.title || data.name || 'Detail';
    document.getElementById('dict-desc').innerText = data.summary || data.desc || 'Ringkasan tidak tersedia';
    document.getElementById('dict-causes').innerText = data.causes || 'Data tidak tersedia';
    document.getElementById('dict-symptoms').innerText = data.symptoms || 'Data tidak tersedia';
    document.getElementById('dict-treatments').innerText = data.treatments || 'Data tidak tersedia';
    document.getElementById('dict-halal').innerText = data.halal_notes || data.halal || 'Data tidak tersedia';

    const modal = document.getElementById('dict-modal');
    modal.style.display = 'flex';
    setTimeout(() => { 
        const card = document.getElementById('dict-modal-card');
        if (card) card.style.transform = 'scale(1)'; 
    }, 50);
}

function closeDictionaryDetail() {
    const card = document.getElementById('dict-modal-card');
    if (card) card.style.transform = 'scale(0.95)';
    setTimeout(() => { document.getElementById('dict-modal').style.display = 'none'; }, 150);
}

// AI Hilda Chat System
function toggleAI() {
    const p = document.getElementById('ai-panel');
    const f = document.getElementById('ai-fab');
    if (p.classList.contains('hidden')) {
        p.classList.remove('hidden');
        p.classList.add('flex');
        f.classList.add('hidden');
        document.getElementById('chat-input').focus();
    } else {
        p.classList.add('hidden');
        p.classList.remove('flex');
        f.classList.remove('hidden');
    }
}

function askChip(t) {
    document.getElementById('chat-input').value = t;
    sendAIMessage();
}

function addAIMessage(text) {
    const m = document.getElementById('chat-messages');
    const d = document.createElement('div');
    d.className = 'flex justify-start mb-4';
    
    // Parse Markdown from Gemini if marked is loaded
    const htmlContent = typeof marked !== 'undefined' ? marked.parse(text) : text.replace(/\n/g, '<br>');
    
    d.innerHTML = `
        <div class="flex flex-col gap-1 max-w-[85%]">
            <span class="text-[10px] font-black text-slate-400 ml-1">HILDA AI</span>
            <div class="bg-white border border-emerald-50 text-slate-800 p-4 rounded-3xl rounded-tl-none text-sm shadow-sm prose prose-sm prose-emerald">
                ${htmlContent}
            </div>
        </div>
    `;
    m.appendChild(d);
    m.scrollTop = m.scrollHeight;
}

function addUserMessage(text) {
    const m = document.getElementById('chat-messages');
    const d = document.createElement('div');
    d.className = 'flex justify-end mb-4';
    d.innerHTML = `
        <div class="bg-emerald-600 text-white p-4 rounded-3xl rounded-tr-none max-w-[85%] text-sm shadow-md font-medium">
            ${text}
        </div>
    `;
    m.appendChild(d);
    m.scrollTop = m.scrollHeight;
}

function sendAIMessage() {
    const i = document.getElementById('chat-input');
    const t = i.value.trim();
    if (!t) return;

    addUserMessage(t);
    i.value = '';
    i.style.height = 'auto';

    // Show typing indicator
    const m = document.getElementById('chat-messages');
    const l = document.createElement('div');
    l.id = 'ai-loading';
    l.className = 'flex justify-start mb-4';
    l.innerHTML = `
        <div class="bg-slate-50 p-4 rounded-2xl flex gap-1">
            <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-bounce"></div>
            <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-bounce" style="animation-delay:0.2s"></div>
            <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-bounce" style="animation-delay:0.4s"></div>
        </div>
    `;
    m.appendChild(l);
    m.scrollTop = m.scrollHeight;

    fetch("{{ route('promo.ai_chat') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: t })
    })
    .then(r => r.json())
    .then(res => {
        const loader = document.getElementById('ai-loading');
        if (loader) loader.remove();
        if (res.success) {
            addAIMessage(res.reply);
        } else {
            addAIMessage("Maaf, Hilda sedang mengalami gangguan teknis. Silakan coba lagi nanti.");
        }
    })
    .catch(() => {
        const loader = document.getElementById('ai-loading');
        if (loader) loader.remove();
        addAIMessage("Koneksi terputus. Pastikan internet Anda stabil ya!");
    });
}

// Auto-resize textarea
document.getElementById('chat-input')?.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

// Close modal if clicking outside the card
document.getElementById('dict-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDictionaryDetail();
    }
});

// Close screening modals on click outside
[document.getElementById('water-modal'), document.getElementById('hpl-modal'), document.getElementById('eye-modal')].forEach(modal => {
    modal?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});
</script>

<style>
/* Custom Scrollbar for Chat & Modal */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
}
.animate-float { animation: float 6s ease-in-out infinite; }
.animate-float-delayed { animation: float 6s ease-in-out 3s infinite; }

.char-btn.active {
    background-color: #059669;
    border-color: #059669;
    color: white;
}
</style>
@endsection