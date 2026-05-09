@extends('promo.layout')
@section('title', $medicine->name . ' - ' . ($settings['site_name'] ?? 'Halalytics'))

@section('styles')
<style>
    .medicine-hero {
        padding-top: 140px;
        background: #ffffff;
    }
    .med-badge {
        background: #f0fdf4;
        color: #166534;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-block;
        margin-bottom: 12px;
    }
    .med-info-box {
        background: #f8fafc;
        border-radius: 24px;
        padding: 30px;
        border: 1px solid #e2e8f0;
    }
    .med-title {
        font-size: 2.5rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
        margin-bottom: 20px;
    }
    .price-tag {
        font-size: 1.5rem;
        font-weight: 900;
        color: #059669;
    }
</style>
@endsection

@section('content')
<section class="medicine-hero px-6">
    <div class="max-w-7xl mx-auto">
        <nav class="flex mb-8 text-sm font-bold text-gray-400 gap-2">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <a href="#">Obat & Vitamin</a>
            <span>/</span>
            <span class="text-gray-900">{{ $medicine->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start pb-24">
            <!-- Left: Image & Main Info -->
            <div class="space-y-8">
                <div class="aspect-square bg-gray-50 rounded-[40px] flex items-center justify-center p-12 border border-gray-100">
                    @if($medicine->image_url)
                    <img src="{{ $medicine->image_url }}" alt="{{ $medicine->name }}" class="max-h-full">
                    @else
                    <span class="text-9xl">💊</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="med-info-box">
                        <p class="text-xs font-black text-gray-400 uppercase mb-2">Pabrik</p>
                        <p class="font-bold text-gray-900">{{ $medicine->manufacturer ?? 'Tidak diketahui' }}</p>
                    </div>
                    <div class="med-info-box">
                        <p class="text-xs font-black text-gray-400 uppercase mb-2">Kemasan</p>
                        <p class="font-bold text-gray-900">{{ $medicine->package_desc ?? 'Strip @ 10 Tablet' }}</p>
                    </div>
                </div>
            </div>

            <!-- Right: Details -->
            <div>
                <span class="med-badge">Terverifikasi Halal</span>
                <h1 class="med-title">{{ $medicine->name }}</h1>
                <p class="text-lg text-gray-500 mb-6 font-medium">{{ $medicine->generic_name }}</p>
                
                <div class="flex items-center gap-6 mb-10">
                    <p class="price-tag">Rp {{ number_format($medicine->price ?? 15000, 0, ',', '.') }}</p>
                    <span class="text-sm font-bold text-gray-400">/ Per Kemasan</span>
                </div>

                <div class="space-y-8">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-3">
                            <span class="text-2xl">📝</span> Deskripsi & Manfaat
                        </h3>
                        <p class="text-gray-600 leading-relaxed">{{ $medicine->description }}</p>
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-3">
                            <span class="text-2xl">⚖️</span> Dosis & Aturan Pakai
                        </h3>
                        <p class="text-gray-600 leading-relaxed">{{ $medicine->dosage ?? 'Gunakan sesuai anjuran dokter atau petunjuk pada kemasan.' }}</p>
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-3">
                            <span class="text-2xl">⚠️</span> Informasi Keamanan
                        </h3>
                        <div class="p-6 bg-rose-50 border border-rose-100 rounded-2xl">
                            <p class="text-sm font-bold text-rose-800">{{ $medicine->side_effects ?? 'Harap berhati-hati bagi penderita gangguan fungsi ginjal dan hati.' }}</p>
                        </div>
                    </div>

                    <button class="w-full py-5 bg-emerald-600 text-white rounded-2xl font-black text-xl shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition-all">
                        Konsultasi via Aplikasi
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- RELATED ARTICLES -->
@if($relatedArticles->count() > 0)
<section class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-black text-gray-900 mb-12">Artikel Terkait Obat Ini</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedArticles as $article)
            <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 group cursor-pointer" onclick="location.href='{{ route('blog.show', $article->slug) }}'">
                @if($article->image)
                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                <div class="w-full h-48 bg-emerald-50"></div>
                @endif
                <div class="p-6">
                    <h4 class="font-black text-gray-900 mb-2 leading-tight">{{ $article->title }}</h4>
                    <p class="text-sm text-gray-500 line-clamp-2">{{ $article->excerpt }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
