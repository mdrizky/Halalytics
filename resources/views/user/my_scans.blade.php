@extends('user.layouts.app')

@section('title', 'Riwayat Scan - Halalytics')

@section('content')
<section class="page-hero mb-4">
    <h1 class="display-6 fw-bold mb-2">Riwayat Scan Saya</h1>
    <p class="mb-0 text-white-50">Semua scan user ditampilkan lengkap dengan thumbnail produk dan status halal.</p>
</section>

<section class="surface-card p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Barcode</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scans as $scan)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $scan->product?->image ?? asset('images/default/general.svg') }}" alt="{{ $scan->nama_produk }}" width="52" height="52" class="rounded-4 object-fit-cover border" onerror="this.onerror=null;this.src='{{ $scan->product?->image_fallback_url ?? asset('images/default/general.svg') }}'">
                                <div>
                                    <div class="fw-bold">{{ $scan->nama_produk }}</div>
                                    <div class="small text-secondary">{{ $scan->kategori ?: 'Tanpa kategori' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $scan->barcode ?: '-' }}</td>
                        <td><span class="status-pill status-{{ str_replace(' ', '_', strtolower($scan->status_halal ?? 'pending')) }}">{{ strtoupper($scan->status_halal ?? 'UNKNOWN') }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($scan->tanggal_scan)->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-secondary">Belum ada riwayat scan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $scans->links() }}
</section>
@endsection
