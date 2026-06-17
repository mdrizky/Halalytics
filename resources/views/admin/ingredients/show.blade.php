@extends('admin.layouts.admin_layout')

@section('title', 'Detail Bahan - Halalytics Admin')
@section('breadcrumb-parent', 'Master Data')
@section('breadcrumb-current', 'Detail Bahan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.ingredients.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl surface-card text-slate-500 hover:text-primary transition-colors">
            <span class="material-icons-round">arrow_back</span>
        </a>
        <div class="flex-1">
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $ingredient->name }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                @if($ingredient->e_number)
                    <span class="font-mono font-bold text-primary">{{ $ingredient->e_number }}</span> &middot;
                @endif
                Detail lengkap informasi bahan
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.ingredients.edit', $ingredient->id_ingredient) }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 hover:border-primary hover:text-primary transition">
                <span class="material-icons-round text-lg">edit</span>
                Edit
            </a>
            <form action="{{ route('admin.ingredients.destroy', $ingredient->id_ingredient) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-rose-200 px-4 py-2 text-sm font-bold text-rose-600 hover:bg-rose-50 transition">
                    <span class="material-icons-round text-lg">delete</span>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="surface-card rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 dark:text-white">Informasi Bahan</h3>
                    <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-bold {{ $ingredient->active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $ingredient->active ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Deskripsi</h4>
                        <p class="text-sm text-slate-700 dark:text-slate-200 leading-relaxed">
                            {{ $ingredient->description ?: 'Tidak ada deskripsi yang tersedia untuk bahan ini.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Sumber</h4>
                            <p class="text-sm text-slate-700 dark:text-slate-200">{{ $ingredient->sources ?: '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Kategori</h4>
                            <p class="text-sm text-slate-700 dark:text-slate-200">{{ $ingredient->category ?: '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Asal / Origin</h4>
                            <p class="text-sm text-slate-700 dark:text-slate-200">{{ $ingredient->origin ?: '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Penggunaan Umum</h4>
                            <p class="text-sm text-slate-700 dark:text-slate-200">{{ $ingredient->common_uses ?: '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Informasi Nutrisi</h4>
                            <p class="text-sm text-slate-700 dark:text-slate-200">{{ $ingredient->nutritional_info ?: '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Catatan Admin</h4>
                            <p class="text-sm text-slate-700 dark:text-slate-200">{{ $ingredient->notes ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="surface-card rounded-2xl overflow-hidden p-6">
                <h3 class="font-bold text-slate-800 dark:text-white mb-6">Status & Keamanan</h3>

                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400 mb-2">Status Halal</p>
                        @php
                            $halalClasses = [
                                'halal' => 'inline-flex rounded-full px-3 py-1 text-[11px] font-bold bg-emerald-50 text-emerald-700',
                                'haram' => 'inline-flex rounded-full px-3 py-1 text-[11px] font-bold bg-red-50 text-red-700',
                                'syubhat' => 'inline-flex rounded-full px-3 py-1 text-[11px] font-bold bg-amber-50 text-amber-700',
                                'unknown' => 'inline-flex rounded-full px-3 py-1 text-[11px] font-bold bg-slate-100 text-slate-600',
                            ];
                            $halalIcons = [
                                'halal' => 'check_circle',
                                'haram' => 'cancel',
                                'syubhat' => 'help',
                                'unknown' => 'question_mark',
                            ];
                        @endphp
                        <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-800">
                            <span class="material-icons-round text-2xl text-slate-500">{{ $halalIcons[$ingredient->halal_status] ?? 'question_mark' }}</span>
                            <span class="{{ $halalClasses[$ingredient->halal_status] ?? $halalClasses['unknown'] }} uppercase">
                                {{ $ingredient->halal_status }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400 mb-2">Risiko Kesehatan</p>
                        @php
                            $riskClasses = [
                                'safe' => 'inline-flex rounded-full px-3 py-1 text-[11px] font-bold bg-emerald-50 text-emerald-700',
                                'low_risk' => 'inline-flex rounded-full px-3 py-1 text-[11px] font-bold bg-blue-50 text-blue-700',
                                'high_risk' => 'inline-flex rounded-full px-3 py-1 text-[11px] font-bold bg-orange-50 text-orange-700',
                                'dangerous' => 'inline-flex rounded-full px-3 py-1 text-[11px] font-bold bg-red-50 text-red-700',
                            ];
                        @endphp
                        <span class="{{ $riskClasses[$ingredient->health_risk] ?? 'inline-flex rounded-full px-3 py-1 text-[11px] font-bold bg-slate-100 text-slate-600' }}">
                            {{ str_replace('_', ' ', $ingredient->health_risk) }}
                        </span>
                    </div>
                </div>
            </div>

            @if($ingredient->image_url)
            <div class="surface-card rounded-2xl overflow-hidden">
                <div class="p-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="font-bold text-xs text-slate-400 uppercase tracking-widest">Gambar</h3>
                </div>
                <div class="p-4">
                    <img src="{{ $ingredient->image_url }}" alt="{{ $ingredient->name }}" class="w-full rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
