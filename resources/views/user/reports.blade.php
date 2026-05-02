@extends('user.layouts.app')

@section('title', 'Laporan Produk - Halalytics')

@section('content')
<section class="page-hero mb-4">
    <h1 class="display-6 fw-bold mb-2">Laporan & Pengaduan Produk</h1>
    <p class="mb-0 text-white-50">User bisa mengirim laporan baru dan melihat progres review dari admin.</p>
</section>

<section class="row g-4">
    <div class="col-lg-4">
        <div class="surface-card p-4">
            <h2 class="h4 fw-bold mb-3">Kirim Laporan Baru</h2>
            <form action="{{ route('user.reports.store') }}" method="POST" class="d-grid gap-3">
                @csrf
                <div>
                    <label class="form-label fw-semibold">Nama produk</label>
                    <input type="text" name="product_name" class="form-control rounded-4" value="{{ old('product_name') }}" required>
                </div>
                <div>
                    <label class="form-label fw-semibold">Alasan singkat</label>
                    <input type="text" name="reason" class="form-control rounded-4" value="{{ old('reason') }}" placeholder="mis. label meragukan">
                </div>
                <div>
                    <label class="form-label fw-semibold">Detail laporan</label>
                    <textarea name="laporan" rows="6" class="form-control rounded-4" required>{{ old('laporan') }}</textarea>
                </div>
                <button class="btn btn-brand rounded-pill" type="submit">Kirim Laporan</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="surface-card p-4">
            <h2 class="h4 fw-bold mb-3">Riwayat Laporan Saya</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Detail</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $report->product?->image ?? asset('images/default/general.svg') }}" alt="Produk" width="48" height="48" class="rounded-4 object-fit-cover border" onerror="this.onerror=null;this.src='{{ $report->product?->image_fallback_url ?? asset('images/default/general.svg') }}'">
                                        <div>
                                            <div class="fw-bold">{{ $report->product?->nama_product ?? 'Produk tidak tersedia' }}</div>
                                            <div class="small text-secondary">{{ $report->reason ?? 'Laporan umum' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-secondary">{{ \Illuminate\Support\Str::limit($report->laporan, 120) }}</td>
                                <td><span class="status-pill status-{{ strtolower($report->status) }}">{{ strtoupper($report->status) }}</span></td>
                                <td>{{ $report->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-secondary">Belum ada laporan yang dikirim.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $reports->links() }}
        </div>
    </div>
</section>
@endsection
