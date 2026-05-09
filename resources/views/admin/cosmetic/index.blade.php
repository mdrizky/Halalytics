@extends('admin.layouts.admin_layout')

@section('title', 'Skincare & Cosmetics Hub - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Catalog</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Cosmetics</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Skincare & Cosmetics Inventory</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-2xl">
                Management of internal cosmetic products and external Open Beauty Facts catalog.
            </p>
        </div>
        <div class="flex items-center gap-3">
             <form action="{{ route('admin.cosmetics.seed') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                    <span class="material-icons-round text-lg text-primary">auto_awesome</span>
                    SEED OBF
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Local Assets</p>
            <div class="mt-4 flex items-end justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['local_total'] ?? 0) }}</h3>
                <div class="h-12 w-12 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center">
                    <span class="material-icons-round">face_retouching_natural</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">External OBF</p>
            <div class="mt-4 flex items-end justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['from_obf'] ?? 0) }}</h3>
                <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <span class="material-icons-round">public</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form action="{{ route('admin.cosmetics.index') }}" method="GET" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-9 relative">
                <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 pl-12 pr-4 py-3.5 text-sm" placeholder="Search cosmetics...">
            </div>
            <div class="lg:col-span-3">
                <button type="submit" class="w-full rounded-2xl bg-slate-900 text-white px-4 py-3.5 text-sm font-bold">FILTER</button>
            </div>
        </form>
    </div>

    <div class="space-y-8">
        <!-- Local Table -->
        <section class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Local Cosmetic Registry</h3>
                <span class="px-4 py-1.5 rounded-full bg-pink-50 text-pink-600 text-[10px] font-bold">{{ $localCosmetics->total() }} ASSETS</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-bold uppercase text-slate-400">
                            <th class="px-8 py-5">Product</th>
                            <th class="px-8 py-5">Brand</th>
                            <th class="px-8 py-5">Halal Status</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($localCosmetics as $item)
                            <tr class="hover:bg-slate-50 transition-all">
                                <td class="px-8 py-6 flex items-center gap-4">
                                    <img src="{{ $item->image_url }}" class="w-12 h-12 rounded-xl object-cover" onerror="this.onerror=null;this.src='/images/placeholders/cosmetic-placeholder.svg'">
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $item->nama_produk }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $item->nomor_reg ?: 'BPOM Pending' }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-sm font-medium">{{ $item->merk ?: '-' }}</td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase">{{ $item->status_halal ?: 'SYUBHAT' }}</span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                     <button class="text-slate-400 hover:text-primary"><span class="material-icons-round">visibility</span></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-8 py-10 text-center text-slate-400">Empty.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-4 border-t">{{ $localCosmetics->appends(['obf_page' => request('obf_page')])->links() }}</div>
        </section>

        <!-- OBF Table -->
        <section class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
             <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Open Beauty Facts Catalog</h3>
                <span class="px-4 py-1.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">{{ $obfCosmetics->total() }} EXTERNAL</span>
            </div>
             <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-bold uppercase text-slate-400">
                            <th class="px-8 py-5">Product</th>
                            <th class="px-8 py-5">Source</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($obfCosmetics as $item)
                            <tr class="hover:bg-slate-50 transition-all">
                                <td class="px-8 py-6 flex items-center gap-4">
                                    <img src="{{ $item->image_url }}" class="w-10 h-10 rounded-lg object-cover" onerror="this.onerror=null;this.src='/images/placeholders/cosmetic-placeholder.svg'">
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $item->nama_produk }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $item->barcode }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6"><span class="px-2 py-0.5 rounded bg-pink-50 text-pink-600 text-[10px] font-bold">OBF</span></td>
                                <td class="px-8 py-6 text-right">
                                     <button class="text-primary font-bold text-xs">IMPORT</button>
                                </td>
                            </tr>
                        @empty
                             <tr><td colspan="3" class="px-8 py-10 text-center text-slate-400">Empty.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-4 border-t">{{ $obfCosmetics->appends(['local_page' => request('local_page')])->links() }}</div>
        </section>
    </div>
</div>
@endsection
