@extends('admin.layouts.admin_layout')

@section('title', 'Donor Participants - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Donor Participants</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Blood Donation Hub</h2>
        <p class="text-slate-500 text-sm mt-1">Manage blood stocks, events, appointments, and emergencies.</p>
    </div>
    <a href="{{ route('admin.blood-appointments.scanner') }}" class="flex items-center space-x-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all shadow-sm">
        <span class="material-icons-round text-sm">qr_code_scanner</span>
        <span class="text-sm font-medium">Scan QR Code</span>
    </a>
</div>

@include('admin.donor.tabs')

<div class="space-y-6">
    @foreach($events as $event)
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-slate-800 dark:text-white text-lg">{{ $event->title }}</h3>
            <p class="text-sm text-slate-500 mt-1">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }} &bull; {{ $event->location }}</p>
        </div>
        <div class="flex items-center space-x-6 text-center">
            <div>
                <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $event->registered_count }}</p>
                <p class="text-xs text-slate-500 uppercase font-semibold">Registered</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-amber-500">{{ $event->pending_count }}</p>
                <p class="text-xs text-slate-500 uppercase font-semibold">Pending Check-in</p>
            </div>
            <a href="{{ route('admin.blood-appointments.scanner') }}?event={{ $event->id }}" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                Manage Event
            </a>
        </div>
    </div>
    @endforeach

    @if($events->isEmpty())
    <div class="text-center py-12">
        <span class="material-icons-round text-5xl text-slate-300 mb-3">event_busy</span>
        <h3 class="text-lg font-bold text-slate-700">No Active Events</h3>
        <p class="text-slate-500">Create a blood event first to start receiving participants.</p>
    </div>
    @endif
</div>
@endsection
