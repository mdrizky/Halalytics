@extends('admin.master')

@section('title', 'Laporan Produk - Halalytics Admin')

@section('isi')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3 text-white">
        <h1 class="h3 mb-0 fw-bold">Laporan Produk</h1>
        <div class="btn-group">
            <button onclick="runAiAnalysis()" class="btn btn-primary rounded-pill px-4 me-2">
                <i class="fas fa-magic me-1"></i> Smart AI Verify
            </button>
            <a href="{{ route('admin.report.export_pdf') }}" class="btn btn-outline-light rounded-pill px-4">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-25 p-3 me-3">
                        <i class="fas fa-file-alt text-primary fs-3"></i>
                    </div>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold">Total Laporan</div>
                        <div class="h3 mb-0 fw-bold text-white">{{ number_format($totalReports) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-25 p-3 me-3">
                        <i class="fas fa-clock text-warning fs-3"></i>
                    </div>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold">Pending</div>
                        <div class="h3 mb-0 fw-bold text-white">{{ number_format($pendingReports) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="glass-card p-0 mb-4 overflow-hidden">
        <div class="p-4 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-white">Daftar Laporan Terkini</h6>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                        Status: {{ ucfirst(request('status')) ?? 'Semua' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark shadow">
                        <li><a class="dropdown-item" href="{{ route('admin.report.index') }}">Semua</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.report.index', ['status' => 'pending']) }}">Pending</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.report.index', ['status' => 'approved']) }}">Approved</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.report.index', ['status' => 'rejected']) }}">Rejected</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-glass mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">User</th>
                            <th>Produk</th>
                            <th>Isi Laporan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-white">{{ $report->user->username ?? 'Guest' }}</span>
                                <br><small class="text-white-50">{{ $report->user->email ?? '' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-white">{{ $report->product->nama_product ?? 'Unknown' }}</div>
                                <small class="text-white-50">{{ $report->product->barcode ?? '-' }}</small>
                            </td>
                            <td><div class="text-truncate text-white-50" style="max-width: 200px;">{{ $report->laporan }}</div></td>
                            <td>
                                @if($report->status == 'pending')
                                <span class="badge bg-warning bg-opacity-25 text-warning rounded-pill px-3">Pending</span>
                                @elseif($report->status == 'approved')
                                <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-3">Approved</span>
                                @else
                                <span class="badge bg-danger bg-opacity-25 text-danger rounded-pill px-3">Rejected</span>
                                @endif
                            </td>
                            <td class="text-white-50">{{ $report->created_at->format('d M Y') }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <form action="{{ route('admin.report.update_status', $report->id_report) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm btn-outline-success me-1" title="Approve"><i class="fas fa-check"></i></button>
                                    </form>
                                    <form action="{{ route('admin.report.update_status', $report->id_report) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-sm btn-outline-danger me-1" title="Reject"><i class="fas fa-times"></i></button>
                                    </form>
                                    <form action="{{ route('admin.report.destroy', $report->id_report) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus laporan?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-white-50">Belum ada laporan yang masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $reports->links() }}
        </div>
    </div>
</div>

<script>
function runAiAnalysis() {
    if(!confirm('Jalankan analisis AI untuk semua laporan pending?')) return;
    
    const btn = event.currentTarget;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Analyzing...';

    fetch('{{ route("admin.report.batch_verify") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        location.reload();
    })
    .catch(() => {
        alert('Terjadi kesalahan saat analisis AI');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}
</script>
@endsection