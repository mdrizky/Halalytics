@extends('admin.layouts.admin_layout')

@section('title', 'Medicine & Healthcare Hub - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Catalog</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Medicines</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Medicine & Healthcare Inventory</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-2xl">
                Management of internal medicine products and external OpenFDA catalog.
            </p>
        </div>
        <div class="flex items-center gap-3">
             <form action="{{ route('admin.medicines.seed') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-5 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                    <span class="material-icons-round text-lg text-primary">auto_awesome</span>
                    SEED FDA
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
                <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-icons-round">medication</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">External FDA</p>
            <div class="mt-4 flex items-end justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['from_fda'] ?? 0) }}</h3>
                <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <span class="material-icons-round">public</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form action="{{ route('admin.medicines.index') }}" method="GET" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-9 relative">
                <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 pl-12 pr-4 py-3.5 text-sm" placeholder="Search medicines...">
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
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Local Medicine Registry</h3>
                <span class="px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-bold">{{ $localMedicines->total() }} ASSETS</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-bold uppercase text-slate-400">
                            <th class="px-8 py-5">Medicine</th>
                            <th class="px-8 py-5">Generic Name</th>
                            <th class="px-8 py-5">Halal Status</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($localMedicines as $item)
                            <tr class="hover:bg-slate-50 transition-all">
                                <td class="px-8 py-6 flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 shadow-sm">
                                        <span class="material-icons-round">medication</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $item->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $item->manufacturer ?: 'Local Pharma' }}</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-sm font-medium">{{ $item->generic_name ?: '-' }}</td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase">{{ $item->halal_status ?: 'HALAL' }}</span>
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
            <div class="px-8 py-4 border-t">{{ $localMedicines->appends(['fda_page' => request('fda_page')])->links() }}</div>
        </section>

        <!-- FDA Table -->
        <section class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
             <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">OpenFDA Global Catalog</h3>
                <span class="px-4 py-1.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">{{ $fdaMedicines->total() }} EXTERNAL</span>
            </div>
             <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-bold uppercase text-slate-400">
                            <th class="px-8 py-5">Drug Name</th>
                            <th class="px-8 py-5">Source</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($fdaMedicines as $item)
                            <tr class="hover:bg-slate-50 transition-all">
                                <td class="px-8 py-6 flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400">
                                        <span class="material-icons-round">medical_services</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900 dark:text-white line-clamp-1">{{ $item->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">FDA-REG</p>
                                    </div>
                                </td>
                                <td class="px-8 py-6"><span class="px-2 py-0.5 rounded bg-blue-50 text-blue-600 text-[10px] font-bold uppercase">FDA</span></td>
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
            <div class="px-8 py-4 border-t">{{ $fdaMedicines->appends(['local_page' => request('local_page')])->links() }}</div>
        </section>
    </div>
</div>
@endsection
