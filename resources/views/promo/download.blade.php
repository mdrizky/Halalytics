@extends('promo.layout')
@section('title', 'Download APK - ' . ($settings['site_name'] ?? 'Halalytics'))

@section('styles')
<style>
    :root {
        --brand-primary: #004D40;
        --brand-secondary: #00C853;
        --brand-surface: #F8FCFB;
        --brand-text: #1A302D;
        --brand-accent: #26A69A;
    }

    .hero-container {
        padding-top: 140px;
        padding-bottom: 100px;
        background: radial-gradient(circle at 80% 20%, rgba(0, 200, 83, 0.1), transparent 40%);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 77, 64, 0.1);
        border-radius: 32px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
    }

    .device-shell {
        background: #0f172a;
        border: 8px solid #1e293b;
        border-radius: 40px;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        position: relative;
    }

    .device-screen {
        width: 100%;
        height: 100%;
        background: #fff;
    }

    .step-number {
        width: 32px;
        height: 32px;
        background: #004D40;
        color: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 14px;
    }
</style>
@endsection

@section('content')
<!-- ===== DOWNLOAD HERO ===== -->
<div class="hero-container px-6">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div>
            <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 px-4 py-2 rounded-full text-sm font-bold border border-emerald-100 mb-8">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Versi {{ $settings['app_version'] ?? '4.0.0' }} Kini Tersedia
            </div>
            <h1 class="text-5xl md:text-6xl font-black text-gray-900 leading-tight mb-8">
                Kesehatan Halal <br> Dalam <span class="text-emerald-600">Genggaman.</span>
            </h1>
            <p class="text-xl text-gray-500 mb-10 leading-relaxed max-w-xl">
                Scan barcode, cek status halal & nutrisi, deteksi interaksi obat, konsultasi dengan AI HILDA, dan pantau kesehatan keluarga. Download aplikasi resmi Halalytics sekarang.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ $settings['playstore_url'] ?? '#' }}" target="_blank"
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-black px-10 py-5 rounded-2xl shadow-xl shadow-emerald-600/30 transition-all text-center">
                    Download di Play Store
                </a>
                <a href="#guide" class="bg-white text-gray-700 border border-gray-200 font-bold px-10 py-5 rounded-2xl hover:bg-gray-50 transition-all text-center">
                    Panduan Instalasi
                </a>
            </div>
        </div>
        <div class="hidden lg:flex justify-end">
            <div class="device-shell w-[300px] h-[600px] rotate-3 shadow-2xl">
                <img src="{{ asset('images/promo/ss-home-1.png') }}" alt="App Home" class="w-full h-full object-cover">
            </div>
        </div>
    </div>
</div>

<!-- ===== INSTALLATION GUIDE ===== -->
<section id="guide" class="py-24 bg-white px-6">
    <div class="max-w-5xl mx-auto">
        <div class="glass-card p-10 md:p-16 border-l-8 border-l-emerald-600">
            <h2 class="text-4xl font-black text-gray-900 mb-10">Cara Install Halalytics</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="space-y-8">
                    @foreach([
                        ['Buka Google Play Store', 'Klik tombol download di atas atau cari "Halalytics" di Play Store.'],
                        ['Klik Tombol Install', 'Tunggu proses download dan instalasi selesai secara otomatis.'],
                        ['Buka & Daftar', 'Buka aplikasi dan buat akun untuk mulai menyimpan riwayat scan kamu.'],
                        ['Mulai Scan!', 'Scan barcode produk apa saja untuk mengetahui status halal dan kesehatannya.']
                    ] as $index => $step)
                    <div class="flex gap-6">
                        <div class="flex-shrink-0">
                            <div class="step-number">{{ $index + 1 }}</div>
                        </div>
                        <div>
                            <h4 class="font-black text-gray-900 text-lg mb-1">{{ $step[0] }}</h4>
                            <p class="text-gray-500 leading-relaxed">{{ $step[1] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="bg-gray-50 rounded-3xl p-8 flex items-center justify-center">
                    <div class="text-center">
                        <div class="text-6xl mb-6">📱</div>
                        <p class="font-bold text-gray-900 mb-2">Butuh Bantuan?</p>
                        <p class="text-sm text-gray-500 mb-6">Tim support kami siap membantu kendala teknis kamu.</p>
                        <a href="https://wa.me/{{ $settings['whatsapp_number'] ?? '628123456789' }}" class="text-emerald-600 font-black hover:underline">Hubungi via WhatsApp &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== APP GALLERY ===== -->
<section class="py-24 bg-gray-50 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-black text-gray-900 mb-4">Eksplorasi Fitur Aplikasi</h2>
            <p class="text-gray-500 font-bold max-w-2xl mx-auto">Tampilan antarmuka yang bersih dan modern dirancang untuk kenyamanan navigasi Anda dalam memantau kesehatan.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            @foreach([
                ['ss-home-1.png', 'Dashboard Utama', 'Pantau skor kesehatan, streak harian, dan riwayat scan.'],
                ['ss-home-2.png', 'AI Scanner', 'Scan barcode untuk analisis halal, gizi & interaksi obat.'],
                ['ss-home-3.png', 'Health Insights', 'Wawasan personal dari AI — diet, risiko, dan rekomendasi.']
            ] as $img)
            <div class="group">
                <div class="device-shell w-full aspect-[9/18] mb-8 group-hover:-translate-y-4 transition-transform duration-500">
                    <img src="{{ asset('images/promo/' . $img[0]) }}" alt="{{ $img[1] }}" class="w-full h-full object-cover">
                </div>
                <div class="text-center">
                    <h4 class="font-black text-gray-900 text-xl mb-2">{{ $img[1] }}</h4>
                    <p class="text-gray-500 text-sm font-bold">{{ $img[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ===== FINAL CALL TO ACTION ===== -->
<section class="py-24 bg-white px-6">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-5xl font-black text-gray-900 leading-tight mb-8">Siap Hidup Lebih Sehat <br> dan <span class="text-emerald-600">Terjaga Halalnya?</span></h2>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ $settings['playstore_url'] ?? '#' }}" class="bg-gray-900 text-white font-black px-12 py-5 rounded-2xl hover:bg-black transition-all">
                Download Gratis Sekarang
            </a>
            <a href="{{ route('features') }}" class="bg-gray-100 text-gray-700 font-black px-12 py-5 rounded-2xl hover:bg-gray-200 transition-all">
                Pelajari Fitur &rarr;
            </a>
        </div>
    </div>
</section>
@endsection
