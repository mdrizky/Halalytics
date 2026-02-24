@extends('promo.layout')
@section('title', ($settings['site_name'] ?? 'HalalScan AI') . ' - AI Halal & Health Scanner')

@section('content')

{{-- ===== HERO SECTION ===== --}}
<section class="gradient-bg text-white min-h-screen flex items-center pt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <!-- Teks Hero -->
            <div class="space-y-6">
                <div class="inline-flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 text-sm font-medium">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span>AI-Powered Halal Intelligence</span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight">
                    {{ $settings['hero_headline'] ?? 'Scan. Analyze. Stay Safe.' }}
                </h1>

                <p class="text-lg md:text-xl text-white/80 leading-relaxed max-w-lg">
                    {{ $settings['hero_subheadline'] ?? 'AI-powered halal & health analyzer. Instantly detect ingredients, drug interactions, and health scores from any product barcode.' }}
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ $settings['playstore_url'] ?? route('download') }}" target="_blank"
                       class="inline-flex items-center justify-center space-x-3 bg-white text-green-700 font-bold px-8 py-4 rounded-2xl hover:bg-green-50 transition-all shadow-lg text-lg">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 20.5v-17c0-.83.94-1.3 1.6-.8l14 8.5a1 1 0 010 1.6l-14 8.5c-.66.5-1.6.03-1.6-.8z"/>
                        </svg>
                        <span>Download di Google Play</span>
                    </a>
                    <a href="{{ route('features') }}"
                       class="inline-flex items-center justify-center space-x-2 bg-white/20 hover:bg-white/30 text-white font-semibold px-8 py-4 rounded-2xl transition-all text-lg border border-white/30">
                        <span>Lihat Fitur</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 pt-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold">1.9B+</div>
                        <div class="text-white/70 text-sm">Muslim Worldwide</div>
                    </div>
                    <div class="text-center border-x border-white/20">
                        <div class="text-3xl font-bold">10+</div>
                        <div class="text-white/70 text-sm">Data Sources</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">Free</div>
                        <div class="text-white/70 text-sm">To Download</div>
                    </div>
                </div>
            </div>

            <!-- Mockup HP -->
            <div class="flex justify-center lg:justify-end">
                <div class="relative">
                    <div class="w-64 h-[500px] bg-gray-900 rounded-[40px] p-3 shadow-2xl border-4 border-gray-700">
                        <div class="w-full h-full bg-gray-800 rounded-[32px] overflow-hidden flex flex-col">
                            <!-- Status bar -->
                            <div class="bg-gray-900 px-4 py-2 flex justify-between text-white text-xs">
                                <span>9:41</span>
                                <span>HalalScan AI</span>
                                <span>●●●</span>
                            </div>
                            <!-- App content preview -->
                            <div class="flex-1 bg-gradient-to-b from-gray-800 to-gray-900 p-4 space-y-3">
                                <div class="bg-green-600/20 border border-green-500/30 rounded-2xl p-3">
                                    <div class="text-green-400 text-xs font-semibold mb-1">✓ HALAL TERVERIFIKASI</div>
                                    <div class="text-white font-bold">Indomie Goreng</div>
                                    <div class="text-gray-400 text-xs">Confidence: 94%</div>
                                </div>
                                <div class="bg-blue-600/20 border border-blue-500/30 rounded-2xl p-3">
                                    <div class="text-blue-400 text-xs font-semibold mb-1">💊 DRUG INFO</div>
                                    <div class="text-white font-bold">Paracetamol 500mg</div>
                                    <div class="text-gray-400 text-xs">Tidak ada interaksi berbahaya</div>
                                </div>
                                <div class="bg-orange-600/20 border border-orange-500/30 rounded-2xl p-3">
                                    <div class="text-orange-400 text-xs font-semibold mb-1">📊 HEALTH SCORE</div>
                                    <div class="flex items-center space-x-2">
                                        <div class="text-3xl font-bold text-white">72</div>
                                        <div class="text-gray-400 text-xs">/ 100<br>Cukup Baik</div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="inline-block bg-green-600 rounded-full px-4 py-2 text-white text-xs font-semibold">
                                        📷 Scan Barcode
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Floating badges -->
                    <div class="absolute -left-8 top-16 bg-white rounded-2xl shadow-xl p-3 text-center w-20">
                        <div class="text-2xl">🕌</div>
                        <div class="text-xs font-bold text-gray-700">Halal</div>
                        <div class="text-xs text-green-600">Check</div>
                    </div>
                    <div class="absolute -right-8 bottom-20 bg-white rounded-2xl shadow-xl p-3 text-center w-20">
                        <div class="text-2xl">💊</div>
                        <div class="text-xs font-bold text-gray-700">Drug</div>
                        <div class="text-xs text-blue-600">Safety</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== PROBLEM SECTION ===== --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Masalah yang Sering Kamu Hadapi?
        </h2>
        <p class="text-gray-500 text-lg mb-12 max-w-2xl mx-auto">
            Jangan khawatir, kami punya solusinya.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['❌', 'Bingung bahan halal atau tidak?', 'Halal analyzer kami deteksi semua bahan kritis secara otomatis.'],
                ['❌', 'Tidak paham kandungan produk?', 'AI kami jelaskan setiap bahan dalam bahasa yang mudah dipahami.'],
                ['❌', 'Khawatir interaksi obat?', 'Drug interaction checker langsung deteksi kombinasi berbahaya.'],
                ['❌', 'Susah baca informasi nutrisi?', 'Health score sistem kami rangkum nilai gizi jadi angka simpel.'],
            ] as $item)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-left card-hover">
                <div class="text-3xl mb-3">{{ $item[0] }}</div>
                <h3 class="font-bold text-gray-800 mb-2">{{ $item[1] }}</h3>
                <p class="text-gray-500 text-sm">{{ $item[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== FITUR UTAMA ===== --}}
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Fitur Unggulan</h2>
            <p class="text-gray-500 text-lg">Semua yang kamu butuhkan dalam satu aplikasi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach([
                ['🕌', 'Halal Confidence Score', 'Deteksi bahan syubhat & haram secara otomatis dengan skor kepercayaan 0-100%.', 'bg-green-50', 'text-green-600'],
                ['💊', 'Drug Interaction Checker', 'Cek interaksi antar obat dan dapatkan peringatan tingkat risiko: minor, moderate, major.', 'bg-blue-50', 'text-blue-600'],
                ['📊', 'Health Score System', 'Skor kesehatan produk berdasarkan NutriScore, kadar gula, lemak, dan aditif.', 'bg-orange-50', 'text-orange-600'],
                ['🔬', 'Ingredient Deep Analysis', 'Klik satu bahan, lihat fungsi, risiko, dan status halalnya secara detail.', 'bg-purple-50', 'text-purple-600'],
                ['⏰', 'Smart Reminder Obat', 'Reminder minum obat otomatis berdasarkan jadwal yang kamu set sendiri.', 'bg-red-50', 'text-red-600'],
                ['🌍', 'Database Internasional', 'Terhubung ke BPOM, Open Food Facts, OpenFDA, dan database global lainnya.', 'bg-indigo-50', 'text-indigo-600'],
            ] as $feat)
            <div class="rounded-2xl p-6 {{ $feat[3] }} card-hover border border-gray-100">
                <div class="text-4xl mb-4">{{ $feat[0] }}</div>
                <h3 class="font-bold text-gray-800 text-lg mb-2">{{ $feat[1] }}</h3>
                <p class="text-gray-600 text-sm leading-relaxed">{{ $feat[2] }}</p>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('features') }}" class="inline-flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-4 rounded-2xl transition-colors">
                <span>Lihat Semua Fitur</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- ===== HOW IT WORKS ===== --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Cara Kerjanya</h2>
            <p class="text-gray-500 text-lg">3 langkah mudah untuk hasil yang akurat.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach([
                ['1', '📷', 'Scan Produk', 'Arahkan kamera ke barcode produk atau foto label kemasan langsung.'],
                ['2', '🤖', 'AI Analisis', 'Sistem cek ke BPOM, Open Food Facts, OpenFDA, dan database global secara otomatis.'],
                ['3', '✅', 'Lihat Hasil', 'Dapatkan Halal Score, Health Score, dan informasi keamanan dalam hitungan detik.'],
            ] as $step)
            <div class="relative bg-white rounded-2xl p-8 shadow-sm border border-gray-100 card-hover text-center">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                    {{ $step[0] }}
                </div>
                <div class="text-5xl mt-4 mb-4">{{ $step[1] }}</div>
                <h3 class="font-bold text-gray-800 text-xl mb-2">{{ $step[2] }}</h3>
                <p class="text-gray-500 text-sm">{{ $step[3] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== BLOG TERBARU ===== --}}
