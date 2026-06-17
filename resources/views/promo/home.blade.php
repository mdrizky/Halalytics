@extends('promo.layout')

@section('title', 'Halalytics - Solusi Kesehatan Dengan Kepastian Halal')

@section('content')
{{-- ==================== HERO ==================== --}}
<section class="relative min-h-screen flex items-center pt-20 overflow-hidden bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-300/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-teal-300/20 rounded-full blur-3xl animate-pulse" style="animation-delay:2s"></div>
        <div class="absolute top-1/3 left-1/4 w-6 h-6 bg-emerald-400 rounded-full animate-bounce" style="animation-duration:3s"></div>
        <div class="absolute top-1/2 right-1/4 w-4 h-4 bg-teal-400 rounded-full animate-bounce" style="animation-duration:4s;animation-delay:1s"></div>
        <div class="absolute bottom-1/3 left-1/3 w-3 h-3 bg-cyan-400 rounded-full animate-bounce" style="animation-duration:3.5s;animation-delay:.5s"></div>
        <div class="absolute top-1/4 right-1/3 w-5 h-5 bg-amber-400 rounded-full animate-bounce" style="animation-duration:4.5s;animation-delay:1.5s"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8">
                <div class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-black tracking-widest uppercase shadow-xl shadow-emerald-900/20 animate-bounce">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                    </span>
                    Halalytics AI v4.0 — Ekosistem Halal & Kesehatan Terlengkap
                </div>

                <h1 class="text-5xl lg:text-7xl font-black leading-[1.1] tracking-tight">
                    <span class="text-slate-900">Skrining Kesehatan</span><br>
                    <span class="bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-500 bg-clip-text text-transparent">Dengan Kepastian</span>
                    <span class="bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500 bg-clip-text text-transparent">Halal.</span>
                </h1>

                <p class="text-base lg:text-lg text-slate-600 font-medium max-w-xl leading-relaxed">
                    Asisten pintar kesehatan terintegrasi pertama yang menghubungkan kecerdasan buatan AI dengan basis data obat-obatan BPOM serta sertifikasi halal resmi. Cepat, akurat, dan aman.
                </p>

                <div class="flex flex-wrap gap-4 items-center">
                    <a href="{{ route('download') }}" class="group bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white px-9 py-4 rounded-2xl font-black shadow-2xl shadow-emerald-900/30 transition-all duration-300 hover:-translate-y-1 hover:scale-105 flex items-center gap-3">
                        Download Aplikasi
                        <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="#health-check" class="bg-white/80 backdrop-blur-sm border-2 border-slate-200 hover:border-emerald-300 hover:text-emerald-700 text-slate-700 px-9 py-4 rounded-2xl font-black transition-all hover:shadow-xl flex items-center gap-2 group">
                        <span class="group-hover:rotate-12 transition-transform inline-block">⚕️</span> Kalkulator Medis
                    </a>
                </div>

                <div class="bg-white/70 backdrop-blur-sm border border-emerald-100 p-6 rounded-3xl max-w-xl shadow-lg shadow-emerald-900/5">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-amber-900/20">📱</div>
                        <div>
                            <h4 class="font-black text-slate-900 text-lg">Ekosistem Halal & Kesehatan Terlengkap</h4>
                            <p class="text-sm text-slate-500 mt-1">Scan barcode, cek kehalalan & nutrisi, interaksi obat, konsultasi AI (HILDA), donor darah, kesehatan keluarga — satu aplikasi gratis.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative hidden lg:block">
                <div class="relative z-10 animate-float flex justify-center">
                    <div class="relative w-[320px] h-[640px] bg-gradient-to-b from-emerald-900 to-slate-900 rounded-[3rem] border-[8px] border-emerald-800 shadow-2xl shadow-emerald-900/30 overflow-hidden">
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-slate-800 rounded-b-2xl z-20"></div>
                        <img src="{{ asset('images/promo/ss-home-1.png') }}" alt="Halalytics App" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-emerald-900/40 to-transparent"></div>
                    </div>
                </div>

                <div class="absolute top-10 -left-10 bg-white p-5 rounded-3xl shadow-2xl border border-emerald-50 w-64 z-20 animate-float" style="animation-delay:0.5s">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white shadow-lg">
                            <span class="material-icons-round text-lg">auto_awesome</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase">AI Halalytics (HILDA)</p>
                            <p class="text-xs font-extrabold text-slate-800">"Apakah kandungan gelatin babi ada pada obat A?"</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 p-3 rounded-2xl border border-emerald-100">
                        <p class="text-[10px] leading-relaxed text-slate-600 italic">"Hasil penelusuran: Obat A terdaftar BPOM dan menggunakan gelatin sapi bersertifikat halal."</p>
                    </div>
                </div>

                <div class="absolute -bottom-4 right-0 bg-white p-5 rounded-3xl shadow-2xl border border-amber-50 w-60 z-20">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Komunitas Halalytics</p>
                    <div class="flex items-center gap-3">
                        <span class="relative flex h-3.5 w-3.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500"></span>
                        </span>
                        <p class="font-extrabold text-sm text-slate-800">{{ number_format($stats['total_users'] ?? 0) }} Pengguna Aktif</p>
                    </div>
                    <div class="mt-2 flex -space-x-2">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400 to-teal-400 border-2 border-white"></div>
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-amber-400 to-orange-400 border-2 border-white"></div>
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-cyan-400 to-blue-400 border-2 border-white"></div>
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-rose-400 to-pink-400 border-2 border-white"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== PARTNER MARQUEE ==================== --}}
<section class="py-14 bg-white overflow-hidden relative">
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-50/50 via-transparent to-teal-50/50"></div>
    <div class="container mx-auto px-4 relative">
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
                    <img src="{{ $p['logo'] }}" alt="{{ $p['name'] }}" class="h-10 md:h-14 w-auto grayscale opacity-30 hover:grayscale-0 hover:opacity-100 transition-all duration-500 object-contain hover:scale-110">
                </div>
                @endforeach
                @foreach($partners as $p)
                <div class="swiper-slide !w-auto px-8">
                    <img src="{{ $p['logo'] }}" alt="{{ $p['name'] }}" class="h-10 md:h-14 w-auto grayscale opacity-30 hover:grayscale-0 hover:opacity-100 transition-all duration-500 object-contain hover:scale-110">
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ==================== STATS BANNER ==================== --}}
<section class="py-16 bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
            <div class="p-6 bg-white/10 backdrop-blur-sm rounded-3xl border border-white/20">
                <div class="text-4xl mb-2">📱</div>
                <div class="text-3xl font-black counter-value" data-target="{{ number_format($stats['total_users'] ?? 12800) }}">{{ number_format($stats['total_users'] ?? 12800) }}</div>
                <p class="text-xs font-bold text-white/70 uppercase tracking-widest mt-1">Pengguna Aktif</p>
            </div>
            <div class="p-6 bg-white/10 backdrop-blur-sm rounded-3xl border border-white/20">
                <div class="text-4xl mb-2">🔬</div>
                <div class="text-3xl font-black">250K+</div>
                <p class="text-xs font-bold text-white/70 uppercase tracking-widest mt-1">Produk Terscan</p>
            </div>
            <div class="p-6 bg-white/10 backdrop-blur-sm rounded-3xl border border-white/20">
                <div class="text-4xl mb-2">🤖</div>
                <div class="text-3xl font-black">100K+</div>
                <p class="text-xs font-bold text-white/70 uppercase tracking-widest mt-1">Konsultasi AI</p>
            </div>
            <div class="p-6 bg-white/10 backdrop-blur-sm rounded-3xl border border-white/20">
                <div class="text-4xl mb-2">🏆</div>
                <div class="text-3xl font-black">98%</div>
                <p class="text-xs font-bold text-white/70 uppercase tracking-widest mt-1">Kepuasan User</p>
            </div>
        </div>
    </div>
