@extends('promo.layout')
@section('title', ($settings['site_name'] ?? 'Halalytics') . ' - Solusi Kesehatan & Halal Terpadu')

@section('styles')
<style>
    :root {
        --brand-primary: #004D40;
        --brand-secondary: #00C853;
        --brand-surface: #F8FCFB;
        --brand-text: #1A302D;
        --brand-accent: #26A69A;
    }

    body {
        background-color: var(--brand-surface);
        color: var(--brand-text);
        scroll-behavior: smooth;
    }

    /* Typography & Spacing Fixes */
    .section-title {
        font-size: 2.5rem;
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        color: var(--brand-primary);
    }

    .section-subtitle {
        font-size: 1.125rem;
        color: #64748b;
        max-width: 700px;
        margin-bottom: 3rem;
    }

    /* Premium Card Styles */
    .pro-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 30px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .pro-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px -12px rgba(0, 77, 64, 0.15);
        border-color: var(--brand-secondary);
    }

    /* Health Tool Interactive Styles */
    .tool-input-group {
        margin-bottom: 20px;
    }
    .tool-label {
        display: block;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 8px;
    }
    .tool-input {
        width: 100%;
        background: #f1f5f9;
        border: 2px solid transparent;
        border-radius: 12px;
        padding: 12px 16px;
        font-weight: 700;
        transition: all 0.2s;
    }
    .tool-input:focus {
        background: #fff;
        border-color: var(--brand-secondary);
        outline: none;
        box-shadow: 0 0 0 4px rgba(0, 200, 83, 0.1);
    }
    .tool-btn {
        width: 100%;
        background: var(--brand-primary);
        color: white;
        font-weight: 900;
        padding: 16px;
        border-radius: 14px;
        transition: all 0.3s;
        box-shadow: 0 10px 20px rgba(0, 77, 64, 0.2);
    }
    .tool-btn:hover {
        background: var(--brand-secondary);
        transform: scale(1.02);
    }

    /* Alphabet Filter Fix */
    .alphabet-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }

    /* Glassmorphism Refinement */
    .glass-panel {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px 0 rgba(0, 77, 64, 0.05);
    }

    /* Search Bar Premium */
    .hero-search {
        background: #ffffff;
        border-radius: 20px;
        padding: 8px;
        display: flex;
        align-items: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        max-width: 600px;
        border: 1px solid #f1f5f9;
        transition: all 0.3s;
    }
    .hero-search:focus-within {
        box-shadow: 0 15px 50px rgba(0, 77, 64, 0.12);
        transform: translateY(-2px);
        border-color: var(--brand-secondary);
    }
    .hero-search input {
        border: none;
        outline: none;
        padding: 12px 20px;
        flex: 1;
        font-size: 16px;
        font-weight: 600;
        color: var(--brand-text);
    }

    /* Expert Card */
    .expert-card {
        background: white;
        border-radius: 24px;
        padding: 24px;
        border: 1px solid #f1f5f9;
        transition: all 0.3s;
        text-align: center;
    }
    .expert-card:hover {
        border-color: var(--brand-accent);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
    }
    .expert-avatar {
        width: 100px;
        height: 100px;
        border-radius: 30px;
        margin: 0 auto 16px;
        object-fit: cover;
        background: #f8fafc;
    }
    .char-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: white;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .char-btn:hover, .char-btn.active {
        background: var(--brand-primary);
        color: white;
        border-color: var(--brand-primary);
    }

    /* Snellen Chart Simulator */
    .snellen-box {
        background: white;
        padding: 40px;
        border-radius: 20px;
        text-align: center;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.05);
    }
    .snellen-char {
        font-family: 'Inter', sans-serif;
        font-weight: 800;
        letter-spacing: 20px;
        margin: 10px 0;
    }

    /* AI Assistant UI (Halalytics AI) */
    #ai-panel {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 350px;
        height: 450px;
        background: white;
        border-radius: 24px;
        box-shadow: 0 25px 60px -15px rgba(0,0,0,0.2);
        display: none;
        flex-direction: column;
        z-index: 2000;
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .ai-header {
        background: var(--brand-primary);
        padding: 20px;
        color: white;
    }
    .ai-chat-box {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f8fafc;
    }
    .ai-bubble-msg {
        background: white;
        padding: 12px 16px;
        border-radius: 16px 16px 16px 4px;
        margin-bottom: 12px;
        font-size: 14px;
        border: 1px solid #e2e8f0;
        max-width: 85%;
    }
    .ai-bubble-msg.user {
        background: var(--brand-secondary);
        color: white;
        border: none;
        align-self: flex-end;
        border-radius: 16px 16px 4px 16px;
        margin-left: auto;
    }

    /* Hero Section Fix */
    .hero-container {
        padding-top: 140px;
        padding-bottom: 80px;
        background: radial-gradient(circle at 85% 30%, rgba(38, 166, 154, 0.15), transparent 50%),
                    radial-gradient(circle at 15% 80%, rgba(77, 182, 172, 0.08), transparent 45%);
    }

    /* Floating Animations */
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(3deg); }
        50% { transform: translateY(-20px) rotate(1.5deg); }
    }
    @keyframes float-delayed {
        0%, 100% { transform: translateY(0px) rotate(-3deg); }
        50% { transform: translateY(-16px) rotate(-1.5deg); }
    }
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    .animate-float-delayed {
        animation: float-delayed 5s ease-in-out infinite;
    }

    /* Partner Slider Marquee Custom Styles */
    .partner-swiper .swiper-wrapper {
        transition-timing-function: linear !important;
    }
    .partner-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 16px;
        padding: 14px 28px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 4px 20px rgba(0, 77, 64, 0.02);
    }
