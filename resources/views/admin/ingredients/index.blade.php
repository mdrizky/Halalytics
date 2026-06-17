@extends('admin.layouts.admin_layout')

@section('title', 'Database Bahan - Halalytics Admin')
@section('breadcrumb-parent', 'Master Data')
@section('breadcrumb-current', 'Database Bahan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Database Bahan</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-2xl">
                Kelola basis data bahan untuk analisis kehalalan otomatis.
            </p>
        </div>
        <a href="{{ route('admin.ingredients.create') }}" class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark transition">
            <span class="material-icons-round text-lg">add</span>
            Tambah Bahan
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="surface-card rounded-2xl p-5">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Total Bahan</p>
            <div class="mt-3 flex items-end justify-between">
                <div>
                    <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                    <p class="text-sm text-slate-500">Seluruh entri</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-icons-round">science</span>
                </div>
            </div>
        </div>
        <div class="surface-card rounded-2xl p-5">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Bahan Halal</p>
            <div class="mt-3 flex items-end justify-between">
                <div>
                    <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['halal']) }}</p>
                    <p class="text-sm text-slate-500">Terverifikasi</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-icons-round">check_circle</span>
                </div>
            </div>
        </div>
        <div class="surface-card rounded-2xl p-5">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Bahan Haram</p>
            <div class="mt-3 flex items-end justify-between">
                <div>
                    <p class="text-3xl font-extrabold text-red-600 dark:text-red-400">{{ number_format($stats['haram']) }}</p>
                    <p class="text-sm text-slate-500">Dihindari</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center">
                    <span class="material-icons-round">cancel</span>
                </div>
            </div>
        </div>
        <div class="surface-card rounded-2xl p-5">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Bahan Syubhat</p>
            <div class="mt-3 flex items-end justify-between">
                <div>
                    <p class="text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ number_format($stats['syubhat']) }}</p>
                    <p class="text-sm text-slate-500">Perlu verifikasi</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-icons-round">help</span>
                </div>
            </div>
        </div>
    </div>

    <div class="surface-card rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <form action="{{ route('admin.ingredients.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama bahan atau E-number..." class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary/50 dark:text-white">
                </div>
                <div class="w-full md:w-48">
                    <select name="status" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary/50 dark:text-white">
                        <option value="all">Semua Status</option>
                        <option value="halal" {{ request('status') == 'halal' ? 'selected' : '' }}>Halal</option>
                        <option value="haram" {{ request('status') == 'haram' ? 'selected' : '' }}>Haram</option>
                        <option value="syubhat" {{ request('status') == 'syubhat' ? 'selected' : '' }}>Syubhat</option>
                        <option value="unknown" {{ request('status') == 'unknown' ? 'selected' : '' }}>Unknown</option>
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-xl text-sm font-medium hover:bg-primary-dark transition-colors shadow-sm shadow-primary/20">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                        <th class="px-6 py-4 w-12">#</th>
                        <th class="px-6 py-4">Nama Bahan</th>
                        <th class="px-6 py-4">E-Number</th>
                        <th class="px-6 py-4">Status Halal</th>
                        <th class="px-6 py-4">Health Risk</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($ingredients as $index => $ingredient)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4 text-sm text-slate-400">{{ $ingredients->firstItem() + $index }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.ingredients.show', $ingredient->id_ingredient) }}" class="text-sm font-bold text-slate-800 dark:text-white hover:text-primary transition-colors">{{ $ingredient->name }}</a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-600 dark:text-slate-400 font-mono">{{ $ingredient->e_number ?: '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $halalBadge = [
                                    'halal' => 'inline-flex rounded-full px-3 py-1 text-[11px] font-bold bg-emerald-50 text-emerald-700',
                                    'haram' => 'inline-flex rounded-full px-3 py-1 text-[11px] font-bold bg-red-50 text-red-700',
                                    'syubhat' => 'inline-flex rounded-full px-3 py-1 text-[11px] font-bold bg-amber-50 text-amber-700',
                                    'unknown' => 'inline-flex rounded-full px-3 py-1 text-[11px] font-bold bg-slate-100 text-slate-600',
                                ];
                            @endphp
                            <span class="{{ $halalBadge[$ingredient->halal_status] ?? $halalBadge['unknown'] }} uppercase">
                                {{ $ingredient->halal_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $riskBadge = [
                                    'safe' => 'text-emerald-600 bg-emerald-50',
                                    'low_risk' => 'text-blue-600 bg-blue-50',
                                    'high_risk' => 'text-orange-600 bg-orange-50',
                                    'dangerous' => 'text-red-600 bg-red-50',
                                ];
                                $rClass = $riskBadge[$ingredient->health_risk] ?? 'text-slate-600 bg-slate-50';
                            @endphp
                            <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-bold {{ $rClass }}">
                                {{ str_replace('_', ' ', $ingredient->health_risk) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.ingredients.edit', $ingredient->id_ingredient) }}" class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:border-primary hover:text-primary transition">
                                    <span class="material-icons-round text-sm mr-1">edit</span>
                                    Edit
                                </a>
                                <form action="{{ route('admin.ingredients.destroy', $ingredient->id_ingredient) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center rounded-full border border-rose-200 px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
                                        <span class="material-icons-round text-sm mr-1">delete</span>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <span class="material-icons-round text-slate-200 dark:text-slate-800 text-5xl mb-3">science</span>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">Data bahan tidak ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ingredients->hasPages())
        <div class="p-6 border-t border-slate-100 dark:border-slate-800">
            {{ $ingredients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