</section>

{{-- ==================== FEATURED SERVICES ==================== --}}
<section class="py-24 bg-gradient-to-b from-white to-emerald-50/30 relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-emerald-100/30 to-transparent pointer-events-none"></div>
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700 text-xs font-black tracking-widest uppercase mb-4 border border-emerald-200">
                ✨ Layanan Unggulan
            </div>
            <h2 class="text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">Super App Kesehatan & Halal</h2>
            <p class="text-slate-500 mt-4 font-medium text-lg">Ekosistem lengkap untuk mendukung gaya hidup halal dan sehat keluarga Anda setiap hari.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
            $mainServices = [
                ['icon' => '🔍', 'name' => 'Scanner Cerdas', 'desc' => 'Scan barcode produk untuk cek status halal & nutrisi instan.', 'gradient' => 'from-emerald-500 to-teal-500', 'shadow' => 'shadow-emerald-500/20', 'border' => 'hover:border-emerald-300'],
                ['icon' => '🤖', 'name' => 'HILDA AI Assistant', 'desc' => 'Tanya apa saja seputar kesehatan, gizi, obat, dan produk halal.', 'gradient' => 'from-teal-500 to-cyan-500', 'shadow' => 'shadow-teal-500/20', 'border' => 'hover:border-teal-300'],
                ['icon' => '💊', 'name' => 'Drug Interaction Checker', 'desc' => 'Deteksi potensi konflik antar obat dengan kategori risiko.', 'gradient' => 'from-rose-500 to-pink-500', 'shadow' => 'shadow-rose-500/20', 'border' => 'hover:border-rose-300'],
                ['icon' => '🧴', 'name' => 'Skincare Analyzer', 'desc' => 'Analisis keamanan & kehalalan produk kosmetik dan skincare.', 'gradient' => 'from-amber-500 to-orange-500', 'shadow' => 'shadow-amber-500/20', 'border' => 'hover:border-amber-300'],
                ['icon' => '🥗', 'name' => 'AI Nutrition Tracker', 'desc' => 'Pantau asupan gula, kalori, dan nutrisi harian.', 'gradient' => 'from-lime-500 to-green-500', 'shadow' => 'shadow-lime-500/20', 'border' => 'hover:border-lime-300'],
                ['icon' => '🩸', 'name' => 'Blood Donation', 'desc' => 'Cek stok darah, daftar donor, dan pantau jadwal donor.', 'gradient' => 'from-red-500 to-rose-500', 'shadow' => 'shadow-red-500/20', 'border' => 'hover:border-red-300'],
                ['icon' => '🩺', 'name' => 'Health Encyclopedia', 'desc' => 'Ensiklopedia penyakit & obat dengan tinjauan halal.', 'gradient' => 'from-cyan-500 to-blue-500', 'shadow' => 'shadow-cyan-500/20', 'border' => 'hover:border-cyan-300'],
                ['icon' => '👨‍👩‍👧‍👦', 'name' => 'Family Health', 'desc' => 'Pantau profil kesehatan seluruh anggota keluarga.', 'gradient' => 'from-teal-500 to-emerald-500', 'shadow' => 'shadow-teal-500/20', 'border' => 'hover:border-teal-300'],
            ];
            @endphp

            @foreach($mainServices as $service)
            <a href="{{ route('download') }}"
               class="group relative bg-white p-8 rounded-[2.5rem] transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl {{ $service['border'] }} border border-transparent {{ $service['shadow'] }} overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br {{ $service['gradient'] }} opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                <div class="relative">
                    <div class="text-4xl mb-6 transform transition-all duration-500 group-hover:scale-125 group-hover:rotate-6 inline-block">{{ $service['icon'] }}</div>
                    <h4 class="text-lg font-black text-slate-900 mb-2 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r {{ $service['gradient'] }} transition-all">{{ $service['name'] }}</h4>
                    <p class="text-xs text-slate-600 leading-relaxed font-medium">{{ $service['desc'] }}</p>
                    <div class="mt-4 flex items-center gap-1 text-xs font-black text-slate-400 group-hover:opacity-100 opacity-0 transition-all">
                        Pelajari <span class="transition-transform group-hover:translate-x-1">&rarr;</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ==================== HEALTH TOOLS ==================== --}}