</style>
@endsection

@section('content')
<!-- ===== HERO SECTION ===== -->
<div class="hero-container px-6 relative overflow-hidden">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
        <!-- Text content -->
        <div class="lg:col-span-7 space-y-8 relative z-10">
            <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 px-4 py-2 rounded-full text-sm font-bold border border-emerald-100/60 shadow-sm animate-pulse">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                Halalytics AI v2.0 - Kini Lebih Cerdas & Responsif
            </div>
            <h1 class="text-5xl md:text-6xl font-black text-slate-900 leading-[1.1] tracking-tight">
                Skrining Kesehatan <br> Dengan <span class="text-emerald-600">Kepastian Halal.</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-500 leading-relaxed max-w-xl">
                Asisten pintar kesehatan terintegrasi pertama yang menghubungkan kecerdasan buatan AI dengan basis data obat-obatan BPOM serta sertifikasi halal resmi. Cepat, akurat, dan aman.
            </p>
            
            <!-- Modern Search Bar -->
            <div class="hero-search mt-4">
                <span class="pl-4 text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" placeholder="Cari obat, penyakit, atau pakar..." onfocus="this.placeholder=''" onblur="this.placeholder='Cari obat, penyakit, atau pakar...'">
                <button class="bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-emerald-700 transition-all">Cari</button>
            </div>

            <div class="flex flex-wrap gap-4 pt-4">
                <a href="{{ route('download') }}" class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-8 py-4 rounded-2xl font-black text-lg hover:from-emerald-500 hover:to-teal-500 transition-all duration-300 shadow-xl shadow-emerald-700/25 flex items-center gap-3">
                    Download Aplikasi
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="#health-tools" class="bg-white text-slate-700 border border-slate-200 px-8 py-4 rounded-2xl font-black text-lg hover:bg-slate-50 transition-all duration-300 shadow-sm flex items-center gap-2">
                    Kalkulator Medis
                </a>
            </div>
        </div>

        <!-- Floating Devices and AI Chat Preview -->
        <div class="lg:col-span-5 hidden lg:flex justify-center items-center relative h-[650px] w-full">
            <!-- Background Decorative Circle -->
            <div class="absolute w-[450px] h-[450px] bg-gradient-to-tr from-emerald-500/10 to-teal-500/5 rounded-full blur-2xl"></div>

            <!-- Device Back (Delayed Float) -->
            <div class="device-shell w-[250px] h-[500px] absolute right-8 top-12 z-0 opacity-80 scale-95 animate-float-delayed">
                <div class="w-full h-full rounded-[40px] border-4 border-slate-800 overflow-hidden bg-slate-900 shadow-xl">
                    <img src="{{ asset('images/promo/ss-home-2.png') }}" alt="App Feature" class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Device Front (Float) -->
            <div class="device-shell w-[270px] h-[540px] absolute left-12 bottom-8 z-10 animate-float">
                <div class="w-full h-full rounded-[44px] border-[6px] border-slate-900 overflow-hidden bg-slate-950 shadow-2xl">
                    <img src="{{ asset('images/promo/ss-home-1.png') }}" alt="App Home" class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Floating AI Chat Bubble Card -->
            <div class="absolute left-0 top-24 bg-white/95 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-emerald-50 w-72 z-20 animate-bounce" style="animation-duration: 4s;">
                <div class="flex items-center gap-2.5 mb-2 pb-2 border-b border-gray-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-bold text-emerald-700">AI Halalytics (HILDA)</span>
                </div>
                <div class="space-y-2">
                    <div class="bg-gray-100 rounded-xl p-2.5 text-[11px] text-gray-700 max-w-[90%]">
                        "Apakah kandungan gelatin babi ada pada obat A?"
                    </div>
                    <div class="bg-emerald-50 rounded-xl p-2.5 text-[11px] text-emerald-800 ml-auto max-w-[90%] text-right font-medium">
                        "Hasil penelusuran: Obat A terdaftar BPOM dan menggunakan gelatin sapi bersertifikat halal."
                    </div>
                </div>
            </div>

            <!-- Live Status Badge -->
            <div class="absolute -bottom-4 right-0 bg-white p-5 rounded-3xl shadow-xl border border-emerald-50 w-60 z-20">
                <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Ketersediaan Pakar</p>
                <div class="flex items-center gap-3">
                    <div class="w-3.5 h-3.5 rounded-full bg-emerald-500 animate-ping absolute"></div>
                    <div class="w-3.5 h-3.5 rounded-full bg-emerald-500 relative"></div>
                    <p class="font-extrabold text-sm text-slate-800">512 Tenaga Medis Siaga</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== ARTIKEL TERBARU (Health News) ===== -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12">
            <div>
                <h2 class="section-title">Informasi Kesehatan Terpercaya</h2>
                <p class="section-subtitle">Dapatkan tips dan berita kesehatan terbaru yang telah divalidasi oleh tim medis Halalytics.</p>
            </div>
            <a href="{{ route('blog.index') }}" class="text-emerald-700 font-black hover:underline mb-8 flex items-center gap-2">
                Lihat Semua Artikel <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($latestBlogs ?? [] as $blog)
            <div class="group cursor-pointer" onclick="location.href='{{ route('blog.show', $blog->slug) }}'">
                <div class="aspect-[16/10] rounded-3xl overflow-hidden mb-6 shadow-sm group-hover:shadow-xl transition-all duration-500">
                    <img src="{{ $blog->image_url ?? 'https://images.unsplash.com/photo-1505751172107-573225a91200?q=80&w=800' }}" alt="{{ $blog->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                </div>
                <div class="inline-block px-3 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-full mb-4">
                    {{ $blog->category ?? 'Kesehatan' }}
                </div>
                <h4 class="text-xl font-black text-slate-900 group-hover:text-emerald-700 transition-colors mb-3 line-clamp-2">
                    {{ $blog->title }}
                </h4>
                <p class="text-sm text-slate-500 leading-relaxed line-clamp-3">
                    {{ Str::limit(strip_tags($blog->content), 120) }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== PARTNERS INFINITE MARQUEE SLIDER ===== -->
<div class="bg-white/60 border-y border-slate-200/50 py-8 relative z-20">
    <div class="max-w-7xl mx-auto px-6">
        <p class="text-center text-[10px] font-black uppercase tracking-widest text-slate-400 mb-5">Keakuratan Data Terintegrasi Secara Resmi</p>
        <div class="swiper partner-swiper overflow-hidden">
            <div class="swiper-wrapper flex items-center">
                <!-- Slide 1 -->
                <div class="swiper-slide !w-auto">
                    <div class="partner-card">
                        <span class="text-lg">🛡️</span>
                        <span class="font-brand font-extrabold text-xs text-slate-700">BPOM RI</span>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="swiper-slide !w-auto">
                    <div class="partner-card">
                        <span class="text-lg">🕌</span>
                        <span class="font-brand font-extrabold text-xs text-slate-700">LPPOM MUI</span>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="swiper-slide !w-auto">
                    <div class="partner-card">
                        <span class="text-lg">🩺</span>
                        <span class="font-brand font-extrabold text-xs text-slate-700">Kemenkes RI</span>
                    </div>
                </div>
                <!-- Slide 4 -->
                <div class="swiper-slide !w-auto">
                    <div class="partner-card">
                        <span class="text-lg">📦</span>
                        <span class="font-brand font-extrabold text-xs text-slate-700">Open Food Facts</span>
                    </div>
                </div>
                <!-- Slide 5 -->
                <div class="swiper-slide !w-auto">
                    <div class="partner-card">
                        <span class="text-lg">🤖</span>
                        <span class="font-brand font-extrabold text-xs text-slate-700">Gemini Cloud AI</span>
                    </div>
                </div>
                <!-- Slide 6 -->
                <div class="swiper-slide !w-auto">
                    <div class="partner-card">
                        <span class="text-lg">🎓</span>
                        <span class="font-brand font-extrabold text-xs text-slate-700">UI & ITB Labs</span>
                    </div>
                </div>
                <!-- Duplicate slides for seamless loop marquee -->
                <div class="swiper-slide !w-auto">
                    <div class="partner-card">
                        <span class="text-lg">🛡️</span>
                        <span class="font-brand font-extrabold text-xs text-slate-700">BPOM RI</span>
                    </div>
                </div>
                <div class="swiper-slide !w-auto">
                    <div class="partner-card">
                        <span class="text-lg">🕌</span>
                        <span class="font-brand font-extrabold text-xs text-slate-700">LPPOM MUI</span>
                    </div>
                </div>
                <div class="swiper-slide !w-auto">
                    <div class="partner-card">
                        <span class="text-lg">🩺</span>
                        <span class="font-brand font-extrabold text-xs text-slate-700">Kemenkes RI</span>
                    </div>
                </div>
                <div class="swiper-slide !w-auto">
                    <div class="partner-card">
                        <span class="text-lg">📦</span>
                        <span class="font-brand font-extrabold text-xs text-slate-700">Open Food Facts</span>
                    </div>
                </div>
                <div class="swiper-slide !w-auto">
                    <div class="partner-card">
                        <span class="text-lg">🤖</span>
                        <span class="font-brand font-extrabold text-xs text-slate-700">Gemini Cloud AI</span>
                    </div>
                </div>
                <div class="swiper-slide !w-auto">
                    <div class="partner-card">
                        <span class="text-lg">🎓</span>
                        <span class="font-brand font-extrabold text-xs text-slate-700">UI & ITB Labs</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== LAYANAN UNGGULAN (Refined) ===== -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center">
            <h2 class="section-title">Layanan Unggulan Kami</h2>
            <p class="section-subtitle mx-auto">Kami menghadirkan ekosistem kesehatan digital terlengkap yang dirancang khusus untuk kebutuhan gaya hidup halal Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            @php
            $mainServices = [
                ['icon' => '👨‍⚕️', 'name' => 'Chat Dokter', 'desc' => 'Tanya pakar 24 jam', 'color' => '#f0fdf4', 'link' => route('download')],
                ['icon' => '🏠', 'name' => 'Homecare & Lab', 'desc' => 'Tes medis di rumah', 'color' => '#eff6ff', 'link' => route('download')],
                ['icon' => '📅', 'name' => 'Buat Janji RS', 'desc' => 'Pesan jadwal offline', 'color' => '#fff7ed', 'link' => route('download')],
                ['icon' => '🧠', 'name' => 'Kesehatan Mental', 'desc' => 'Konseling psikolog', 'color' => '#faf5ff', 'link' => route('specialized.show', 'mental')],
                ['icon' => '🛡️', 'name' => 'Klaim Asuransi', 'desc' => 'Integrasi cashless', 'color' => '#fef2f2', 'link' => route('download')]
            ];
            @endphp

            @foreach($mainServices as $s)
            <div class="pro-card group cursor-pointer" onclick="location.href='{{ $s['link'] }}'">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl mb-6 transition-transform group-hover:scale-110" style="background: {{ $s['color'] }}">
                    {{ $s['icon'] }}
                </div>
                <h3 class="font-black text-gray-900 text-lg mb-2">{{ $s['name'] }}</h3>
                <p class="text-sm text-gray-500">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== TANYA PAKAR (Specialist Section) ===== -->
<section class="py-24 bg-slate-50/50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12">
            <div>
                <h2 class="section-title">Tanya Pakar Halalytics</h2>
                <p class="section-subtitle">Konsultasikan kesehatan Anda dengan tenaga medis profesional yang memahami standar halal.</p>
            </div>
            <a href="{{ route('download') }}" class="text-emerald-700 font-black hover:underline mb-8 flex items-center gap-2">
                Lihat Semua Pakar <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @php
            $experts = [
                ['name' => 'dr. Sarah Annisa', 'spec' => 'Spesialis Gizi Klinik', 'img' => 'expert-1.png', 'rate' => '98%', 'price' => '50.000'],
                ['name' => 'dr. Ahmad Fauzi', 'spec' => 'Dokter Umum & Halal Auditor', 'img' => 'expert-2.png', 'rate' => '100%', 'price' => '35.000'],
                ['name' => 'apt. Rina Wati', 'spec' => 'Apoteker (Spesialis Obat Halal)', 'img' => 'expert-3.png', 'rate' => '96%', 'price' => '25.000'],
                ['name' => 'dr. Linda Kusuma', 'spec' => 'Spesialis Kebidanan (Obgyn)', 'img' => 'expert-4.png', 'rate' => '99%', 'price' => '65.000']
            ];
            @endphp

            @foreach($experts as $e)
            <div class="expert-card group cursor-pointer" onclick="location.href='{{ route('download') }}'">
                <div class="relative inline-block mb-4">
                    <div class="expert-avatar overflow-hidden border-4 border-white shadow-sm group-hover:border-emerald-100 transition-all">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($e['name']) }}&background=E6FFFA&color=004D40&bold=true&size=128" alt="{{ $e['name'] }}" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute bottom-4 right-0 bg-emerald-500 w-5 h-5 rounded-full border-2 border-white"></div>
                </div>
                <h4 class="font-black text-slate-900 mb-1">{{ $e['name'] }}</h4>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">{{ $e['spec'] }}</p>
                
                <div class="flex items-center justify-center gap-4 mb-6">
                    <div class="text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase">Kepuasan</p>
                        <p class="text-sm font-black text-emerald-600">{{ $e['rate'] }}</p>
                    </div>
                    <div class="w-px h-8 bg-slate-100"></div>
                    <div class="text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase">Mulai Dari</p>
                        <p class="text-sm font-black text-slate-800">Rp{{ $e['price'] }}</p>
                    </div>
                </div>

                <button class="w-full py-3 bg-slate-900 text-white rounded-xl font-black text-xs group-hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10 group-hover:shadow-emerald-600/20">
                    Chat Sekarang
                </button>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== BELI OBAT & SUPLEMEN (Halodoc Style) ===== -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12">
            <div>
                <h2 class="section-title">Beli Obat & Suplemen</h2>
                <p class="section-subtitle">Dapatkan obat-obatan dan vitamin terverifikasi halal dengan pengiriman cepat.</p>
            </div>
            <a href="#" class="text-emerald-700 font-black hover:underline mb-8">Lihat Semua Kategori</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @foreach($medicines ?? [] as $m)
            <div class="pro-card p-4 group cursor-pointer" onclick="location.href='{{ route('medicine.show', $m->id_medicine) }}'">
                <div class="aspect-square bg-gray-50 rounded-2xl mb-4 overflow-hidden flex items-center justify-center p-4">
                    @if($m->image_url)
                    <img src="{{ $m->image_url }}" alt="{{ $m->name }}" class="max-h-full transition-transform group-hover:scale-110">
                    @else
                    <span class="text-4xl">💊</span>
                    @endif
                </div>
                <h4 class="font-bold text-sm text-gray-900 line-clamp-2 min-h-[40px] mb-2">{{ $m->name }}</h4>
                <p class="text-emerald-600 font-black text-sm">Rp {{ number_format($m->price ?? 15000, 0, ',', '.') }}</p>
                <button class="w-full mt-4 py-2 border border-emerald-600 text-emerald-600 rounded-lg text-xs font-black group-hover:bg-emerald-600 group-hover:text-white transition-all">
                    Cek Detail
                </button>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== PERAWATAN KHUSUS (New Section) ===== -->
