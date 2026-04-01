@extends('admin.layouts.admin_layout')

@section('title', 'Product Inventory - Halalytics Admin')
@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-current', 'Product Inventory')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight mb-2">Product Inventory</h2>
        <p class="text-slate-500 dark:text-slate-400 max-w-md">Manage and monitor halal product verification data, scanning metrics, and compliance status.</p>
    </div>
    <a href="{{ route('admin.product.create') }}" class="flex items-center gap-2 bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-xl transition-all shadow-lg shadow-primary/20 active:scale-95">
        <span class="material-icons-round">add</span>
        <span>Add New Product</span>
    </a>
</div>

<!-- Filters & Search Card -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 mb-6 shadow-sm">
    <form action="{{ route('admin.product.index') }}" method="GET">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search Bar -->
            <div class="flex-1 relative">
                <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary/50 dark:text-white placeholder:text-slate-400 transition-all" placeholder="Search by name, barcode, or SKU...">
            </div>
            
            <!-- Quick Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <select name="category" class="flex items-center gap-2 px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-300 focus:ring-primary/20 focus:border-primary">
                    <option value="">All Categories</option>
                    @foreach($categories ?? [] as $category)
                    <option value="{{ $category->id_kategori }}" {{ request('category') == $category->id_kategori ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                    @endforeach
                </select>
                
                <select name="halal_status" class="flex items-center gap-2 px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-300 focus:ring-primary/20 focus:border-primary">
                    <option value="">Halal Status</option>
                    <option value="halal" {{ request('halal_status') == 'halal' ? 'selected' : '' }}>Halal</option>
                    <option value="diragukan" {{ request('halal_status') == 'diragukan' ? 'selected' : '' }}>Syubhat</option>
                    <option value="tidak halal" {{ request('halal_status') == 'tidak halal' ? 'selected' : '' }}>Haram</option>
                </select>
                
                <select name="active" class="flex items-center gap-2 px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-300 focus:ring-primary/20 focus:border-primary">
                    <option value="">Active Status</option>
                    <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                
                <button type="submit" class="p-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                    <span class="material-icons-round">filter_list</span>
                </button>
                
                <div class="h-8 w-px bg-slate-200 dark:bg-slate-700 mx-1"></div>
                
                <a href="{{ route('admin.product.index') }}" class="text-sm font-bold text-primary hover:text-primary-dark px-2 transition-colors">Reset Filters</a>
            </div>
        </div>
    </form>
</div>

<!-- Tables Container -->
<div class="grid grid-cols-1 gap-8">
    
    <!-- Table 1: Local Verified Products -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons-round text-primary text-xl">verified</span>
                <span>Local Verified Products</span>
            </h3>
            <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full uppercase tracking-wider">
                {{ $localProducts->total() ?? 0 }} Total
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Product Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Category</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Halal Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Active</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Scans</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($localProducts as $product)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center p-1 shadow-inner">
                                    @if($product->image)
                                    @php
                                        $localImage = str_starts_with((string) $product->image, 'http')
                                            ? $product->image
                                            : asset($product->image);
                                    @endphp
                                    <img src="{{ $localImage }}" alt="{{ $product->nama_product }}" class="w-full h-full object-contain" onerror="this.onerror=null;this.src='{{ asset('images/placeholders/product-placeholder.svg') }}'">
                                    @else
                                    <img src="{{ asset('images/placeholders/product-placeholder.svg') }}" alt="No product image" class="w-full h-full object-contain">
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">{{ Str::limit($product->nama_product, 25) }}</p>
                                    <p class="text-[10px] text-slate-500 font-medium">{{ $product->barcode }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-medium text-slate-600 dark:text-slate-400">
                             {{ $product->kategori->nama_kategori ?? 'Uncategorized' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $status = strtolower($product->status);
                                $statusClass = match($status) {
                                    'halal' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                    'syubhat', 'diragukan' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    'tidak halal', 'haram' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',
                                    default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $statusClass }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.product.toggle_active', $product->id_product) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" {{ $product->active ? 'checked' : '' }} onchange="this.form.submit()">
                                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right text-xs font-bold text-slate-600">
                            {{ number_format($product->scans_count) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.product.edit', $product->id_product) }}" class="p-2 text-slate-400 hover:text-primary transition-all"><span class="material-icons-round text-sm">edit</span></a>
                                <form action="{{ route('admin.product.destroy', $product->id_product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 transition-all"><span class="material-icons-round text-sm">delete</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400">No managed local products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 bg-slate-50/30">
            {{ $localProducts->appends(['api_page' => request('api_page')])->links() }}
        </div>
    </div>

    <!-- Table 2: API Search Results (Enriched Data) -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons-round text-indigo-500 text-xl">public</span>
                <span>External / Imported Products</span>
            </h3>
            <span class="px-3 py-1 bg-indigo-500/10 text-indigo-500 text-xs font-bold rounded-full uppercase tracking-wider">
                {{ $apiProducts->total() ?? 0 }} Synced
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Global Product</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Category</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Source</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Verification</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">AI Analysis</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Review</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($apiProducts as $product)
                    <tr class="hover:bg-indigo-50/20 dark:hover:bg-indigo-900/10 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center p-1 overflow-hidden shadow-inner transform transition-transform group-hover:scale-110">
                                    @php
                                        $externalImage = $product->image
                                            ? (str_starts_with((string) $product->image, 'http') ? $product->image : asset($product->image))
                                            : asset('images/placeholders/product-placeholder.svg');
                                    @endphp
                                    <img src="{{ $externalImage }}" alt="" class="w-full h-full object-contain" onerror="this.onerror=null;this.src='{{ asset('images/placeholders/product-placeholder.svg') }}'">
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">{{ Str::limit($product->nama_product, 25) }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $product->barcode }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs italic text-slate-500">
                             {{ $product->kategori->nama_kategori ?? 'Auto-Detected' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                {{ $product->source ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($product->verification_status == 'verified')
                                <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                    <span class="material-icons-round text-sm">verified</span> Verified
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 font-bold text-xs bg-amber-50 dark:bg-amber-900/20 px-2 py-1 rounded-lg">
                                    <span class="material-icons-round text-sm">pending</span> Needs Review
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($product->halal_analysis)
                                <div class="group relative inline-block">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded border border-indigo-200 dark:border-indigo-800 text-[10px] font-extrabold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 uppercase tracking-tighter cursor-help">
                                        <span class="material-icons-round text-[10px]">auto_awesome</span>
                                        {{ $product->halal_analysis['status'] ?? $product->status }}
                                    </span>
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-48 p-2 bg-slate-900 text-white text-[10px] rounded shadow-xl z-50">
                                        <p class="font-bold border-b border-slate-700 pb-1 mb-1">AI Recommendation</p>
                                        <p class="opacity-80">{{ $product->halal_analysis['recommendation'] ?? 'No specific recommendation.' }}</p>
                                        @if(!empty($product->halal_analysis['suspicious_ingredients']))
                                            <p class="text-rose-400 mt-1 font-bold">Suspicious: {{ implode(', ', $product->halal_analysis['suspicious_ingredients']) }}</p>
                                        @endif
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-slate-900"></div>
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-800 text-[10px] font-extrabold text-slate-500 bg-slate-50 dark:bg-slate-900/20 uppercase tracking-tighter">
                                    {{ $product->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.product.edit', $product->id_product) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 hover:bg-primary hover:text-white dark:bg-slate-800 dark:text-white text-[10px] font-bold rounded transition-all">
                                <span>Analyze</span>
                                <span class="material-icons-round text-xs">arrow_forward_ios</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400">No API products found in current search.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 bg-slate-50/30">
            {{ $apiProducts->appends(['local_page' => request('local_page')])->links() }}
        </div>
    </div>
</div>
@endsection
