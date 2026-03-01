@extends('admin.layouts.admin_layout')

@section('title', 'Promo Messages - Halalytics Admin')
@section('breadcrumb-parent', 'Content')
@section('breadcrumb-current', 'Messages')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Pesan Masuk</h2>
    <p class="text-slate-500 text-sm mt-1">Pesan dari user/partner pada halaman promo.</p>
</div>

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">{{ session('success') }}</div>
@endif

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="px-5 py-4">Tanggal</th>
                    <th class="px-5 py-4">Pengirim</th>
                    <th class="px-5 py-4">Subjek</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($messages as $msg)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 {{ !$msg->is_read ? 'bg-slate-50/70 dark:bg-slate-800/20' : '' }}">
                    <td class="px-5 py-4 text-xs text-slate-500">{{ $msg->created_at->format('d M Y, H:i') }}</td>
                    <td class="px-5 py-4">
                        <div class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $msg->name }}</div>
                        <div class="text-xs text-slate-500">{{ $msg->email }}</div>
                    </td>
                    <td class="px-5 py-4 text-sm text-slate-700 dark:text-slate-300">{{ Str::limit($msg->subject, 60, '...') }}</td>
                    <td class="px-5 py-4">
                        @if($msg->is_read)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Sudah Dibaca</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-bold bg-rose-100 dark:bg-rose-900/30 text-rose-600">Baru</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.promo.messages.show', $msg->id) }}" class="p-2 rounded-lg text-slate-500 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-800">
                                <span class="material-icons-round text-lg">mark_email_read</span>
                            </a>
                            <form action="{{ route('admin.promo.messages.destroy', $msg->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <span class="material-icons-round text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-16 text-center">
                        <div class="text-slate-400 text-sm">Belum ada pesan masuk.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">{{ $messages->links() }}</div>
</div>
@endsection