<section class="py-24 bg-white relative overflow-hidden" id="health-check">
    <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-100/50 rounded-full blur-3xl -z-10"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-teal-100/50 rounded-full blur-3xl -z-10"></div>

    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-gradient-to-r from-amber-100 to-orange-100 text-amber-700 text-[10px] font-black tracking-widest uppercase mb-6 border border-amber-200">
                    🔥 Self-Screening Tools
                </div>
                <h2 class="text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight mb-6">
                    Pantau Kesehatan Anda <br>
                    <span class="bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500 bg-clip-text text-transparent">Secara Mandiri & Presisi.</span>
                </h2>
                <p class="text-slate-500 font-medium leading-relaxed mb-8 text-lg">
                    Gunakan kalkulator medis berbasis standar internasional untuk mengetahui profil kesehatan Anda. Data Anda aman dan diproses secara privat oleh sistem AI kami.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-6 rounded-3xl border-2 border-emerald-100 bg-gradient-to-br from-emerald-50 to-teal-50 hover:border-emerald-300 transition-all group cursor-default">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-lg shadow-emerald-900/10 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform group-hover:-rotate-6">⚖️</div>
                        <h4 class="font-black text-slate-900 mb-1">Kalkulator BMI</h4>
                        <p class="text-xs text-slate-500">Cek indeks massa tubuh dan berat badan ideal Anda.</p>
                    </div>
                    <div onclick="openWaterModal()" class="p-6 rounded-3xl border-2 border-sky-100 bg-gradient-to-br from-sky-50 to-blue-50 hover:border-sky-300 transition-all group cursor-pointer">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-lg shadow-sky-900/10 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform group-hover:-rotate-6">💧</div>
                        <h4 class="font-black text-slate-900 mb-1">Target Air Minum</h4>
                        <p class="text-xs text-slate-500">Hitung kebutuhan hidrasi harian berdasarkan berat badan.</p>
                    </div>
                    <div onclick="openHPLModal()" class="p-6 rounded-3xl border-2 border-rose-100 bg-gradient-to-br from-rose-50 to-pink-50 hover:border-rose-300 transition-all group cursor-pointer">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-lg shadow-rose-900/10 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform group-hover:-rotate-6">🤰</div>
                        <h4 class="font-black text-slate-900 mb-1">Estimasi HPL</h4>
                        <p class="text-xs text-slate-500">Hitung hari perkiraan lahir untuk ibu hamil.</p>
                    </div>
                    <div onclick="openEyeModal()" class="p-6 rounded-3xl border-2 border-emerald-100 bg-gradient-to-br from-emerald-50 to-teal-50 hover:border-emerald-300 transition-all group cursor-pointer">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-lg shadow-emerald-900/10 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform group-hover:-rotate-6">👁️</div>
                        <h4 class="font-black text-slate-900 mb-1">Skrining Mata</h4>
                        <p class="text-xs text-slate-500">Tes ketajaman mata sederhana (Snellen Chart).</p>
                    </div>
                </div>

                <div class="mt-10 p-6 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-[2.5rem] text-white flex items-center justify-between gap-6 shadow-2xl shadow-emerald-900/30">
                    <div>
                        <p class="text-xs font-black opacity-80 uppercase tracking-widest mb-1">Ingin Hasil Lebih Detail?</p>
                        <p class="text-sm font-bold">Download aplikasi untuk analisis AI lengkap!</p>
                    </div>
                    <a href="{{ route('download') }}" class="px-6 py-3 bg-white text-emerald-600 rounded-2xl font-black text-xs hover:bg-emerald-50 transition-all whitespace-nowrap hover:scale-105">
                        Download Now 🚀
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -inset-4 bg-gradient-to-br from-emerald-200/50 via-teal-200/50 to-amber-200/50 rounded-[3rem] blur-3xl -z-10"></div>
                <div class="bg-white rounded-[2.5rem] p-8 md:p-12 border-2 border-emerald-100 shadow-2xl shadow-emerald-900/10 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                        <span class="text-8xl font-black bg-gradient-to-br from-emerald-600 to-teal-600 bg-clip-text text-transparent">BMI</span>
                    </div>

                    <div id="bmi-calculator" class="relative z-10">
                        <h3 class="text-2xl font-black text-slate-900 mb-8 flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 text-white flex items-center justify-center shadow-lg">
                                <span class="material-icons-round text-xl">monitor_weight</span>
                            </span>
                            BMI Quick Check ⚡
                        </h3>

                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Berat Badan (kg)</label>
                                    <input type="number" id="bmi-weight" placeholder="Contoh: 65" class="w-full bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-100 rounded-2xl p-4 text-sm font-bold focus:ring-0 focus:border-emerald-400 transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Tinggi Badan (cm)</label>
                                    <input type="number" id="bmi-height" placeholder="Contoh: 170" class="w-full bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-100 rounded-2xl p-4 text-sm font-bold focus:ring-0 focus:border-emerald-400 transition-all">
                                </div>
                            </div>

                            <button onclick="calculateBMI()" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-5 rounded-2xl font-black hover:from-emerald-500 hover:to-teal-500 transition-all shadow-2xl shadow-emerald-900/30 flex items-center justify-center gap-3 hover:scale-[1.02] active:scale-95">
                                Hitung Skor BMI ⚡
                            </button>

                            <div id="bmi-result" class="hidden animate-fade-in">
                                <div class="p-6 rounded-3xl bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-100">
                                    <div class="flex justify-between items-end mb-4">
                                        <div>
                                            <div class="flex items-center gap-1.5 mb-1">
                                                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Hasil Analisis</p>
                                                <span class="px-1.5 py-0.5 rounded-md bg-gradient-to-r from-emerald-600 to-teal-600 text-[8px] font-black text-white flex items-center gap-0.5">
                                                    <span class="material-icons-round text-[10px]">auto_awesome</span>
                                                    AI POWERED
                                                </span>
                                            </div>
                                            <h4 id="bmi-status" class="text-xl font-black text-slate-900">Normal</h4>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Skor BMI</p>
                                            <p id="bmi-score" class="text-3xl font-black bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">22.4</p>
                                        </div>
                                    </div>
                                    <div id="bmi-advice" class="text-xs text-slate-600 leading-relaxed font-medium"></div>
                                    <button onclick="resetBMI()" class="mt-6 w-full py-3 rounded-xl border-2 border-emerald-200 text-emerald-600 text-[10px] font-black uppercase tracking-widest hover:bg-emerald-100 transition-all">
                                        ↻ Cek Ulang Lagi
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

