@extends('user.layouts.app')

@section('title', data_get($product, 'nama_product', data_get($product, 'name', 'Produk')) . ' - Halalytics')

@section('content')
@php
    $productImage = data_get($product, 'image') ?: data_get($product, 'image_url');
    $productFallback = data_get($product, 'image_fallback_url', asset('images/placeholders/product-placeholder.svg'));
    $productName = data_get($product, 'nama_product', data_get($product, 'name', 'Produk'));
    $category = data_get($product, 'kategori.nama_kategori') ?: data_get($product, 'category_name') ?: data_get($product, 'category') ?: 'Umum';
    $sourceLabel = data_get($product, 'source_label', data_get($product, 'source', 'Internal DB'));
    $status = data_get($product, 'status') ?: data_get($product, 'status_halal') ?: 'UNKNOWN';
    $brand = data_get($product, 'brand') ?: 'Tidak tersedia';
    $barcode = data_get($product, 'barcode') ?: 'Tidak tersedia';
    $quantity = data_get($product, 'quantity');
    $packaging = data_get($product, 'packaging');
    $labels = data_get($product, 'labels');
    $stores = data_get($product, 'stores');
    $countries = data_get($product, 'countries');
    $completenessScore = data_get($product, 'data_completeness_score');
    $halalAnalysis = data_get($product, 'halal_analysis');
    $composition = data_get($product, 'komposisi') ?: data_get($product, 'ingredients_text');
    $nutritionInfo = data_get($product, 'info_gizi') ?: data_get($product, 'analisis_kandungan') ?: data_get($product, 'nutriments');
@endphp

