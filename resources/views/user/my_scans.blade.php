@extends('master')
@section('isi')
<div class="container py-5" style="background-color: #121212; min-height: 100vh; color: #E0E0E0;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #3A9D66; font-weight: 700;"><i class="fas fa-history me-3"></i>Riwayat Scan Saya</h2>
        <a href="{{ url('/user') }}" class="btn btn-outline-success"><i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard</a>
    </div>

    <div class="card bg-dark border-secondary shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead class="table-secondary text-dark">
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
                                    <div class="rounded bg-secondary d-flex align-items-center justify-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-box"></i>
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
