@forelse($products as $product)
<div class="col-md-6 col-lg-4">
    <div class="card h-100 border-0 shadow-sm card-hover">
        <div style="position: relative; height: 200px; overflow: hidden; background: #f8f9fa;">
            <img src="{{ $product['image'] ?? 'https://via.placeholder.com/400x300?text=No+Image' }}" 
                 class="card-img-top" 
                 style="width: 100%; height: 100%; object-fit: contain; padding: 10px;"
                 alt="{{ $product['nama_product'] }}">
            
            @if($product['completeness'] > 80)
            <span class="badge bg-success" style="position: absolute; top: 10px; right: 10px;">
                <i class="fas fa-star me-1"></i> Data Lengkap
            </span>
            @endif
        </div>
        
        <div class="card-body p-3">
            <div class="mb-2">
                <span class="badge bg-light text-primary border px-2 py-0.5 mb-1" style="font-size: 10px;">
                    <i class="fas fa-barcode me-1"></i> {{ $product['barcode'] ?? 'N/A' }}
                </span>
            </div>
            
            <h5 class="card-title mb-1 text-truncate" title="{{ $product['nama_product'] }}">
                {{ $product['nama_product'] }}
            </h5>
            
            <p class="text-muted small mb-3">
                {{ $product['brand'] ?: 'Brand tidak tersedia' }}
            </p>

            @php
                $existing = \App\Models\ProductModel::where('off_product_id', $product['off_product_id'])
                                                   ->orWhere('barcode', $product['barcode'])
                                                   ->first();
            @endphp

            @if($existing)
                <div class="d-grid">
                    <a href="{{ route('admin.product.edit', $existing->id_product) }}" class="btn btn-outline-success btn-sm rounded-pill fw-bold">
                        <i class="fas fa-check-circle me-1"></i> Sudah di Katalog
                    </a>
                </div>
            @else
                <div class="d-grid">
                    <a href="{{ route('admin.products.off.preview', $product['off_product_id']) }}" class="btn btn-primary btn-sm rounded-pill fw-bold shadow-sm">
                        <i class="fas fa-eye me-1"></i> Preview & Simpan
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@empty
<div class="col-12 text-center py-5">
    <div class="text-muted mb-3">
        <i class="fas fa-search fa-3x"></i>
    </div>
    <h3>Tidak ada produk ditemukan</h3>
    <p class="text-muted">Coba kata kunci lain atau periksa koneksi internet Anda.</p>
</div>
@endforelse

<div class="col-12 mt-4 d-flex justify-content-center">
    @if(($results['page_count'] ?? 1) > 1)
        <nav>
            <ul class="pagination pagination-pill">
                @php 
                    $currentPage = $results['page'] ?? 1;
                    $totalPages = min($results['page_count'] ?? 1, 10);
                @endphp

                @if($currentPage > 1)
                <li class="page-item">
                    <a class="page-link shadow-sm border-0" href="javascript:void(0)" onclick="fetchPage({{ $currentPage - 1 }})">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                @endif

                @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                    <a class="page-link shadow-sm border-0" href="javascript:void(0)" onclick="fetchPage({{ $i }})">{{ $i }}</a>
                </li>
                @endfor

                @if($currentPage < $totalPages)
                <li class="page-item">
                    <a class="page-link shadow-sm border-0" href="javascript:void(0)" onclick="fetchPage({{ $currentPage + 1 }})">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                @endif
            </ul>
        </nav>
    @endif
</div>