<section id="specialized" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12">
            <div>
                <h2 class="section-title">Perawatan Khusus</h2>
                <p class="text-gray-500 font-bold">Program kesehatan spesifik untuk penyakit kronis & lifestyle.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="pro-card border-l-8 border-l-emerald-600 cursor-pointer hover:bg-emerald-50" onclick="location.href='{{ route('specialized.show', 'diabetes') }}'">
                <h3 class="text-2xl font-black text-gray-900 mb-4">Diabetes Care</h3>
                <p class="text-gray-600 mb-6">Program manajemen gula darah terpadu dengan pemantauan AI dan konsultasi spesialis endokrin.</p>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm font-bold text-gray-700">✅ Alat Monitor Gula Darah</li>
                    <li class="flex items-center gap-3 text-sm font-bold text-gray-700">✅ Konsultasi Spesialis</li>
                </ul>
            </div>

            <div class="pro-card border-l-8 border-l-rose-500 cursor-pointer hover:bg-rose-50" onclick="location.href='{{ route('specialized.show', 'heart') }}'">
                <h3 class="text-2xl font-black text-gray-900 mb-4">Heart Health</h3>
                <p class="text-gray-600 mb-6">Pemantauan tekanan darah, tips diet rendah kolesterol, dan deteksi dini risiko kardiovaskular.</p>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm font-bold text-gray-700">✅ Cek Kolesterol Homecare</li>
                    <li class="flex items-center gap-3 text-sm font-bold text-gray-700">✅ Diet Plan Jantung</li>
                </ul>
            </div>

            <div class="pro-card border-l-8 border-l-purple-600 cursor-pointer hover:bg-purple-50" onclick="location.href='{{ route('specialized.show', 'mental') }}'">
                <h3 class="text-2xl font-black text-gray-900 mb-4">Mental Health Center</h3>
                <p class="text-gray-600 mb-6">Sesi konseling mendalam dengan psikolog klinis untuk depresi, kecemasan, dan manajemen stres.</p>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-sm font-bold text-gray-700">✅ Skrining Mental Gratis</li>
                    <li class="flex items-center gap-3 text-sm font-bold text-gray-700">✅ Sesi Terapi Privat</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ===== CEK KESEHATAN MANDIRI (Expanded) ===== -->
