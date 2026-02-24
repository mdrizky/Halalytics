@extends('admin.master')

@section('title', 'Data Produk - Halalytics Admin')

@section('isi')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3 text-white">
        <h1 class="h3 mb-0 fw-bold">Manajemen Produk</h1>
        <a href="{{ route('admin.product.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus me-1"></i> Tambah Produk
        </a>
    </div>

    <!-- Filter Card -->
    <div class="glass-card mb-4 p-4">
        <form action="{{ route('admin.product.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-white-50"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control bg-dark border-secondary text-white" placeholder="Cari barcode atau nama..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select bg-dark border-secondary text-white">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id_kategori }}" {{ request('category') == $cat->id_kategori ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="halal_status" class="form-select bg-dark border-secondary text-white">
                    <option value="">Semua Status</option>
                    <option value="halal" {{ request('halal_status') == 'halal' ? 'selected' : '' }}>Halal</option>
                    <option value="syubhat" {{ request('halal_status') == 'syubhat' ? 'selected' : '' }}>Syubhat</option>
                    <option value="tidak halal" {{ request('halal_status') == 'tidak halal' ? 'selected' : '' }}>Haram</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-light w-100">Filter</button>
            </div>
        </form>
    </div>

    <!-- Local Table -->
    <div class="glass-card p-0 mb-4 overflow-hidden">
        <div class="p-4 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
            <h6 class="m-0 fw-bold text-white">Daftar Produk Lokal</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-glass mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Produk</th>
                            <th>Kategori</th>
                            <th>Status Halal</th>
                            <th>Scan</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($localProducts as $p)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white bg-opacity-10 rounded p-1 me-3 text-center d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        @if($p->image)
                                        <img src="{{ asset($p->image) }}" class="img-fluid rounded" style="max-height: 100%;" alt="">
                                        @else
                                        <i class="fas fa-box text-white-50 fs-4"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white">{{ $p->nama_product }}</div>
                                        <small class="text-white-50 font-monospace">{{ $p->barcode }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-white-50">{{ $p->kategori->nama_kategori ?? '-' }}</td>
                            <td>
                                @if($p->status == 'halal')
                                <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-3">Halal</span>
                                @elseif($p->status == 'syubhat')
                                <span class="badge bg-warning bg-opacity-25 text-warning rounded-pill px-3">Syubhat</span>
                                @else
                                <span class="badge bg-danger bg-opacity-25 text-danger rounded-pill px-3">Haram</span>
                                @endif
                            </td>
                            <td class="text-white-50">{{ number_format($p->scans_count) }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('admin.product.edit', $p->id_product) }}" class="btn btn-sm btn-outline-info me-1" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.product.destroy', $p->id_product) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 pt-3 pb-4">
            {{ $localProducts->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection