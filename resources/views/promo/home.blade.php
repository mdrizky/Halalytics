@extends('promo.layout')
@section('title', ($settings['site_name'] ?? 'HalalScan AI') . ' - AI Halal & Health Scanner')
@section('description', 'HalalScan AI membantu cek status halal, interaksi obat, dan health score produk dari barcode atau foto kemasan.')
@section('keywords', 'halal scanner, cek halal produk, interaksi obat, health score, BPOM, aplikasi halal')
@section('canonical', route('home'))

@section('styles')
<style>
    :root {
        --brand-ink: #163832;
        --brand-deep: #00372e;
        --brand-main: #004D40;
        --brand-soft: #E0F2F1;
        --brand-alt: #26A69A;
        --surface: #F4F9F8;
        --line: #dbe3ea;
    }

    body {
        background: var(--surface);
    }

    .home-hero {
        background:
            radial-gradient(1200px 500px at 100% -20%, rgba(38,166,154,.30), transparent 60%),
            radial-gradient(900px 500px at -5% 5%, rgba(224,242,241,.28), transparent 55%),
            linear-gradient(145deg, #00372e 0%, #004D40 38%, #26A69A 100%);
    }

    .glass {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
    }

    .pro-card {
        border: 1px solid var(--line);
        border-radius: 20px;
        background: #fff;
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .pro-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 38px rgba(2, 24, 20, 0.12);
    }

    .fade-up {
        opacity: 0;
        transform: translateY(18px);
        animation: fadeUp .65s ease forwards;
    }

    .delay-1 { animation-delay: .08s; }
    .delay-2 { animation-delay: .16s; }
    .delay-3 { animation-delay: .24s; }

    .phone-frame {
        background: linear-gradient(150deg, #10342e, #0f1d1a);
        border: 4px solid rgba(255,255,255,0.22);
        border-radius: 38px;
        box-shadow: 0 26px 60px rgba(0, 0, 0, .4);
        transform: perspective(1200px) rotateY(-10deg) rotateX(4deg);
    }

    .phone-screen {
        border-radius: 30px;
        overflow: hidden;
        background: linear-gradient(180deg, #0f172a 0%, #111827 100%);
    }

    .ss-preview {
        background: linear-gradient(135deg, #dff6eb, #ffffff);
        border: 1px dashed #9fb2c9;
    }
    .article-stack {
        box-shadow: 0 26px 60px rgba(0, 77, 64, 0.14);
        transform-style: preserve-3d;
    }

    @keyframes fadeUp {
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<section class="home-hero text-white pt-12 pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
            <div>
                <div class="inline-flex items-center gap-2 glass px-4 py-2 rounded-full text-sm font-semibold mb-6 fade-up">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    AI Halal + Drug Safety + Health Score
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-[1.05] fade-up delay-1">
                    {{ $settings['hero_headline'] ?? 'Scan produk lebih pintar, halal lebih pasti.' }}
                </h1>

                <p class="text-white/80 text-lg mt-6 max-w-xl fade-up delay-2">
                    {{ $settings['hero_subheadline'] ?? 'Satu aplikasi untuk cek status halal, interaksi obat, dan kualitas nutrisi produk secara real-time dari barcode atau foto kemasan.' }}
                </p>

                <div class="mt-8 flex flex-col sm:flex-row gap-4 fade-up delay-3">
                    <a href="{{ $settings['playstore_url'] ?? route('download') }}" target="_blank"
                       class="inline-flex items-center justify-center gap-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-8 py-4 rounded-2xl transition-all shadow-lg shadow-emerald-900/30">
                        <span>Download di Google Play</span>
                    </a>
                    <a href="{{ route('features') }}"
                       class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 font-semibold px-8 py-4 rounded-2xl border border-white/30 transition-all">
                        <span>Lihat Fitur Lengkap</span>
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-10">
                    <div class="glass rounded-2xl p-4 text-center fade-up">
                        <p class="text-3xl font-black">1.9B+</p>
                        <p class="text-xs text-white/70 mt-1">Muslim Audience</p>
                    </div>
                    <div class="glass rounded-2xl p-4 text-center fade-up delay-1">
                        <p class="text-3xl font-black">10+</p>
                        <p class="text-xs text-white/70 mt-1">Connected Sources</p>
                    </div>
                    <div class="glass rounded-2xl p-4 text-center fade-up delay-2">
                        <p class="text-3xl font-black">24/7</p>
                        <p class="text-xs text-white/70 mt-1">AI Analysis</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-center lg:justify-end">
                <div class="relative fade-up delay-2">
                    <div class="phone-frame w-[290px] h-[590px] p-3">
                        <div class="phone-screen w-full h-full">
                            <div class="h-8 bg-slate-950 text-[11px] text-slate-200 px-4 flex items-center justify-between">
                                <span>9:41</span>
                                <span>HalalScan AI</span>
                                <span>5G</span>
                            </div>
                            <div class="p-4 space-y-3">
                                <div class="bg-emerald-500/15 border border-emerald-400/35 rounded-2xl p-3">
                                    <p class="text-[11px] text-emerald-300 font-bold uppercase">Halal Confidence</p>
                                    <p class="text-white text-lg font-bold mt-1">Indomie Goreng</p>
                                    <p class="text-emerald-200 text-xs">96% Verified</p>
                                </div>
                                <div class="bg-primary/20 border border-emerald-300/35 rounded-2xl p-3">
                                    <p class="text-[11px] text-emerald-200 font-bold uppercase">Drug Interaction</p>
                                    <p class="text-white text-sm font-semibold mt-1">Paracetamol + Caffeine</p>
                                    <p class="text-emerald-100 text-xs">No major conflict</p>
                                </div>
                                <div class="bg-amber-500/15 border border-amber-400/35 rounded-2xl p-3">
                                    <p class="text-[11px] text-amber-300 font-bold uppercase">Health Score</p>
                                    <p class="text-white text-3xl font-black leading-none mt-1">72</p>
                                    <p class="text-amber-200 text-xs">Moderate Nutrition</p>
                                </div>
                                <button class="w-full mt-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl py-2.5 text-sm font-bold">
                                    Scan Barcode
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -left-14 top-20 bg-white rounded-2xl p-3 w-28 shadow-xl text-center">
                        <p class="text-2xl">🕌</p>
                        <p class="text-xs font-bold text-slate-700">Halal Cert</p>
                        <p class="text-[11px] text-emerald-600 font-semibold">Verified</p>
                    </div>
                    <div class="absolute -right-10 bottom-24 bg-white rounded-2xl p-3 w-28 shadow-xl text-center">
                        <p class="text-2xl">💊</p>
                        <p class="text-xs font-bold text-slate-700">Drug Safety</p>
                        <p class="text-[11px] text-primary font-semibold">Realtime</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">Masalah yang Sering Kamu Hadapi?</h2>
            <p class="text-slate-500 mt-3 text-lg">Kami desain sistem yang langsung menjawab masalah inti user.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['Bingung bahan halal atau tidak?', 'Analisis bahan kritis dan aditif syubhat secara otomatis.'],
                ['Tidak paham kandungan produk?', 'Setiap bahan dijelaskan AI dengan bahasa yang mudah.'],
                ['Khawatir interaksi obat?', 'Deteksi kombinasi obat berisiko dengan level peringatan.'],
                ['Susah baca informasi nutrisi?', 'Skor kesehatan diringkas jadi angka jelas dan cepat dipahami.'],
            ] as $item)
            <div class="pro-card p-6">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-black mb-4">!</div>
                <h3 class="font-bold text-lg text-slate-800 leading-snug">{{ $item[0] }}</h3>
                <p class="text-sm text-slate-500 mt-2">{{ $item[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-20 bg-white border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">Fitur Kunci HalalScan AI</h2>
                <p class="text-slate-500 mt-2">Stack fitur real-world, bukan sekadar tampilan.</p>
            </div>
            <a href="{{ route('features') }}" class="hidden md:inline-flex text-sm font-bold text-emerald-700 hover:text-emerald-800">Lihat Semua Fitur</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['Halal Confidence Score', 'Deteksi bahan haram/syubhat dengan skor kepercayaan dan alasan.'],
                ['Drug Interaction Checker', 'Cek konflik antar-obat dan efek terhadap kondisi kesehatan user.'],
                ['Health Score Engine', 'Rangkum kualitas nutrisi produk dalam satu skor yang mudah dibaca.'],
                ['Ingredient Explorer', 'Klik bahan tertentu untuk lihat fungsi, risiko, dan status halal.'],
                ['Smart Reminder Obat', 'Jadwal minum obat dan pengingat adaptif berdasarkan aktivitas.'],
                ['Global Data Federation', 'Integrasi BPOM, Open Food Facts, OpenFDA, dan sumber lainnya.'],
            ] as $f)
            <div class="pro-card p-6">
                <h3 class="font-bold text-lg text-slate-900">{{ $f[0] }}</h3>
                <p class="text-slate-500 text-sm mt-2 leading-relaxed">{{ $f[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">Screenshot Aplikasi di HP</h2>
            <p class="text-slate-500 mt-2">Section khusus untuk SS halaman home yang kamu ambil dari aplikasi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach([
                ['Home Overview', 'images/promo/ss-home-1.jpg'],
                ['Scan Result', 'images/promo/ss-home-2.jpg'],
                ['Health & Halal Detail', 'images/promo/ss-home-3.jpg'],
            ] as $ss)
            <div class="mx-auto">
                <div class="phone-frame w-[250px] h-[500px] p-3">
                    <div class="phone-screen w-full h-full">
                        <div class="h-7 bg-slate-950"></div>
                        <img src="{{ asset($ss[1]) }}" alt="{{ $ss[0] }}" class="w-full h-[calc(100%-1.75rem)] object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="ss-preview h-[calc(100%-1.75rem)] hidden items-center justify-center text-center px-5">
                            <div>
                                <p class="text-sm font-bold text-slate-700">{{ $ss[0] }}</p>
                                <p class="text-xs text-slate-500 mt-1">Taruh file screenshot di<br><code>public/{{ $ss[1] }}</code></p>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center mt-3 text-sm font-semibold text-slate-700">{{ $ss[0] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-20 bg-white border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">Cara Kerja dalam 3 Langkah</h2>
            <p class="text-slate-500 mt-2">Cepat, praktis, langsung actionable.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['1', 'Scan produk via barcode atau foto kemasan.'],
                ['2', 'AI memproses data lintas sumber secara real-time.'],
                ['3', 'Lihat status halal, kesehatan, dan rekomendasi aman.'],
            ] as $step)
            <div class="pro-card p-7 text-center">
                <div class="w-11 h-11 rounded-full bg-emerald-600 text-white font-black flex items-center justify-center mx-auto">{{ $step[0] }}</div>
                <p class="mt-4 text-slate-700 font-semibold">{{ $step[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

@if($latestBlogs->count() > 0 || ($externalArticles->count() ?? 0) > 0)
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900">Artikel Terbaru</h2>
                <p class="text-slate-500 mt-1">Artikel lokal dan feed eksternal kesehatan yang tampil langsung di website promosi.</p>
            </div>
            <a href="{{ route('blog.index') }}" class="text-emerald-700 hover:text-emerald-800 font-bold">Lihat Semua</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-7">
            @foreach($latestBlogs as $blog)
            <article class="pro-card article-stack overflow-hidden">
                @if($blog->image)
                <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="w-full h-48 object-cover">
                @else
                <div class="h-48 bg-gradient-to-br from-[#E0F2F1] to-white"></div>
                @endif
                <div class="p-6">
                    <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700">
                        {{ $blog->category ?? 'Edukasi' }}
                    </span>
                    <h3 class="mt-3 font-extrabold text-slate-800 leading-snug">{{ $blog->title }}</h3>
                    <p class="text-slate-500 text-sm mt-2">{{ $blog->excerpt }}</p>
                    <a href="{{ route('blog.show', $blog->slug) }}" class="inline-flex mt-4 text-sm font-bold text-emerald-700 hover:text-emerald-800">
                        Baca Selengkapnya
                    </a>
                </div>
            </article>
            @endforeach

            @foreach($externalArticles ?? collect() as $article)
            <article class="pro-card article-stack overflow-hidden">
                @if(!empty($article['image_url']))
                <img src="{{ $article['image_url'] }}" alt="{{ $article['title'] }}" class="w-full h-48 object-cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                @endif
                <div class="h-48 bg-gradient-to-br from-[#E0F2F1] to-white items-center justify-center hidden">
                    <span class="text-sm font-bold text-[#004D40]">External Health Feed</span>
                </div>
                <div class="p-6">
                    <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-[#E0F2F1] text-[#004D40]">
                        {{ $article['source'] }}
                    </span>
                    <h3 class="mt-3 font-extrabold text-slate-800 leading-snug">{{ $article['title'] }}</h3>
                    <p class="text-slate-500 text-sm mt-2">{{ $article['excerpt'] }}</p>
                    <a href="{{ $article['source_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex mt-4 text-sm font-bold text-[#004D40] hover:text-[#00372e]">
                        Buka Sumber
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="py-20 home-hero text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold">Mulai Scan Lebih Cerdas Sekarang</h2>
        <p class="text-white/80 text-lg mt-4">Gratis, cepat, dan siap pakai untuk keputusan produk yang lebih aman.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
            <a href="{{ $settings['playstore_url'] ?? route('download') }}" target="_blank"
               class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-8 py-4 rounded-2xl">
                Download di Google Play
            </a>
            <a href="{{ route('download') }}"
               class="inline-flex items-center justify-center bg-white/10 hover:bg-white/20 font-semibold px-8 py-4 rounded-2xl border border-white/30">
                Lihat Panduan Download
            </a>
        </div>
        <p class="text-white/60 text-sm mt-4">Versi {{ $settings['app_version'] ?? '1.0.0' }} • Android 7.0+</p>
    </div>
</section>
@endsection
