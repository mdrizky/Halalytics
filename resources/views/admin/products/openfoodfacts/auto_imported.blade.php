@extends('admin.layouts.admin_layout')

@section('header')
<div class="header-body">
    <div class="row align-items-end">
        <div class="col">
            <h6 class="header-pretitle">OpenFoodFacts</h6>
            <h1 class="header-title">Data Import Otomatis</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.products.off.index') }}" class="btn btn-white px-4 rounded-pill">
                <i class="fas fa-search me-2"></i> Cari Global
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="alert alert-warning border-0 shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <div class="icon-circle bg-warning-soft text-warning me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: rgba(246, 194, 62, 0.1);">
                <i class="fas fa-robot"></i>
            </div>
            <div>
                <h4 class="alert-heading mb-1">Perlu Review Manual</h4>
                <p class="mb-0 small text-dark">Daftar di bawah adalah produk yang diimport otomatis oleh sistem saat user melakukan scan. Produk ini <strong>belum terverifikasi</strong> dan belum memiliki status halal yang valid.</p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-nowrap table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th>Barcode</th>
                            <th>Skor Data</th>
                            <th>Tanggal Import</th>
                            <th class="text-end px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3 border rounded">
                                        <img src="{{ $product->image ?? 'https://via.placeholder.com/50' }}" class="avatar-img rounded" style="object-fit: contain;">
                                    </div>
                                    <div>
                                        <h5 class="mb-0 text-dark">{{ $product->nama_product }}</h5>
                                        <small class="text-muted">{{ $product->source }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded">{{ $product->barcode }}</code>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-2">{{ $product->data_completeness_score }}%</span>
                                    <div class="progress" style="width: 60px; height: 4px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $product->data_completeness_score }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $product->auto_imported_at ? $product->auto_imported_at->format('d M Y, H:i') : 'N/A' }}</small>
                            </td>
                            <td class="text-end px-4">
                                <a href="{{ route('admin.product.edit', $product->id_product) }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                                    <i class="fas fa-check-double me-1"></i> Verifikasi
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted mb-3">
                                    <i class="fas fa-clipboard-check fa-3x"></i>
                                </div>
                                <h4 class="text-muted">Semua produk sudah terverifikasi</h4>
                                <p class="mb-0">Tidak ada produk import otomatis yang menunggu antrian.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($products->hasPages())
        <div class="card-footer bg-white border-0 py-4 px-4">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
