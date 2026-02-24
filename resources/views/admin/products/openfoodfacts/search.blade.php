@extends('admin.master')

@section('header')
<div class="header-body">
    <div class="row align-items-end">
        <div class="col">
            <h6 class="header-pretitle">OpenFoodFacts</h6>
            <h1 class="header-title">Hasil Pencarian Global</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.products.off.index') }}" class="btn btn-white px-4 rounded-pill">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <p class="text-muted">Ditemukan <strong>{{ $results['count'] ?? 0 }}</strong> produk untuk kata kunci <strong>"{{ $query }}"</strong></p>
    </div>

    @if(empty($results['products']))
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-5">
                <div class="mb-3 text-muted">
                    <i class="fas fa-search fa-3x"></i>
                </div>
                <h3>Tidak ada produk ditemukan</h3>
                <p class="text-muted mb-0">Coba gunakan kata kunci yang lebih spesifik atau periksa penulisan nama produk / barcode.</p>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($results['products'] as $product)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm card-hover">
                    <!-- Product Image -->
                    <div style="position: relative; height: 220px; overflow: hidden; background: #f8f9fa;">
                        <img src="{{ $product['image'] ?? 'https://via.placeholder.com/400x300?text=No+Image' }}" 
                             class="card-img-top" 
                             style="width: 100%; height: 100%; object-fit: contain; padding: 10px;"
                             alt="{{ $product['nama_product'] }}">
                        
                        @if($product['completeness'] > 80)
                        <span class="badge bg-success" style="position: absolute; top: 15px; right: 15px;">
                            <i class="fas fa-star me-1"></i> Data Lengkap
                        </span>
                        @endif
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="mb-2">
                            <span class="badge bg-light text-primary border px-2 py-1 mb-2">
                                <i class="fas fa-barcode me-1"></i> {{ $product['barcode'] ?? 'N/A' }}
                            </span>
                        </div>
                        
                        <h4 class="card-title mb-2 text-truncate" title="{{ $product['nama_product'] }}">
                            {{ $product['nama_product'] }}
                        </h4>
                        
                        <p class="text-muted small mb-4">
                            <i class="fas fa-tag me-1"></i> {{ $product['brand'] ?: 'Brand tidak tersedia' }}
                        </p>

                        <!-- Data Completeness Progress -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Kualitas Data</small>
                                <small class="fw-bold">{{ $product['completeness'] }}%</small>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 10px;">
                                <div class="progress-bar {{ $product['completeness'] > 70 ? 'bg-success' : ($product['completeness'] > 40 ? 'bg-warning' : 'bg-danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ $product['completeness'] }}%"
                                     aria-valuenow="{{ $product['completeness'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        <!-- Action -->
                        @php
                            $existing = \App\Models\ProductModel::where('off_product_id', $product['off_product_id'])
                                                               ->orWhere('barcode', $product['barcode'])
                                                               ->first();
                        @endphp

                        @if($existing)
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.product.edit', $existing->id_product) }}" class="btn btn-outline-success border-2 rounded-pill fw-bold">
                                    <i class="fas fa-check-circle me-2"></i> Sudah di Katalog
                                </a>
                            </div>
                        @else
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.products.off.preview', $product['off_product_id']) }}" class="btn btn-primary rounded-pill fw-bold shadow-sm">
                                    <i class="fas fa-eye me-2"></i> Preview & Simpan
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination (Simple manual pagination) -->
        @if(($results['page_count'] ?? 1) > 1)
        <div class="d-flex justify-content-center mt-5">
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-pill">
                    @php 
                        $currentPage = $results['page'] ?? 1;
                        $totalPages = min($results['page_count'] ?? 1, 10); // Limit to 10 for UI
                    @endphp

                    @if($currentPage > 1)
                    <li class="page-item">
                        <a class="page-link shadow-sm border-0" href="{{ route('admin.products.off.search', ['query' => $query, 'page' => $currentPage - 1]) }}">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    @endif

                    @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                        <a class="page-link shadow-sm border-0" href="{{ route('admin.products.off.search', ['query' => $query, 'page' => $i]) }}">{{ $i }}</a>
                    </li>
                    @endfor

                    @if($currentPage < $totalPages)
                    <li class="page-item">
                        <a class="page-link shadow-sm border-0" href="{{ route('admin.products.off.search', ['query' => $query, 'page' => $currentPage + 1]) }}">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>
        @endif
    @endif
</div>

<style>
    .card-hover:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
    }
    .pagination-pill .page-item .page-link {
        border-radius: 50%;
        margin: 0 5px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6e84a3;
    }
    .pagination-pill .page-item.active .page-link {
        background-color: #2c7be5;
        color: white;
    }
</style>
@endsection
