@extends('admin.layouts.admin_layout')

@section('title', 'Product Hub - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Catalog</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Product Hub</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Product Inventory</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-2xl">
                Unified management of internal local products and external Open Food Facts catalog. Safety data for cosmetics and medicines are managed in dedicated modules.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.products.off.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                <span class="material-icons-round text-lg text-primary">cloud_download</span>
                SYNC OFF
            </a>
            <a href="{{ route('admin.product.ocr') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                <span class="material-icons-round text-lg text-emerald-500">document_scanner</span>
                OCR SCANNER
            </a>
            <a href="{{ route('admin.product.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-primary px-6 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all transform hover:-translate-y-1">
                <span class="material-icons-round text-lg">add</span>
                ADD LOCAL PRODUCT
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-24 w-24 bg-primary/5 rounded-full group-hover:scale-110 transition-transform"></div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Local Assets</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($productStats['local_total'] ?? 0) }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Admin curated</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-icons-round">inventory_2</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-24 w-24 bg-emerald-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Verified</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($productStats['local_verified'] ?? 0) }}</h3>
                    <p class="text-xs text-emerald-600 mt-1">User-facing active</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 flex items-center justify-center">
                    <span class="material-icons-round">verified</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-24 w-24 bg-slate-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">External OFF</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($productStats['external_total'] ?? 0) }}</h3>
                    <p class="text-xs text-slate-500 mt-1">F&B Imported</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                    <span class="material-icons-round">public</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 h-24 w-24 bg-amber-500/5 rounded-full group-hover:scale-110 transition-transform"></div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Audit Queue</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($productStats['external_review'] ?? 0) }}</h3>
                    <p class="text-xs text-amber-600 mt-1">Needs verification</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 flex items-center justify-center">
                    <span class="material-icons-round">pending_actions</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form action="{{ route('admin.product.index') }}" method="GET" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-5 relative">
                <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 pl-12 pr-4 py-3.5 text-sm focus:ring-2 focus:ring-primary transition-all"
                    placeholder="Search product name or barcode..."
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
            <div class="lg:col-span-2">
                <select name="halal_status" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3.5 text-sm focus:ring-2 focus:ring-primary transition-all">
                    <option value="">All Status</option>
                    <option value="halal" {{ request('halal_status') === 'halal' ? 'selected' : '' }}>Halal</option>
                    <option value="syubhat" {{ request('halal_status') === 'syubhat' ? 'selected' : '' }}>Syubhat</option>
                    <option value="tidak halal" {{ request('halal_status') === 'tidak halal' ? 'selected' : '' }}>Haram</option>
                </select>
            </div>
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full rounded-2xl bg-slate-900 dark:bg-white dark:text-slate-900 px-4 py-3.5 text-sm font-bold text-white hover:opacity-90 transition-all">
                    FILTER
                </button>
                <a href="{{ route('admin.product.index') }}" class="inline-flex items-center justify-center h-12 w-12 rounded-2xl border border-slate-200 dark:border-slate-700 text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                    <span class="material-icons-round">refresh</span>
                </a>
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
                        Local Asset Registry
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Directly managed database records.</p>
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
                            <th class="px-8 py-5">Pricing</th>
                            <th class="px-8 py-5">Halal Status</th>
                            <th class="px-8 py-5">Visibility</th>
                            <th class="px-8 py-5 text-right">Analytics</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($localProducts as $product)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="h-16 w-16 overflow-hidden rounded-2xl border-2 border-white dark:border-slate-800 bg-slate-100 dark:bg-slate-800 shadow-sm group-hover:scale-105 transition-transform duration-500">
                                            <img src="{{ $product->image }}" alt="{{ $product->nama_product }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='{{ $product->image_fallback_url }}'">
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
                                    <span class="text-xs font-extrabold text-slate-900 dark:text-white">Rp{{ number_format((float) ($product->price ?? 0), 0, ',', '.') }}</span>
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
                                    <form action="{{ route('admin.product.toggle_active', $product->id_product) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="peer sr-only" {{ $product->active ? 'checked' : '' }} onchange="this.form.submit()">
                                            <div class="h-5 w-9 rounded-full bg-slate-200 dark:bg-slate-700 peer-checked:bg-primary after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-all peer-checked:after:translate-x-4"></div>
                                        </label>
                                    </form>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex flex-col items-end">
                                        <span class="text-xs font-extrabold text-slate-900 dark:text-white">{{ number_format($product->scans_count) }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Total Scans</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-1">
                                        <a href="{{ route('admin.product.edit', $product->id_product) }}" class="h-9 w-9 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary transition-all">
                                            <span class="material-icons-round text-lg">edit</span>
                                        </a>
                                        <form action="{{ route('admin.product.destroy', $product->id_product) }}" method="POST" onsubmit="return confirm('Archive this product permanently?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-9 w-9 rounded-xl flex items-center justify-center text-slate-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:text-rose-500 transition-all">
                                                <span class="material-icons-round text-lg">delete_outline</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-20 w-20 rounded-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center mb-4">
                                            <span class="material-icons-round text-4xl text-slate-300">inventory</span>
                                        </div>
                                        <h4 class="text-xl font-bold text-slate-800 dark:text-white">Local Registry Empty</h4>
                                        <p class="text-sm text-slate-400 mt-2">No local products match your current filtering criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30">
                {{ $localProducts->appends(['api_page' => request('api_page')])->links() }}
            </div>
        </section>

        <!-- External Section -->
        <section class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 border-b border-slate-100 dark:border-slate-800 px-8 py-6">
                <div>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                        <span class="h-8 w-1 bg-slate-400 rounded-full"></span>
                        External Cloud Catalog
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Products imported from Open Food Facts API.</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                    {{ number_format($apiProducts->total()) }} ENTRIES
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            <th class="px-8 py-5">Cloud Entity</th>
                            <th class="px-8 py-5">Pricing</th>
                            <th class="px-8 py-5">Source Engine</th>
                            <th class="px-8 py-5">Verification</th>
                            <th class="px-8 py-5">AI Analysis</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($apiProducts as $product)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="h-16 w-16 overflow-hidden rounded-2xl border-2 border-white dark:border-slate-800 bg-slate-100 dark:bg-slate-800 shadow-sm group-hover:scale-105 transition-transform duration-500">
                                            <img src="{{ $product->image }}" alt="{{ $product->nama_product }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='{{ $product->image_fallback_url }}'">
                                        </div>
                                        <div>
                                            <p class="text-sm font-extrabold text-slate-900 dark:text-white line-clamp-1">{{ $product->nama_product }}</p>
                                            <p class="mt-1 text-[10px] font-bold text-slate-400 tracking-tighter">{{ $product->barcode ?: 'No barcode' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-xs font-extrabold text-slate-900 dark:text-white">Rp{{ number_format((float) ($product->price ?? 0), 0, ',', '.') }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex px-2 py-0.5 rounded border border-primary/20 bg-primary/5 text-[9px] font-extrabold uppercase tracking-widest text-primary">
                                        {{ str_replace('_', ' ', strtolower((string) $product->source)) }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    @if($product->verification_status === 'verified')
                                        <div class="flex items-center gap-2">
                                            <span class="material-icons-round text-sm text-emerald-500">verified</span>
                                            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">VERIFIED</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <span class="material-icons-round text-sm text-amber-500">pending</span>
                                            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">AUDIT QUEUE</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-6">
                                    @if($product->halal_analysis)
                                        <span class="inline-flex px-3 py-1 rounded-full border border-primary/20 bg-primary/5 text-[10px] font-bold uppercase tracking-widest text-primary">
                                            {{ $product->halal_analysis['status'] ?? $product->status }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">No Analysis</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('admin.product.edit', $product->id_product) }}" class="inline-flex items-center gap-2 px-6 py-2 rounded-xl bg-slate-900 dark:bg-white dark:text-slate-900 text-white text-[10px] font-bold uppercase tracking-widest hover:opacity-90 transition-all shadow-lg shadow-slate-900/10">
                                        REVIEW DETAIL
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-20 w-20 rounded-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center mb-4">
                                            <span class="material-icons-round text-4xl text-slate-300">cloud_off</span>
                                        </div>
                                        <h4 class="text-xl font-bold text-slate-800 dark:text-white">Cloud Catalog Empty</h4>
                                        <p class="text-sm text-slate-400 mt-2">No imported entries match your filter or search.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/30">
                {{ $apiProducts->appends(['local_page' => request('local_page')])->links() }}
            </div>
        </section>
    </div>
</div>
@endsection
