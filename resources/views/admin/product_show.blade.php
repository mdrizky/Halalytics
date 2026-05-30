@extends('admin.layouts.admin_layout')

@section('title', 'Product Detail - ' . ($product->nama_product ?? $product->name) . ' | Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Inventory</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<a href="{{ route('admin.product.index') }}" class="text-slate-400 hover:text-primary">Products</a>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Detail</span>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Header with Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="h-24 w-24 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-2 shadow-xl">
                <img src="{{ $product->image ?? $product->image_url }}" alt="{{ $product->nama_product ?? $product->name }}" class="h-full w-full object-contain rounded-2xl" onerror="this.onerror=null;this.src='https://loremflickr.com/400/400/{{ urlencode($product->nama_product ?? $product->name) }},{{ $type === 'medicine' ? 'medicine' : 'food' }}?lock={{ $product->id_product ?? $product->id_medicine }}'">
            </div>
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $product->nama_product ?? $product->name }}</h2>
                    <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $type }}</span>
                </div>
                <div class="flex items-center gap-4 text-sm font-bold">
                    <span class="text-slate-400 font-mono tracking-tighter">{{ $product->barcode ?: 'NO_BARCODE' }}</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                    <span class="text-primary">{{ $product->kategori->nama_kategori ?? ($product->source ?? 'FDA-Imported') }}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($type === 'general')
                <a href="{{ route('admin.product.edit', $product->id_product) }}" class="h-12 px-6 rounded-2xl bg-primary text-white text-sm font-bold flex items-center gap-2 shadow-lg shadow-primary/25 hover:bg-primary-dark transition-all">
                    <span class="material-icons-round text-lg">edit</span>
                    EDIT PRODUCT
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Section: Details -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Core Specs Card -->
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-3">
                        <span class="material-icons-round text-primary">analytics</span>
                        Product Specifications
                    </h3>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            @if($type === 'medicine')
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Generic Name</label>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $product->generic_name ?: '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Brand Name</label>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $product->brand_name ?: '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Manufacturer</label>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $product->manufacturer ?: '-' }}</p>
                                </div>
                            @else
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Merk / Brand</label>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $product->merk ?: '-' }}</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Halal Analysis</label>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $product->halal_analysis ?: 'No analysis data' }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Halal Status</label>
                                @php
                                    $hStatus = strtolower((string) ($product->status ?? $product->halal_status ?? 'unknown'));
                                    $hConfig = match($hStatus) {
                                        'halal' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-600', 'icon' => 'verified'],
                                        'syubhat', 'diragukan' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-600', 'icon' => 'report_problem'],
                                        default => ['bg' => 'bg-rose-50 dark:bg-rose-900/20', 'text' => 'text-rose-600', 'icon' => 'gavel'],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl {{ $hConfig['bg'] }} {{ $hConfig['text'] }} text-xs font-black uppercase tracking-widest">
                                    <span class="material-icons-round text-sm">{{ $hConfig['icon'] }}</span>
                                    {{ $hStatus }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Verification Status</label>
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-slate-50 dark:bg-slate-800 text-slate-500 text-xs font-black uppercase tracking-widest border border-slate-100 dark:border-slate-700">
                                    <span class="material-icons-round text-sm">security</span>
                                    {{ $product->is_verified_by_admin ? 'VERIFIED BY ADMIN' : 'PENDING REVIEW' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Composition / Ingredients -->
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-3">
                        <span class="material-icons-round text-primary">biotech</span>
                        Composition & Ingredients
                    </h3>
                </div>
                <div class="p-8">
                    <div class="p-6 rounded-3xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700">
                        <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400 font-medium">
                            {{ $product->komposisi ?? $product->ingredients_text ?? ($product->active_ingredient ?? 'No composition data available.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Section: Insights & Metadata -->
        <div class="space-y-8">
            <!-- Scan Engagement -->
            <div class="bg-primary rounded-[2.5rem] p-8 text-white shadow-xl shadow-primary/30 relative overflow-hidden">
                <span class="material-icons-round absolute -bottom-4 -right-4 text-9xl opacity-10">qr_code_scanner</span>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60">Engagement Metric</p>
                <h4 class="text-4xl font-black mt-2">{{ number_format($product->scans_count ?? 0) }}</h4>
                <p class="text-xs font-bold opacity-80 mt-1">Total user interactions</p>
                <div class="mt-8 pt-6 border-t border-white/10 flex items-center gap-3">
                    <div class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest">Trending in its category</span>
                </div>
            </div>

            <!-- Metadata Info -->
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm p-8">
                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6">Database Metadata</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Source Entity</span>
                        <span class="text-xs font-black text-slate-600 dark:text-slate-300 uppercase">{{ $product->source ?? 'Internal DB' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Entry ID</span>
                        <span class="text-xs font-mono font-bold text-slate-600 dark:text-slate-300">#{{ $product->id_product ?? $product->id_medicine }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Created At</span>
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $product->created_at ? $product->created_at->format('M d, Y') : '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Last Updated</span>
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $product->updated_at ? $product->updated_at->diffForHumans() : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