<section id="health-tools" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="section-title">Cek Kesehatan Mandiri</h2>
            <p class="section-subtitle mx-auto">Gunakan kalkulator medis kami yang divalidasi oleh tenaga profesional untuk memantau kondisi tubuh Anda secara instan.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- 1. Nutrisi & Kalori -->
            <div class="pro-card shadow-xl border-emerald-50">
                <h3 class="text-2xl font-black text-gray-900 mb-8 flex items-center gap-4">
                    <span class="text-3xl">🥗</span> Kalkulator Nutrisi & BMR
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="tool-input-group">
                        <label class="tool-label">Usia</label>
                        <input type="number" id="nut_age" placeholder="Contoh: 25" class="tool-input">
                    </div>
                    <div class="tool-input-group">
                        <label class="tool-label">Jenis Kelamin</label>
                        <select id="nut_gender" class="tool-input">
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>
                    <div class="tool-input-group">
                        <label class="tool-label">Berat Badan (kg)</label>
                        <input type="number" id="nut_weight" placeholder="Contoh: 70" class="tool-input">
                    </div>
                    <div class="tool-input-group">
                        <label class="tool-label">Tinggi (cm)</label>
                        <input type="number" id="nut_height" placeholder="Contoh: 175" class="tool-input">
                    </div>
                </div>
                <div class="tool-input-group">
                    <label class="tool-label">Tingkat Aktivitas</label>
                    <select id="nut_activity" class="tool-input">
                        <option value="1.2">Jarang Bergerak (Sedentary)</option>
                        <option value="1.375">Aktivitas Ringan (1-3 hari/minggu)</option>
                        <option value="1.55">Aktivitas Sedang (3-5 hari/minggu)</option>
                        <option value="1.725">Sangat Aktif (6-7 hari/minggu)</option>
                    </select>
                </div>
                <button onclick="calculateBMR()" class="tool-btn">Hitung Kebutuhan Kalori</button>
                <div id="bmr_res" class="hidden mt-6 p-6 bg-emerald-50 rounded-2xl text-center">
                    <p class="text-sm font-bold text-emerald-800">Kebutuhan Kalori Harian Anda:</p>
                    <p id="bmr_val" class="text-4xl font-black text-emerald-600 mt-1">2,450 kcal</p>
                </div>
            </div>

            <!-- 2. Target Air Minum -->
            <div class="pro-card shadow-xl border-blue-50">
                <h3 class="text-2xl font-black text-gray-900 mb-8 flex items-center gap-4">
                    <span class="text-3xl">💧</span> Target Air Minum
                </h3>
                <div class="tool-input-group">
                    <label class="tool-label">Berat Badan (kg)</label>
                    <input type="number" id="water_weight" placeholder="Contoh: 60" class="tool-input">
                </div>
                <div class="tool-input-group">
                    <label class="tool-label">Lingkungan / Cuaca</label>
                    <select id="water_weather" class="tool-input">
                        <option value="normal">Normal / Dingin</option>
                        <option value="hot">Cuaca Panas / Berkeringat</option>
                    </select>
                </div>
                <button onclick="calculateWater()" class="tool-btn bg-blue-600 hover:bg-blue-700">Cek Target Air</button>
                <div id="water_res" class="hidden mt-6 p-6 bg-blue-50 rounded-2xl text-center">
                    <p class="text-sm font-bold text-blue-800">Target Air Harian Anda:</p>
                    <p id="water_val" class="text-4xl font-black text-blue-600 mt-1">2.5 Liter</p>
                </div>
            </div>

            <!-- 3. Skrining Risiko (Lifestyle) -->
            <div class="pro-card shadow-xl border-rose-50 lg:col-span-1">
                <h3 class="text-2xl font-black text-gray-900 mb-8 flex items-center gap-4">
                    <span class="text-3xl">🫀</span> Skrining Risiko Lifestyle
                </h3>
                <div class="space-y-4 mb-6">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm font-bold">Apakah Anda merokok?</span>
                        <input type="checkbox" id="risk_smoke" class="w-5 h-5 accent-rose-500">
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm font-bold">Ada riwayat penyakit keluarga?</span>
                        <input type="checkbox" id="risk_family" class="w-5 h-5 accent-rose-500">
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm font-bold">Tidur kurang dari 6 jam/hari?</span>
                        <input type="checkbox" id="risk_sleep" class="w-5 h-5 accent-rose-500">
                    </div>
                </div>
                <button onclick="calculateRisk()" class="tool-btn bg-rose-600 hover:bg-rose-700">Cek Skor Risiko</button>
                <div id="risk_res" class="hidden mt-6 p-6 rounded-2xl text-center">
                    <p class="text-sm font-bold opacity-80">Skor Risiko Anda:</p>
                    <p id="risk_val" class="text-2xl font-black mt-1">RENDAH</p>
                </div>
            </div>

            <!-- 4. Kehamilan & Masa Subur -->
            <div class="pro-card shadow-xl border-amber-50">
                <h3 class="text-2xl font-black text-gray-900 mb-8 flex items-center gap-4">
                    <span class="text-3xl">👶</span> Kalkulator Kehamilan
                </h3>
                <div class="tool-input-group">
                    <label class="tool-label">Hari Pertama Haid Terakhir (HPHT)</label>
                    <input type="date" id="hpht_date" class="tool-input">
                </div>
                <button onclick="calculateDueDate()" class="tool-btn bg-amber-600 hover:bg-amber-700">Estimasi Kelahiran</button>
                <div id="due_res" class="hidden mt-6 p-6 bg-amber-50 rounded-2xl text-center">
                    <p class="text-sm font-bold text-amber-800">Estimasi Tanggal Persalinan:</p>
                    <p id="due_val" class="text-2xl font-black text-amber-700 mt-1">12 Desember 2026</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== KAMUS KESEHATAN A-Z (Valid Data) ===== -->
