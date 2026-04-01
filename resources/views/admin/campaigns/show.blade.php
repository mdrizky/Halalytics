@extends('admin.layouts.admin_layout')

@section('title', 'Detail Campaign - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Admin</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<a href="{{ route('admin.campaigns.index') }}" class="text-slate-500 hover:text-primary font-semibold">Campaigns</a>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">{{ $campaign->name }}</span>
@endsection

@section('content')
@php
    $segment = $campaign->target_segment ?? [];
    $userIds = $segment['user_ids'] ?? [];
@endphp

<div class="flex items-start justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">{{ $campaign->name }}</h2>
        <p class="text-slate-500 text-sm mt-1">Status saat ini: <span class="font-bold">{{ ucfirst($campaign->status) }}</span></p>
    </div>
    <div class="flex items-center gap-3">
        @if(in_array($campaign->status, ['draft', 'scheduled', 'failed']))
            <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                Edit
            </a>
            <form action="{{ route('admin.campaigns.send', $campaign) }}" method="POST" onsubmit="return confirm('Kirim campaign ini sekarang?')">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary-dark transition">
                    Kirim Sekarang
                </button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-xl text-sm font-medium">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 text-red-600 rounded-xl text-sm font-medium">{{ session('error') }}</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex items-start justify-between gap-6">
                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-400 font-bold">Judul</p>
                    <h3 class="mt-2 text-xl font-extrabold text-slate-800 dark:text-white">{{ $campaign->title }}</h3>
                    <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $campaign->body }}</p>
                </div>

                @if($campaign->image_url)
                    <img src="{{ $campaign->image_url }}" alt="Campaign image" class="w-28 h-28 rounded-2xl object-cover border border-slate-200 dark:border-slate-700">
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Payload & Target</h3>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-400 font-bold">Tipe Data</p>
                    <p class="mt-2 font-semibold text-slate-800 dark:text-white">{{ $segment['data_type'] ?? 'general' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-400 font-bold">Target Mode</p>
                    <p class="mt-2 font-semibold text-slate-800 dark:text-white">{{ ($segment['mode'] ?? 'all') === 'specific_users' ? 'User tertentu' : 'Semua user' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-400 font-bold">Active Filter</p>
                    <p class="mt-2 font-semibold text-slate-800 dark:text-white">{{ !empty($segment['active_only']) ? '7 hari terakhir' : 'Tidak dibatasi' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-400 font-bold">Perkiraan Device Target</p>
                    <p class="mt-2 font-semibold text-slate-800 dark:text-white">{{ number_format($estimatedTargetCount) }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs uppercase tracking-wider text-slate-400 font-bold">Action URL</p>
                    <p class="mt-2 font-semibold text-slate-800 dark:text-white break-all">{{ $campaign->action_url ?: '—' }}</p>
                </div>
                @if(!empty($userIds))
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-wider text-slate-400 font-bold">User ID</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($userIds as $userId)
                                <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-600 dark:text-slate-200">{{ $userId }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Ringkasan Pengiriman</h3>

            <div class="mt-5 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Target Device</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ number_format($campaign->target_count) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Berhasil Dikirim</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ number_format($campaign->sent_count) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Open Rate</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ number_format($campaign->open_rate, 1) }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Scheduled</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ optional($campaign->scheduled_at)->format('d M Y H:i') ?: '—' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Sent At</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ optional($campaign->sent_at)->format('d M Y H:i') ?: '—' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-500">Dibuat Oleh</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ optional($campaign->creator)->full_name ?: optional($campaign->creator)->username ?: 'System' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <p class="text-xs uppercase tracking-wider text-slate-400 font-bold">Catatan</p>
            <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">
                Campaign ini memakai tabel token terpisah `user_fcm_tokens`, jadi jumlah target akan mengikuti token yang aktif di database saat proses kirim dijalankan.
            </p>
        </div>
    </div>
</div>
@endsection
