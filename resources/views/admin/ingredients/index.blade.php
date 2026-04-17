@extends('admin.layouts.admin_layout')

@section('title', 'Ingredient Encyclopedia')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Ingredient Encyclopedia</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Kelola basis data bahan untuk analisis kehalalan otomatis.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ingredients.create') }}" class="flex items-center space-x-2 px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors shadow-sm shadow-primary/20 text-sm font-medium">
                <span class="material-icons-round text-sm">add</span>
                <span>Tambah Bahan</span>
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase tracking-wider">Total Bahan</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                    <span class="material-icons-round">science</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase tracking-wider">Bahan Halal</p>
                    <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['halal']) }}</h3>
                </div>
                <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center text-emerald-500">
                    <span class="material-icons-round">check_circle</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase tracking-wider">Bahan Haram</p>
                    <h3 class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-1">{{ number_format($stats['haram']) }}</h3>
                </div>
                <div class="w-10 h-10 bg-rose-50 dark:bg-rose-900/20 rounded-xl flex items-center justify-center text-rose-500">
                    <span class="material-icons-round">cancel</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase tracking-wider">Bahan Syubhat</p>
                    <h3 class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ number_format($stats['syubhat']) }}</h3>
                </div>
                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-xl flex items-center justify-center text-amber-500">
                    <span class="material-icons-round">help</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
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
                        <th class="px-6 py-4">Nama Bahan / E-Number</th>
                        <th class="px-6 py-4">Status Halal</th>
                        <th class="px-6 py-4">Risiko Kesehatan</th>
                        <th class="px-6 py-4">Sumber</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($ingredients as $ingredient)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                    <img src="{{ $ingredient->image_url }}" alt="{{ $ingredient->name }}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='{{ asset('images/placeholders/ingredient-placeholder.svg') }}'">
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $ingredient->name }}</span>
                                    <span class="text-xs text-slate-400 font-medium">{{ $ingredient->e_number ?: 'Tanpa E-Number' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = [
                                    'halal' => 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
                                    'haram' => 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
                                    'syubhat' => 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                                    'unknown' => 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-500/10 dark:text-slate-400 dark:border-slate-500/20',
                                ];
                                $class = $statusClasses[$ingredient->halal_status] ?? $statusClasses['unknown'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $class }} uppercase">
                                {{ $ingredient->halal_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $riskClasses = [
                                    'safe' => 'text-emerald-600 dark:text-emerald-400',
                                    'low_risk' => 'text-teal-600 dark:text-teal-400',
                                    'high_risk' => 'text-amber-600 dark:text-amber-400 font-bold',
                                    'dangerous' => 'text-rose-600 dark:text-rose-400 font-bold animate-pulse',
                                ];
                                $riskClass = $riskClasses[$ingredient->health_risk] ?? 'text-slate-500';
                            @endphp
                            <span class="text-xs {{ $riskClass }}">{{ str_replace('_', ' ', strtoupper($ingredient->health_risk)) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-slate-500 dark:text-slate-400 italic">{{ $ingredient->sources ?: '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full {{ $ingredient->active ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700' }}"></span>
                                <span class="text-xs {{ $ingredient->active ? 'text-slate-700 dark:text-slate-300' : 'text-slate-400' }}">
                                    {{ $ingredient->active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.ingredients.edit', $ingredient->id_ingredient) }}" class="p-1.5 text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-icons-round text-sm">edit</span>
                                </a>
                                <form action="{{ route('admin.ingredients.destroy', $ingredient->id_ingredient) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-500 transition-colors">
                                        <span class="material-icons-round text-sm">delete</span>
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