<section id="kamus" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="section-title">Kamus Kesehatan A-Z</h2>
            <p class="section-subtitle mx-auto">Database penyakit, gejala, dan obat-obatan terlengkap yang disusun secara alfabetis oleh tim medis Halalytics.</p>
        </div>

        <div class="alphabet-grid mb-16">
            @foreach(range('A', 'Z') as $c)
            <div class="char-btn @if($c == 'A') active @endif" onclick="filterAlphabet('{{ $c }}')">{{ $c }}</div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="dictionary-grid">
            @foreach($diseases ?? [] as $d)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-emerald-300 hover:shadow-md transition-all cursor-pointer group" onclick="openDictionaryDetail({{ json_encode($d) }})">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-black text-gray-900 group-hover:text-emerald-700 transition-colors">{{ $d->title ?? $d['name'] ?? 'Penyakit' }}</h4>
                    <span class="text-xs font-extrabold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">{{ $d->alphabet ?? 'A' }}</span>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed line-clamp-2">{{ $d->summary ?? $d['desc'] ?? '' }}</p>
                <div class="mt-4 flex items-center text-xs font-extrabold text-emerald-600 gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    Lihat Selengkapnya <span>&rarr;</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== DICTIONARY DETAIL MODAL ===== -->
<div id="dict-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9999] hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-2xl rounded-3xl overflow-hidden shadow-2xl border border-gray-100 flex flex-col max-h-[85vh] scale-95 transition-transform duration-300" id="dict-modal-card">
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-700 to-teal-700 p-6 text-white flex justify-between items-start">
            <div>
                <span class="text-xs font-black uppercase tracking-widest text-teal-100 bg-white/10 px-3 py-1 rounded-full">Kamus Medis & Halal</span>
                <h3 id="dict-title" class="text-2xl font-black mt-2">Nama Penyakit</h3>
            </div>
            <button onclick="closeDictionaryDetail()" class="text-white hover:text-teal-200 text-3xl font-bold leading-none">&times;</button>
        </div>
        
        <!-- Content (Scrollable) -->
        <div class="p-8 overflow-y-auto space-y-6 text-sm leading-relaxed">
            <div>
                <h5 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-2">Definisi Singkat</h5>
                <p id="dict-desc" class="text-slate-700 font-medium text-base"></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <h5 class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <span>❓</span> Penyebab
                    </h5>
                    <p id="dict-causes" class="text-slate-600"></p>
                </div>
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <h5 class="text-xs font-black text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-2">
                        <span>⚠️</span> Gejala Umum
                    </h5>
                    <p id="dict-symptoms" class="text-slate-600"></p>
                </div>
            </div>

            <div class="bg-emerald-50/50 p-5 rounded-2xl border border-emerald-100/60">
                <h5 class="text-xs font-black text-emerald-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <span>🩺</span> Pengobatan Medis
                </h5>
                <p id="dict-treatments" class="text-emerald-700 font-medium"></p>
            </div>

            <div class="bg-amber-50/60 p-5 rounded-2xl border border-amber-100/70">
                <h5 class="text-xs font-black text-amber-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <span>🕌</span> Perspektif Halal & Pantangan
                </h5>
                <p id="dict-halal" class="text-amber-700 font-medium"></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <button onclick="closeDictionaryDetail()" class="px-6 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl transition-all">
                Tutup
            </button>
            <button class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl transition-all shadow-md shadow-emerald-700/10" onclick="closeDictionaryDetail(); toggleAI();">
                Tanyakan AI Halalytics
            </button>
        </div>
    </div>
