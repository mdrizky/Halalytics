@extends('admin.layouts.admin_layout')

@section('title', 'Notification Campaigns - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Admin</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Notification Campaigns</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Push Notification Campaigns</h2>
        <p class="text-slate-500 text-sm mt-1">Create and manage push notification campaigns via Firebase FCM.</p>
    </div>
    <a href="{{ route('admin.campaigns.create') }}" class="px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition flex items-center space-x-2">
        <span class="material-icons-round text-sm">campaign</span>
        <span>New Campaign</span>
    </a>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-xl text-sm font-medium">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 text-red-600 rounded-xl text-sm font-medium">{{ session('error') }}</div>
@endif

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['Total', $stats['total'], 'campaign', 'primary'],
        ['Draft', $stats['draft'], 'edit_note', 'slate-500'],
        ['Sent', $stats['sent'], 'send', 'emerald-500'],
        ['Scheduled', $stats['scheduled'], 'schedule', 'amber-500'],
    ] as [$label, $count, $icon, $color])
    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
        <div class="flex items-center space-x-3">
            <span class="material-icons-round text-{{ $color }}">{{ $icon }}</span>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $count }}</p>
                <p class="text-xs text-slate-500">{{ $label }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Campaigns Table -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    <th class="px-6 py-3">Campaign</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Target</th>
                    <th class="px-6 py-3">Sent</th>
                    <th class="px-6 py-3">Open Rate</th>
                    <th class="px-6 py-3">Created</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($campaigns as $campaign)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $campaign->name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $campaign->title }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @php $statusColors = ['draft'=>'slate', 'scheduled'=>'amber', 'sending'=>'blue', 'sent'=>'emerald', 'failed'=>'red']; @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-{{ $statusColors[$campaign->status] ?? 'slate' }}-100 text-{{ $statusColors[$campaign->status] ?? 'slate' }}-600">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ number_format($campaign->target_count) }}</td>
                    <td class="px-6 py-4 text-sm font-bold">{{ number_format($campaign->sent_count) }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($campaign->sent_count > 0)
                            {{ number_format($campaign->open_rate, 1) }}%
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-400">{{ $campaign->created_at->diffForHumans() }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.campaigns.show', $campaign) }}" class="text-slate-500 font-bold text-sm hover:underline">View</a>
                            @if($campaign->status === 'draft')
                            <form action="{{ route('admin.campaigns.send', $campaign) }}" method="POST" onsubmit="return confirm('Send this campaign now?')">
                                @csrf
                                <button class="text-emerald-500 font-bold text-sm hover:underline">Send</button>
                            </form>
                            @endif
                            @if(in_array($campaign->status, ['draft', 'scheduled']))
                            <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="text-primary font-bold text-sm hover:underline">Edit</a>
                            @endif
                            <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 font-bold text-sm hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                        <span class="material-icons-round text-4xl mb-2">notifications_off</span>
                        <p>No campaigns yet</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
        {{ $campaigns->links() }}
    </div>
</div>
@endsection
