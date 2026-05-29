@extends('admin.layouts.admin_layout')

@section('title', 'Donation Campaigns - Halalytics Admin')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Donation Campaigns</h2>
        <p class="text-slate-500 text-sm mt-1">Manage charity and donation funding campaigns.</p>
    </div>
    <a href="{{ route('admin.donation-campaigns.create') }}" class="flex items-center space-x-2 bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-sm shadow-primary/20">
        <span class="material-icons-round text-lg">add</span>
        <span>Create Campaign</span>
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Campaigns</p>
                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $stats['total'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                <span class="material-icons-round">campaign</span>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Active</p>
                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $stats['active'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600">
                <span class="material-icons-round">check_circle</span>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Donors</p>
                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $stats['total_donors'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600">
                <span class="material-icons-round">people</span>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Funds</p>
                <h3 class="text-2xl font-extrabold text-slate-800 dark:text-white">Rp {{ number_format($stats['total_donations'], 0, ',', '.') }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-icons-round">payments</span>
            </div>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Campaign</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Target vs Collected</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($campaigns as $campaign)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden flex-shrink-0">
                                @if($campaign->image)
                                <img src="{{ $campaign->image }}" class="w-full h-full object-cover">
                                @else
                                <span class="material-icons-round text-slate-400">image</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-white line-clamp-1">{{ $campaign->title }}</p>
                                <p class="text-xs text-slate-500 flex items-center mt-0.5">
                                    <span class="material-icons-round text-[12px] mr-1">person</span> {{ $campaign->donations_count }} Donors
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                            {{ $campaign->category }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-xs font-medium text-slate-800 dark:text-white mb-1">
                            Rp {{ number_format($campaign->collected_amount, 0, ',', '.') }} 
                            <span class="text-slate-400 font-normal">of Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-primary h-1.5 rounded-full" style="width: {{ min(100, ($campaign->collected_amount / max(1, $campaign->target_amount)) * 100) }}%"></div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($campaign->is_active)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">Active</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <form action="{{ route('admin.donation-campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Delete this campaign?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all" title="Delete">
                                    <span class="material-icons-round text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800 mb-4">
                            <span class="material-icons-round text-3xl text-slate-300">volunteer_activism</span>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400">Belum ada kampanye donasi.</p>
                        <a href="{{ route('admin.donation-campaigns.create') }}" class="inline-flex items-center space-x-2 mt-4 text-primary font-bold hover:underline">
                            <span>Buat Kampanye Pertama</span>
                            <span class="material-icons-round text-sm">arrow_forward</span>
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($campaigns->hasPages())
    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
        {{ $campaigns->links() }}
    </div>
    @endif
</div>
@endsection
