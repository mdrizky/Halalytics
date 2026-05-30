@extends('admin.layouts.admin_layout')

@section('title', 'Universal Inventory | Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Logistics</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Universal Inventory Hub</span>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Sophisticated Header -->
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
        <div class="space-y-2">
            <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Product Inventory</h2>
            <p class="text-slate-500 dark:text-slate-400 font-medium max-w-2xl">
                Pusat kendali inventaris global Halalytics. Kelola produk lokal dan pantau sinkronisasi database eksternal dari Open Food Facts, Open Beauty Facts, dan OpenFDA.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button onclick="document.getElementById('add-product-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 dark:bg-white px-6 py-3 text-sm font-bold text-white dark:text-slate-900 shadow-xl hover:opacity-90 transition-all transform hover:-translate-y-1">
                <span class="material-icons-round text-lg">add_box</span>
                ADD LOCAL PRODUCT
            </button>
        </div>
    </div>

    <!-- Inventory Analytics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-7 border border-slate-200/60 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 h-32 w-32 bg-primary/5 rounded-full group-hover:scale-125 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="h-12 w-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                    <span class="material-icons-round">inventory_2</span>
                </div>
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Local Inventory</p>
                <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($localProducts->total()) }}</h3>
                <p class="text-[10px] font-bold text-emerald-500 mt-2 flex items-center gap-1">
                    <span class="material-icons-round text-xs">verified</span>
                    100% VERIFIED
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-7 border border-slate-200/60 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 h-32 w-32 bg-emerald-500/5 rounded-full group-hover:scale-125 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="h-12 w-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mb-5">
                    <span class="material-icons-round">restaurant</span>
                </div>
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Food Database (OFF)</p>
                <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($offProducts->total()) }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-7 border border-slate-200/60 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 h-32 w-32 bg-pink-500/5 rounded-full group-hover:scale-125 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="h-12 w-12 rounded-2xl bg-pink-500/10 text-pink-600 flex items-center justify-center mb-5">
                    <span class="material-icons-round">face</span>
                </div>
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Cosmetics (OBF)</p>
                <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($obfProducts->total()) }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-7 border border-slate-200/60 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 h-32 w-32 bg-blue-500/5 rounded-full group-hover:scale-125 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="h-12 w-12 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center mb-5">
                    <span class="material-icons-round">medication</span>
                </div>
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Medicine (FDA)</p>
                <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($fdaProducts->total()) }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form action="{{ route('admin.product.index') }}" method="GET" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-6 relative">
                <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 pl-12 pr-4 py-3.5 text-sm focus:ring-2 focus:ring-primary transition-all"
                    placeholder="Search food name or barcode..."
                >
            </div>
            <div class="lg:col-span-3">
                <select name="category" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3.5 text-sm focus:ring-2 focus:ring-primary transition-all">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id_kategori }}" {{ (string) request('category') === (string) $category->id_kategori ? 'selected' : '' }}>
                            {{ $category->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-3 flex items-center gap-2">
                <button type="submit" class="w-full rounded-2xl bg-slate-900 dark:bg-white dark:text-slate-900 px-4 py-3.5 text-sm font-bold text-white hover:opacity-90 transition-all">
                    FILTER
                </button>
            </div>
        </form>
    </div>

    <!-- Tab Navigation -->
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-px">
        <button onclick="switchTab('local')" id="tab-local" class="tab-btn px-6 py-3 text-sm font-black border-b-2 transition-all border-primary text-primary flex items-center gap-2">
            <span class="material-icons-round text-sm">inventory</span>
            LOCAL ASSETS
        </button>
        <button onclick="switchTab('off')" id="tab-off" class="tab-btn px-6 py-3 text-sm font-black border-b-2 transition-all border-transparent text-slate-400 hover:text-slate-600 flex items-center gap-2">
            <span class="material-icons-round text-sm">restaurant</span>
            FOODS (OFF)
        </button>
        <button onclick="switchTab('obf')" id="tab-obf" class="tab-btn px-6 py-3 text-sm font-black border-b-2 transition-all border-transparent text-slate-400 hover:text-slate-600 flex items-center gap-2">
            <span class="material-icons-round text-sm">face</span>
            COSMETICS (OBF)
        </button>
        <button onclick="switchTab('fda')" id="tab-fda" class="tab-btn px-6 py-3 text-sm font-black border-b-2 transition-all border-transparent text-slate-400 hover:text-slate-600 flex items-center gap-2">
            <span class="material-icons-round text-sm">medication</span>
            MEDICINES (FDA)
        </button>
    </div>

    <!-- Inventory Sections -->
    <div class="mt-8">
        <!-- Local Section -->
        <div id="section-local" class="tab-section space-y-6">
            <section class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 px-8 py-6">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                            <span class="h-8 w-1 bg-primary rounded-full"></span>
                            Local Product Registry
                        </h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center rounded-full bg-primary/5 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-primary border border-primary/10">
                            {{ number_format($localProducts->total()) }} ASSETS
                        </span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                <th class="px-8 py-5">Product Entity</th>
                                <th class="px-8 py-5">Classification</th>
                                <th class="px-8 py-5">Halal Status</th>
                                <th class="px-8 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($localProducts as $product)
                                <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 overflow-hidden rounded-xl border-2 border-white dark:border-slate-800 bg-slate-100 dark:bg-slate-800 shadow-sm group-hover:scale-105 transition-transform duration-500">
                                                <img src="{{ $product->image }}" alt="{{ $product->nama_product }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='/images/placeholders/food-placeholder.svg'">
                                            </div>
                                            <div>
                                                <p class="text-sm font-extrabold text-slate-900 dark:text-white line-clamp-1">{{ $product->nama_product }}</p>
                                                <p class="mt-1 text-[10px] font-bold text-slate-400 tracking-tighter">{{ $product->barcode ?: 'No barcode' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-xs font-semibold text-slate-600 dark:text-slate-400">
                                        <span class="inline-flex px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800">{{ $product->kategori->nama_kategori ?? 'Uncategorized' }}</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        @php
                                            $status = strtolower((string) $product->status);
                                            $statusConfig = match ($status) {
                                                'halal' => ['color' => 'bg-emerald-500', 'text' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'label' => 'HALAL'],
                                                'syubhat', 'diragukan' => ['color' => 'bg-amber-500', 'text' => 'text-amber-600', 'bg' => 'bg-amber-50', 'label' => 'SYUBHAT'],
                                                default => ['color' => 'bg-rose-500', 'text' => 'text-rose-600', 'bg' => 'bg-rose-50', 'label' => 'HARAM'],
                                            };
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <div class="h-1.5 w-1.5 rounded-full {{ $statusConfig['color'] }}"></div>
                                            <span class="text-[10px] font-extrabold {{ $statusConfig['text'] }} tracking-widest uppercase">{{ $statusConfig['label'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-1">
                                            <a href="{{ route('admin.product.show', $product->id_product) }}" class="h-9 w-9 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all" title="View Detail">
                                                <span class="material-icons-round text-lg">visibility</span>
                                            </a>
                                            <a href="{{ route('admin.product.edit', $product->id_product) }}" class="h-9 w-9 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all">
                                                <span class="material-icons-round text-lg">edit</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-8 py-10 text-center text-slate-400">Empty.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-8 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30">
                    {{ $localProducts->appends(['off_page' => request('off_page')])->links() }}
                </div>
            </section>
        </div>

        <!-- External Section (OFF) -->
        <div id="section-off" class="tab-section hidden space-y-6">
            <section class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 px-8 py-6">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                            <span class="h-8 w-1 bg-emerald-500 rounded-full"></span>
                            Open Food Facts (Cached Locally)
                        </h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center rounded-full bg-emerald-500/5 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-emerald-600 border border-emerald-500/10">
                            {{ number_format($offProducts->total()) }} FOOD
                        </span>
                        <form action="{{ route('admin.product.sync', 'off') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-500 px-4 py-2 text-xs font-bold text-white shadow-lg shadow-emerald-500/20 hover:bg-emerald-600 transition-all">
                                <span class="material-icons-round text-sm">sync</span>
                                SYNC LIVE OFF
                            </button>
                        </form>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                <th class="px-8 py-5">Product Intelligence</th>
                                <th class="px-8 py-5">Category & Brands</th>
                                <th class="px-8 py-5">Status & Source</th>
                                <th class="px-8 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($offProducts as $product)
                                <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 overflow-hidden rounded-xl border-2 border-white dark:border-slate-800 bg-slate-100 dark:bg-slate-800 shadow-sm group-hover:scale-105 transition-transform duration-500">
                                                <img src="{{ $product->image }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='/images/placeholders/food-placeholder.svg'">
                                            </div>
                                            <div>
                                                <p class="text-sm font-extrabold text-slate-900 dark:text-white line-clamp-1">{{ $product->nama_product }}</p>
                                                <p class="mt-1 text-[10px] font-bold text-slate-400 tracking-tighter">{{ $product->barcode }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="space-y-1">
                                            <p class="text-xs font-black text-slate-700 dark:text-slate-200">{{ $product->category_name ?: 'Food & Beverage' }}</p>
                                            <p class="text-[10px] font-bold text-slate-400">{{ $product->brand ?: 'Global Brand' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2">
                                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span class="inline-flex px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 text-[10px] font-black tracking-widest uppercase">OFF GLOBAL DB</span>
                                            </div>
                                            <span class="inline-flex px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-900/20 text-amber-600 text-[10px] font-black tracking-widest uppercase border border-amber-100 dark:border-amber-800/50">SYUBHAT (PENDING)</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.product.show', $product->id_product) }}" class="h-9 w-9 rounded-xl flex items-center justify-center text-slate-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all" title="Full Analytics">
                                                <span class="material-icons-round text-lg">analytics</span>
                                            </a>
                                            <a href="{{ route('admin.product.edit', $product->id_product) }}" class="h-9 w-9 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                                                <span class="material-icons-round text-lg">edit</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="h-16 w-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                                <span class="material-icons-round text-3xl">restaurant</span>
                                            </div>
                                            <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">No Food data found.</p>
                                            <p class="text-slate-300 text-xs mt-1">Try syncing with Open Food Facts.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-8 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30">
                    {{ $offProducts->appends(['local_page' => request('local_page')])->links() }}
                </div>
            </section>
        </div>

        <!-- External Section (OBF) -->
        <div id="section-obf" class="tab-section hidden space-y-6">
            <section class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 px-8 py-6">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                            <span class="h-8 w-1 bg-pink-500 rounded-full"></span>
                            Open Beauty Facts (Cached Locally)
                        </h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center rounded-full bg-pink-500/5 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-pink-600 border border-pink-500/10">
                            {{ number_format($obfProducts->total()) }} COSMETICS
                        </span>
                        <form action="{{ route('admin.product.sync', 'obf') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-pink-500 px-4 py-2 text-xs font-bold text-white shadow-lg shadow-pink-500/20 hover:bg-pink-600 transition-all">
                                <span class="material-icons-round text-sm">sync</span>
                                SYNC LIVE OBF
                            </button>
                        </form>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                <th class="px-8 py-5">Beauty Intelligence</th>
                                <th class="px-8 py-5">Brand & Manufacturer</th>
                                <th class="px-8 py-5">Status & Source</th>
                                <th class="px-8 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($obfProducts as $product)
                                <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 overflow-hidden rounded-xl border-2 border-white dark:border-slate-800 bg-slate-100 dark:bg-slate-800 shadow-sm group-hover:scale-105 transition-transform duration-500">
                                                <img src="{{ $product->image }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='/images/placeholders/cosmetic-placeholder.svg'">
                                            </div>
                                            <div>
                                                <p class="text-sm font-extrabold text-slate-900 dark:text-white line-clamp-1">{{ $product->nama_product }}</p>
                                                <p class="mt-1 text-[10px] font-bold text-slate-400 tracking-tighter">{{ $product->barcode }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="space-y-1">
                                            <p class="text-xs font-black text-slate-700 dark:text-slate-200">{{ $product->brand ?: 'Global Beauty' }}</p>
                                            <p class="text-[10px] font-bold text-slate-400">Manufactured Internationally</p>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2">
                                                <span class="h-2 w-2 rounded-full bg-pink-500 animate-pulse"></span>
                                                <span class="inline-flex px-2 py-0.5 rounded-md bg-pink-50 dark:bg-pink-900/20 text-pink-600 text-[10px] font-black tracking-widest uppercase">OBF GLOBAL DB</span>
                                            </div>
                                            <span class="inline-flex px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-900/20 text-amber-600 text-[10px] font-black tracking-widest uppercase border border-amber-100 dark:border-amber-800/50">SYUBHAT (PENDING)</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.product.show', $product->id_product) }}" class="h-9 w-9 rounded-xl flex items-center justify-center text-slate-400 hover:bg-pink-50 hover:text-pink-600 transition-all" title="Full Analytics">
                                                <span class="material-icons-round text-lg">analytics</span>
                                            </a>
                                            <a href="{{ route('admin.product.edit', $product->id_product) }}" class="h-9 w-9 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                                                <span class="material-icons-round text-lg">edit</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="h-16 w-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                                <span class="material-icons-round text-3xl">face</span>
                                            </div>
                                            <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">No Beauty data found.</p>
                                            <p class="text-slate-300 text-xs mt-1">Try syncing with Open Beauty Facts.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-8 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30">
                    {{ $obfProducts->appends(['local_page' => request('local_page')])->links() }}
                </div>
            </section>
        </div>

        <!-- External Section (FDA) -->
        <div id="section-fda" class="tab-section hidden space-y-6">
            <section class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 px-8 py-6">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                            <span class="h-8 w-1 bg-blue-500 rounded-full"></span>
                            OpenFDA (Cached Locally)
                        </h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center rounded-full bg-blue-500/5 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-blue-600 border border-blue-500/10">
                            {{ number_format($fdaProducts->total()) }} MEDICINES
                        </span>
                        <form action="{{ route('admin.product.sync', 'fda') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-blue-500 px-4 py-2 text-xs font-bold text-white shadow-lg shadow-blue-500/20 hover:bg-blue-600 transition-all">
                                <span class="material-icons-round text-sm">sync</span>
                                SYNC LIVE FDA
                            </button>
                        </form>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                <th class="px-8 py-5">Medicine Intelligence</th>
                                <th class="px-8 py-5">Manufacturer & Brand</th>
                                <th class="px-8 py-5">Global Source</th>
                                <th class="px-8 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($fdaProducts as $product)
                                <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 overflow-hidden rounded-xl border-2 border-white dark:border-slate-800 bg-slate-100 dark:bg-slate-800 shadow-sm group-hover:scale-105 transition-transform duration-500">
                                                <img src="{{ $product->image_url }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='/images/placeholders/medicine-placeholder.svg'">
                                            </div>
                                            <div>
                                                <p class="text-sm font-extrabold text-slate-900 dark:text-white line-clamp-1">{{ $product->name }}</p>
                                                <p class="mt-1 text-[10px] font-bold text-slate-400 tracking-tighter">{{ $product->barcode ?: 'FDA-REG-NONE' }}</p>
                                                @if($product->generic_name)
                                                    <p class="text-[9px] font-black text-primary/60 uppercase mt-0.5">{{ $product->generic_name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="space-y-1">
                                            <p class="text-xs font-black text-slate-700 dark:text-slate-200">{{ $product->manufacturer ?: 'Unknown Mfr' }}</p>
                                            <p class="text-[10px] font-bold text-slate-400">{{ $product->brand_name ?: 'Generic Brand' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <span class="h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>
                                            <span class="inline-flex px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-900/20 text-blue-600 text-[10px] font-black tracking-widest uppercase">FDA GLOBAL DB</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.product.show', $product->id_medicine) }}" class="h-9 w-9 rounded-xl flex items-center justify-center text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-all" title="Full Analytics">
                                                <span class="material-icons-round text-lg">analytics</span>
                                            </a>
                                            <a href="{{ route('admin.product.edit', $product->id_medicine) }}" class="h-9 w-9 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                                                <span class="material-icons-round text-lg">edit</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="h-16 w-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                                <span class="material-icons-round text-3xl">medical_services</span>
                                            </div>
                                            <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">No Medicine data found.</p>
                                            <p class="text-slate-300 text-xs mt-1">Try syncing with FDA Live Database.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-8 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30">
                    {{ $fdaProducts->appends(['local_page' => request('local_page')])->links() }}
                </div>
            </section>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tab) {
        // Hide all sections
        document.querySelectorAll('.tab-section').forEach(el => el.classList.add('hidden'));
        // Show selected section
        document.getElementById('section-' + tab).classList.remove('hidden');

        // Reset all buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-primary', 'text-primary');
            btn.classList.add('border-transparent', 'text-slate-400');
        });

        // Set active button
        const activeBtn = document.getElementById('tab-' + tab);
        activeBtn.classList.remove('border-transparent', 'text-slate-400');
        activeBtn.classList.add('border-primary', 'text-primary');

        // Save tab to local storage if needed
        localStorage.setItem('admin_product_tab', tab);
    }

    // Auto switch to last tab on reload
    document.addEventListener('DOMContentLoaded', () => {
        const lastTab = localStorage.getItem('admin_product_tab');
        if (lastTab && document.getElementById('section-' + lastTab)) {
            switchTab(lastTab);
        }
    });
</script>
@endpush
@endsection
