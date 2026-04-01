@extends('admin.layouts.admin_layout')

@section('title', 'Edit Campaign - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Admin</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<a href="{{ route('admin.campaigns.index') }}" class="text-slate-500 hover:text-primary font-semibold">Campaigns</a>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Edit Campaign</span>
@endsection

@section('content')
<div class="flex items-start justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Edit Push Campaign</h2>
        <p class="text-slate-500 text-sm mt-1">Perbarui detail campaign sebelum dikirim.</p>
    </div>
    <a href="{{ route('admin.campaigns.show', $campaign) }}" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
        Lihat Detail
    </a>
</div>

<form action="{{ route('admin.campaigns.update', $campaign) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')
    @include('admin.campaigns._form', ['campaign' => $campaign])

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.campaigns.show', $campaign) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            Batal
        </a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-dark transition">
            Simpan Perubahan
        </button>
    </div>
</form>
@endsection