@if($latestBlogs->count() > 0)
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Artikel Terbaru</h2>
                <p class="text-gray-500 mt-1">Edukasi halal, kesehatan, dan keamanan produk.</p>
            </div>
            <a href="{{ route('blog.index') }}" class="text-green-600 hover:text-green-700 font-semibold flex items-center space-x-1">
                <span>Lihat Semua</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($latestBlogs as $blog)
            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
                @if($blog->image)
                <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="w-full h-48 object-cover">
                @else
                <div class="w-full h-48 bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center">
                    <span class="text-5xl">📝</span>
                </div>
                @endif
                <div class="p-6">
                    <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full mb-3">
                        {{ $blog->category ?? 'Edukasi' }}
                    </span>
                    <h3 class="font-bold text-gray-800 text-lg mb-2 line-clamp-2">{{ $blog->title }}</h3>
                    <p class="text-gray-500 text-sm mb-4">{{ $blog->excerpt }}</p>
                    <a href="{{ route('blog.show', $blog->slug) }}" class="text-green-600 hover:text-green-700 font-semibold text-sm flex items-center space-x-1">
                        <span>Baca Selengkapnya</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== CTA DOWNLOAD ===== --}}
<section class="py-20 gradient-bg text-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
            Mulai Scan Lebih Cerdas Sekarang
        </h2>
        <p class="text-white/80 text-lg mb-8 max-w-xl mx-auto">
            Gratis. Tersedia di Android. Database global. AI-powered analysis.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ $settings['playstore_url'] ?? route('download') }}" target="_blank"
               class="inline-flex items-center justify-center space-x-3 bg-white text-green-700 font-bold px-8 py-4 rounded-2xl hover:bg-green-50 transition-all shadow-lg text-lg">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 20.5v-17c0-.83.94-1.3 1.6-.8l14 8.5a1 1 0 010 1.6l-14 8.5c-.66.5-1.6.03-1.6-.8z"/>
                </svg>
                <span>Download di Google Play</span>
            </a>
            <a href="{{ route('download') }}"
               class="inline-flex items-center justify-center space-x-2 bg-white/20 hover:bg-white/30 text-white font-semibold px-8 py-4 rounded-2xl border border-white/30 transition-all text-lg">
                <span>Lihat Cara Download</span>
            </a>
        </div>
        <p class="text-white/60 text-sm mt-4">
            Versi {{ $settings['app_version'] ?? '1.0.0' }} • Android 7.0+
        </p>
    </div>
</section>

@endsection
