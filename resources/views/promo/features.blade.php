@extends('promo.layout')
@section('title', 'Fitur Lengkap - ' . ($settings['site_name'] ?? 'Halalytics'))
@section('description', 'Jelajahi fitur Halalytics: halal confidence score, drug interaction checker, health score, dan integrasi data global.')
@section('keywords', 'fitur halalytics, halal confidence score, drug interaction checker, health score')
@section('canonical', route('features'))

@section('styles')
<style>
    .feature-hero {
        background:
            radial-gradient(900px 460px at 90% -20%, rgba(38,166,154,.24), transparent 62%),
            radial-gradient(900px 420px at 0% 0%, rgba(14,165,107,.24), transparent 60%),
            linear-gradient(180deg, #f5f9f7 0%, #eef8f5 100%);
    }
    .feature-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.05), 0 2px 4px -1px rgba(16, 185, 129, 0.03);
    }
    .feature-card:hover {
        transform: translateY(-8px);
        border-color: #10b981;
        box-shadow: 0 20px 40px -10px rgba(16, 185, 129, 0.15), 0 10px 20px -5px rgba(15, 23, 42, 0.1);
    }
    .phone-shell {
        background: linear-gradient(145deg, #101522, #0d1018);
        border: 4px solid #20283a;
        border-radius: 34px;
        transform-style: preserve-3d;
        transform: perspective(1200px) rotateY(-10deg) rotateX(5deg);
        box-shadow: 0 30px 60px rgba(0, 0, 0, .24);
    }
    .phone-shell .screen {
        border-radius: 26px;
        overflow: hidden;
        background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
    }
</style>
@endsection

@section('content')
<section class="feature-hero pt-24 pb-16 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-flex px-4 py-2 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wider">Feature Suite</span>
                <h1 class="mt-5 text-4xl md:text-5xl font-extrabold text-slate-900 leading-tight">Super App Kesehatan & Halal Terlengkap</h1>
                <p class="mt-4 text-lg text-slate-600 max-w-xl">
                    Scan barcode, cek status halal & nutrisi, deteksi interaksi obat, konsultasi AI, donor darah, dan kelola kesehatan keluarga — semua dalam satu aplikasi.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('download') }}" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-xl">Coba Aplikasi</a>
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center bg-white border border-slate-300 text-slate-700 font-semibold px-6 py-3 rounded-xl hover:bg-slate-50">Kembali ke Home</a>
                </div>
            </div>
            <div class="flex justify-center lg:justify-end">
                <div class="phone-shell w-[280px] h-[560px] p-3 shadow-2xl">
                    <div class="screen w-full h-full">
                        <div class="h-8 bg-slate-950 text-[11px] text-slate-300 px-4 flex items-center justify-between">
                            <span>9:41</span><span>Halalytics</span><span>5G</span>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="rounded-2xl border border-emerald-400/35 bg-emerald-500/10 p-3">
                                <p class="text-emerald-300 text-[11px] font-bold uppercase">Halal Analyzer</p>
                                <p class="text-white font-semibold text-sm mt-1">Ingredient Risk Matrix</p>
                            </div>
                            <div class="rounded-2xl border border-emerald-300/35 bg-primary/18 p-3">
                                <p class="text-emerald-200 text-[11px] font-bold uppercase">Drug Checker</p>
                                <p class="text-white font-semibold text-sm mt-1">Major / Moderate / Minor</p>
                            </div>
                            <div class="rounded-2xl border border-amber-400/35 bg-amber-500/10 p-3">
                                <p class="text-amber-300 text-[11px] font-bold uppercase">Health Score</p>
                                <p class="text-white font-semibold text-sm mt-1">Scoring nutrisi terstruktur</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['Scanner Produk Cerdas', 'Scan barcode untuk cek status halal, nutrisi, dan informasi lengkap produk dari database BPOM & Open Food Facts.'],
                ['HILDA AI Assistant', 'Tanya apa saja tentang kesehatan, gizi, obat, dan produk halal — dijawab instan oleh AI.'],
                ['Drug Interaction Checker', 'Deteksi potensi konflik antar obat dengan kategori risiko Major, Moderate, dan Minor.'],
                ['Skincare & Kosmetik Analyzer', 'Analisis keamanan dan kehalalan produk kecantikan serta kosmetik secara otomatis.'],
                ['Health Score & Nutrition Tracker', 'Pantau asupan gula, kalori, dan nutrisi harian dengan skor kesehatan personal.'],
                ['Donor Darah & Stok Darah', 'Cek ketersediaan stok darah, daftar donor, dan kelola jadwal donor dengan mudah.'],
                ['Konsultasi Ahli Gizi', 'Chat langsung dengan ahli gizi profesional untuk rekomendasi diet dan pola makan.'],
                ['Ensiklopedia Kesehatan', 'Database penyakit, obat, dan istilah medis lengkap dengan tinjauan titik kritis halal.'],
            ] as $item)
            <article class="feature-card p-6">
                <h3 class="text-xl font-extrabold text-slate-900">{{ $item[0] }}</h3>
                <p class="text-slate-600 text-sm mt-2 leading-relaxed">{{ $item[1] }}</p>
            </article>
            @endforeach
        </div>
    </div>
</section>

<section class="py-16 bg-slate-50 border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">Preview Fitur di HP</h2>
            <p class="text-slate-500 mt-2">Gunakan screenshot asli aplikasi kamu untuk memperkuat trust.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach([
                ['Scan Result UI', 'images/promo/ss-home-1.jpg'],
                ['Ingredient Analysis UI', 'images/promo/ss-home-2.jpg'],
                ['Health Insight UI', 'images/promo/ss-home-3.jpg'],
            ] as $ss)
            <div class="mx-auto">
                <div class="phone-shell w-[240px] h-[490px] p-3">
                    <div class="screen w-full h-full">
                        <div class="h-7 bg-slate-950"></div>
                        <img src="{{ asset($ss[1]) }}" alt="{{ $ss[0] }}" class="w-full h-[calc(100%-1.75rem)] object-cover"
                             onerror="this.onerror=null;this.parentElement.classList.add('bg-gradient-to-br','from-emerald-50','to-teal-50','flex','items-center','justify-center','text-center','p-4');this.parentElement.innerHTML='<span class=text-xs\\ text-slate-500>📱 Preview</span>'">
                    </div>
                </div>
                <p class="text-center mt-3 text-sm font-bold text-slate-700">{{ $ss[0] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">Siap Pakai untuk User Harian</h2>
        <p class="text-slate-600 mt-3 text-lg">Optimalkan kepercayaan user dengan pengalaman scan yang cepat, jelas, dan konsisten.</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('download') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-3 rounded-xl">Download Sekarang</a>
            <a href="{{ route('about') }}" class="bg-white border border-slate-300 text-slate-700 font-semibold px-8 py-3 rounded-xl hover:bg-slate-50">Pelajari Tim Kami</a>
        </div>
    </div>
</section>
@endsection