</div>


<!-- ===== HALALYTICS AI (HILDA) ===== -->
<style>
    .ai-fab { position: fixed; bottom: 30px; right: 30px; z-index: 1999; display: flex; align-items: center; gap: 12px; }
    .ai-fab-label { background: white; color: #004D40; padding: 8px 16px; border-radius: 12px; font-weight: 800; font-size: 13px; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
    .ai-fab-btn { width: 60px; height: 60px; border-radius: 20px; background: linear-gradient(135deg, #004D40, #00C853); color: white; display: flex; align-items: center; justify-content: center; font-size: 28px; cursor: pointer; box-shadow: 0 8px 30px rgba(0,77,64,.4); transition: transform .2s; }
    .ai-fab-btn:hover { transform: scale(1.1); }
    .ai-chip { display: inline-block; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all .2s; margin: 3px; }
    .ai-chip:hover { background: #166534; color: white; }
    .ai-typing span { width: 8px; height: 8px; background: #94a3b8; border-radius: 50%; display: inline-block; animation: typing .8s infinite; margin: 0 2px; }
    .ai-typing span:nth-child(2) { animation-delay: .15s; }
    .ai-typing span:nth-child(3) { animation-delay: .3s; }
    @keyframes typing { 0%,100% { opacity:.3; } 50% { opacity:1; } }
</style>

<div class="ai-fab" id="ai-fab">
    <div class="ai-fab-label">Tanya AI Halalytics</div>
    <div class="ai-fab-btn" onclick="toggleAI()">🤖</div>
</div>

<div id="ai-panel">
    <div class="ai-header flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-lg">🤖</div>
            <div>
                <p class="font-black text-sm">AI Halalytics</p>
                <p class="text-[10px] text-emerald-300">Online</p>
            </div>
        </div>
        <button onclick="toggleAI()" class="text-white opacity-50 hover:opacity-100 text-xl">&times;</button>
    </div>
    <div class="ai-chat-box" id="chat-messages">
        <div class="ai-bubble-msg">Halo! Saya <b>AI Halalytics</b> 🤖 Asisten cerdas kesehatan, gizi, diet, obat, dan produk halal Anda. Silakan tanyakan apa saja!</div>
        <div style="padding:8px 0">
            <span class="ai-chip" onclick="askChip('Beri saya tips diet sehat bergizi')">Tips Diet Sehat</span>
            <span class="ai-chip" onclick="askChip('Apa saja gejala diabetes dan cara mencegahnya?')">Gejala Diabetes</span>
            <span class="ai-chip" onclick="askChip('Bagaimana cara mengetahui produk kosmetik aman dan halal?')">Skincare Halal</span>
        </div>
    </div>
    <div class="p-3 bg-white border-t border-gray-100 flex gap-2">
        <input type="text" id="ai-input" placeholder="Tulis pertanyaan Anda di sini..." class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none" onkeypress="if(event.key==='Enter')sendAIMessage()">
        <button onclick="sendAIMessage()" class="bg-emerald-600 text-white w-12 h-12 rounded-xl flex items-center justify-center shadow-lg hover:bg-emerald-700 flex-shrink-0">&rarr;</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Initialize Swiper.js Partner Marquee
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.partner-swiper', {
        loop: true,
        autoplay: {
            delay: 0,
            disableOnInteraction: false,
        },
        speed: 5000,
        slidesPerView: 'auto',
        spaceBetween: 24,
        allowTouchMove: false, // Prevents manual swipe interference
    });
});

function calculateBMR(){const a=document.getElementById('nut_age').value,w=document.getElementById('nut_weight').value,h=document.getElementById('nut_height').value,g=document.getElementById('nut_gender').value,ac=document.getElementById('nut_activity').value;if(!a||!w||!h){alert('Harap lengkapi semua data!');return}let b=g==='male'?88.362+(13.397*w)+(4.799*h)-(5.677*a):447.593+(9.247*w)+(3.098*h)-(4.330*a);document.getElementById('bmr_val').innerText=Math.round(b*ac).toLocaleString()+' kcal';document.getElementById('bmr_res').classList.remove('hidden')}
function calculateWater(){const w=document.getElementById('water_weight').value;if(!w){alert('Masukkan berat badan!');return}let l=(w*0.033).toFixed(1);if(document.getElementById('water_weather').value==='hot')l=(parseFloat(l)+0.8).toFixed(1);document.getElementById('water_val').innerText=l+' Liter';document.getElementById('water_res').classList.remove('hidden')}
function calculateRisk(){let s=0;if(document.getElementById('risk_smoke').checked)s+=40;if(document.getElementById('risk_family').checked)s+=30;if(document.getElementById('risk_sleep').checked)s+=20;const b=document.getElementById('risk_res'),v=document.getElementById('risk_val');b.classList.remove('hidden');if(s>=60){v.innerText='TINGGI';b.className='mt-6 p-6 rounded-2xl text-center bg-rose-50 text-rose-600'}else if(s>=30){v.innerText='SEDANG';b.className='mt-6 p-6 rounded-2xl text-center bg-amber-50 text-amber-600'}else{v.innerText='RENDAH';b.className='mt-6 p-6 rounded-2xl text-center bg-emerald-50 text-emerald-600'}}
function calculateDueDate(){const h=document.getElementById('hpht_date').value;if(!h){alert('Masukkan tanggal HPHT!');return}const d=new Date(h);d.setDate(d.getDate()+7);d.setMonth(d.getMonth()-3);d.setFullYear(d.getFullYear()+1);document.getElementById('due_val').innerText=d.toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'});document.getElementById('due_res').classList.remove('hidden')}

// Health Dictionary Interactive Controls
let diseaseDataset = {
    'A': @json($diseases ?? [])
    
};

function filterAlphabet(letter) {
    document.querySelectorAll('.char-btn').forEach(btn => {
        if (btn.innerText === letter) btn.classList.add('active');
        else btn.classList.remove('active');
    });

    const grid = document.getElementById('dictionary-grid');
    grid.innerHTML = '<div class="col-span-full py-12 text-center"><div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-emerald-600 border-t-transparent"></div></div>';

    fetch(`/api/health-encyclopedia?alphabet=${letter}`)
        .then(r => r.json())
        .then(res => {
            grid.innerHTML = '';
            if (res.success && res.data.length > 0) {
                res.data.forEach(d => {
                    const card = document.createElement('div');
                    card.className = 'bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-emerald-300 hover:shadow-md transition-all cursor-pointer group';
                    card.onclick = () => openDictionaryDetail(d);
                    card.innerHTML = `
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-black text-gray-900 group-hover:text-emerald-700 transition-colors">${d.title}</h4>
                            <span class="text-xs font-extrabold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">${d.alphabet}</span>
                        </div>
                        <p class="text-sm text-gray-500 leading-relaxed line-clamp-2">${d.summary || ''}</p>
                    `;
                    grid.appendChild(card);
                });
            } else {
                grid.innerHTML = '<div class="col-span-full py-20 text-center"><p class="text-slate-400 font-black">Data belum tersedia.</p></div>';
            }
        });
}

function openDictionaryDetail(data) {
    document.getElementById('dict-title').innerText = data.title || data.name;
    document.getElementById('dict-desc').innerText = data.summary || data.desc || '';
    document.getElementById('dict-causes').innerText = data.causes || 'Data tidak tersedia';
    document.getElementById('dict-symptoms').innerText = data.symptoms || 'Data tidak tersedia';
    document.getElementById('dict-treatments').innerText = data.treatments || 'Data tidak tersedia';
    document.getElementById('dict-halal').innerText = data.halal_notes || data.halal || 'Data tidak tersedia';

    const modal = document.getElementById('dict-modal');
    modal.style.display = 'flex';
    setTimeout(() => { document.getElementById('dict-modal-card').style.transform = 'scale(1)'; }, 50);
}

function closeDictionaryDetail() {
    document.getElementById('dict-modal-card').style.transform = 'scale(0.95)';
    setTimeout(() => {
        document.getElementById('dict-modal').style.display = 'none';
    }, 150);
}

// Close modal if clicking outside the card
document.getElementById('dict-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDictionaryDetail();
    }
});