{{-- ==================== HEALTH DICTIONARY ==================== --}}
<section class="py-24 bg-gradient-to-b from-slate-50 to-white relative overflow-hidden" id="dictionary">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-2 h-2 bg-emerald-400 rounded-full animate-ping"></div>
        <div class="absolute top-40 right-20 w-3 h-3 bg-teal-400 rounded-full animate-ping" style="animation-delay:1s"></div>
        <div class="absolute bottom-40 left-1/4 w-2 h-2 bg-amber-400 rounded-full animate-ping" style="animation-delay:2s"></div>
    </div>

    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row justify-between items-end mb-16 gap-8">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-cyan-100 to-blue-100 text-cyan-700 text-[10px] font-black tracking-widest uppercase mb-4 border border-cyan-200">
                    📖 Kamus Kesehatan
                </div>
                <h2 class="text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">Ensiklopedia A-Z</h2>
                <p class="text-slate-500 mt-4 font-medium leading-relaxed text-lg">
                    Jelajahi penyakit, obat-obatan, dan istilah medis dari perspektif kesehatan umum serta tinjauan titik kritis kehalalan.
                </p>
            </div>

            <div class="flex flex-wrap gap-2 justify-center lg:justify-end max-w-xl">
                @foreach(range('A', 'Z') as $char)
                <button onclick="filterAlphabet('{{ $char }}')"
                        class="char-btn w-10 h-10 rounded-xl bg-white border-2 border-slate-100 flex items-center justify-center text-xs font-black text-slate-400 hover:border-emerald-300 hover:text-emerald-600 hover:bg-emerald-50 transition-all hover:scale-110">
                    {{ $char }}
                </button>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="dictionary-grid">
            @forelse($diseases ?? [] as $d)
            <div class="bg-white p-6 rounded-3xl border-2 border-slate-100 shadow-sm hover:shadow-2xl hover:shadow-emerald-900/10 hover:-translate-y-1 transition-all cursor-pointer group"
                 data-disease="{{ json_encode($d) }}"
                 onclick="openDictionaryDetail(JSON.parse(this.getAttribute('data-disease')))">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-black text-gray-900 group-hover:text-emerald-700 transition-colors">{{ $d->title ?? ($d['title'] ?? 'Penyakit') }}</h4>
                    <span class="text-xs font-extrabold text-white bg-gradient-to-r from-emerald-500 to-teal-500 px-2.5 py-1 rounded-lg shadow-sm">{{ $d->alphabet ?? ($d['alphabet'] ?? 'A') }}</span>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed line-clamp-2">{{ $d->summary ?? ($d['summary'] ?? '') }}</p>
                <div class="mt-4 flex items-center text-xs font-extrabold text-emerald-600 gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    Lihat Selengkapnya <span>&rarr;</span>
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 text-center bg-white rounded-3xl border-2 border-dashed border-slate-200">
                <p class="text-slate-400 font-black">Data ensiklopedia belum tersedia.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ==================== LATEST BLOGS ==================== --}}
