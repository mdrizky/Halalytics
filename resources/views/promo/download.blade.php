@extends('promo.layout')
@section('title', 'Download APK - ' . ($settings['site_name'] ?? 'HalalScan AI'))

@section('content')
<div class="py-24 bg-gray-50 flex flex-col items-center justify-center min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">
        
        <div class="inline-flex items-center justify-center space-x-2 bg-green-100 text-green-700 font-semibold px-4 py-2 rounded-full mb-4">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            <span>Versi {{ $settings['app_version'] ?? '1.0.0' }} Tersedia</span>
        </div>

        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
            Download HalalScan AI Sekarang
        </h1>
        <p class="text-xl text-gray-500 mb-8 max-w-2xl mx-auto">
            Mulailah mengontrol kesehatan dan kehalalan konsumsi Anda dengan asisten cerdas yang muat di saku Anda.
        </p>

        <a href="{{ $settings['playstore_url'] ?? '#' }}" target="_blank"
           class="inline-flex items-center justify-center space-x-3 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-bold px-10 py-5 rounded-2xl shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1 text-xl">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                <path d="M3 20.5v-17c0-.83.94-1.3 1.6-.8l14 8.5a1 1 0 010 1.6l-14 8.5c-.66.5-1.6.03-1.6-.8z"/>
            </svg>
            <span>Dapatkan di Google Play</span>
        </a>

        <div class="mt-16 bg-white rounded-2xl p-8 shadow-sm border border-gray-100 text-left">
            <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Cara Install Aplikasi Android:</h3>
            <ol class="list-decimal list-inside space-y-3 text-gray-600">
                <li>Klik tombol "Dapatkan di Google Play" di atas.</li>
                <li>Anda akan diarahkan ke halaman resmi Play Store kami.</li>
                <li>Pilih "Install" dan tunggu proses unduh selesai.</li>
                <li>Buka aplikasi, daftar atau login, dan mulai scan!</li>
            </ol>
        </div>
    </div>
</div>
@endsection
