@extends('promo.layout')
@section('title', 'Tentang Kami - ' . ($settings['site_name'] ?? 'HalalScan AI'))

@section('content')
<div class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">Misi Kami</h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                Membangun ekosistem yang aman untuk umat Muslim dan memastikan setiap konsumen sadar akan apa yang mereka konsumsi.
            </p>
        </div>

        <div class="prose prose-lg prose-green mx-auto text-gray-600">
            <p>Berawal dari kepedulian terhadap sulitnya mengidentifikasi bahan-bahan pada kemasan makanan dan obat, kami menciptakan HalalScan AI. Visi kami adalah menyederhanakan cara Anda mengambil keputusan tentang produk yang aman dan halal untuk dikonsumsi.</p>
            
            <h3>Data Terpercaya</h3>
            <p>Kami bangga terintegrasi dengan berbagai organisasi database internasional maupun lokal seperti BPOM, Open Food Facts, dan database obat internasional untuk menjamin informasi yang kamu terima tidak hanya akurat, tapi juga mutakhir (up-to-date).</p>
            
            <h3>Teknologi AI Terbaru</h3>
            <p>Sistem kami ditenagai oleh Kecerdasan Buatan (AI) terdepan yang tidak hanya membaca teks, tapi juga memahaminya di dalam konteks kimia, kedokteran, dan syariah.</p>
        </div>
    </div>
</div>
@endsection
