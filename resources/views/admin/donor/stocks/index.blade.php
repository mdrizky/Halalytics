@extends('admin.layouts.admin_layout')

@section('title', 'Blood Stocks - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Blood Stocks</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Blood Donation Hub</h2>
        <p class="text-slate-500 text-sm mt-1">Manage blood stocks, events, appointments, and emergencies.</p>
    </div>
</div>

@include('admin.donor.tabs')

<!-- Summary Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach(['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-'] as $type)
        @php
            $stockItem = $summary->firstWhere('blood_type', $type);
            $count = $stockItem ? $stockItem->total_bags : 0;
            
            $bg = 'bg-emerald-50 dark:bg-emerald-900/20';
            $text = 'text-emerald-700 dark:text-emerald-400';
            $iconColor = 'text-emerald-500';
            
            if($count < 5) {
                $bg = 'bg-red-50 dark:bg-red-900/20';
                $text = 'text-red-700 dark:text-red-400';
                $iconColor = 'text-red-500';
            } elseif($count < 15) {
                $bg = 'bg-amber-50 dark:bg-amber-900/20';
                $text = 'text-amber-700 dark:text-amber-400';
                $iconColor = 'text-amber-500';
            }
        @endphp
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 mb-1">Type {{ $type }}</p>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ $count }} <span class="text-sm font-medium text-slate-400">bags</span></h3>
            </div>
            <div class="w-10 h-10 rounded-full {{ $bg }} flex items-center justify-center">
                <span class="material-icons-round {{ $iconColor }} text-xl">water_drop</span>
            </div>
        </div>
    @endforeach
</div>

<!-- Detailed Logs -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 dark:border-slate-800">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Recent Blood Collections</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <th class="px-6 py-4">Blood Type</th>
                    <th class="px-6 py-4">Volume</th>
                    <th class="px-6 py-4">Source / Location</th>
                    <th class="px-6 py-4">Collected Date</th>
                    <th class="px-6 py-4">Expiry Date</th>
                    <th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($stocks as $stock)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-bold text-primary">{{ $stock->blood_type }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-700 dark:text-slate-300">
                        {{ $stock->volume_ml }} ml
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-slate-700 dark:text-slate-300">{{ $stock->location }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">
                        {{ \Carbon\Carbon::parse($stock->collected_date)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $daysLeft = \Carbon\Carbon::parse($stock->expiry_date)->diffInDays(now(), false) * -1;
                        @endphp
                        <div class="text-sm font-medium {{ $daysLeft < 7 ? 'text-red-500' : 'text-slate-700 dark:text-slate-300' }}">
                            {{ \Carbon\Carbon::parse($stock->expiry_date)->format('d M Y') }}
                        </div>
                        <div class="text-[10px] text-slate-400">{{ $daysLeft }} days left</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full text-xs font-semibold">{{ ucfirst($stock->status) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
        {{ $stocks->links() }}
    </div>
</div>
@endsection