<section class="py-24 bg-white relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 via-teal-500 to-amber-500"></div>
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700 text-[10px] font-black tracking-widest uppercase mb-4 border border-emerald-200">
                    📚 Edukasi Halal & Sehat
                </div>
                <h2 class="text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">Wawasan Terkini</h2>
                <p class="text-slate-500 mt-2 font-medium text-lg">Informasi terkini dari pakar nutrisi dan kesehatan Halalytics.</p>
            </div>
            <a href="{{ route('blog.index') }}" class="group bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white px-6 py-3 rounded-2xl font-black flex items-center gap-2 transition-all shadow-xl shadow-emerald-900/20 hover:scale-105">
                Semua Artikel
                <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($latestBlogs as $blog)
            <div class="group bg-white rounded-[2.5rem] overflow-hidden border-2 border-slate-50 shadow-sm hover:shadow-2xl hover:shadow-emerald-900/10 transition-all duration-500 hover:-translate-y-2">
                <div class="aspect-[16/10] overflow-hidden relative bg-gradient-to-br from-emerald-100 to-teal-100">
                    <img src="{{ $blog->image_url }}"
                         alt="{{ $blog->title }}"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($blog->title ?? 'Halalytics') }}&background=059669&color=fff&size=400'">
                    <div class="absolute top-4 left-4">
                        <span class="bg-white/90 backdrop-blur-md text-emerald-600 text-[10px] font-black px-3 py-1.5 rounded-full shadow-sm border border-emerald-100">
                            {{ $blog->category ?? 'Edukasi' }}
                        </span>
                    </div>
                </div>
                <div class="p-8">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">{{ $blog->formatted_date }}</p>
                    <h3 class="text-xl font-black text-slate-900 group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-emerald-600 group-hover:to-teal-600 transition-all leading-tight mb-4">
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

{{-- ==================== AI HILDA FLOATING PANEL ==================== --}}
<div id="ai-fab" onclick="toggleAI()" class="fixed bottom-8 right-8 z-[1500] group flex items-center gap-4 cursor-pointer">
    <div class="bg-white/95 backdrop-blur-md px-5 py-3 rounded-2xl shadow-2xl border border-emerald-50 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0">
        <p class="text-[10px] font-black bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent uppercase tracking-widest">Tanya Hilda AI</p>
    </div>
    <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br from-emerald-600 to-teal-600 text-white flex items-center justify-center shadow-2xl shadow-emerald-900/40 hover:shadow-teal-900/40 transition-all hover:scale-110 hover:rotate-6">
        <span class="material-icons-round text-3xl">auto_awesome</span>
    </div>
</div>

<div id="ai-panel" class="fixed bottom-8 right-8 z-[1600] w-[400px] h-[600px] bg-white rounded-[2.5rem] shadow-2xl border border-emerald-100 hidden flex-col overflow-hidden transition-all duration-300">
    <div class="p-6 bg-gradient-to-r from-emerald-600 to-teal-600 text-white flex items-center justify-between">
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

    <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar bg-gradient-to-b from-emerald-50/20 to-white">
        <div class="flex justify-start mb-4">
            <div class="flex flex-col gap-1 max-w-[85%]">
                <span class="text-[10px] font-black text-emerald-600 ml-1">HILDA AI</span>
                <div class="bg-white border-2 border-emerald-50 text-slate-800 p-4 rounded-3xl rounded-tl-none text-sm shadow-md">
                    Halo! Saya <b>AI Halalytics</b> 🤖 Asisten cerdas kesehatan, gizi, diet, obat, dan produk halal Anda. Silakan tanyakan apa saja!
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 pt-2">
            <button onclick="askChip('Beri saya tips diet sehat bergizi')" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 text-emerald-600 text-[10px] font-black hover:from-emerald-600 hover:to-teal-600 hover:text-white transition-all shadow-sm hover:shadow-lg">🍎 Tips Diet Sehat</button>
            <button onclick="askChip('Apa saja gejala diabetes dan cara mencegahnya?')" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 text-emerald-600 text-[10px] font-black hover:from-emerald-600 hover:to-teal-600 hover:text-white transition-all shadow-sm hover:shadow-lg">🩺 Gejala Diabetes</button>
            <button onclick="askChip('Bagaimana cara mengetahui produk kosmetik aman dan halal?')" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 text-emerald-600 text-[10px] font-black hover:from-emerald-600 hover:to-teal-600 hover:text-white transition-all shadow-sm hover:shadow-lg">🧴 Skincare Halal</button>
        </div>
    </div>

    <div class="p-4 bg-white border-t border-emerald-100">
        <div class="relative flex items-end gap-2 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-3xl p-2 border-2 border-emerald-100 focus-within:border-emerald-400 transition-all">
            <textarea id="chat-input" rows="1" placeholder="Tulis pertanyaan Anda..." class="flex-1 bg-transparent border-none focus:ring-0 text-sm font-medium p-3 resize-none max-h-32" onkeypress="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendAIMessage()}"></textarea>
            <button onclick="sendAIMessage()" class="w-10 h-10 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white flex items-center justify-center hover:from-emerald-500 hover:to-teal-500 transition-all shadow-lg flex-shrink-0 hover:scale-105">
                <span class="material-icons-round">send</span>
            </button>
        </div>
    </div>
