@extends('admin.layouts.admin_layout')

@section('title', 'Blood Events - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Blood Events</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Blood Donation Hub</h2>
        <p class="text-slate-500 text-sm mt-1">Manage blood stocks, events, appointments, and emergencies.</p>
    </div>
    <a href="{{ route('admin.blood-events.create') }}" class="flex items-center space-x-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all shadow-sm">
        <span class="material-icons-round text-sm">add</span>
        <span class="text-sm font-medium">Create Event</span>
    </a>
</div>

@include('admin.donor.tabs')

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <th class="px-6 py-4">Event Details</th>
                    <th class="px-6 py-4">Date & Time</th>
                    <th class="px-6 py-4">Quota</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($events as $event)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-slate-800 dark:text-white text-sm">{{ $event->title }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $event->location }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $event->start_time }} - {{ $event->end_time }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-16 h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-primary" style="width: {{ ($event->registered_count / max(1, $event->quota)) * 100 }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-slate-600 dark:text-slate-400">
                                {{ $event->registered_count }}/{{ $event->quota }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($event->status == 'active')
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full text-xs font-semibold">Active</span>
                        @elseif($event->status == 'draft')
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 rounded-full text-xs font-semibold">Draft</span>
                        @else
                        <span class="px-2.5 py-1 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 rounded-full text-xs font-semibold">{{ ucfirst($event->status) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.blood-events.edit', $event->id) }}" class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-icons-round text-sm">edit</span>
                        </a>
                        <form action="{{ route('admin.blood-events.destroy', $event->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors">
                                <span class="material-icons-round text-sm">delete</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
        {{ $events->links() }}
    </div>
</div>
@endsection
