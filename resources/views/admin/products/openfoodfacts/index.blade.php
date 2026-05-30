@extends('admin.master')

@section('header')
<div class="header-body">
    <div class="row align-items-end">
        <div class="col">
            <h6 class="header-pretitle">OpenFoodFacts</h6>
            <h1 class="header-title">Database Global</h1>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Search Form -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.products.off.search') }}" method="GET">
                <div class="input-group input-group-lg bg-light rounded-pill overflow-hidden border">
                    <span class="input-group-text border-0 bg-transparent ps-4">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" 
                           name="query" 
                           class="form-control border-0 bg-transparent py-3" 
                           placeholder="Cari produk di database global (Nama atau Barcode)..."
                           value="{{ request('query') }}"
                           required>
                    <button type="submit" class="btn btn-primary px-4 me-2 my-1 rounded-pill">
                        Cari Global
                    </button>
                </div>
                <div class="mt-3 px-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Contoh: "Indomie Goreng", "8992696011106", "Coca Cola"
                    </small>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Section -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="icon-circle bg-primary-soft text-primary mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: rgba(44, 123, 229, 0.1);">
                        <i class="fas fa-globe-asia fa-2x"></i>
                    </div>
                    <h4>Database Global</h4>
                    <p class="text-muted mb-0 small">Akses jutaan data produk dari OpenFoodFacts untuk memperkaya katalog lokal.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="icon-circle bg-warning-soft text-warning mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: rgba(246, 194, 62, 0.1);">
                        <i class="fas fa-file-import fa-2x"></i>
                    </div>
                    <h4>Import Cepat</h4>
                    <p class="text-muted mb-0 small">Cukup satu klik untuk mengimport data nutrisi, komposisi, dan gambar produk.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="icon-circle bg-success-soft text-success mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: rgba(0, 217, 126, 0.1);">
                        <i class="fas fa-check-double fa-2x"></i>
                    </div>
                    <h4>Verifikasi Halal</h4>
                    <p class="text-muted mb-0 small">Produk yang diimport tetap memerlukan verifikasi manual untuk menjamin keakuratan status halal.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recently Imported Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-b py-3 px-4 flex items-center justify-between">
            <h5 class="mb-0 font-bold text-slate-800">Produk yang Telah Diimport</h5>
            <span class="text-xs text-slate-400">{{ $products->total() }} Produk Terdaftar</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                            <th class="ps-4">Produk</th>
                            <th>Barcode</th>
                            <th>Kategori</th>
                            <th>Status Halal</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-light rounded overflow-hidden me-3" style="width: 40px; height: 40px;">
                                        <img src="{{ $product->image_url }}" alt="" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://loremflickr.com/100/100/{{ urlencode($product->nama_product) }},food?lock={{ $product->id_product }}'">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-slate-800 small">{{ $product->nama_product }}</div>
                                        <div class="text-muted" style="font-size: 10px;">{{ $product->brand }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-slate-600 border px-2 py-1 small">
                                    {{ $product->barcode }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $product->kategori->nama_kategori ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @php
                                    $status = strtolower($product->status ?? 'syubhat');
                                    $badgeClass = match($status) {
                                        'halal' => 'bg-success',
                                        'haram', 'tidak halal' => 'bg-danger',
                                        default => 'bg-warning'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2 py-1" style="font-size: 10px;">{{ strtoupper($status) }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.product.edit', $product->id_product) }}" class="btn btn-sm btn-white text-primary rounded-pill px-3 border shadow-sm">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                Belum ada produk OpenFoodFacts yang diimport.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
        <div class="card-footer bg-white border-t py-3">
            {{ $products->links('vendor.pagination.tailwind-admin') }}
        </div>
        @endif
    </div>

    <!-- Footer Links -->
    <div class="d-flex justify-content-center mt-5">
        <a href="{{ route('admin.products.off.auto-imported') }}" class="btn btn-link text-muted">
            <i class="fas fa-list-check me-2"></i> Lihat Produk yang Diimport Otomatis
        </a>
    </div>
</div>
@endsection
