@extends('admin.layouts.admin_layout')

@section('title', 'User Control - Halalytics Admin')
@section('breadcrumb-parent', 'Administration')
@section('breadcrumb-current', 'User Control')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">User Control Dashboard</h2>
        <p class="text-slate-500 text-sm mt-1">Ringkasan aktivitas user dan perilaku scan.</p>
    </div>
    <a href="{{ route('admin.user.index') }}" class="px-4 py-2 text-sm font-bold bg-primary text-white rounded-lg hover:bg-primary-dark transition-all">
        Kelola Data User
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5">
        <p class="text-xs font-bold text-slate-400 uppercase">Total Users</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-800 dark:text-white">{{ number_format($stats['total_users'] ?? 0) }}</p>
    </div>
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5">
        <p class="text-xs font-bold text-slate-400 uppercase">Active Users</p>
        <p class="mt-2 text-3xl font-extrabold text-emerald-600">{{ number_format($stats['active_users'] ?? 0) }}</p>
    </div>
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5">
        <p class="text-xs font-bold text-slate-400 uppercase">Total Scans</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-800 dark:text-white">{{ number_format($stats['total_scans'] ?? 0) }}</p>
    </div>
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5">
        <p class="text-xs font-bold text-slate-400 uppercase">Scans Today</p>
        <p class="mt-2 text-3xl font-extrabold text-primary">{{ number_format($stats['scans_today'] ?? 0) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="font-bold text-slate-800 dark:text-white">Top Users by Scans</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-xs font-bold text-slate-500 uppercase">
                        <th class="px-5 py-3 text-left">User</th>
                        <th class="px-5 py-3 text-left">Email</th>
                        <th class="px-5 py-3 text-right">Scans</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse(($stats['top_users_by_scans'] ?? []) as $user)
                    <tr>
                        <td class="px-5 py-3 text-sm font-semibold text-slate-800 dark:text-white">{{ $user->full_name ?? $user->username }}</td>
                        <td class="px-5 py-3 text-sm text-slate-500">{{ $user->email }}</td>
                        <td class="px-5 py-3 text-right text-sm font-bold text-primary">
                            {{ number_format($user->scan_histories_count ?? $user->scans_count ?? 0) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada data scan user.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5">
        <h3 class="font-bold text-slate-800 dark:text-white mb-4">Scan Distribution</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-slate-500">Halal</span><span class="font-bold text-emerald-600">{{ number_format($stats['scan_status_distribution']['halal'] ?? 0) }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Haram</span><span class="font-bold text-red-600">{{ number_format($stats['scan_status_distribution']['haram'] ?? 0) }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Syubhat</span><span class="font-bold text-amber-600">{{ number_format($stats['scan_status_distribution']['syubhat'] ?? 0) }}</span></div>
            <div class="flex justify-between"><span class="text-slate-500">Unknown</span><span class="font-bold text-slate-600">{{ number_format($stats['scan_status_distribution']['unknown'] ?? 0) }}</span></div>
        </div>
        <div class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-800">
            <p class="text-xs text-slate-400">
                Source mode:
                <span class="font-semibold {{ !empty($stats['using_realtime_table']) ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ !empty($stats['using_realtime_table']) ? 'scan_histories (realtime)' : 'scans (legacy fallback)' }}
                </span>
            </p>
        </div>
    </div>
</div>
@endsection