</div>

{{-- ==================== DICTIONARY DETAIL MODAL ==================== --}}
<div id="dict-modal" class="fixed inset-0 z-[2000] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-300">
    <div id="dict-modal-card" class="bg-white w-full max-w-3xl rounded-[3rem] shadow-2xl overflow-hidden transform scale-90 transition-all duration-300 border-2 border-emerald-100">
        <div class="p-8 md:p-12 max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="flex justify-between items-start mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center text-3xl">🩺</div>
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
                        <span class="w-1.5 h-6 bg-gradient-to-b from-emerald-500 to-teal-500 rounded-full"></span> Ringkasan
                    </h4>
                    <p id="dict-desc" class="text-slate-600 leading-relaxed font-medium"></p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-gradient-to-br from-rose-50 to-pink-50 p-6 rounded-3xl border border-rose-100">
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-3 flex items-center gap-2">⚠️ Penyebab</h4>
                        <p id="dict-causes" class="text-xs text-slate-500 leading-relaxed"></p>
                    </div>
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-6 rounded-3xl border border-amber-100">
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-3 flex items-center gap-2">🔥 Gejala</h4>
                        <p id="dict-symptoms" class="text-xs text-slate-500 leading-relaxed"></p>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-gradient-to-b from-emerald-500 to-teal-500 rounded-full"></span> Penanganan & Pengobatan
                    </h4>
                    <p id="dict-treatments" class="text-slate-600 leading-relaxed font-medium"></p>
                </div>

                <div class="bg-gradient-to-br from-emerald-600 to-teal-600 p-8 rounded-[2.5rem] text-white relative overflow-hidden shadow-2xl shadow-emerald-900/30">
                    <div class="absolute -right-4 -bottom-4 text-white/10 text-8xl font-black">HALAL</div>
                    <h4 class="text-xs font-black uppercase tracking-widest mb-3 opacity-80 flex items-center gap-2">🕌 Tinjauan Titik Kritis Halal</h4>
                    <p id="dict-halal" class="text-sm font-bold leading-relaxed relative z-10"></p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- WATER MODAL --}}
<div id="water-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-[3rem] p-8 shadow-2xl scale-95 transition-transform duration-300 overflow-hidden relative border-2 border-sky-100">
        <button onclick="closeWaterModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <span class="material-icons-round">close</span>
        </button>
        <h3 class="text-2xl font-black text-slate-900 mb-2 flex items-center gap-2">💧 Target Air Minum</h3>
        <p class="text-xs text-slate-500 font-medium mb-8">Hitung kebutuhan air harian Anda sesuai berat badan.</p>

        <div class="space-y-6">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Berat Badan Anda (kg)</label>
                <input type="number" id="water-weight" placeholder="Contoh: 60" class="w-full bg-gradient-to-r from-sky-50 to-blue-50 border-2 border-sky-100 rounded-2xl p-4 text-sm font-bold focus:ring-0 focus:border-sky-400 transition-all">
            </div>
            <button onclick="calculateWater()" class="w-full bg-gradient-to-r from-sky-500 to-blue-500 text-white py-4 rounded-2xl font-black hover:from-sky-600 hover:to-blue-600 transition-all shadow-xl shadow-sky-500/20 hover:scale-[1.02] active:scale-95">
                Hitung Target 🌊
            </button>
            <div id="water-result" class="hidden p-6 rounded-3xl bg-gradient-to-br from-sky-50 to-blue-50 border-2 border-sky-100 text-center animate-fade-in">
                <p class="text-[10px] font-black text-sky-600 uppercase tracking-widest mb-1">Target Harian Anda</p>
                <p id="water-score" class="text-3xl font-black bg-gradient-to-r from-sky-600 to-blue-600 bg-clip-text text-transparent mb-2">2.1 Liter</p>
                <p class="text-[10px] text-sky-500 font-bold leading-relaxed mb-4">Setara dengan sekitar 8-9 gelas air per hari.</p>
                <button onclick="resetWater()" class="w-full py-2 rounded-xl border-2 border-sky-200 text-sky-600 text-[10px] font-black uppercase tracking-widest hover:bg-sky-100 transition-all">
                    ↻ Hitung Ulang
                </button>
            </div>
        </div>
    </div>
</div>

{{-- HPL MODAL --}}
<div id="hpl-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-[3rem] p-8 shadow-2xl scale-95 transition-transform duration-300 overflow-hidden relative border-2 border-rose-100">
        <button onclick="closeHPLModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <span class="material-icons-round">close</span>
        </button>
        <h3 class="text-2xl font-black text-slate-900 mb-2 flex items-center gap-2">🤰 Estimasi HPL</h3>
        <p class="text-xs text-slate-500 font-medium mb-8">Hitung Hari Perkiraan Lahir berdasarkan hari pertama haid terakhir.</p>

        <div class="space-y-6">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Hari Pertama Haid Terakhir (HPHT)</label>
                <input type="date" id="hpl-date" class="w-full bg-gradient-to-r from-rose-50 to-pink-50 border-2 border-rose-100 rounded-2xl p-4 text-sm font-bold focus:ring-0 focus:border-rose-400 transition-all">
            </div>
            <button onclick="calculateHPL()" class="w-full bg-gradient-to-r from-rose-500 to-pink-500 text-white py-4 rounded-2xl font-black hover:from-rose-600 hover:to-pink-600 transition-all shadow-xl shadow-rose-500/20 hover:scale-[1.02] active:scale-95">
                Hitung Estimasi 🎀
            </button>
            <div id="hpl-result" class="hidden p-6 rounded-3xl bg-gradient-to-br from-rose-50 to-pink-50 border-2 border-rose-100 text-center animate-fade-in">
                <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-1">Estimasi Kelahiran</p>
                <p id="hpl-score" class="text-2xl font-black bg-gradient-to-r from-rose-600 to-pink-600 bg-clip-text text-transparent mb-1">-</p>
                <p class="text-[10px] text-rose-400 font-bold leading-relaxed">Estimasi ini menggunakan aturan Naegele (280 hari).</p>
            </div>
        </div>
    </div>
