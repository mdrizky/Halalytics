@extends('promo.layout')
@section('title', 'Fitur Lengkap - ' . ($settings['site_name'] ?? 'HalalScan AI'))

@section('content')
<div class="pt-24 pb-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">Fitur Lengkap Aplikasi</h1>
        <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
            Jelajahi semua kemampuan yang membuat aplikasi kami menjadi asisten kesehatan dan halal terbaik Anda.
        </p>
    </div>
</div>

<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-16">
            <!-- Feature 1 -->
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div>
                    <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight sm:text-3xl">Halal Confidence Score</h3>
                    <p class="mt-3 text-lg text-gray-500">Analisis instan status kehalalan suatu produk. AI kami memeriksa miliaran data untuk memberikan skor kepercayaan yang akurat berdasarkan bahan dan aditif.</p>
                </div>
                <div class="mt-8 lg:mt-0">
                    <div class="bg-green-100 rounded-lg p-8 text-center text-green-700 text-6xl shadow-inner">
                        <i class="bi bi-shield-check"></i>
                        <div class="mt-4 text-xl font-bold">Akurat & Terpercaya</div>
                    </div>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center flex flex-col-reverse">
                <div class="mt-8 lg:mt-0">
                    <div class="bg-blue-100 rounded-lg p-8 text-center text-blue-700 text-6xl shadow-inner">
                        <i class="bi bi-capsule"></i>
                        <div class="mt-4 text-xl font-bold">Keamanan Obat</div>
                    </div>
                </div>
                <div>
                    <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight sm:text-3xl">Drug Interaction Checker</h3>
                    <p class="mt-3 text-lg text-gray-500">Mencegah efek samping yang berbahaya. Sistem cerdas kami menganalisis campuran obat dan memberikan peringatan untuk interaksi risiko tinggi, sedang, dan rendah.</p>
                </div>
            </div>
            
            <!-- Feature 3 -->
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 lg:items-center">
                <div>
                    <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight sm:text-3xl">Health Score System</h3>
                    <p class="mt-3 text-lg text-gray-500">Memahami profil nutrisi dengan cepat. Dapatkan ringkasan instan tentang kesehatan suatu produk berdasarkan kandungan gula, sodium, dan nutrisi secara keseluruhan.</p>
                </div>
                <div class="mt-8 lg:mt-0">
                    <div class="bg-orange-100 rounded-lg p-8 text-center text-orange-700 text-6xl shadow-inner">
                        <i class="bi bi-heart-pulse"></i>
                        <div class="mt-4 text-xl font-bold">Nutrisi Holistik</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
