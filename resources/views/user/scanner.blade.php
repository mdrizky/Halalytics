@extends('user.layouts.app')

@section('title', 'Scanner Web - Halalytics')

@section('content')
<section class="page-hero mb-4">
    <h1 class="display-6 fw-bold mb-2">Scanner Web & Akses Cepat ke Katalog</h1>
    <p class="mb-0 text-white-50">Untuk demo web, gunakan pencarian barcode atau nama produk. Untuk pengalaman scan kamera terbaik, gunakan aplikasi Android.</p>
</section>

<section class="row g-4">
    <div class="col-lg-7">
        <div class="surface-card p-4 h-100">
            <h2 class="h4 fw-bold mb-3">Cari Produk Manual</h2>
            <form action="{{ route('user.products') }}" method="GET" class="row g-3">
                <div class="col-sm-9">
                    <input type="text" name="search" class="form-control rounded-4" placeholder="Masukkan barcode atau nama produk">
                </div>
                <div class="col-sm-3 d-grid">
                    <button class="btn btn-brand rounded-4" type="submit">Cari</button>
                </div>
            </form>
            <div class="mt-4 d-flex flex-wrap gap-2">
                <span class="badge text-bg-light border rounded-pill px-3 py-2">Barcode Search</span>
                <span class="badge text-bg-light border rounded-pill px-3 py-2">Image Sync Aktif</span>
                <span class="badge text-bg-light border rounded-pill px-3 py-2">Catalog Demo Siap</span>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="surface-card p-4 h-100">
            <h2 class="h4 fw-bold mb-3">Direkomendasikan untuk Android</h2>
            <p class="text-secondary">Aplikasi Android akan memakai loader gambar dengan placeholder dan error fallback, jadi pengalaman scan serta katalog akan tetap stabil saat presentasi.</p>
            <div class="d-grid gap-2">
                <a href="{{ route('user.compose') }}" class="btn btn-brand rounded-pill">Buka Compose</a>
                <a href="{{ route('user.cart.index') }}" class="btn btn-outline-dark rounded-pill">Lihat Keranjang</a>
            </div>
        </div>
    </div>
</section>
@endsection
