@extends('master')
@section('isi')
<div class="container py-5" style="background-color: #F4F9F8; min-height: 100vh; color: #163832;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #004D40; font-weight: 700;"><i class="fas fa-history me-3"></i>Riwayat Scan Saya</h2>
        <a href="{{ url('/user') }}" class="btn btn-outline-success" style="border-color:#26A69A;color:#004D40;"><i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard</a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:20px;background:#ffffff;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#E0F2F1;color:#004D40;">
                        <tr>
                            <th class="ps-4">Produk</th>
                            <th>Barcode</th>
                            <th>Status Halal</th>
                            <th>Tanggal Scan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scans as $scan)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded overflow-hidden d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;background:#E0F2F1;">
                                        <img src="{{ $scan->product->image ?? asset('images/placeholders/product-placeholder.svg') }}" alt="{{ $scan->nama_produk }}" style="width:100%;height:100%;object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('images/placeholders/product-placeholder.svg') }}'">
                                    </div>
                                    <span class="font-weight-medium">{{ $scan->nama_produk }}</span>
                                </div>
                            </td>
                            <td>{{ $scan->barcode ?: '-' }}</td>
                            <td>
                                @php
                                    $statusClass = 'bg-secondary';
                                    if(strtolower($scan->status_halal) == 'halal') $statusClass = 'bg-success';
                                    else if(strtolower($scan->status_halal) == 'haram') $statusClass = 'bg-danger';
                                    else if(strtolower($scan->status_halal) == 'syubhat' || strtolower($scan->status_halal) == 'diragukan') $statusClass = 'bg-warning text-dark';
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ strtoupper($scan->status_halal) }}</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($scan->tanggal_scan)->format('d M Y, H:i') }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info" title="Detail"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-search-minus fa-3x mb-3"></i>
                                <p>Belum ada riwayat scan. Mulailah menscan produk Anda!</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-4 d-flex justify-content-center">
        {{ $scans->links() }}
    </div>
</div>
@endsection
