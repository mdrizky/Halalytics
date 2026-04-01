@extends('admin.layouts.admin_layout')

@section('title', 'Scan History - Halalytics Admin')
@section('breadcrumb-parent', 'Analytics')
@section('breadcrumb-current', 'Scan History')

@section('content')
<!-- Header -->
<div class="flex justify-between items-end mb-8">
    <div class="max-w-2xl">
        <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">Scan Activity History</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-2">Monitor product verification logs in real-time across all active user sessions.</p>
    </div>
    <div class="flex gap-3">
        <button class="flex items-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
            <span class="material-icons-round text-[20px]">download</span>
            Export CSV
        </button>
        <button onclick="window.location.reload()" class="flex items-center gap-2 bg-primary px-4 py-2.5 rounded-lg text-sm font-bold text-white hover:bg-primary-dark transition-all shadow-lg shadow-primary/20">
            <span class="material-icons-round text-[20px]">refresh</span>
            Live Refresh
        </button>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Scans Today -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
            <span class="material-icons-round text-6xl text-primary">qr_code_scanner</span>
        </div>
        <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Total Scans Today</p>
        <div class="flex items-baseline gap-3 mt-2">
            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['today_scans'] ?? 0) }}</p>
            @php
                $scanTrend = $stats['scan_trend'] ?? 0;
                $isPositive = $scanTrend >= 0;
            @endphp
            <span class="{{ $isPositive ? 'text-emerald-500' : 'text-red-500' }} text-sm font-bold flex items-center gap-0.5">
                <span class="material-icons-round text-xs">{{ $isPositive ? 'trending_up' : 'trending_down' }}</span> {{ abs($scanTrend) }}%
            </span>
        </div>
    </div>
    
    <!-- Haram Flags -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
            <span class="material-icons-round text-6xl text-red-500">warning</span>
        </div>
        <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Haram Flags</p>
        <div class="flex items-baseline gap-3 mt-2">
            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['haram_flags'] ?? 0) }}</p>
            <span class="text-red-500 text-sm font-bold flex items-center gap-0.5">
                <span class="material-icons-round text-xs">priority_high</span> Today
            </span>
        </div>
    </div>
    
    <!-- Active Users -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
            <span class="material-icons-round text-6xl text-blue-500">person</span>
        </div>
        <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Active Users (7d)</p>
        <div class="flex items-baseline gap-3 mt-2">
            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['active_users'] ?? 0) }}</p>
            @php
                $userTrend = $stats['user_trend'] ?? 0;
                $isUserPositive = $userTrend >= 0;
            @endphp
            <span class="{{ $isUserPositive ? 'text-emerald-500' : 'text-red-500' }} text-sm font-bold flex items-center gap-0.5">
                <span class="material-icons-round text-xs">{{ $isUserPositive ? 'trending_up' : 'trending_down' }}</span> {{ abs($userTrend) }}%
            </span>
        </div>
    </div>
</div>

