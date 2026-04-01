@extends('promo.layout')
@section('title', 'Fitur Lengkap - ' . ($settings['site_name'] ?? 'HalalScan AI'))
@section('description', 'Jelajahi fitur HalalScan AI: halal confidence score, drug interaction checker, health score, dan integrasi data global.')
@section('keywords', 'fitur halalscan ai, halal confidence score, drug interaction checker, health score')
@section('canonical', route('features'))

@section('styles')
<style>
    .feature-hero {
        background:
            radial-gradient(900px 460px at 90% -20%, rgba(31,79,214,.24), transparent 62%),
            radial-gradient(900px 420px at 0% 0%, rgba(14,165,107,.24), transparent 60%),
            linear-gradient(180deg, #f5f9f7 0%, #f5f8ff 100%);
    }
    .feature-card {
        background: #fff;
        border: 1px solid #dbe3ea;
        border-radius: 20px;
        transition: transform .22s ease, box-shadow .22s ease;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 34px rgba(15, 23, 42, .10);
    }
    .phone-shell {
        background: linear-gradient(145deg, #101522, #0d1018);
        border: 4px solid #20283a;
        border-radius: 34px;
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
                <h1 class="mt-5 text-4xl md:text-5xl font-extrabold text-slate-900 leading-tight">Fitur Lengkap untuk Keputusan Produk yang Lebih Aman</h1>
                <p class="mt-4 text-lg text-slate-600 max-w-xl">
                    HalalScan AI menggabungkan analisis halal, nutrisi, dan interaksi obat dalam satu alur yang cepat dipakai user.
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
                            <span>9:41</span><span>HalalScan AI</span><span>5G</span>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="rounded-2xl border border-emerald-400/35 bg-emerald-500/10 p-3">
                                <p class="text-emerald-300 text-[11px] font-bold uppercase">Halal Analyzer</p>
                                <p class="text-white font-semibold text-sm mt-1">Ingredient Risk Matrix</p>
                            </div>
                            <div class="rounded-2xl border border-blue-400/35 bg-blue-500/10 p-3">
                                <p class="text-blue-300 text-[11px] font-bold uppercase">Drug Checker</p>
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
                ['Halal Confidence Score', 'Memeriksa bahan kritis, aditif, dan istilah teknis untuk status halal lebih jelas.'],
                ['Drug Interaction Checker', 'Deteksi potensi konflik obat dan berikan kategori risiko yang mudah dipahami.'],
                ['Health Score System', 'Rangkum kualitas gizi produk agar keputusan konsumsi lebih cepat.'],
                ['Ingredient Deep Dive', 'User bisa telusuri tiap ingredient: fungsi, risiko, dan konteks halal.'],
                ['Smart Medicine Reminder', 'Jadwal minum obat dengan kontrol reminder yang fleksibel.'],
                ['Data Integrations', 'Terhubung ke BPOM, Open Food Facts, OpenFDA, dan sumber relevan lain.'],
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
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden w-full h-[calc(100%-1.75rem)] bg-gradient-to-br from-emerald-50 to-blue-50 items-center justify-center text-center px-4 text-xs text-slate-500">
                            Tambahkan SS: <code class="mx-1">public/{{ $ss[1] }}</code>
                        </div>
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
