@extends('user.layouts.app')

@section('title', 'Dashboard User - Halalytics')

@section('content')
<section class="page-hero mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <span class="badge badge-soft rounded-pill px-3 py-2 mb-3">Portal Verifikasi Halal</span>
            <h1 class="display-6 fw-bold mb-3">Verifikasi produk halal dengan mudah dan dapatkan informasi lengkap tentang status kehalalan produk.</h1>
            <p class="mb-4 text-white-50">Platform lengkap untuk memverifikasi kehalalan produk melalui scan barcode dan pencarian manual.</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('user.scanner') }}" class="btn btn-light rounded-pill px-4 fw-bold">Mulai Scan</a>
                <a href="{{ route('user.products') }}" class="btn btn-outline-light rounded-pill px-4">Lihat Katalog</a>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="surface-card p-4 text-dark">
                <div class="small text-secondary mb-2">Akun aktif</div>
                <div class="fw-bold fs-4">{{ Auth::user()->full_name ?? Auth::user()->username }}</div>
                <div class="text-secondary mb-3">{{ Auth::user()->email ?? 'Email belum diisi' }}</div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge text-bg-light border">{{ Auth::user()->phone ?? 'Tanpa telepon' }}</span>
                    <span class="badge text-bg-light border">{{ Auth::user()->address ?? 'Alamat demo belum diisi' }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-chip">
            <div class="text-secondary small mb-2">Total Scan Saya</div>
            <div class="fs-3 fw-bold">{{ number_format($stats['scan_count']) }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-chip">
            <div class="text-secondary small mb-2">Laporan Terkirim</div>
            <div class="fs-3 fw-bold">{{ number_format($stats['report_count']) }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-chip">
            <div class="text-secondary small mb-2">Produk Aktif</div>
            <div class="fs-3 fw-bold">{{ number_format($stats['catalog_count']) }}</div>
        </div>
    </div>
</section>

<section class="row g-4">
    <div class="col-lg-7">
        <div class="surface-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="h4 fw-bold mb-1">Produk Unggulan</h2>
                    <p class="text-secondary mb-0">Semua kartu sudah memakai image resolver yang sama dengan halaman admin.</p>
                </div>
                <a href="{{ route('user.products') }}" class="btn btn-sm btn-ghost-brand rounded-pill">Lihat Semua</a>
            </div>
            <div class="row g-3">
                @foreach($featuredProducts as $product)
                    <div class="col-md-6">
                        <div class="product-card h-100 overflow-hidden">
                            <img src="{{ $product->image }}" alt="{{ $product->nama_product }}" class="product-thumb" onerror="this.onerror=null;this.src='{{ $product->image_fallback_url }}'">
                            <div class="p-3">
                                <div class="d-flex justify-content-between gap-2 mb-2">
                                    <span class="badge badge-soft rounded-pill">{{ $product->kategori->nama_kategori ?? 'Umum' }}</span>
                                    <span class="status-pill status-{{ str_replace(' ', '_', strtolower($product->status ?? 'pending')) }}">{{ strtoupper($product->status ?? 'unknown') }}</span>
                                </div>
                                <h3 class="h6 fw-bold mb-1">{{ $product->nama_product }}</h3>
                                <div class="text-secondary small mb-3">Rp{{ number_format((float) ($product->price ?? 0), 0, ',', '.') }}</div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('user.products.show', $product) }}" class="btn btn-sm btn-outline-dark rounded-pill">Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="surface-card p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h2 class="h4 fw-bold mb-1">Scan Terbaru</h2>
                    <p class="text-secondary mb-0">Riwayat scan produk Anda untuk verifikasi halal.</p>
                </div>
                <a href="{{ route('user.scans') }}" class="btn btn-sm btn-ghost-brand rounded-pill">Semua Scan</a>
            </div>

            <div class="row g-3">
                @forelse($recentScans as $scan)
                    <div class="col-12">
                        <div class="surface-card p-3 d-flex align-items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="badge text-bg-{{ $scan->status_halal == 'halal' ? 'success' : ($scan->status_halal == 'haram' ? 'danger' : 'warning') }} rounded-circle p-2">
                                    <i class="fas fa-{{ $scan->status_halal == 'halal' ? 'check' : ($scan->status_halal == 'haram' ? 'times' : 'question') }}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-bold text-truncate">{{ $scan->nama_produk ?? $scan->product->nama_product ?? 'Produk Tanpa Nama' }}</div>
                                <div class="text-secondary small">{{ $scan->tanggal_scan ? \Carbon\Carbon::parse($scan->tanggal_scan)->diffForHumans() : $scan->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="{{ $scan->product_id ? route('user.products.show', $scan->product_id) : '#' }}" class="btn btn-sm btn-light rounded-pill">Detail</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="surface-card p-4 text-center">
                        <div class="fw-bold mb-2">Belum ada scan</div>
                        <p class="text-secondary mb-3">Mulai scan produk menggunakan barcode scanner untuk verifikasi kehalalan.</p>
                        <a href="{{ route('user.scanner') }}" class="btn btn-brand rounded-pill px-4">Mulai Scan</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
