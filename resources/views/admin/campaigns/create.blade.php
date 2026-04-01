@extends('admin.layouts.admin_layout')

@section('title', 'Buat Campaign - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Admin</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<a href="{{ route('admin.campaigns.index') }}" class="text-slate-500 hover:text-primary font-semibold">Campaigns</a>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Buat Campaign</span>
@endsection

@section('content')
<div class="flex items-start justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Buat Push Campaign</h2>
        <p class="text-slate-500 text-sm mt-1">Siapkan broadcast Firebase FCM dari panel admin.</p>
    </div>
    <a href="{{ route('admin.campaigns.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
        Kembali
    </a>
</div>

@if(!empty($templates))
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 mb-6">
    <h3 class="text-sm font-extrabold text-slate-800 dark:text-white">Template Cepat</h3>
    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach($templates as $template)
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                <p class="font-bold text-slate-800 dark:text-white">{{ $template['name'] }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $template['title'] }}</p>
                <p class="text-xs text-slate-400 mt-2">{{ $template['body'] }}</p>
            </div>
        @endforeach
    </div>
</div>
@endif

<form action="{{ route('admin.campaigns.store') }}" method="POST" class="space-y-6">
    @csrf
    @include('admin.campaigns._form', ['campaign' => new \App\Models\NotificationCampaign()])

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.campaigns.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            Batal
        </a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-dark transition">
            Simpan Campaign
        </button>
    </div>
</form>
@endsection
