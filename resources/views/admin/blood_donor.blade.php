@extends('admin.master')

@section('title', 'Blood Donor Central | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Blood Donation</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: #e74c3c;">Blood Donor Central</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Monitor stok darah, kelola event donor, dan tanggapi permintaan darurat secara real-time.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('admin.blood-emergency.index') }}" class="btn btn-outline" style="color: #e74c3c; border-color: #e74c3c;">
            <i class="fas fa-exclamation-triangle"></i> Broadcast Emergency
        </a>
        <a href="{{ route('admin.blood-events.create') }}" class="btn btn-primary" style="background: #e74c3c; border-color: #e74c3c;">
            <i class="fas fa-calendar-plus"></i> Create Donor Event
        </a>
    </div>
</div>

<!-- Blood Stock Level Visualization -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 32px;">
    @php
        $types = ['A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-'];
        // Mocking stock levels if summary is empty
        $stockMap = $summary->pluck('total_bags', 'blood_type')->toArray();
    @endphp
    
    @foreach(['A', 'B', 'AB', 'O'] as $type)
        @php
            $pos = $stockMap[$type.'+'] ?? 0;
            $neg = $stockMap[$type.'-'] ?? 0;
            $total = $pos + $neg;
            $percentage = min(100, ($total / 50) * 100); // Max 50 bags for 100% scale
        @endphp
        <div class="card" style="border: 1px solid #fecaca; background: white; transition: transform 0.3s;">
            <div class="card-body" style="padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div style="font-size: 32px; font-weight: 900; color: #e74c3c;">{{ $type }}</div>
                    <div style="text-align: right;">
                        <div style="font-size: 20px; font-weight: 800; color: var(--text-main);">{{ $total }} <span style="font-size: 12px; font-weight: 600; color: var(--text-muted);">Bags</span></div>
                        <div style="font-size: 11px; color: var(--text-muted); font-weight: 700;">STOK LEVEL</div>
                    </div>
                </div>
                <div style="height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden; margin-bottom: 16px;">
                    <div style="width: {{ $percentage }}%; height: 100%; background: linear-gradient(90deg, #f87171, #ef4444); border-radius: 4px;"></div>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 700;">
                    <span style="color: #666;">Positive: <span style="color: #e74c3c;">{{ $pos }}</span></span>
                    <span style="color: #666;">Negative: <span style="color: #e74c3c;">{{ $neg }}</span></span>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Active Events Section -->
    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 16px; color: var(--text-main);">Scheduled Donor Events</h3>
                <a href="{{ route('admin.blood-events.index') }}" style="font-size: 13px; color: var(--primary-color); font-weight: 700; text-decoration: none;">View All</a>
            </div>
            <div class="table-container">
                <table style="border-collapse: collapse; width: 100%;">
                    <thead>
                        <tr>
                            <th style="padding: 16px 24px;">Event Details</th>
                            <th style="padding: 16px;">Date & Time</th>
                            <th style="padding: 16px;">Quota / Registrations</th>
                            <th style="padding: 16px 24px; text-align: right;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr>
                            <td style="padding: 16px 24px;">
                                <div style="font-weight: 700; color: var(--text-main); font-size: 14px;">{{ $event->title }}</div>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;"><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</div>
                            </td>
                            <td style="padding: 16px;">
                                <div style="font-size: 13px; font-weight: 700;">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</div>
                            </td>
                            <td style="padding: 16px;">
                                @php
                                    $regCount = $event->appointments_count ?? 0;
                                    $quota = $event->quota ?: 1;
                                    $fillPercent = min(100, ($regCount / $quota) * 100);
                                @endphp
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="flex: 1; height: 6px; background: #eee; border-radius: 3px; overflow: hidden;">
                                        <div style="width: {{ $fillPercent }}%; height: 100%; background: var(--primary-color);"></div>
                                    </div>
                                    <span style="font-size: 12px; font-weight: 700; color: var(--text-main);">{{ $regCount }}/{{ $quota }}</span>
                                </div>
                            </td>
                            <td style="padding: 16px 24px; text-align: right;">
                                <span class="badge" style="background: {{ $event->status == 'active' ? 'rgba(45, 106, 79, 0.1)' : 'rgba(231, 76, 60, 0.1)' }}; color: {{ $event->status == 'active' ? 'var(--primary-color)' : 'var(--danger)' }};">
                                    {{ strtoupper($event->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 48px; color: var(--text-muted);">No upcoming events scheduled.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Emergency Requests Feed -->
    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-heartbeat" style="color: #e74c3c;"></i>
                <h3 style="margin: 0; font-size: 16px; color: var(--text-main);">Emergency Alerts</h3>
            </div>
            <div style="padding: 12px; max-height: 500px; overflow-y: auto;">
                @forelse($emergencies ?? [] as $req)
                <div style="padding: 16px; border-radius: 12px; background: #fff5f5; border: 1px solid #feb2b2; margin-bottom: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <span style="background: #e74c3c; color: white; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 800;">URGENT</span>
                        <span style="font-size: 10px; color: #991b1b; font-weight: 700;">{{ \Carbon\Carbon::parse($req->created_at)->diffForHumans() }}</span>
                    </div>
                    <div style="font-size: 14px; font-weight: 700; color: #991b1b; margin-bottom: 4px;">{{ $req->blood_type }} Needed at {{ $req->hospital_name }}</div>
                    <div style="font-size: 12px; color: #b91c1c; margin-bottom: 10px;">{{ $req->patient_name }} · {{ $req->bags_needed }} bags</div>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-primary" style="flex: 1; padding: 8px; font-size: 11px; background: #e74c3c; border-color: #e74c3c;">Verify</button>
                        <button class="btn btn-outline" style="flex: 1; padding: 8px; font-size: 11px; color: #e74c3c; border-color: #feb2b2;">Share</button>
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 48px; color: var(--text-muted);">
                    <i class="fas fa-check-circle" style="font-size: 32px; margin-bottom: 12px; opacity: 0.2;"></i>
                    <div style="font-size: 13px;">No active emergency requests.</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