</div>

{{-- EYE MODAL --}}
<div id="eye-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white w-full max-w-lg rounded-[3rem] p-8 shadow-2xl scale-95 transition-transform duration-300 overflow-hidden relative border-2 border-emerald-100">
        <button onclick="closeEyeModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600">
            <span class="material-icons-round">close</span>
        </button>
        <h3 class="text-2xl font-black text-slate-900 mb-2 flex items-center gap-2">👁️ Skrining Mata</h3>
        <p class="text-xs text-slate-500 font-medium mb-8">Posisikan layar 1 meter dari mata Anda dan coba baca huruf terkecil.</p>

        <div class="bg-gradient-to-br from-slate-50 to-white p-8 rounded-[2rem] flex flex-col items-center gap-6 select-none border-2 border-slate-100">
            <div class="text-[60px] font-black text-slate-900 leading-none">E</div>
            <div class="text-[40px] font-black text-slate-900 leading-none flex gap-4"><span>F</span> <span>P</span></div>
            <div class="text-[25px] font-black text-slate-900 leading-none flex gap-4"><span>T</span> <span>O</span> <span>Z</span></div>
            <div class="text-[15px] font-black text-slate-900 leading-none flex gap-4"><span>L</span> <span>P</span> <span>E</span> <span>D</span></div>
            <div class="text-[10px] font-black text-slate-700 leading-none flex gap-4 italic">Bisa baca baris ini? Penglihatan Anda sangat tajam!</div>
        </div>

        <div class="mt-8 p-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border-2 border-emerald-100">
            <p class="text-[10px] text-emerald-700 font-bold leading-relaxed">
                <strong>Catatan:</strong> Ini adalah skrining dasar. Jika Anda merasa penglihatan kabur, segera konsultasikan ke dokter spesialis mata.
            </p>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #6ee7b7; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #34d399; }

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}
.animate-float { animation: float 6s ease-in-out infinite; }

.char-btn.active {
    background: linear-gradient(135deg, #059669, #0d9488);
    border-color: #059669;
    color: white;
    transform: scale(1.1);
}

@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fade-in 0.3s ease-out; }

.counter-value {
    font-variant-numeric: tabular-nums;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.partner-swiper')) {
        new Swiper('.partner-swiper', {
            loop: true,
            autoplay: { delay: 0, disableOnInteraction: false },
            speed: 5000,
            slidesPerView: 'auto',
            spaceBetween: 0,
            allowTouchMove: false,
            breakpoints: {
                320: { slidesPerView: 2 },
                480: { slidesPerView: 3 },
                768: { slidesPerView: 4 },
                1024: { slidesPerView: 6 }
            }
        });
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.dataset.target.replace(/,/g, ''));
                if (!target) return;
                animateCounter(el, target);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.counter-value').forEach(el => observer.observe(el));
});

function animateCounter(el, target) {
    let current = 0;
    const step = Math.ceil(target / 60);
    const timer = setInterval(() => {
        current += step;
        if (current >= target) { current = target; clearInterval(timer); }
        el.textContent = current.toLocaleString();
    }, 25);
}

function toggleAI() {
    const panel = document.getElementById('ai-panel');
    const fab = document.getElementById('ai-fab');
    if (panel.classList.contains('hidden')) {
        panel.classList.remove('hidden');
        panel.classList.add('flex');
        fab.classList.add('hidden');
    } else {
        panel.classList.add('hidden');
        panel.classList.remove('flex');
        fab.classList.remove('hidden');
    }
}

let chatHistory = [];

function addMessage(sender, text) {
    chatHistory.push({ sender, text });
    const container = document.getElementById('chat-messages');
    const msgDiv = document.createElement('div');
    msgDiv.className = sender === 'user' ? 'flex justify-end mb-4' : 'flex justify-start mb-4';
    const innerClass = sender === 'user'
        ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white p-4 rounded-3xl rounded-tr-none text-sm shadow-md max-w-[85%]'
        : 'bg-white border-2 border-emerald-50 text-slate-800 p-4 rounded-3xl rounded-tl-none text-sm shadow-md max-w-[85%]';
    msgDiv.innerHTML = `<div class="${innerClass}">${text}</div>`;
    container.insertBefore(msgDiv, container.querySelector('.flex.flex-wrap.gap-2'));
    container.scrollTop = container.scrollHeight;
}

function askChip(question) {
    document.getElementById('chat-input').value = question;
    sendAIMessage();
}

