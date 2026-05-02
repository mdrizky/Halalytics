@extends('admin.layouts.admin_layout')

@section('title', 'Broadcast Center - Halalytics Admin')
@section('breadcrumb-parent', 'Communication')
@section('breadcrumb-current', 'Broadcast Center')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Push Notifications</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-2xl">
                Manage and monitor push notification campaigns sent to the mobile application via Firebase Cloud Messaging.
            </p>
        </div>
        <a href="{{ route('admin.notifications.create') }}" class="inline-flex items-center gap-3 rounded-2xl bg-primary px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all transform hover:-translate-y-1 group">
            <span class="material-icons-round text-lg group-hover:rotate-12 transition-transform">send</span>
            COMPOSE BROADCAST
        </a>
    </div>

    <!-- Quick Insights -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Broadcast Volume</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($notifications->total()) }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Total campaigns sent</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-icons-round text-2xl">campaign</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Reach</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($notifications->sum('sent_count')) }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Cumulative deliveries</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 flex items-center justify-center">
                    <span class="material-icons-round text-2xl">people_alt</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Success Rate</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    @php
                        $sent = $notifications->getCollection()->filter(fn($n) => $n->status == 'sent')->count();
                        $rate = $notifications->count() > 0 ? ($sent / $notifications->count()) * 100 : 0;
                    @endphp
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($rate, 1) }}%</h3>
                    <p class="text-xs text-slate-500 mt-1">Delivery confidence</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 flex items-center justify-center">
                    <span class="material-icons-round text-2xl">bolt</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Feed -->
    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <th class="px-6 py-5">Campaign Status</th>
                        <th class="px-6 py-5">Content Details</th>
                        <th class="px-6 py-5">Targeting</th>
                        <th class="px-6 py-5">Reach Stats</th>
                        <th class="px-6 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($notifications as $notification)
                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                        <td class="px-6 py-6">
                            @if($notification->status == 'sent')
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">DELIVERED</span>
                                </div>
                            @elseif($notification->status == 'failed')
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-2 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]"></div>
                                    <span class="text-[10px] font-bold text-rose-600 uppercase tracking-widest">FAILED</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <div class="h-2 w-2 rounded-full bg-slate-400"></div>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $notification->status }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-extrabold text-slate-900 dark:text-white line-clamp-1">{{ $notification->title }}</span>
                                <span class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $notification->body }}</span>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[9px] font-extrabold text-slate-400 uppercase tracking-tighter">{{ $notification->type }}</span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">• {{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex items-center gap-2">
                                <span class="material-icons-round text-sm text-slate-400">{{ $notification->target_type == 'all' ? 'public' : 'person_search' }}</span>
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">{{ $notification->target_type == 'all' ? 'Universal' : 'Segmented' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2 text-slate-900 dark:text-white font-extrabold">
                                    <span class="material-icons-round text-sm text-emerald-500">done_all</span>
                                    {{ number_format($notification->sent_count) }}
                                </div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Successful Deliveries</span>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-right">
                            <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Archive this notification record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-9 w-9 rounded-xl flex items-center justify-center text-slate-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:text-rose-500 transition-all">
                                        <span class="material-icons-round text-lg">delete_outline</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="h-20 w-20 rounded-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center mb-4">
                                    <span class="material-icons-round text-4xl text-slate-300">notifications_none</span>
                                </div>
                                <h4 class="text-xl font-bold text-slate-800 dark:text-white">Broadcast History Empty</h4>
                                <p class="text-sm text-slate-400 mt-2 max-w-sm mx-auto">You haven't sent any push notifications yet. Start by composing your first broadcast.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 bg-slate-50/30 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection
