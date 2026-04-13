@extends('promo.layout')
@section('title', 'Download APK - ' . ($settings['site_name'] ?? 'HalalScan AI'))
@section('description', 'Download APK HalalScan AI untuk cek halal produk, interaksi obat, dan health score langsung dari ponsel Android.')
@section('keywords', 'download halalscan ai, apk halal, cek halal android')
@section('canonical', route('download'))

@section('styles')
<style>
    .download-hero {
        background:
            radial-gradient(900px 420px at 100% -10%, rgba(38,166,154,.22), transparent 62%),
            radial-gradient(800px 420px at -5% 0%, rgba(224,242,241,.24), transparent 60%),
            linear-gradient(140deg, #0b3a32 0%, #004D40 46%, #13695e 100%);
    }
    .download-card {
        background: #fff;
        border: 1px solid #dbe3ea;
        border-radius: 20px;
    }
    .device-shell {
        background: linear-gradient(145deg, #0f1420, #0c1018);
        border: 4px solid #1f2738;
        border-radius: 34px;
        transform-style: preserve-3d;
        transform: perspective(1400px) rotateY(-14deg) rotateX(8deg);
        box-shadow: 0 40px 70px rgba(0, 0, 0, .24);
    }
    .device-screen {
        border-radius: 26px;
        overflow: hidden;
        background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
    }
</style>
@endsection

@section('content')
<section class="download-hero text-white pt-24 pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-flex items-center gap-2 bg-white/15 border border-white/30 rounded-full px-4 py-2 text-xs font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Versi {{ $settings['app_version'] ?? '1.0.0' }} tersedia
                </span>
                <h1 class="mt-5 text-4xl md:text-5xl font-extrabold leading-tight">Download HalalScan AI dan Mulai Scan Sekarang</h1>
                <p class="mt-4 text-lg text-white/80 max-w-xl">
                    Akses fitur halal check, interaksi obat, dan health score langsung dari ponsel kamu.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="{{ $settings['playstore_url'] ?? '#' }}" target="_blank"
                       class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-8 py-4 rounded-2xl shadow-lg shadow-black/30">
                        Dapatkan di Google Play
                    </a>
                    <a href="{{ route('features') }}"
                       class="inline-flex items-center justify-center bg-white/10 hover:bg-white/20 border border-white/30 font-semibold px-8 py-4 rounded-2xl">
                        Lihat Fitur
                    </a>
                </div>
            </div>
            <div class="flex justify-center lg:justify-end">
                <div class="device-shell w-[280px] h-[560px] p-3 shadow-2xl">
                    <div class="device-screen w-full h-full">
                        <div class="h-8 bg-slate-950"></div>
                        <img src="{{ asset('images/promo/ss-home-1.jpg') }}" alt="App Preview" class="w-full h-[calc(100%-2rem)] object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden h-[calc(100%-2rem)] items-center justify-center text-center px-6 bg-gradient-to-br from-emerald-50 to-blue-50">
                            <div>
                                <p class="text-sm font-bold text-slate-700">Preview Screenshot</p>
                                <p class="text-xs text-slate-500 mt-1">Tambahkan file di <code>public/images/promo/ss-home-1.jpg</code></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="download-card p-8 md:p-10">
            <h2 class="text-3xl font-extrabold text-slate-900">Panduan Install Android</h2>
            <ol class="mt-6 space-y-4 text-slate-600">
                <li><span class="font-bold text-slate-800">1.</span> Klik tombol <b>Dapatkan di Google Play</b>.</li>
                <li><span class="font-bold text-slate-800">2.</span> Pastikan akun Google kamu aktif pada Play Store.</li>
                <li><span class="font-bold text-slate-800">3.</span> Tekan <b>Install</b> dan tunggu proses selesai.</li>
                <li><span class="font-bold text-slate-800">4.</span> Buka aplikasi, login/daftar, lalu mulai scan produk.</li>
            </ol>
        </div>
    </div>
</section>

<section class="py-16 bg-slate-50 border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">Galeri Screenshot HP</h2>
            <p class="text-slate-500 mt-2">Tempat paling pas untuk menampilkan SS halaman home dari aplikasi kamu.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach([
                ['images/promo/ss-home-1.jpg', 'Home'],
                ['images/promo/ss-home-2.jpg', 'Scan Result'],
                ['images/promo/ss-home-3.jpg', 'Detail Insight'],
            ] as $item)
            <div class="mx-auto">
                <div class="device-shell w-[235px] h-[470px] p-3">
                    <div class="device-screen w-full h-full">
                        <div class="h-7 bg-slate-950"></div>
                        <img src="{{ asset($item[0]) }}" alt="{{ $item[1] }}" class="w-full h-[calc(100%-1.75rem)] object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden h-[calc(100%-1.75rem)] items-center justify-center text-center px-4 bg-gradient-to-br from-emerald-50 to-blue-50 text-xs text-slate-500">
                            Taruh file di <code class="mx-1">public/{{ $item[0] }}</code>
                        </div>
                    </div>
                </div>
                <p class="text-center mt-3 text-sm font-bold text-slate-700">{{ $item[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">Butuh Bantuan Sebelum Install?</h2>
        <p class="text-slate-600 mt-3">Cek halaman fitur atau hubungi kami dari form kontak di website.</p>
        <div class="mt-7 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('features') }}" class="bg-[#004D40] text-white font-bold px-8 py-3 rounded-xl hover:bg-[#00372e]">Pelajari Fitur</a>
            <a href="{{ route('about') }}" class="border border-slate-300 text-slate-700 font-semibold px-8 py-3 rounded-xl hover:bg-slate-50">Tentang Kami</a>
        </div>
    </div>
</section>
@endsection
