@extends('admin.layouts.admin_layout')

@section('title', $donationCampaign->title . ' - Halalytics Admin')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.donation-campaigns.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:text-slate-800 hover:bg-slate-200 transition-all">
                <span class="material-icons-round">arrow_back</span>
            </a>
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Detail Kampanye</h2>
                <p class="text-slate-500 text-sm mt-0.5">{{ $donationCampaign->title }}</p>
            </div>
        </div>
        <a href="{{ route('admin.donation-campaigns.edit', $donationCampaign) }}" class="flex items-center space-x-2 bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-sm">
            <span class="material-icons-round text-lg">edit</span>
            <span>Edit</span>
        </a>
    </div>

    {{-- Campaign Info Card --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
        <div class="md:flex">
            {{-- Image --}}
            <div class="md:w-1/3 bg-slate-100 dark:bg-slate-800 flex items-center justify-center min-h-[200px]">
                @if($donationCampaign->image)
                <img src="{{ $donationCampaign->image }}" class="w-full h-full object-cover" alt="{{ $donationCampaign->title }}">
                @else
                <span class="material-icons-round text-6xl text-slate-300">volunteer_activism</span>
                @endif
            </div>

            {{-- Info --}}
            <div class="md:w-2/3 p-6 space-y-4">
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $donationCampaign->is_active ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                        {{ $donationCampaign->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    @if($donationCampaign->is_urgent)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">
                        <span class="material-icons-round text-sm mr-1">priority_high</span> Urgent
                    </span>
                    @endif
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200">
                        {{ ucfirst($donationCampaign->category) }}
                    </span>
                </div>

                <h3 class="text-xl font-extrabold text-slate-800 dark:text-white">{{ $donationCampaign->title }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $donationCampaign->description }}</p>

                {{-- Progress Bar --}}
                <div>
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="font-bold text-primary">Rp {{ number_format($donationCampaign->collected_amount, 0, ',', '.') }}</span>
                        <span class="text-slate-500">Target: Rp {{ number_format($donationCampaign->target_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-primary to-emerald-400 h-3 rounded-full transition-all" style="width: {{ $donationCampaign->progressPercent() }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ $donationCampaign->progressPercent() }}% tercapai · {{ $donationCampaign->donor_count }} donatur</p>
                </div>

                @if($donationCampaign->deadline)
                <div class="flex items-center text-sm text-slate-500">
                    <span class="material-icons-round text-[16px] mr-1.5">schedule</span>
                    Deadline: {{ $donationCampaign->deadline->format('d M Y') }}
                    @if($donationCampaign->deadline->isPast())
                    <span class="text-red-500 font-bold ml-2">(Expired)</span>
                    @else
                    <span class="text-emerald-600 ml-2">({{ $donationCampaign->deadline->diffForHumans() }})</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Donations Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
            <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-icons-round text-primary text-lg">receipt_long</span>
                Riwayat Donasi ({{ $donations->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Donatur</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($donations as $donation)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-white">
                                    {{ $donation->is_anonymous ? 'Anonim' : ($donation->donor_name ?? $donation->user?->full_name ?? 'N/A') }}
                                </p>
                                @if(!$donation->is_anonymous && $donation->user)
                                <p class="text-xs text-slate-500">{{ $donation->user->email }}</p>
                                @endif
                                @if($donation->donor_message)
                                <p class="text-xs text-slate-400 mt-1 italic line-clamp-1">"{{ $donation->donor_message }}"</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-slate-800 dark:text-white">Rp {{ number_format($donation->amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($donation->payment_status == 'settlement')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600">Sukses</span>
                            @elseif($donation->payment_status == 'pending')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600">Pending</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">{{ $donation->payment_status }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $donation->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-slate-50 dark:bg-slate-800 mb-3">
                                <span class="material-icons-round text-2xl text-slate-300">receipt_long</span>
                            </div>
                            <p class="text-slate-500">Belum ada donasi untuk kampanye ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($donations->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $donations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
