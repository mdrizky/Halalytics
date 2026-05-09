@extends('admin.layouts.admin_layout')

@section('title', 'Foods & Beverages Hub - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Catalog</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Foods & Beverages</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Foods & Beverages Inventory</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-2xl">
                Management of internal F&B products and external Open Food Facts catalog.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.product.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-primary px-6 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all transform hover:-translate-y-1">
                <span class="material-icons-round text-lg">add</span>
                ADD LOCAL PRODUCT
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-24 w-24 bg-primary/5 rounded-full group-hover:scale-110 transition-transform"></div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Products</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format(($productStats['local_total'] ?? 0) + ($productStats['off_total'] ?? 0) + ($productStats['obf_total'] ?? 0) + ($productStats['fda_total'] ?? 0)) }}</h3>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-icons-round">inventory_2</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-24 w-24 bg-emerald-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Verified Local</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($productStats['local_verified'] ?? 0) }}</h3>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 flex items-center justify-center">
                    <span class="material-icons-round">verified</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-24 w-24 bg-amber-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Wait for Sync</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format(($productStats['local_total'] ?? 0) - ($productStats['local_verified'] ?? 0)) }}</h3>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 flex items-center justify-center">
                    <span class="material-icons-round">sync</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-24 w-24 bg-blue-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">External Items</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format(($productStats['off_total'] ?? 0) + ($productStats['obf_total'] ?? 0) + ($productStats['fda_total'] ?? 0)) }}</h3>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center">
                    <span class="material-icons-round">cloud_done</span>
                </div>
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

    <!-- Inventory Sections -->
    <div class="space-y-8">
        <!-- Local Section -->
        <section class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 px-8 py-6">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                        <span class="h-8 w-1 bg-primary rounded-full"></span>
                        Local Food Registry
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
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-1">
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

        <!-- External Section (OFF) -->
        <section class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 px-8 py-6">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                        <span class="h-8 w-1 bg-emerald-500 rounded-full"></span>
                        Open Food Facts Catalog
                    </h3>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full bg-emerald-500/5 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-emerald-600 border border-emerald-500/10">
                        {{ number_format($offProducts->total()) }} FOOD
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            <th class="px-8 py-5">Product Entity</th>
                            <th class="px-8 py-5">Source</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($offProducts as $product)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 overflow-hidden rounded-lg bg-slate-100 dark:bg-slate-800 shadow-sm">
                                            <img src="{{ $product->image }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='/images/placeholders/food-placeholder.svg'">
                                        </div>
                                        <div>
                                            <p class="text-sm font-extrabold text-slate-900 dark:text-white line-clamp-1">{{ $product->nama_product }}</p>
                                            <p class="mt-1 text-[10px] font-bold text-slate-400 tracking-tighter">{{ $product->barcode }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 text-[10px] font-bold uppercase">OFF Global</span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('admin.product.edit', $product->id_product) }}" class="text-primary hover:underline text-xs font-bold">VERIFY</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-8 py-10 text-center text-slate-400">No Food data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30">
                {{ $offProducts->appends(['local_page' => request('local_page'), 'obf_page' => request('obf_page'), 'fda_page' => request('fda_page')])->links() }}
            </div>
        </section>

        <!-- External Section (OBF) -->
        <section class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 px-8 py-6">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                        <span class="h-8 w-1 bg-pink-500 rounded-full"></span>
                        Open Beauty Facts Catalog
                    </h3>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full bg-pink-500/5 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-pink-600 border border-pink-500/10">
                        {{ number_format($obfProducts->total()) }} COSMETICS
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            <th class="px-8 py-5">Product Entity</th>
                            <th class="px-8 py-5">Source</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($obfProducts as $product)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 overflow-hidden rounded-lg bg-slate-100 dark:bg-slate-800 shadow-sm">
                                            <img src="{{ $product->image }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='/images/placeholders/cosmetic-placeholder.svg'">
                                        </div>
                                        <div>
                                            <p class="text-sm font-extrabold text-slate-900 dark:text-white line-clamp-1">{{ $product->nama_product }}</p>
                                            <p class="mt-1 text-[10px] font-bold text-slate-400 tracking-tighter">{{ $product->barcode }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex px-2 py-0.5 rounded-md bg-pink-50 dark:bg-pink-900/20 text-pink-600 text-[10px] font-bold uppercase">OBF Global</span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('admin.product.edit', $product->id_product) }}" class="text-primary hover:underline text-xs font-bold">VERIFY</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-8 py-10 text-center text-slate-400">No Beauty data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30">
                {{ $obfProducts->appends(['local_page' => request('local_page'), 'off_page' => request('off_page'), 'fda_page' => request('fda_page')])->links() }}
            </div>
        </section>

        <!-- External Section (FDA) -->
        <section class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 px-8 py-6">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                        <span class="h-8 w-1 bg-blue-500 rounded-full"></span>
                        OpenFDA Global Catalog
                    </h3>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full bg-blue-500/5 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-blue-600 border border-blue-500/10">
                        {{ number_format($fdaProducts->total()) }} MEDICINES
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            <th class="px-8 py-5">Medicine Entity</th>
                            <th class="px-8 py-5">Source</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($fdaProducts as $product)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 overflow-hidden rounded-lg bg-slate-100 dark:bg-slate-800 shadow-sm">
                                            <img src="{{ $product->image }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='/images/placeholders/medicine-placeholder.svg'">
                                        </div>
                                        <div>
                                            <p class="text-sm font-extrabold text-slate-900 dark:text-white line-clamp-1">{{ $product->name }}</p>
                                            <p class="mt-1 text-[10px] font-bold text-slate-400 tracking-tighter">{{ $product->barcode ?: ($product->brand_name ?: 'FDA-DB') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-900/20 text-blue-600 text-[10px] font-bold uppercase">FDA Global</span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('admin.medicines.index') }}" class="text-primary hover:underline text-xs font-bold">MANAGE</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-8 py-10 text-center text-slate-400">No Medicine data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30">
                {{ $fdaProducts->appends(['local_page' => request('local_page'), 'off_page' => request('off_page'), 'obf_page' => request('obf_page')])->links() }}
            </div>
        </section>
    </div>
</div>
@endsection
