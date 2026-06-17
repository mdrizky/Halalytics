@extends('admin.layouts.admin_layout')

@section('title', 'AI Analysis Results - Halalytics Admin')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">AI Analysis Results</h2>
            <p class="text-slate-500 text-sm mt-1">Monitor all AI-generated product analysis results across the platform.</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase">Total</p>
            <p class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase">Terverifikasi</p>
            <p class="text-2xl font-extrabold text-emerald-600">{{ $stats['verified'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase">Pending</p>
            <p class="text-2xl font-extrabold text-amber-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase">Halal</p>
            <p class="text-2xl font-extrabold text-emerald-600">{{ $stats['halal_count'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase">Syubhat</p>
            <p class="text-2xl font-extrabold text-amber-600">{{ $stats['syubhat_count'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase">Haram</p>
            <p class="text-2xl font-extrabold text-red-600">{{ $stats['haram_count'] }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <tr>
                        <th class="text-left p-4">ID</th>
                        <th class="text-left p-4">Product</th>
                        <th class="text-left p-4">User</th>
                        <th class="text-center p-4">Halal</th>
                        <th class="text-center p-4">Health</th>
                        <th class="text-center p-4">Nutri</th>
                        <th class="text-center p-4">Verified</th>
                        <th class="text-center p-4">Expert</th>
                        <th class="text-right p-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($results as $r)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="p-4 font-mono text-xs text-slate-400">#{{ $r->id }}</td>
                        <td class="p-4">
                            <p class="font-bold text-slate-800 dark:text-white">{{ $r->product?->nama_product ?? 'N/A' }}</p>
                            <p class="text-[10px] text-slate-400">{{ $r->input_type }}</p>
                        </td>
                        <td class="p-4 text-slate-600">{{ $r->user?->name ?? 'Guest' }}</td>
                        <td class="p-4 text-center">
                            @php $hc = ['HALAL' => 'text-emerald-600', 'HARAM' => 'text-red-600', 'SYUBHAT' => 'text-amber-600', 'HALAL_CERTIFIED' => 'text-emerald-700', 'HALAL_UNCERTIFIED' => 'text-emerald-500']; @endphp
                            <span class="text-xs font-black {{ $hc[$r->halal_verdict] ?? 'text-slate-500' }}">{{ $r->halal_verdict }}</span>
                        </td>
                        <td class="p-4 text-center text-xs font-bold">{{ $r->health_verdict ?? '-' }}</td>
                        <td class="p-4 text-center text-xs font-black">{{ $r->nutri_score ?? '-' }}</td>
                        <td class="p-4 text-center">
                            @if($r->is_verified_by_expert)
                                <span class="text-emerald-500 material-icons-round text-sm">verified</span>
                            @else
                                <span class="text-amber-400 material-icons-round text-sm">hourglass_empty</span>
                            @endif
                        </td>
                        <td class="p-4 text-center text-xs text-slate-500">{{ $r->expert?->name ?? '-' }}</td>
                        <td class="p-4 text-right text-[10px] text-slate-400">{{ $r->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-12 text-center text-slate-400 font-bold">No analysis results yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($results->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $results->links() }}
        </div>
        @endif
    </div>
</div>
@endsection