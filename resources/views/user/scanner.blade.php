@extends('master')
@section('isi')
<div class="container py-5" style="background-color: #121212; min-height: 100vh; color: #E0E0E0;">
    <div class="text-center mb-5">
        <h2 style="color: #3A9D66; font-weight: 700;"><i class="fas fa-qrcode fa-2x mb-3"></i><br>Web Scanner</h2>
        <p class="text-muted">Gunakan fitur scanner kami untuk verifikasi kehalalan produk secara instan.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark border-secondary shadow-lg overflow-hidden">
                <div class="bg-primary text-white p-5 text-center">
                    <h3 class="font-weight-bold mb-4">Pengalaman Terbaik di Aplikasi Mobile</h3>
                    <p class="mb-4">Untuk pemindaian barcode yang lebih cepat dan akurat menggunakan kamera smartphone Anda, kami merekomendasikan penggunaan aplikasi Halalytics Mobile.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <button class="btn btn-light btn-lg rounded-pill px-4"><i class="fab fa-google-play me-2"></i>Play Store</button>
                        <button class="btn btn-outline-light btn-lg rounded-pill px-4"><i class="fab fa-apple me-2"></i>App Store</button>
                    </div>
                </div>
                <div class="card-body p-5">
                    <h4 class="text-secondary mb-4"><i class="fas fa-keyboard me-2"></i>Atau Cek Manual di Web</h4>
                    <form action="{{ url('/products') }}" method="GET">
                        <div class="input-group input-group-lg">
                            <input type="text" name="search" class="form-control bg-dark border-secondary text-white" placeholder="Masukkan Nomor Barcode atau Nama Produk...">
                            <button class="btn btn-success px-4" type="submit">Cari Produk</button>
                        </div>
                    </form>
                    <div class="mt-4">
                        <p class="small text-muted mb-2">Tersedia untuk:</p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-secondary">Produk Kemasan</span>
                            <span class="badge bg-secondary">Street Food (UMKM)</span>
                            <span class="badge bg-secondary">Bahan Baku</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <a href="{{ url('/user') }}" class="btn btn-link text-success text-decoration-none"><i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection
