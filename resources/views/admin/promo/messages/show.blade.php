@extends('admin.layouts.admin_layout')

@section('title', 'Message Detail - Halalytics Admin')
@section('breadcrumb-parent', 'Content')
@section('breadcrumb-current', 'Message Detail')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Detail Pesan</h2>
        <p class="text-slate-500 text-sm mt-1">Baca dan respon pesan masuk.</p>
    </div>
    <a href="{{ route('admin.promo.messages.index') }}" class="px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold">Kembali</a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
    <h3 class="text-xl font-bold text-slate-800 dark:text-white">{{ $message->subject ?? '(Tanpa Subjek)' }}</h3>
    <div class="mt-2 text-sm text-slate-500">
        Dari: <strong>{{ $message->name }}</strong> ({{ $message->email }}) • {{ $message->created_at->format('d M Y, H:i') }}
    </div>

    <div class="mt-6 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/40 text-slate-700 dark:text-slate-200 whitespace-pre-wrap leading-7">
        {{ $message->message }}
    </div>

    <div class="mt-6 flex items-center justify-end gap-3">
        <a href="mailto:{{ $message->email }}?subject=RE: {{ $message->subject ?? 'Balasan dari Halalytics' }}" class="px-4 py-2 rounded-lg bg-emerald-500 text-white font-semibold hover:bg-emerald-600">Balas Email</a>
        <form action="{{ route('admin.promo.messages.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesan ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 rounded-lg bg-red-500 text-white font-semibold hover:bg-red-600">Hapus</button>
        </form>
    </div>
</div>
@endsection
