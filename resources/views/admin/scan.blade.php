@extends('admin.master')

@section('title', 'Riwayat Scan - Halalytics Admin')

@section('isi')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3 text-white">
        <h1 class="h3 mb-0 fw-bold">Riwayat Scan</h1>
        <div class="btn-group">
            <a href="{{ route('admin.scan.export_pdf') }}" class="btn btn-outline-light rounded-pill px-4 me-2">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </a>
            <button onclick="location.reload()" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="glass-card mb-4 p-4">
        <form action="{{ route('admin.scan.index') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-white-50"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control bg-dark border-secondary text-white" placeholder="Cari barcode, nama produk, atau user..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select bg-dark border-secondary text-white">
                    <option value="all">Semua Status</option>
                    <option value="halal" {{ request('status') == 'halal' ? 'selected' : '' }}>Halal</option>
                    <option value="syubhat" {{ request('status') == 'syubhat' ? 'selected' : '' }}>Syubhat</option>
                    <option value="haram" {{ request('status') == 'haram' ? 'selected' : '' }}>Haram</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-light w-100">Filter</button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="glass-card p-0 mb-4 overflow-hidden">
        <div class="p-4 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
            <h6 class="m-0 fw-bold text-white">Log Aktivitas Scan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-glass mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">User</th>
                            <th>Produk</th>
                            <th>Barcode</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scans as $scan)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-white bg-opacity-10 d-flex justify-content-center align-items-center me-3 text-white fw-bold" style="width: 36px; height: 36px">
                                        {{ strtoupper(substr($scan->user->username ?? 'G', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-white">{{ $scan->user->username ?? 'Guest' }}</div>
                                        <small class="text-white-50">{{ $scan->user->email ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-success">{{ $scan->nama_produk ?? 'Unknown' }}</div>
                                <small class="text-white-50">Kategori: {{ $scan->kategori ?? '-' }}</small>
                            </td>
                            <td><code class="text-white">{{ $scan->barcode }}</code></td>
                            <td>
                                @if($scan->status_halal == 'halal')
                                <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-3">Halal</span>
                                @elseif($scan->status_halal == 'syubhat')
                                <span class="badge bg-warning bg-opacity-25 text-warning rounded-pill px-3">Syubhat</span>
                                @else
                                <span class="badge bg-danger bg-opacity-25 text-danger rounded-pill px-3">Haram</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-white">{{ \Carbon\Carbon::parse($scan->tanggal_scan)->format('d/m/Y') }}</div>
                                <small class="text-white-50">{{ \Carbon\Carbon::parse($scan->tanggal_scan)->format('H:i') }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-white-50">Tidak ada riwayat scan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $scans->links() }}
        </div>
    </div>
</div>
@endsection