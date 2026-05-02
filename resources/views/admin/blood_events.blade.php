@extends('admin.master')

@section('title', 'Blood Donor Events | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Blood Events</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">Donor Event Management</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Atur jadwal kegiatan donor darah dan pantau pendaftaran relawan.</p>
    </div>
    <div>
        <a href="{{ route('admin.blood-events.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Event
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table style="border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="padding: 16px 24px;">Event Info</th>
                        <th style="padding: 16px;">Venue / Address</th>
                        <th style="padding: 16px;">Schedule</th>
                        <th style="padding: 16px;">Quota</th>
                        <th style="padding: 16px;">Status</th>
                        <th style="padding: 16px 24px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr>
                        <td style="padding: 16px 24px;">
                            <div style="font-weight: 700; color: var(--text-main); font-size: 14px;">{{ $event->title }}</div>
                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Org: {{ $event->organizer }}</div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-size: 13px; font-weight: 700; color: var(--text-main);">{{ $event->location }}</div>
                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">{{ Str::limit($event->address, 40) }}</div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-size: 13px; font-weight: 700;">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-size: 13px; font-weight: 700;">{{ $event->quota }} <span style="font-size: 11px; color: var(--text-muted); font-weight: 400;">Bags</span></div>
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $status = strtolower($event->status);
                            @endphp
                            @if($status == 'active')
                                <span class="badge badge-success">ACTIVE</span>
                            @elseif($status == 'completed')
                                <span class="badge" style="background: var(--bg-light); color: var(--text-muted);">COMPLETED</span>
                            @elseif($status == 'cancelled')
                                <span class="badge badge-danger">CANCELLED</span>
                            @else
                                <span class="badge" style="background: #eee; color: #777;">{{ strtoupper($status) }}</span>
                            @endif
                        </td>
                        <td style="padding: 16px 24px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('admin.blood-events.edit', $event->id) }}" class="btn btn-outline" style="padding: 8px; color: var(--primary-color); border-color: var(--border-color);"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.blood-events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Hapus event ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--danger); border-color: var(--border-color);"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 64px; color: var(--text-muted);">No blood donor events found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer" style="padding: 16px 24px;">
        {{ $events->links() }}
    </div>
</div>
@endsection
