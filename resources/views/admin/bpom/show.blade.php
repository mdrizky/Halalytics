@extends('admin.layouts.admin_layout')

@section('title', 'Detail Produk BPOM')

@section('content')
<div class="container-fluid">
    <a href="{{ route('admin.bpom.index') }}" class="btn btn-sm btn-secondary mb-4">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
    </a>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold">Informasi Produk</h5>
                    @if($product->verification_status == 'verified')
                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Terverifikasi</span>
                    @else
                        <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i> Belum Verifikasi</span>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%" class="bg-light">Nama Produk</th>
                            <td>{{ $product->nama_produk }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Nomor Registrasi</th>
                            <td><code>{{ $product->nomor_reg }}</code></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Merk / Brand</th>
                            <td>{{ $product->merk ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Kategori</th>
                            <td>{{ strtoupper($product->kategori) }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Pendaftar/Perusahaan</th>
                            <td>{{ $product->pendaftar ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Alamat Produsen</th>
                            <td>{{ $product->alamat_produsen ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Bentuk Sediaan</th>
                            <td>{{ $product->bentuk_sediaan ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Ingredien / Komposisi</th>
                            <td>{{ $product->ingredients_text ?: 'Tidak ada data bahan' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Analisis AI -->
            <div class="card mb-4 border-info">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-brain mr-1"></i> Analisis AI (Halal & Kandungan)
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6 text-center">
                            <h6>Status Halal</h6>
                            @if($product->status_halal == 'halal')
                                <div class="h3 text-success font-weight-bold">HALAL</div>
                            @elseif($product->status_halal == 'haram')
                                <div class="h3 text-danger font-weight-bold">HARAM</div>
                            @else
                                <div class="h3 text-warning font-weight-bold">SYUBHAT</div>
                            @endif
                        </div>
                        <div class="col-md-6 text-center">
                            <h6>Skor Keamanan</h6>
                            <div class="h3 font-weight-bold {{ $product->skor_keamanan >= 70 ? 'text-success' : ($product->skor_keamanan >= 40 ? 'text-warning' : 'text-danger') }}">
                                {{ $product->skor_keamanan ?: 'N/A' }} / 100
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h6>Analisis Halal:</h6>
                    <p>{{ $product->analisis_halal }}</p>
                    <h6>Analisis Kandungan:</h6>
                    <p>{{ $product->analisis_kandungan }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Sidebar info -->
            <div class="card mb-4">
                <div class="card-header bg-white font-weight-bold">Metadata Pencatatan</div>
                <div class="card-body">
                    <p class="mb-1 small text-muted">Sumber Data:</p>
                    <p class="font-weight-bold">{{ strtoupper($product->sumber_data) }}</p>
                    
                    <p class="mb-1 small text-muted">Dibuat Pada:</p>
                    <p class="font-weight-bold">{{ $product->created_at->format('d M Y H:i') }}</p>
                    
                    @if($product->verified_at)
                    <p class="mb-1 small text-muted">Diverifikasi Pada:</p>
                    <p class="font-weight-bold">{{ \Carbon\Carbon::parse($product->verified_at)->format('d M Y H:i') }}</p>
                    @endif
                    
                    <hr>
                    <div class="btn-group w-100">
                        @if($product->verification_status != 'verified')
                        <form action="{{ route('admin.bpom.verify', $product->id) }}" method="POST" class="w-100 mr-1">
                            @csrf
                            <button class="btn btn-success btn-block" onclick="return confirm('Verifikasi produk ini?')">
                                <i class="fas fa-check"></i> Verifikasi
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('admin.bpom.destroy', $product->id) }}" method="POST" class="w-100 ml-1">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-block" onclick="return confirm('Hapus data ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
