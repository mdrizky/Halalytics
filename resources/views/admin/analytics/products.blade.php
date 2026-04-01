@extends('admin.layouts.admin_layout')

@section('title', 'Product Analytics - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Admin</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="text-slate-400">Analytics</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Products</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Product Analytics</h2>
        <p class="text-slate-500 text-sm mt-1">Product scan distribution, top products, and category breakdown.</p>
    </div>
    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.analytics.users') }}" class="px-3 py-1.5 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-100">Users</a>
        <a href="{{ route('admin.analytics.ai') }}" class="px-3 py-1.5 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-100">AI</a>
        <a href="{{ route('admin.analytics.growth') }}" class="px-3 py-1.5 text-xs font-bold rounded-lg text-slate-500 hover:bg-slate-100">Growth</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Total Products -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <p class="text-xs text-slate-500 font-semibold uppercase mb-2">Total Products</p>
        <p class="text-3xl font-extrabold text-slate-800 dark:text-white">{{ number_format($totalProducts) }}</p>
    </div>

    <!-- Status Distribution -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <p class="text-xs text-slate-500 font-semibold uppercase mb-3">Halal Status Distribution</p>
        @foreach($statusDistribution as $status)
        <div class="flex items-center justify-between text-sm mb-2">
            <span class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full {{ $status->status_halal === 'halal' ? 'bg-emerald-500' : ($status->status_halal === 'syubhat' ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                <span>{{ ucfirst($status->status_halal) }}</span>
            </span>
            <span class="font-bold">{{ number_format($status->count) }}</span>
        </div>
        @endforeach
    </div>

    <!-- Top Categories -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <p class="text-xs text-slate-500 font-semibold uppercase mb-3">Top Categories</p>
        @foreach($categoryDistribution->take(5) as $cat)
        <div class="flex items-center justify-between text-sm mb-2">
            <span class="text-slate-600 dark:text-slate-400">{{ $cat->name }}</span>
            <span class="font-bold text-slate-800 dark:text-white">{{ number_format($cat->count) }}</span>
        </div>
        @endforeach
    </div>
</div>

<!-- Top Scanned Products -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
    <h4 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Top 20 Most Scanned Products</h4>
    <div class="space-y-3">
        @foreach($topScanned as $index => $product)
        <div class="flex items-center justify-between p-3 rounded-lg {{ $index < 3 ? 'bg-primary/5' : 'hover:bg-slate-50 dark:hover:bg-slate-800/30' }}">
            <div class="flex items-center space-x-4">
                <span class="w-8 h-8 rounded-lg {{ $index < 3 ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }} text-sm font-extrabold flex items-center justify-center">
                    {{ $index + 1 }}
                </span>
                <span class="text-sm font-medium text-slate-800 dark:text-white">{{ $product->nama_produk }}</span>
            </div>
            <span class="text-sm font-extrabold text-primary">{{ number_format($product->scan_count) }} scans</span>
        </div>
        @endforeach
    </div>
</div>
@endsection