<!-- Filter & Toolbar -->
<div class="bg-white dark:bg-slate-900 rounded-t-xl border-x border-t border-slate-200 dark:border-slate-800 p-4">
    <form action="{{ route('admin.scan.index') }}" method="GET">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4 flex-1">
                <div class="relative w-full max-w-sm">
                    <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary/50 transition-all placeholder:text-slate-400" placeholder="Search products, users or barcodes...">
                </div>
                <div class="h-8 w-px bg-slate-200 dark:bg-slate-700 hidden md:block"></div>
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                    <button type="submit" name="status" value="" class="{{ !request('status') ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900' : 'bg-slate-50 dark:bg-slate-800 text-slate-500' }} text-[11px] font-bold px-3 py-1.5 rounded-full uppercase tracking-tight transition-colors">All Scans</button>
                    <button type="submit" name="status" value="halal" class="{{ request('status') == 'halal' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-50 dark:bg-slate-800 text-slate-500' }} text-[11px] font-bold px-3 py-1.5 rounded-full uppercase tracking-tight hover:bg-emerald-100/50 hover:text-emerald-600 transition-colors">Halal</button>
                    <button type="submit" name="status" value="syubhat" class="{{ request('status') == 'syubhat' ? 'bg-amber-100 text-amber-600' : 'bg-slate-50 dark:bg-slate-800 text-slate-500' }} text-[11px] font-bold px-3 py-1.5 rounded-full uppercase tracking-tight hover:bg-amber-100/50 hover:text-amber-600 transition-colors">Syubhat</button>
                    <button type="submit" name="status" value="haram" class="{{ request('status') == 'haram' ? 'bg-red-100 text-red-600' : 'bg-slate-50 dark:bg-slate-800 text-slate-500' }} text-[11px] font-bold px-3 py-1.5 rounded-full uppercase tracking-tight hover:bg-red-100/50 hover:text-red-600 transition-colors">Haram</button>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <button type="button" class="flex items-center gap-2 p-2 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300">
                    <span class="material-icons-round text-xl">calendar_today</span>
                    <span class="text-xs font-bold px-1 whitespace-nowrap">{{ date('M d') }} - Now</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm rounded-b-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">User</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Product Verification</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Barcode</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Time of Scan</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Result</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($scans as $scan)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-400 text-xs overflow-hidden">
                                @if(isset($scan->user) && $scan->user->profile_image)
                                    <img src="{{ asset('storage/' . $scan->user->profile_image) }}" alt="Profile" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($scan->user->username ?? 'G', 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $scan->user->username ?? 'Guest User' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $scan->user->email ?? 'No email' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center overflow-hidden">
                                @php
                                    $productImage = null;
                                    if (isset($scan->product)) {
                                        if ($scan->product->gambar) {
                                            $productImage = asset('storage/' . $scan->product->gambar);
                                        } elseif ($scan->product->image) {
                                            $imgVal = $scan->product->image;
                                            $productImage = str_starts_with($imgVal, 'http') ? $imgVal : asset('storage/' . $imgVal);
                                        } elseif ($scan->product->image_url) {
                                            $productImage = $scan->product->image_url;
                                        }
                                    }
                                @endphp
                                @if($productImage)
                                    <img src="{{ $productImage }}" alt="Product" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <span class="material-icons-round text-slate-400" style="display:none">fastfood</span>
                                @else
                                    <span class="material-icons-round text-slate-400">fastfood</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $scan->nama_produk ?? 'Unknown Product' }}</p>
                                <p class="text-[11px] text-slate-400">Category: {{ $scan->product->kategori->nama_kategori ?? $scan->kategori ?? 'General' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-xs font-mono font-medium text-slate-500 bg-slate-50 dark:bg-slate-800 px-2 py-1 rounded">{{ $scan->barcode }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($scan->tanggal_scan)->format('M d, Y') }}</p>
                            <p class="text-[11px] text-primary font-bold uppercase tracking-tight">{{ \Carbon\Carbon::parse($scan->tanggal_scan)->format('H:i:s A') }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @php
                            $status = strtolower($scan->status_halal ?? 'unknown');
                            $statusClass = match($status) {
                                'halal' => 'bg-emerald-100 text-emerald-600 border-emerald-200/50',
                                'haram' => 'bg-red-100 text-red-600 border-red-200/50',
                                'syubhat', 'diragukan' => 'bg-amber-100 text-amber-600 border-amber-200/50',
                                default => 'bg-slate-100 text-slate-600 border-slate-200/50'
                            };
                        @endphp
                        <span class="{{ $statusClass }} dark:bg-opacity-20 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border">
                            {{ ucfirst($status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                        <span class="material-icons-round text-4xl mb-2">history_edu</span>
                        <p>No scan history found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <p class="text-sm text-slate-500 font-medium">
            Showing <span class="text-slate-900 dark:text-white">{{ $scans->firstItem() ?? 0 }}</span> 
            to <span class="text-slate-900 dark:text-white">{{ $scans->lastItem() ?? 0 }}</span> 
            of <span class="text-slate-900 dark:text-white">{{ number_format($scans->total()) }}</span> scans
        </p>
        <div class="flex items-center gap-2">
            {{ $scans->links('vendor.pagination.tailwind-admin') }}
        </div>
    </div>
</div>
@endsection
