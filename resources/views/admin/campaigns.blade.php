@extends('admin.master')

@section('title', 'Campaign Notifications | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Campaigns</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">Notification Campaigns</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Broadcast pesan push notification ke seluruh pengguna aplikasi melalui Firebase FCM.</p>
    </div>
    <div>
        <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary">
            <i class="fas fa-bullhorn"></i> Start New Campaign
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 24px; margin-bottom: 32px;">
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Total Campaigns</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-main);">{{ number_format($stats['total']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Successfully Sent</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--success);">{{ number_format($stats['sent']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Scheduled</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--accent-color);">{{ number_format($stats['scheduled']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Drafts</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-muted);">{{ number_format($stats['draft']) }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 16px;">Campaign Performance</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table style="border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="padding: 16px 24px;">Campaign Name</th>
                        <th style="padding: 16px;">Status</th>
                        <th style="padding: 16px;">Target</th>
                        <th style="padding: 16px;">Open Rate</th>
                        <th style="padding: 16px;">Sent Date</th>
                        <th style="padding: 16px 24px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                    <tr>
                        <td style="padding: 16px 24px;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 14px;">{{ $campaign->name }}</div>
                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">{{ Str::limit($campaign->title, 40) }}</div>
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $status = strtolower($campaign->status);
                            @endphp
                            @if($status == 'sent')
                                <span class="badge badge-success">SENT</span>
                            @elseif($status == 'scheduled')
                                <span class="badge badge-warning">SCHEDULED</span>
                            @elseif($status == 'draft')
                                <span class="badge" style="background: #eee; color: #777;">DRAFT</span>
                            @else
                                <span class="badge badge-danger">{{ strtoupper($status) }}</span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-size: 13px; font-weight: 700; color: var(--text-main);">{{ number_format($campaign->target_count) }}</div>
                            <div style="font-size: 10px; color: var(--text-muted);">Recipients</div>
                        </td>
                        <td style="padding: 16px;">
                            @if($campaign->sent_count > 0)
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="flex: 1; height: 6px; background: #eee; border-radius: 3px; max-width: 60px;">
                                        <div style="width: {{ $campaign->open_rate }}%; height: 100%; background: var(--primary-color); border-radius: 3px;"></div>
                                    </div>
                                    <span style="font-size: 12px; font-weight: 700; color: var(--primary-color);">{{ number_format($campaign->open_rate, 1) }}%</span>
                                </div>
                            @else
                                <span style="font-size: 12px; color: var(--text-muted);">--</span>
                            @endif
                        </td>
                        <td style="padding: 16px; font-size: 12px; color: var(--text-muted);">
                            {{ $campaign->created_at->diffForHumans() }}
                        </td>
                        <td style="padding: 16px 24px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('admin.campaigns.show', $campaign) }}" class="btn btn-outline" style="padding: 8px; color: var(--primary-color); border-color: var(--border-color);"><i class="fas fa-chart-bar"></i></a>
                                
                                @if($campaign->status === 'draft')
                                <form action="{{ route('admin.campaigns.send', $campaign) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--success); border-color: var(--success);" onclick="return confirm('Kirim campaign ini sekarang?')">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                                @endif
                                
                                @if(in_array($campaign->status, ['draft', 'scheduled']))
                                <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-outline" style="padding: 8px; color: var(--text-muted); border-color: var(--border-color);"><i class="fas fa-edit"></i></a>
                                @endif
                                
                                <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Hapus campaign ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--danger); border-color: var(--danger);"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 64px; color: var(--text-muted);">No campaigns found. Start reaching out to your users!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer" style="padding: 16px 24px;">
        {{ $campaigns->links() }}
    </div>
</div>

@endsection
