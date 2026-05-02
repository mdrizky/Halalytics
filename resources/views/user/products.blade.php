@extends('user.layouts.app')

@section('title', 'Katalog Produk - Halalytics')

@section('content')
<section class="page-hero mb-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
        <div>
            <span class="badge badge-soft rounded-pill px-3 py-2 mb-3">Katalog Produk Demo</span>
            <h1 class="display-6 fw-bold mb-2">Semua produk tampil dengan gambar aktif, status halal, dan harga demo.</h1>
            <p class="mb-0 text-white-50">Katalog ini sudah sinkron dengan data produk admin dan image resolver yang sama.</p>
        </div>
        <a href="{{ route('user.compose') }}" class="btn btn-light rounded-pill px-4 fw-bold">Compose Order</a>
    </div>
</section>

<section class="surface-card p-4 mb-4">
    <form method="GET" action="{{ route('user.products') }}" class="row g-3 align-items-end">
        <div class="col-lg-6">
            <label class="form-label fw-semibold">Cari produk</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-4" placeholder="Cari nama produk atau barcode">
        </div>
        <div class="col-lg-4">
            <label class="form-label fw-semibold">Kategori</label>
            <select name="category" class="form-select rounded-4">
                <option value="">Semua kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id_kategori }}" @selected((string) request('category') === (string) $category->id_kategori)>{{ $category->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 d-grid">
            <button class="btn btn-brand rounded-4" type="submit">Filter</button>
        </div>
    </form>
</section>

<section class="row g-4">
    @forelse($products as $product)
        <div class="col-md-6 col-xl-3">
            <div class="product-card h-100 overflow-hidden">
                <img src="{{ $product->image }}" alt="{{ $product->nama_product }}" class="product-thumb" onerror="this.onerror=null;this.src='{{ $product->image_fallback_url }}'">
                <div class="p-3">
                    <div class="d-flex justify-content-between gap-2 mb-2">
                        <span class="badge badge-soft rounded-pill">{{ data_get($product, 'category_name', $product->kategori->nama_kategori ?? 'Umum') }}</span>
                        <span class="badge badge-soft rounded-pill">{{ data_get($product, 'source_label', 'Internal DB') }}</span>
                    </div>
                    <h2 class="h6 fw-bold mb-2">{{ $product->nama_product }}</h2>
                    <div class="small text-secondary mb-1">{{ $product->barcode ?: 'Tanpa barcode' }}</div>
                    <div class="fs-5 fw-bold mb-3">Rp{{ number_format((float) ($product->price ?? 0), 0, ',', '.') }}</div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('user.products.show', $product) }}" class="btn btn-outline-dark rounded-pill flex-fill">Detail</a>
                        <form action="{{ route('user.cart.add') }}" method="POST" class="flex-fill">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id_product }}">
                            <input type="hidden" name="quantity" value="1">
                            <button class="btn btn-brand rounded-pill w-100" type="submit">Tambah</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="surface-card p-5 text-center">
                <div class="fw-bold h4 mb-2">Produk tidak ditemukan</div>
                <p class="text-secondary mb-0">Coba ubah kata kunci pencarian atau kategori.</p>
            </div>
        </div>
    @endforelse
</section>

<div class="mt-4">
    {{ $products->links() }}
</div>
@endsection
