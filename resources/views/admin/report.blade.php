@extends('admin.master')

@section('title', 'User Reports | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Reports</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">User Reports</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Review laporan ketidaksesuaian data, pemalsuan sertifikat, atau konten dari pengguna.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <button class="btn btn-accent" onclick="runAiBatchVerify()">
            <i class="fas fa-robot"></i> Smart AI Analysis
        </button>
        <a href="{{ route('admin.report.export_pdf') }}" class="btn btn-outline" style="color: var(--danger); border-color: var(--danger);">
            <i class="fas fa-file-pdf"></i> Export Summary
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 32px;">
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Pending Reports</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--accent-color);">{{ number_format($stats['pending_reports']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Resolved Today</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--primary-color);">{{ number_format($stats['resolved_today']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Rejection Rate</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--danger);">{{ $stats['rejection_rate'] }}%</div>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body">
        <form action="{{ url('/admin/reports') }}" method="GET" style="display: grid; grid-template-columns: 1.5fr 1.5fr auto; gap: 16px; align-items: center;">
            <select name="status" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="all">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Only</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved Only</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected Only</option>
            </select>
            
            <select name="reason" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="all">All Reasons</option>
                <option value="incorrect_status" {{ request('reason') == 'incorrect_status' ? 'selected' : '' }}>Incorrect Halal Status</option>
                <option value="expired_cert" {{ request('reason') == 'expired_cert' ? 'selected' : '' }}>Expired Certificate</option>
                <option value="fake_forgery" {{ request('reason') == 'fake_forgery' ? 'selected' : '' }}>Fake/Forgery Logo</option>
                <option value="other" {{ request('reason') == 'other' ? 'selected' : '' }}>Other Reasons</option>
            </select>
            
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                <i class="fas fa-sync-alt"></i> Filter
            </button>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table style="border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="padding: 16px 24px;">Reporter</th>
                        <th style="padding: 16px;">Product Affected</th>
                        <th style="padding: 16px;">Issue / Description</th>
                        <th style="padding: 16px;">Status</th>
                        <th style="padding: 16px;">Time</th>
                        <th style="padding: 16px 24px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td style="padding: 16px 24px;">
                            <div style="font-weight: 700; color: var(--text-main);">{{ $report->user->username ?? 'Anonymous' }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">UID: #{{ $report->user_id }}</div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-weight: 700; color: var(--text-main);">{{ $report->product->nama_product ?? 'Product Removed' }}</div>
                            <div style="font-size: 11px; color: var(--primary-color);">ID: {{ $report->product_id }}</div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 13px;">{{ Str::headline($report->reason) }}</div>
                            <div style="max-width: 250px; font-size: 11px; color: var(--text-muted); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $report->laporan }}
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $status = strtolower($report->status);
                            @endphp
                            @if($status == 'pending')
                                <span class="badge badge-warning">PENDING</span>
                            @elseif($status == 'approved' || $status == 'resolved')
                                <span class="badge badge-success">RESOLVED</span>
                            @else
                                <span class="badge" style="background: #eee; color: #777;">REJECTED</span>
                            @endif
                        </td>
                        <td style="padding: 16px; font-size: 12px; color: var(--text-muted);">
                            {{ $report->created_at->diffForHumans() }}
                        </td>
                        <td style="padding: 16px 24px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                @if($status == 'pending')
                                <button onclick="updateReportStatus({{ $report->id }}, 'approved')" class="btn btn-outline" style="padding: 8px; color: var(--primary-color); border-color: var(--primary-color);" title="Approve & Resolve"><i class="fas fa-check"></i></button>
                                <button onclick="updateReportStatus({{ $report->id }}, 'rejected')" class="btn btn-outline" style="padding: 8px; color: var(--danger); border-color: var(--danger);" title="Reject Report"><i class="fas fa-times"></i></button>
                                @endif
                                <form action="{{ route('admin.report.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Hapus laporan ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--text-muted); border-color: var(--border-color);"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 64px; color: var(--text-muted);">All reports are processed. Great job!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer" style="padding: 16px 24px;">
        {{ $reports->links() }}
    </div>
</div>

@endsection

@push('scripts')
<script>
    function updateReportStatus(id, status) {
        if (!confirm(`Ubah status laporan menjadi ${status.toUpperCase()}?`)) return;
        
        fetch(`/admin/reports/${id}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
    
    function runAiBatchVerify() {
        if (!confirm('Jalankan Batch Smart AI Analysis? AI akan meninjau semua laporan pending berdasarkan komposisi produk.')) return;
        
        const btn = event.currentTarget;
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
        btn.disabled = true;
        
        fetch('/admin/reports/batch-verify', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = originalContent;
            btn.disabled = false;
        });
    }
</script>
@endpush
