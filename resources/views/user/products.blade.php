@extends('master')
@section('isi')
<div class="container py-5" style="background-color: #F4F9F8; min-height: 100vh; color: #163832;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #004D40; font-weight: 700;"><i class="fas fa-box-open me-3"></i>Katalog Produk</h2>
        <a href="{{ url('/user') }}" class="btn btn-outline-success" style="border-color:#26A69A;color:#004D40;"><i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard</a>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 mb-4 shadow-sm" style="border-radius:20px;background:#ffffff;">
        <div class="card-body">
            <form action="{{ url('/products') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text border-0" style="background:#E0F2F1;color:#004D40;"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-0" style="background:#F7FBFA;color:#163832;" placeholder="Cari nama produk atau barcode..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="category" class="form-select border-0" style="background:#F7FBFA;color:#163832;">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id_kategori }}" {{ request('category') == $category->id_kategori ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn w-100" style="background:#004D40;color:#fff;">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        @forelse($products as $product)
        <div class="col">
            <div class="card h-100 border-0 product-card shadow-sm" style="border-radius:20px;background:#ffffff;">
                <div class="position-relative">
                    <img src="{{ $product->image }}" class="card-img-top" alt="{{ $product->nama_product }}" style="height: 200px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholders/product-placeholder.svg') }}'">
                    
                    @php
                        $statusBadge = 'bg-secondary';
                        if(strtolower($product->status) == 'halal') $statusBadge = 'bg-success';
                        else if(strtolower($product->status) == 'tidak halal') $statusBadge = 'bg-danger';
                        else if(strtolower($product->status) == 'diragukan') $statusBadge = 'bg-warning text-dark';
                    @endphp
                    <span class="badge {{ $statusBadge }} position-absolute top-0 end-0 m-2">{{ strtoupper($product->status) }}</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title truncate" style="color:#004D40;">{{ $product->nama_product }}</h5>
                    <p class="card-text text-muted small mb-1"><i class="fas fa-tag me-2"></i>{{ $product->kategori->nama_kategori ?? 'Uncategorized' }}</p>
                    <p class="card-text text-muted small"><i class="fas fa-barcode me-2"></i>{{ $product->barcode ?: 'No barcode' }}</p>
                </div>
                <div class="card-footer bg-transparent border-0 text-center">
                    <button class="btn btn-sm w-100" style="border:1px solid #26A69A;color:#004D40;">Lihat Detail</button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="fas fa-box-open fa-4x mb-3 text-muted"></i>
            <p class="text-muted">Tidak ada produk ditemukan.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $products->links() }}
    </div>
</div>

<style>
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 28px rgba(0, 77, 64, 0.14) !important;
    }
    .truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection
