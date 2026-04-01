@extends('promo.layout')
@section('title', 'Tentang Kami - ' . ($settings['site_name'] ?? 'HalalScan AI'))
@section('description', 'Tentang HalalScan AI: misi, pendekatan produk, dan komitmen kami dalam membantu keputusan konsumsi yang aman.')
@section('keywords', 'tentang halalscan ai, misi aplikasi halal, keamanan konsumsi')
@section('canonical', route('about'))

@section('styles')
<style>
    .about-hero {
        background:
            radial-gradient(900px 420px at 110% -20%, rgba(31,79,214,.20), transparent 60%),
            radial-gradient(780px 420px at -5% 0%, rgba(14,165,107,.20), transparent 58%),
            linear-gradient(180deg, #f7fbf9 0%, #ffffff 100%);
    }
    .about-card {
        border: 1px solid #dbe3ea;
        border-radius: 20px;
        background: #fff;
    }
</style>
@endsection

@section('content')
<section class="about-hero pt-24 pb-16 border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <span class="inline-flex px-4 py-2 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wider">About HalalScan AI</span>
            <h1 class="mt-5 text-4xl md:text-5xl font-extrabold text-slate-900">Misi Kami: Membuat Keputusan Konsumsi Lebih Aman dan Halal</h1>
            <p class="mt-4 max-w-3xl mx-auto text-lg text-slate-600">
                Kami membangun platform yang membantu user memahami apa yang mereka konsumsi dengan cara yang cepat, transparan, dan mudah dipraktikkan.
            </p>
        </div>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <article class="about-card p-8">
            <h2 class="text-2xl font-extrabold text-slate-900">Kenapa HalalScan AI Dibangun</h2>
            <p class="text-slate-600 mt-3 leading-relaxed">
                Banyak konsumen kesulitan membaca komposisi produk, menilai status halal bahan teknis, atau memahami risiko interaksi obat. HalalScan AI lahir untuk menutup gap ini lewat pengalaman scan yang sederhana namun berbasis data.
            </p>
        </article>
        <article class="about-card p-8">
            <h2 class="text-2xl font-extrabold text-slate-900">Pendekatan Produk Kami</h2>
            <p class="text-slate-600 mt-3 leading-relaxed">
                Kami fokus pada usability dan akurasi: informasi harus cepat dipahami, bisa ditindaklanjuti, dan tetap terbuka terhadap validasi sumber data.
            </p>
        </article>
    </div>
</section>

<section class="py-16 bg-slate-50 border-y border-slate-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-extrabold text-slate-900 text-center mb-10">Pilar Utama</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['Data Terpercaya', 'Integrasi database publik dan sumber resmi untuk menjaga kualitas informasi.'],
                ['AI Kontekstual', 'Model AI menganalisis bahan, interaksi, dan pola risiko sesuai konteks produk.'],
                ['User-Centered UX', 'Output dibuat ringkas, jelas, dan mudah dipakai di situasi harian.'],
            ] as $pillar)
            <div class="about-card p-6">
                <h3 class="text-xl font-extrabold text-slate-900">{{ $pillar[0] }}</h3>
                <p class="text-slate-600 text-sm mt-2">{{ $pillar[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="about-card p-8 md:p-10">
            <h2 class="text-3xl font-extrabold text-slate-900">Komitmen Transparansi</h2>
            <p class="text-slate-600 mt-4 leading-relaxed">
                HalalScan AI bersifat alat bantu keputusan. Untuk validitas formal/sertifikasi, pengguna tetap disarankan mengecek dokumen resmi (misalnya BPOM, sertifikat halal, atau rekomendasi tenaga medis).
            </p>
            <div class="mt-7 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('privacy') }}" class="inline-flex items-center justify-center px-6 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800">Lihat Privacy Policy</a>
                <a href="{{ route('download') }}" class="inline-flex items-center justify-center px-6 py-3 border border-slate-300 text-slate-700 rounded-xl font-semibold hover:bg-slate-50">Download Aplikasi</a>
            </div>
        </div>
    </div>
</section>
@endsection
