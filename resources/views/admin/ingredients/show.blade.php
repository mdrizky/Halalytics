@extends('admin.layouts.admin_layout')

@section('title', 'Detail Bahan: ' . $ingredient->name)

@section('content')
<div class="space-y-6">
    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.ingredients.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 hover:text-primary transition-colors shadow-sm">
                <span class="material-icons-round">arrow_back</span>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $ingredient->name }}</h2>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-slate-500 dark:text-slate-400">E-Number:</span>
                    <span class="font-mono font-bold text-primary">{{ $ingredient->e_number ?: 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ingredients.edit', $ingredient->id_ingredient) }}" class="flex items-center space-x-2 px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm text-sm font-medium">
                <span class="material-icons-round text-sm">edit</span>
                <span>Edit Bahan</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 dark:text-white">Informasi Bahan</h3>
                    <span class="flex items-center gap-1.5 px-3 py-1 rounded-full {{ $ingredient->active ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }} text-[10px] font-bold uppercase tracking-wider border {{ $ingredient->active ? 'border-emerald-100 dark:border-emerald-500/20' : 'border-slate-200 dark:border-slate-700' }}">
                        {{ $ingredient->active ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Deskripsi & Karakteristik</h4>
                        <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 leading-relaxed">
                            {{ $ingredient->description ?: 'Tidak ada deskripsi yang tersedia untuk bahan ini.' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Asal / Origin</h4>
                            <p class="text-sm text-slate-700 dark:text-slate-200">
                                {{ $ingredient->origin ?: '-' }}
                            </p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Penggunaan Umum</h4>
                            <p class="text-sm text-slate-700 dark:text-slate-200">
                                {{ $ingredient->common_uses ?: '-' }}
                            </p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Informasi Nutrisi</h4>
                            <p class="text-sm text-slate-700 dark:text-slate-200">
                                {{ $ingredient->nutritional_info ?: '-' }}
                            </p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Sumber Data</h4>
                            <p class="text-sm text-slate-700 dark:text-slate-200 italic">
                                {{ $ingredient->sources ?: 'Sumber tidak dispesifikasikan' }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Catatan Admin</h4>
                            <p class="text-sm text-slate-700 dark:text-slate-200">
                                {{ $ingredient->notes ?: '-' }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Halal & Safety Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6">
                <h3 class="font-bold text-slate-800 dark:text-white mb-6">Status & Keamanan</h3>
                
                <div class="space-y-6">
                    <!-- Status Halal -->
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Status Halal</p>
                        @php
                            $halalColors = [
                                'halal' => 'text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10',
                                'haram' => 'text-rose-500 bg-rose-50 dark:bg-rose-500/10',
                                'syubhat' => 'text-amber-500 bg-amber-50 dark:bg-amber-500/10',
                                'unknown' => 'text-slate-400 bg-slate-50 dark:bg-slate-800',
                            ];
                            $halalIcons = [
                                'halal' => 'verified',
                                'haram' => 'dangerous',
                                'syubhat' => 'help',
                                'unknown' => 'question_mark',
                            ];
                            $color = $halalColors[$ingredient->halal_status] ?? $halalColors['unknown'];
                            $icon = $halalIcons[$ingredient->halal_status] ?? $halalIcons['unknown'];
                        @endphp
                        <div class="flex items-center gap-4 p-4 rounded-xl {{ $color }} border border-current/10">
                            <span class="material-icons-round text-3xl">{{ $icon }}</span>
                            <div>
                                <p class="text-lg font-black uppercase leading-none">{{ $ingredient->halal_status }}</p>
                                <p class="text-[10px] opacity-70 mt-1">Klasifikasi Fatwa Halalytics</p>
                            </div>
                        </div>
                    </div>

                    <!-- Health Risk -->
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Risiko Kesehatan</p>
                        @php
                            $riskColors = [
                                'safe' => 'text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10',
                                'low_risk' => 'text-teal-500 bg-teal-50 dark:bg-teal-500/10',
                                'high_risk' => 'text-amber-500 bg-amber-50 dark:bg-amber-500/10',
                                'dangerous' => 'text-rose-500 bg-rose-50 dark:bg-rose-500/10',
                            ];
                            $riskLabels = [
                                'safe' => 'Sangat Aman',
                                'low_risk' => 'Risiko Rendah',
                                'high_risk' => 'Risiko Tinggi',
                                'dangerous' => 'Berbahaya',
                            ];
                            $rColor = $riskColors[$ingredient->health_risk] ?? 'text-slate-400 bg-slate-50';
                            $rLabel = $riskLabels[$ingredient->health_risk] ?? 'Unknown';
                        @endphp
                        <div class="flex items-center justify-between p-4 rounded-xl {{ $rColor }} border border-current/10">
                            <span class="font-bold">{{ $rLabel }}</span>
                            <div class="flex gap-0.5">
                                @for($i=1; $i<=4; $i++)
                                    @php
                                        $levels = ['safe'=>1, 'low_risk'=>2, 'high_risk'=>3, 'dangerous'=>4];
                                        $currentLevel = $levels[$ingredient->health_risk] ?? 0;
                                    @endphp
                                    <div class="w-2 h-4 rounded-sm {{ $i <= $currentLevel ? 'bg-current' : 'bg-current/10' }}"></div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visual Info -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="font-bold text-xs text-slate-400 uppercase tracking-widest">Visualisasi Bahan</h3>
                </div>
                <div class="aspect-square bg-slate-100 dark:bg-slate-800 flex items-center justify-center p-8">
                    <img src="{{ $ingredient->image_url }}" alt="{{ $ingredient->name }}" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal opacity-80" onerror="this.onerror=null;this.src='/images/placeholders/ingredient-placeholder.svg'">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