<section class="surface-card p-4 p-lg-5 mb-4">
    <div class="row g-4">
        <div class="col-lg-5">
            <img src="{{ $productImage }}" alt="{{ $productName }}" class="w-100 rounded-5 border" style="background:#f3fbf9; object-fit:cover;" onerror="this.onerror=null;this.src='{{ $productFallback }}'">
        </div>
        <div class="col-lg-7">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge badge-soft rounded-pill">{{ $category }}</span>
                <span class="badge badge-soft rounded-pill">{{ $sourceLabel }}</span>
                <span class="status-pill status-{{ str_replace(' ', '_', strtolower($status)) }}">{{ strtoupper($status) }}</span>
            </div>
            <h1 class="display-6 fw-bold mb-2">{{ $productName }}</h1>
            <div class="text-secondary mb-2">Barcode: {{ $barcode }}</div>
            @if(data_get($product, 'price') !== null)
                <div class="fs-2 fw-bold mb-4">Rp{{ number_format((float) data_get($product, 'price', 0), 0, ',', '.') }}</div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="surface-card p-3 h-100">
                        <div class="small text-secondary mb-1">Brand</div>
                        <div class="fw-semibold">{{ $brand }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="surface-card p-3 h-100">
                        <div class="small text-secondary mb-1">Informasi Sumber</div>
                        <div class="fw-semibold">{{ $sourceLabel }}</div>
                    </div>
                </div>
            </div>

            @if($product instanceof \App\Models\ProductModel && $product->active)
                <div class="surface-card p-3 rounded-4 border border-dashed border-secondary">
                    <div class="fw-semibold mb-2">Produk internal</div>
                    <p class="mb-0 text-secondary">Produk ini tersedia dalam katalog kami untuk verifikasi halal.</p>
                </div>
            @else
                <div class="surface-card p-3 rounded-4 border border-dashed border-secondary">
                    <div class="fw-semibold mb-2">Produk eksternal</div>
                    <p class="mb-0 text-secondary">Produk ini berasal dari sumber eksternal dan tidak tersedia untuk pemesanan langsung.</p>
                </div>
            @endif
        </div>
    </div>
</section>

<section class="row g-4">
    <div class="col-lg-7">
        <div class="surface-card p-4 h-100">
            <h2 class="h4 fw-bold mb-3">Detail Produk</h2>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="small text-secondary fw-semibold mb-2">Komposisi</div>
                    @php
                        $decodedComposition = is_string($composition) ? json_decode($composition, true) : null;
                    @endphp
                    @if(is_array($decodedComposition))
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($decodedComposition as $ingredient)
                                <span class="badge text-bg-light border rounded-pill px-3 py-2">{{ $ingredient }}</span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-secondary">{{ $composition ?: 'Komposisi belum tersedia.' }}</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="small text-secondary fw-semibold mb-2">Informasi Gizi</div>
                    <div class="text-secondary">{{ $nutritionInfo ?: 'Informasi gizi belum diisi.' }}</div>
                </div>
            </div>

            @if($halalAnalysis)
                <div class="mb-4">
                    <div class="small text-secondary fw-semibold mb-2">Analisis Halal</div>
                    <div class="text-secondary">{{ is_array($halalAnalysis) ? ($halalAnalysis['summary'] ?? json_encode($halalAnalysis)) : $halalAnalysis }}</div>
                </div>
            @endif

            @if ($completenessScore !== null)
                @php $dataCompletenessScore = min(max((float) $completenessScore, 0), 100); @endphp
                <div class="mb-4">
                    <div class="small text-secondary fw-semibold mb-2">Skor Kelengkapan Data</div>
                    <div class="progress rounded-pill bg-secondary-soft" style="height: 10px;">
                        <div class="progress-bar bg-brand rounded-pill" role="progressbar" style="width: {{ $dataCompletenessScore }}%;"></div>
                    </div>
                    <div class="mt-2 small text-secondary">Nilai: {{ number_format($dataCompletenessScore, 0) }}%</div>
                </div>
            @endif

            @if($labels || $stores || $countries || $packaging || $quantity)
                <div class="row g-3">
                    @if($labels)
                        <div class="col-md-6">
                            <div class="surface-card p-3 h-100">
                                <div class="small text-secondary mb-1">Label</div>
                                <div class="fw-semibold">{{ is_array($labels) ? implode(', ', $labels) : $labels }}</div>
                            </div>
                        </div>
                    @endif
                    @if($packaging)
                        <div class="col-md-6">
                            <div class="surface-card p-3 h-100">
                                <div class="small text-secondary mb-1">Kemasan</div>
                                <div class="fw-semibold">{{ $packaging }}</div>
                            </div>
                        </div>
                    @endif
                    @if($quantity)
                        <div class="col-md-6">
                            <div class="surface-card p-3 h-100">
                                <div class="small text-secondary mb-1">Kuantitas</div>
                                <div class="fw-semibold">{{ $quantity }}</div>
                            </div>
                        </div>
                    @endif
                    @if($stores)
                        <div class="col-md-6">
                            <div class="surface-card p-3 h-100">
                                <div class="small text-secondary mb-1">Tersedia di</div>
                                <div class="fw-semibold">{{ is_array($stores) ? implode(', ', $stores) : $stores }}</div>
                            </div>
                        </div>
                    @endif
                    @if($countries)
                        <div class="col-md-6">
                            <div class="surface-card p-3 h-100">
                                <div class="small text-secondary mb-1">Negara</div>
                                <div class="fw-semibold">{{ is_array($countries) ? implode(', ', $countries) : $countries }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <div class="col-lg-5">
        <div class="surface-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 fw-bold mb-0">Produk Terkait</h2>
                <a href="{{ route('user.products') }}" class="btn btn-sm btn-ghost-brand rounded-pill">Katalog</a>
            </div>
            <div class="row g-3">
                @forelse($relatedProducts as $relatedProduct)
                    <div class="col-md-6">
                        <div class="product-card h-100 overflow-hidden">
                            <img src="{{ data_get($relatedProduct, 'image') }}" alt="{{ data_get($relatedProduct, 'nama_product', data_get($relatedProduct, 'name')) }}" class="product-thumb" onerror="this.onerror=null;this.src='{{ data_get($relatedProduct, 'image_fallback_url', asset('images/placeholders/product-placeholder.svg')) }}'">
                            <div class="p-3">
                                <div class="fw-semibold mb-2">{{ data_get($relatedProduct, 'nama_product', data_get($relatedProduct, 'name')) }}</div>
                                <div class="small text-secondary mb-2">Rp{{ number_format((float) data_get($relatedProduct, 'price', 0), 0, ',', '.') }}</div>
                                <a href="{{ route('user.products.show', $relatedProduct) }}" class="btn btn-sm btn-outline-dark rounded-pill">Lihat</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-secondary">Tidak ada produk terkait untuk ditampilkan.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
