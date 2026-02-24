@extends('master')
@section('isi')
<div class="container py-5" style="background-color: #121212; min-height: 100vh; color: #E0E0E0;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #3A9D66; font-weight: 700;"><i class="fas fa-flag me-3"></i>Laporan & Pengaduan</h2>
        <a href="{{ url('/user') }}" class="btn btn-outline-success"><i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show bg-success text-white border-0 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- New Report Form -->
        <div class="col-lg-4">
            <div class="card bg-dark border-secondary shadow mb-4">
                <div class="card-header bg-secondary text-white font-weight-bold">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Laporan Baru
                </div>
                <div class="card-body">
                    <form action="{{ url('/reports') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted">Nama Produk</label>
                            <input type="text" name="product_name" class="form-control bg-dark border-secondary text-white" placeholder="Contoh: Indomie Goreng" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Detail Laporan</label>
                            <textarea name="laporan" class="form-control bg-dark border-secondary text-white" rows="5" placeholder="Jelaskan masalah halal atau keraguan Anda pada produk ini..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100 font-weight-bold">Kirim Laporan</button>
                    </form>
                </div>
            </div>
            
            <div class="card bg-dark border-secondary shadow">
                <div class="card-body text-center p-4">
                    <i class="fas fa-info-circle fa-2x text-info mb-3"></i>
                    <p class="small text-muted mb-0">Laporan Anda sangat berharga bagi komunitas Halalytics. Setiap laporan akan ditinjau oleh tim ahli kami.</p>
                </div>
            </div>
        </div>

        <!-- Reports List -->
        <div class="col-lg-8">
            <div class="card bg-dark border-secondary shadow h-100">
                <div class="card-header bg-dark border-secondary text-success font-weight-bold">
                    Riwayat Laporan Saya
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr class="text-muted small uppercase">
                                    <th class="ps-4">No</th>
                                    <th>Produk</th>
                                    <th>Detail</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $index => $report)
                                <tr>
                                    <td class="ps-4">{{ $reports->firstItem() + $index }}</td>
                                    <td class="font-weight-bold text-success">{{ $report->product_name }}</td>
                                    <td style="max-width: 250px;"><div class="truncate">{{ $report->laporan }}</div></td>
                                    <td>
                                        @php
                                            $badgeClass = 'bg-secondary';
                                            if($report->status == 'approved') $badgeClass = 'bg-success';
                                            else if($report->status == 'rejected') $badgeClass = 'bg-danger';
                                            else if($report->status == 'pending') $badgeClass = 'bg-warning text-dark';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-2 py-1">{{ strtoupper($report->status) }}</span>
                                    </td>
                                    <td class="small">{{ $report->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted"> Belum ada laporan yang diajukan. </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($reports->hasPages())
                <div class="card-footer bg-transparent border-secondary py-3">
                    {{ $reports->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection
