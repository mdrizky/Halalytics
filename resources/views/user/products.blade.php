@extends('master')
@section('isi')
<div class="container py-5" style="background-color: #121212; min-height: 100vh; color: #E0E0E0;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #3A9D66; font-weight: 700;"><i class="fas fa-box-open me-3"></i>Katalog Produk</h2>
        <a href="{{ url('/user') }}" class="btn btn-outline-success"><i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard</a>
    </div>

    <!-- Filters & Search -->
    <div class="card bg-dark border-secondary mb-4 shadow">
        <div class="card-body">
            <form action="{{ url('/products') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-secondary text-white"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control bg-dark border-secondary text-white" placeholder="Cari nama produk atau barcode..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="category" class="form-select bg-dark border-secondary text-white">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id_kategori }}" {{ request('category') == $category->id_kategori ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        @forelse($products as $product)
        <div class="col">
            <div class="card h-100 bg-dark border-secondary product-card shadow-sm">
                <div class="position-relative">
                    @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->nama_product }}" style="height: 200px; object-fit: cover;">
                    @else
                    <div class="d-flex align-items-center justify-content-center bg-secondary card-img-top" style="height: 200px;">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                    @endif
                    
                    @php
                        $statusBadge = 'bg-secondary';
                        if(strtolower($product->status) == 'halal') $statusBadge = 'bg-success';
                        else if(strtolower($product->status) == 'tidak halal') $statusBadge = 'bg-danger';
                        else if(strtolower($product->status) == 'diragukan') $statusBadge = 'bg-warning text-dark';
                    @endphp
                    <span class="badge {{ $statusBadge }} position-absolute top-0 end-0 m-2">{{ strtoupper($product->status) }}</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title text-success truncate">{{ $product->nama_product }}</h5>
                    <p class="card-text text-muted small mb-1"><i class="fas fa-tag me-2"></i>{{ $product->kategori->nama_kategori ?? 'Uncategorized' }}</p>
                    <p class="card-text text-muted small"><i class="fas fa-barcode me-2"></i>{{ $product->barcode ?: 'No barcode' }}</p>
                </div>
                <div class="card-footer bg-transparent border-secondary text-center">
                    <button class="btn btn-sm btn-outline-success w-100">Lihat Detail</button>
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
        box-shadow: 0 10px 20px rgba(46, 139, 87, 0.2) !important;
    }
    .truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection
