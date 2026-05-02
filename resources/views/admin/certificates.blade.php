@extends('admin.master')

@section('title', 'Halal Certificates | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Halal Certificates</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">Halal Certificate Repository</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Basis data sertifikat halal dari BPJPH, MUI, dan lembaga sertifikasi halal internasional.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <button class="btn btn-outline" onclick="openImportModal()">
            <i class="fas fa-file-import"></i> Import CSV
        </button>
        <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Certificate
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Total Certificates</div>
            <div style="font-size: 24px; font-weight: 800; color: var(--text-main);">{{ number_format($stats['total']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Active</div>
            <div style="font-size: 24px; font-weight: 800; color: var(--success);">{{ number_format($stats['active']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Expired</div>
            <div style="font-size: 24px; font-weight: 800; color: var(--danger);">{{ number_format($stats['expired']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Expiring Soon</div>
            <div style="font-size: 24px; font-weight: 800; color: var(--accent-color);">{{ number_format($stats['expiring_soon']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Revoked</div>
            <div style="font-size: 24px; font-weight: 800; color: #777;">{{ number_format($stats['revoked']) }}</div>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body">
        <form action="{{ route('admin.certificates.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 16px; align-items: center;">
            <div class="input-group" style="position: relative; margin-bottom: 0;">
                <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by number, product, or manufacturer..." style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
            </div>
            
            <select name="status" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired Only</option>
                <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>Revoked Only</option>
            </select>
            
            <select name="issuing_body" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="">All Issuers</option>
                <option value="BPJPH" {{ request('issuing_body') == 'BPJPH' ? 'selected' : '' }}>BPJPH (Kemenag)</option>
                <option value="MUI" {{ request('issuing_body') == 'MUI' ? 'selected' : '' }}>MUI</option>
                <option value="LPPOM" {{ request('issuing_body') == 'LPPOM' ? 'selected' : '' }}>LPPOM</option>
            </select>
            
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                <i class="fas fa-filter"></i>
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
                        <th style="padding: 16px 24px;">Certificate Info</th>
                        <th style="padding: 16px;">Product / Manufacturer</th>
                        <th style="padding: 16px;">Issuer</th>
                        <th style="padding: 16px;">Expiry Date</th>
                        <th style="padding: 16px;">Status</th>
                        <th style="padding: 16px 24px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $cert)
                    <tr>
                        <td style="padding: 16px 24px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(45, 106, 79, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-certificate" style="font-size: 16px; color: var(--primary-color);"></i>
                                </div>
                                <div>
                                    <div style="font-family: monospace; font-weight: 700; color: var(--primary-color); font-size: 13px;">{{ $cert->certificate_number }}</div>
                                    <div style="font-size: 10px; color: var(--text-muted); margin-top: 2px;">ID: {{ $cert->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 13px;">{{ Str::limit($cert->product_name, 35) }}</div>
                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">{{ Str::limit($cert->manufacturer, 30) }}</div>
                        </td>
                        <td style="padding: 16px;">
                            <span class="badge" style="background: var(--bg-light); color: var(--text-main); border: 1px solid var(--border-color);">{{ $cert->issuing_body }}</span>
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $expiry = \Carbon\Carbon::parse($cert->expires_at);
                                $isExpired = $expiry->isPast();
                                $isSoon = !$isExpired && $expiry->diffInDays(now()) < 30;
                            @endphp
                            <div style="font-size: 12px; font-weight: 700; color: {{ $isExpired ? 'var(--danger)' : ($isSoon ? 'var(--accent-color)' : 'var(--text-main)') }}">
                                {{ $expiry->format('d M Y') }}
                            </div>
                            @if($isSoon)
                                <div style="font-size: 10px; color: var(--accent-color); margin-top: 2px;"><i class="fas fa-clock"></i> Expiring Soon</div>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @if($cert->status === 'active')
                                <span class="badge badge-success">ACTIVE</span>
                            @elseif($cert->status === 'expired')
                                <span class="badge badge-danger">EXPIRED</span>
                            @else
                                <span class="badge" style="background: #eee; color: #777;">REVOKED</span>
                            @endif
                        </td>
                        <td style="padding: 16px 24px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('admin.certificates.edit', $cert) }}" class="btn btn-outline" style="padding: 8px; color: var(--primary-color); border-color: var(--border-color);"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.certificates.destroy', $cert) }}" method="POST" onsubmit="return confirm('Hapus sertifikat ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--danger); border-color: var(--border-color);"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 64px; color: var(--text-muted);">No halal certificates found in the repository.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer" style="padding: 16px 24px;">
        {{ $certificates->withQueryString()->links() }}
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; padding: 32px; border-radius: 16px; width: 100%; max-width: 500px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <h2 style="margin: 0 0 8px; font-size: 22px; color: var(--primary-color);">Import from CSV</h2>
        <p style="margin: 0 0 24px; color: var(--text-muted); font-size: 14px;">Upload file CSV dengan kolom: certificate_number, product_name, manufacturer, issuing_body, issued_at, expires_at.</p>
        
        <form action="{{ route('admin.certificates.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group" style="margin-bottom: 24px;">
                <input type="file" name="csv_file" accept=".csv" required style="width: 100%; padding: 12px; border: 2px dashed var(--border-color); border-radius: 8px;">
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeImportModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Start Import</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openImportModal() {
        document.getElementById('importModal').style.display = 'flex';
    }
    
    function closeImportModal() {
        document.getElementById('importModal').style.display = 'none';
    }
</script>
@endpush
