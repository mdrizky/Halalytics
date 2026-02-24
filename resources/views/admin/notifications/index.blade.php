@extends('admin.layouts.admin_layout')

@section('title', 'Push Notifications')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Push Notifications</h2>
        <p class="text-slate-500 text-sm mt-1">Riwayat notifikasi yang telah dikirim ke pengguna.</p>
    </div>
    <a href="{{ route('admin.notifications.create') }}" class="px-6 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary-dark transition-all shadow-md shadow-primary/20 flex items-center space-x-2">
        <span class="material-icons-round text-sm">add</span>
        <span>Kirim Notifikasi</span>
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/30 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Konten</th>
                <th class="px-6 py-4">Target</th>
                <th class="px-6 py-4">Stats</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($notifications as $notification)
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                <td class="px-6 py-4">
                    @if($notification->status == 'sent')
                        <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold uppercase rounded-md">Sent</span>
                    @elseif($notification->status == 'failed')
                        <span class="px-2 py-1 bg-rose-100 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[10px] font-bold uppercase rounded-md">Failed</span>
                    @else
                        <span class="px-2 py-1 bg-slate-100 dark:bg-slate-500/10 text-slate-600 dark:text-slate-400 text-[10px] font-bold uppercase rounded-md">{{ $notification->status }}</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="font-bold text-slate-800 dark:text-white text-sm">{{ $notification->title }}</div>
                    <div class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $notification->body }}</div>
                    <div class="text-[10px] text-slate-400 mt-1 uppercase">{{ $notification->type }} • {{ $notification->created_at->diffForHumans() }}</div>
                </td>
                <td class="px-6 py-4">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ ucfirst($notification->target_type) }}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-3 text-xs">
                        <span class="flex items-center text-slate-500" title="Sent"><span class="material-icons-round text-sm mr-1">send</span>{{ $notification->sent_count }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-right">
                    <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Delete this log?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 transition-colors">
                            <span class="material-icons-round text-lg">delete_outline</span>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <span class="material-icons-round text-4xl text-slate-300">notifications_off</span>
                        <p class="text-slate-400 text-sm mt-2">Belum ada notifikasi yang dikirim.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $notifications->links() }}
</div>
@endsection
