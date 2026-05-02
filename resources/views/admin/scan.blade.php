@extends('admin.master')

@section('title', 'Scan History | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Scan History</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">Scan History Log</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Monitor aktivitas scan produk secara real-time oleh seluruh pengguna Halalytics.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('admin.scan.export_pdf') }}" class="btn btn-outline" style="color: var(--danger); border-color: var(--danger);">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ url('/admin/scan/export') }}" class="btn btn-outline">
            <i class="fas fa-file-csv"></i> Export CSV
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 32px;">
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Scans Today</div>
            <div style="display: flex; align-items: baseline; gap: 12px;">
                <div style="font-size: 28px; font-weight: 800; color: var(--text-main);">{{ number_format($stats['today_scans']) }}</div>
                <div style="font-size: 12px; color: {{ $stats['scan_trend'] >= 0 ? 'var(--success)' : 'var(--danger)' }}; font-weight: 700;">
                    <i class="fas fa-arrow-{{ $stats['scan_trend'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($stats['scan_trend']) }}%
                </div>
            </div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Active Users (7d)</div>
            <div style="display: flex; align-items: baseline; gap: 12px;">
                <div style="font-size: 28px; font-weight: 800; color: var(--primary-color);">{{ number_format($stats['active_users']) }}</div>
                <div style="font-size: 12px; color: {{ $stats['user_trend'] >= 0 ? 'var(--success)' : 'var(--danger)' }}; font-weight: 700;">
                    <i class="fas fa-arrow-{{ $stats['user_trend'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($stats['user_trend']) }}%
                </div>
            </div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Haram Flags Today</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--danger);">{{ number_format($stats['haram_flags']) }}</div>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body">
        <form action="{{ url('/admin/scan') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1.5fr 1.5fr auto; gap: 16px; align-items: center;">
            <div class="input-group" style="position: relative; margin-bottom: 0;">
                <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search product, user, or barcode..." style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
            </div>
            
            <select name="status" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="all">All Status</option>
                <option value="halal" {{ request('status') == 'halal' ? 'selected' : '' }}>Halal Only</option>
                <option value="syubhat" {{ request('status') == 'syubhat' ? 'selected' : '' }}>Syubhat Only</option>
                <option value="haram" {{ request('status') == 'haram' ? 'selected' : '' }}>Haram Only</option>
            </select>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 11px; color: var(--text-muted); font-weight: 700;">FROM</span>
                <input type="date" name="date_from" value="{{ request('date_from') }}" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); font-size: 12px;">
            </div>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 11px; color: var(--text-muted); font-weight: 700;">TO</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); font-size: 12px;">
            </div>
            
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                <i class="fas fa-sync-alt"></i>
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
                        <th style="padding: 16px 24px;">User</th>
                        <th style="padding: 16px;">Product / Barcode</th>
                        <th style="padding: 16px;">Status</th>
                        <th style="padding: 16px;">Health</th>
                        <th style="padding: 16px;">Scan Time</th>
                        <th style="padding: 16px 24px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scans as $scan)
                    <tr>
                        <td style="padding: 16px 24px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--bg-light); display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color);">
                                    <i class="fas fa-user" style="font-size: 14px; color: var(--text-muted);"></i>
                                </div>
                                <div style="font-weight: 700; color: var(--text-main); font-size: 14px;">{{ $scan->user->username ?? 'Guest' }}</div>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-weight: 700; color: var(--text-main);">{{ $scan->nama_produk ?? 'Unknown Product' }}</div>
                            <div style="font-size: 11px; color: var(--text-muted); font-family: monospace;">{{ $scan->barcode }}</div>
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $status = strtolower($scan->status_halal);
                            @endphp
                            @if($status == 'halal')
                                <span class="badge badge-success">HALAL</span>
                            @elseif($status == 'syubhat' || $status == 'diragukan')
                                <span class="badge badge-warning">SYUBHAT</span>
                            @else
                                <span class="badge badge-danger">HARAM</span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @if($scan->status_kesehatan == 'sehat')
                                <span style="color: var(--success); font-size: 12px; font-weight: 700;"><i class="fas fa-heartbeat"></i> Healthy</span>
                            @else
                                <span style="color: var(--danger); font-size: 12px; font-weight: 700;"><i class="fas fa-heart-crack"></i> Alert</span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-size: 13px; color: var(--text-main);">{{ \Carbon\Carbon::parse($scan->tanggal_scan)->format('d M Y') }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">{{ \Carbon\Carbon::parse($scan->tanggal_scan)->format('H:i:s') }}</div>
                        </td>
                        <td style="padding: 16px 24px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('admin.scan.show', $scan->id_scan) }}" class="btn btn-outline" style="padding: 8px; color: var(--primary-color); border-color: var(--border-color);"><i class="fas fa-eye"></i></a>
                                <form action="{{ route('admin.scan.destroy', $scan->id_scan) }}" method="POST" onsubmit="return confirm('Hapus log scan ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--danger); border-color: var(--border-color);"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 64px; color: var(--text-muted);">No scan logs found for the selected period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer" style="padding: 16px 24px;">
        {{ $scans->links() }}
    </div>
</div>

@endsection
