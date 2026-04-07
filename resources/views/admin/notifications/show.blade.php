@extends('admin.layouts.admin_layout')

@section('title', 'Detail Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Detail Notifikasi</h2>
            <p class="text-slate-500 text-sm mt-1">Notifikasi #{{ $notification->id }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.notifications.edit', $notification->id) }}" class="px-5 py-2 bg-amber-500 text-white rounded-lg text-sm font-bold hover:bg-amber-600 transition-all flex items-center space-x-2">
                <span class="material-icons-round text-sm">edit</span>
                <span>Edit</span>
            </a>
            <a href="{{ route('admin.notifications.index') }}" class="px-5 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-bold hover:bg-slate-300 transition-all">Kembali</a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Judul</label>
                    <p class="text-lg font-bold text-slate-800 dark:text-white mt-1">{{ $notification->title }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pesan</label>
                    <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ $notification->body }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tipe</label>
                    <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ ucfirst(str_replace('_', ' ', $notification->type)) }}</p>
                </div>
            </div>
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</label>
                    <div class="mt-2">
                        @if($notification->status == 'sent')
                            <span class="px-3 py-1.5 bg-emerald-100 text-emerald-600 text-sm font-bold rounded-lg">Sent</span>
                        @elseif($notification->status == 'failed')
                            <span class="px-3 py-1.5 bg-rose-100 text-rose-600 text-sm font-bold rounded-lg">Failed</span>
                        @else
                            <span class="px-3 py-1.5 bg-slate-100 text-slate-600 text-sm font-bold rounded-lg">{{ ucfirst($notification->status ?? 'Draft') }}</span>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Target</label>
                    <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ ucfirst($notification->target_type) }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sent Count</label>
                    <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ $notification->sent_count ?? 0 }}</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dibuat</label>
                    <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ $notification->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