// AI Halalytics Panel Controls
function toggleAI(){const p=document.getElementById('ai-panel'),f=document.getElementById('ai-fab');const o=p.style.display==='flex';p.style.display=o?'none':'flex';f.style.display=o?'flex':'none'}
function askChip(t){document.getElementById('ai-input').value=t;sendAIMessage()}

function sendAIMessage(){const i=document.getElementById('ai-input'),c=document.getElementById('chat-messages'),t=i.value.trim();if(!t)return;const u=document.createElement('div');u.className='ai-bubble-msg user';u.innerText=t;c.appendChild(u);i.value='';c.scrollTop=c.scrollHeight;const tp=document.createElement('div');tp.innerHTML='<span></span><span></span><span></span>';tp.className='ai-typing';c.appendChild(tp);c.scrollTop=c.scrollHeight;
fetch('{{ route('promo.ai_chat') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({message:t})}).then(response=>response.json()).then(data=>{tp.remove();const b=document.createElement('div');b.className='ai-bubble-msg';b.innerHTML=data.reply||'Maaf, ada kendala koneksi dengan AI Halalytics.';c.appendChild(b);c.scrollTop=c.scrollHeight}).catch(error=>{tp.remove();const b=document.createElement('div');b.className='ai-bubble-msg';b.innerHTML='Maaf, gagal menghubungi server AI Halalytics. Coba lagi nanti.';c.appendChild(b);c.scrollTop=c.scrollHeight})}
</script>
@endsection
