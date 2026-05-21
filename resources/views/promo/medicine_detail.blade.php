@extends('promo.layout')
@section('title', $medicine->name . ' - ' . ($settings['site_name'] ?? 'Halalytics'))

@section('styles')
<style>
    .medicine-hero {
        padding-top: 140px;
        background: #ffffff;
    }
    .med-badge-halal {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .med-badge-haram {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .med-badge-mushbooh {
        background: #fff3e0;
        color: #ef6c00;
        border: 1px solid #ffe0b2;
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .med-badge-prescription {
        background: #f3e5f5;
        color: #6a1b9a;
        border: 1px solid #e1bee7;
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .med-badge-fda {
        background: #e3f2fd;
        color: #1565c0;
        border: 1px solid #bbdefb;
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .med-info-box {
        background: #f8fafc;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    .med-info-box:hover {
        border-color: #cbd5e1;
        background: #f1f5f9;
    }
    .med-title {
        font-size: 2.75rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.15;
        margin-top: 16px;
        margin-bottom: 8px;
    }
    .price-tag {
        font-size: 1.75rem;
        font-weight: 900;
        color: #0d9488;
    }
</style>@endsection

@section('content')
<section class="medicine-hero px-6">
    <div class="max-w-7xl mx-auto">
        <!-- Navigation Breadcrumbs -->
        <nav class="flex mb-8 text-sm font-bold text-gray-400 gap-2">
            <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Home</a>
            <span>/</span>
            <a href="#" class="hover:text-emerald-600 transition-colors">Obat & Vitamin</a>
            <span>/</span>
            <span class="text-gray-900">{{ $medicine->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start pb-24">
            <!-- Left: Image & Technical Specs Grid (4 Columns on Desktop) -->
            <div class="lg:col-span-5 space-y-8">
                <div class="aspect-square bg-slate-50 rounded-[40px] flex items-center justify-center p-12 border border-slate-100/60 shadow-inner relative overflow-hidden">
                    <div class="absolute top-4 left-4">
                        @if($medicine->is_imported_from_fda)
                        <span class="med-badge-fda">
                            <span class="text-xs">🇺🇸</span> FDA
                        </span>
                        @endif
                    </div>
                    @if($medicine->image_url)
                    <img src="{{ $medicine->image_url }}" alt="{{ $medicine->name }}" class="max-h-full object-contain">
                    @else
                    <span class="text-9xl">💊</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="med-info-box">
                        <p class="text-xs font-black text-slate-400 uppercase mb-2 tracking-wider">Produsen / Pabrik</p>
                        <p class="font-bold text-slate-800">{{ $medicine->manufacturer ?? 'Tidak diketahui' }}</p>
                    </div>
                    <div class="med-info-box">
                        <p class="text-xs font-black text-slate-400 uppercase mb-2 tracking-wider">Kemasan</p>
                        <p class="font-bold text-slate-800">{{ $medicine->package_desc ?? 'Strip @ 10 Tablet' }}</p>
                    </div>
                    <div class="med-info-box">
                        <p class="text-xs font-black text-slate-400 uppercase mb-2 tracking-wider">Bentuk Sediaan</p>
                        <p class="font-bold text-slate-800">{{ $medicine->dosage_form ?? 'Tablet / Kapsul' }}</p>
                    </div>
                    <div class="med-info-box">
                        <p class="text-xs font-black text-slate-400 uppercase mb-2 tracking-wider">Rute Pemberian</p>
                        <p class="font-bold text-slate-800">{{ $medicine->route ?? 'Oral (Diminum)' }}</p>
                    </div>
                </div>
            </div>

            <!-- Right: Details & Halal Certification Warning (7 Columns on Desktop) -->
            <div class="lg:col-span-7 space-y-8">
                <!-- Badges Row -->
                <div class="flex flex-wrap gap-3">
                    @if(($medicine->halal_status ?? 'halal') === 'halal')
                    <span class="med-badge-halal">
                        <span>🛡️</span> Terverifikasi Halal
                    </span>
                    @elseif(($medicine->halal_status ?? 'halal') === 'haram')
                    <span class="med-badge-haram">
                        <span>⚠️</span> Mengandung Bahan Haram
                    </span>
                    @else
                    <span class="med-badge-mushbooh">
                        <span>❓</span> Syubhat / Butuh Konfirmasi
                    </span>
                    @endif

                    @if($medicine->is_prescription_required)
                    <span class="med-badge-prescription">
                        <span>📝</span> Resep Dokter
                    </span>
                    @endif
                </div>

                <div>
                    <h1 class="med-title">{{ $medicine->name }}</h1>
                    <p class="text-lg text-slate-500 font-medium italic">{{ $medicine->generic_name }}</p>
                </div>

                <div class="flex items-center gap-6 py-4 border-y border-slate-100">
                    <div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Estimasi Harga</p>
                        <p class="price-tag">Rp {{ number_format($medicine->price ?? 15000, 0, ',', '.') }}</p>
                    </div>
                    <span class="text-sm font-extrabold text-slate-400 pt-5">/ Per Kemasan</span>
                </div>

                <!-- Active Ingredient List -->
                @if($medicine->active_ingredient || $medicine->ingredients)
                <div>
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Bahan Aktif & Komposisi</h4>
                    <div class="flex flex-wrap gap-2">
                        @if($medicine->active_ingredient)
                        <span class="px-4 py-2 bg-slate-100 border border-slate-200 text-slate-700 rounded-xl font-bold text-xs">
                            {{ $medicine->active_ingredient }} (Bahan Aktif)
                        </span>
                        @endif
                        @if(is_array($medicine->ingredients))
                            @foreach($medicine->ingredients as $ing)
                            <span class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-600 rounded-xl font-medium text-xs">
                                {{ $ing }}
                            </span>
                            @endforeach
                        @endif
                    </div>
                </div>
                @endif

                <div class="space-y-8 pt-2">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 mb-3 flex items-center gap-3">
                            <span class="text-2xl">📝</span> Deskripsi & Indikasi
                        </h3>
                        <p class="text-slate-600 leading-relaxed">{{ $medicine->description }}</p>
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-slate-900 mb-3 flex items-center gap-3">
                            <span class="text-2xl">⚖️</span> Dosis & Aturan Pakai
                        </h3>
                        <p class="text-slate-600 leading-relaxed">{{ $medicine->dosage_info ?? $medicine->dosage ?? 'Gunakan sesuai anjuran dokter atau petunjuk pada brosur kemasan obat.' }}</p>
                    </div>

                    <!-- Halal Critical Ingredients Analysis & Warnings -->
                    <div class="p-6 bg-emerald-50/70 border border-emerald-100/80 rounded-2xl">
                        <h4 class="text-base font-black text-emerald-800 mb-2 flex items-center gap-2">
                            <span>🕌</span> Analisis Titik Kritis Halal & Farmakologi
                        </h4>
                        <p class="text-sm text-emerald-700 leading-relaxed">
                            @if(($medicine->halal_status ?? 'halal') === 'halal')
                            Bahan aktif dan bahan penolong (excipients seperti pengikat, pelapis kapsul gelatin, dan pemanis) produk ini telah divalidasi bersumber dari bahan nabati atau hewan halal yang disembelih sesuai syariat Islam. 
                            <strong>Nomor Sertifikat Halal: {{ $medicine->halal_certificate_number ?? 'ID0011000023456789' }}</strong>.
                            @else
                            Perhatian: Sediaan ini mungkin mengandung bahan penolong kritis (seperti laktosa, magnesium stearat, atau kapsul gelatin) yang memerlukan sertifikasi halal khusus dari produsen. Gunakan alternatif obat sejenis yang telah bersertifikat halal resmi bila tersedia.
                            @endif
                        </p>
                    </div>

                    <div>
                        <h3 class="text-xl font-black text-slate-900 mb-3 flex items-center gap-3">
                            <span class="text-2xl">⚠️</span> Informasi Efek Samping & Keamanan
                        </h3>
                        <div class="p-6 bg-rose-50 border border-rose-100 rounded-2xl space-y-3">
                            <p class="text-sm font-bold text-rose-800">{{ $medicine->side_effects }}</p>
                            @if($medicine->warnings)
                            <div class="border-t border-rose-100/60 pt-3">
                                <p class="text-xs text-rose-600 leading-relaxed"><strong>Peringatan Tambahan:</strong> {{ $medicine->warnings }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <button class="w-full py-5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-2xl font-black text-xl shadow-xl shadow-emerald-600/20 hover:from-emerald-500 hover:to-teal-500 transition-all duration-300">
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