function sendAIMessage() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    if (!message) return;

    addMessage('user', message);
    input.value = '';

    fetch('/ai/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message })
    })
    .then(res => res.json())
    .then(data => {
        const reply = data.reply || data.message || 'Maaf, terjadi kesalahan.';
        const formatted = marked.parse(reply);
        addMessage('ai', formatted);
    })
    .catch(() => {
        addMessage('ai', 'Maaf, Hilda sedang sibuk. Silakan coba lagi!');
    });
}

function calculateBMI() {
    const w = parseFloat(document.getElementById('bmi-weight').value);
    const h = parseFloat(document.getElementById('bmi-height').value);
    if (!w || !h) { alert('Mohon isi berat dan tinggi badan'); return; }

    const bmi = w / ((h/100) ** 2);
    let status, color, advice;
    if (bmi < 18.5) { status = 'Underweight'; color = 'text-blue-600'; advice = 'Anda berada dalam kategori kekurangan berat badan. Disarankan untuk meningkatkan asupan nutrisi seimbang dan konsultasi dengan ahli gizi.'; }
    else if (bmi < 25) { status = 'Normal'; color = 'text-emerald-600'; advice = 'Berat badan Anda ideal! Pertahankan pola makan sehat dan olahraga teratur.'; }
    else if (bmi < 30) { status = 'Overweight'; color = 'text-amber-600'; advice = 'Anda kelebihan berat badan. Mulai program diet seimbang dan tingkatkan aktivitas fisik.'; }
    else { status = 'Obesitas'; color = 'text-red-600'; advice = 'Anda berada dalam kategori obesitas. Segera konsultasikan dengan dokter untuk program penurunan berat badan yang aman.'; }

    document.getElementById('bmi-score').textContent = bmi.toFixed(1);
    document.getElementById('bmi-score').className = `text-3xl font-black ${color}`;
    document.getElementById('bmi-status').textContent = status;
    document.getElementById('bmi-status').className = `text-xl font-black ${color}`;
    document.getElementById('bmi-advice').textContent = advice;
    document.getElementById('bmi-result').classList.remove('hidden');
    document.getElementById('bmi-result').classList.add('block');
}

function resetBMI() {
    document.getElementById('bmi-weight').value = '';
    document.getElementById('bmi-height').value = '';
    document.getElementById('bmi-result').classList.add('hidden');
}

function calculateWater() {
    const w = parseFloat(document.getElementById('water-weight').value);
    if (!w) { alert('Mohon isi berat badan'); return; }
    const liters = (w * 0.033).toFixed(1);
    document.getElementById('water-score').textContent = liters + ' Liter';
    document.getElementById('water-result').classList.remove('hidden');
}

function resetWater() {
    document.getElementById('water-weight').value = '';
    document.getElementById('water-result').classList.add('hidden');
}

function openWaterModal() {
    document.getElementById('water-modal').classList.remove('hidden');
    document.getElementById('water-modal').classList.add('flex');
}

function closeWaterModal() {
    document.getElementById('water-modal').classList.add('hidden');
}

function calculateHPL() {
    const date = document.getElementById('hpl-date').value;
    if (!date) { alert('Mohon pilih tanggal HPHT'); return; }
    const hpht = new Date(date);
    const hpl = new Date(hpht);
    hpl.setDate(hpl.getDate() + 280);
    document.getElementById('hpl-score').textContent = hpl.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    document.getElementById('hpl-result').classList.remove('hidden');
}

function openHPLModal() {
    document.getElementById('hpl-modal').classList.remove('hidden');
    document.getElementById('hpl-modal').classList.add('flex');
}

function closeHPLModal() {
    document.getElementById('hpl-modal').classList.add('hidden');
}

function openEyeModal() {
    document.getElementById('eye-modal').classList.remove('hidden');
    document.getElementById('eye-modal').classList.add('flex');
}

function closeEyeModal() {
    document.getElementById('eye-modal').classList.add('hidden');
}

function filterAlphabet(char) {
    document.querySelectorAll('.char-btn').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
}

let currentDisease = null;

function openDictionaryDetail(disease) {
    currentDisease = disease;
    document.getElementById('dict-title').textContent = disease.title || disease.nama || 'Detail';
    document.getElementById('dict-desc').textContent = disease.summary || disease.deskripsi || '-';
    document.getElementById('dict-causes').textContent = disease.causes || disease.penyebab || '-';
    document.getElementById('dict-symptoms').textContent = disease.symptoms || disease.gejala || '-';
    document.getElementById('dict-treatments').textContent = disease.treatments || disease.pengobatan || '-';
    document.getElementById('dict-halal').textContent = disease.halal_review || disease.tinjauan_halal || 'Tidak ada data tinjauan halal.';

    const modal = document.getElementById('dict-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        document.getElementById('dict-modal-card').classList.remove('scale-90');
        document.getElementById('dict-modal-card').classList.add('scale-100');
    }, 10);
}

function closeDictionaryDetail() {
    const card = document.getElementById('dict-modal-card');
    card.classList.remove('scale-100');
    card.classList.add('scale-90');
    setTimeout(() => {
        document.getElementById('dict-modal').classList.add('hidden');
        document.getElementById('dict-modal').classList.remove('flex');
    }, 200);
}

document.getElementById('dict-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDictionaryDetail();
});
document.getElementById('water-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeWaterModal();
});
document.getElementById('hpl-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeHPLModal();
});
document.getElementById('eye-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeEyeModal();
});
</script>
@endsection