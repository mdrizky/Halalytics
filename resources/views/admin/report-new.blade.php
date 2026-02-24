@extends('admin.layouts.admin_layout')

@section('title', 'User Product Reports - Halalytics Admin')
@section('breadcrumb-parent', 'Reports')
@section('breadcrumb-current', 'User Product Reports')

@section('content')
<!-- Heading & Actions -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div class="space-y-1">
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">User Product Reports</h1>
        <p class="text-slate-500 dark:text-slate-400 max-w-xl">Monitor and validate integrity reports submitted by the community to ensure product status accuracy.</p>
    </div>
    <div class="flex items-center gap-2">
        <button class="px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 transition-all flex items-center gap-2">
            <span class="material-icons-round text-lg">download</span>
            Export
        </button>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col gap-1">
        <p class="text-sm font-bold text-slate-400 uppercase tracking-tight">Total Reports</p>
        <div class="flex items-baseline justify-between">
            <p class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_reports'] ?? 0) }}</p>
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+12%</span>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col gap-1 ring-1 ring-primary/10">
        <p class="text-sm font-bold text-primary uppercase tracking-tight">Pending Review</p>
        <div class="flex items-baseline justify-between">
            <p class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['pending_reports'] ?? 0) }}</p>
            <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-1 rounded-full">Active</span>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col gap-1">
        <p class="text-sm font-bold text-slate-400 uppercase tracking-tight">Resolved Today</p>
        <div class="flex items-baseline justify-between">
            <p class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['resolved_today'] ?? 0) }}</p>
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+3</span>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col gap-1">
        <p class="text-sm font-bold text-slate-400 uppercase tracking-tight">Rejection Rate</p>
        <div class="flex items-baseline justify-between">
            <p class="text-3xl font-black text-slate-900 dark:text-white">{{ $stats['rejection_rate'] ?? '0' }}%</p>
            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Stable</span>
        </div>
    </div>
</div>

<!-- Table Section -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <!-- Filter Bar -->
    <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-800/20">
        <form action="{{ route('admin.report.index') }}" method="GET" class="flex items-center gap-3 w-full sm:w-auto">
            <select name="reason" class="text-sm font-medium border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg focus:ring-primary focus:border-primary py-1.5" onchange="this.form.submit()">
                <option value="">All Reasons</option>
                <option value="incorrect_status" {{ request('reason') == 'incorrect_status' ? 'selected' : '' }}>Incorrect Status</option>
                <option value="expired_cert" {{ request('reason') == 'expired_cert' ? 'selected' : '' }}>Expired Certificate</option>
                <option value="fake_forgery" {{ request('reason') == 'fake_forgery' ? 'selected' : '' }}>Fake/Forgery</option>
                <option value="other" {{ request('reason') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            <select name="status" class="text-sm font-medium border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg focus:ring-primary focus:border-primary py-1.5" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Resolved (Approved)</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </form>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
            Showing {{ $reports->count() }} of {{ $reports->total() }} Reports
        </p>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50">
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Product Info</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Submitted By</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Reason</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($reports as $report)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-lg flex-shrink-0 flex items-center justify-center p-1 overflow-hidden">
                                @if($report->product && $report->product->gambar)
                                    <img src="{{ asset('storage/' . $report->product->gambar) }}" alt="Product" class="w-full h-full object-cover">
                                @else
                                    <span class="material-icons-round text-slate-400">inventory_2</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $report->product->nama_product ?? 'Unknown Product' }}</p>
                                <p class="text-xs text-slate-500">ID: {{ $report->product_id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-primary text-xs font-bold">
                                {{ strtoupper(substr($report->user->username ?? 'U', 0, 1)) }}
                            </div>
                            <span class="text-sm text-slate-600 dark:text-slate-400">{{ $report->user->email ?? 'Unknown User' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-black uppercase tracking-widest rounded bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">
                            {{ str_replace('_', ' ', ucfirst($report->reason ?? 'Report')) }}
                        </span>
                        @if($report->laporan)
                        <p class="text-xs text-slate-500 mt-1 truncate max-w-[150px]" title="{{ $report->laporan }}">{{ $report->laporan }}</p>
                        @endif
                        @if($report->evidence_image)
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $report->evidence_image) }}" target="_blank" class="group relative flex items-center justify-center w-16 h-16 rounded-lg border-2 border-dashed border-slate-200 hover:border-primary transition-all overflow-hidden bg-slate-50">
                                <img src="{{ asset('storage/' . $report->evidence_image) }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                    <span class="material-icons-round text-white text-xs">visibility</span>
                                </div>
                            </a>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center">
                            @php
                                $status = strtolower($report->status ?? 'pending');
                                $statusClass = match($status) {
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-slate-100 text-slate-500',
                                    'pending' => 'bg-primary/10 text-primary',
                                    default => 'bg-slate-100 text-slate-500'
                                };
                                $dotColor = match($status) {
                                    'approved' => 'bg-emerald-500',
                                    'rejected' => 'bg-slate-400',
                                    'pending' => 'bg-primary',
                                    default => 'bg-slate-400'
                                };
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $statusClass }} flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                                {{ ucfirst($status === 'approved' ? 'Resolved' : $status) }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            @if($report->status == 'pending')
                                @if($report->reason == 'fake_forgery' || str_contains(strtolower($report->reason), 'fake') || str_contains(strtolower($report->reason), 'palsu'))
                                <div class="flex flex-col gap-1 items-end">
                                    <form action="{{ route('admin.report.resolve_forgery', $report->id_report) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="confirm_fake">
                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all shadow-sm shadow-red-200 flex items-center gap-1.5">
                                            <span class="material-icons-round text-sm">gpp_bad</span>
                                            Confirm Fake
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.report.resolve_forgery', $report->id_report) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="dismiss">
                                        <button type="submit" class="px-3 py-1.5 text-xs font-bold text-slate-500 hover:text-red-500 transition-all">Dismiss Reports</button>
                                    </form>
                                </div>
                                @else
                                <form action="{{ route('admin.report.update_status', $report->id_report) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold bg-primary text-white rounded-lg hover:bg-primary-dark transition-all shadow-sm shadow-primary/20">Verify</button>
                                </form>
                                <form action="{{ route('admin.report.update_status', $report->id_report) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold text-slate-500 hover:text-red-500 bg-slate-100 dark:bg-slate-800 rounded-lg transition-all">Dismiss</button>
                                </form>
                                @endif
                            @else
                            <div class="text-right flex flex-col items-end">
                                <span class="text-xs font-bold text-slate-400">Archived</span>
                                @if($report->admin_notes)
                                <p class="text-[10px] text-slate-400 mt-0.5 italic max-w-[120px]">{{ $report->admin_notes }}</p>
                                @endif
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                        <span class="material-icons-round text-4xl mb-2">check_circle</span>
                        <p>No reports found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <p class="text-sm text-slate-500 font-medium">
            Showing <span class="text-slate-900 dark:text-white">{{ $reports->firstItem() ?? 0 }}</span> 
            to <span class="text-slate-900 dark:text-white">{{ $reports->lastItem() ?? 0 }}</span> 
            of <span class="text-slate-900 dark:text-white">{{ number_format($reports->total()) }}</span> reports
        </p>
        <div class="flex items-center gap-2">
            {{ $reports->links('vendor.pagination.tailwind-admin') }}
        </div>
    </div>
</div>

<!-- Footer Summary Card -->
<div class="bg-primary/5 dark:bg-primary/10 p-6 rounded-2xl border border-primary/10 flex flex-col md:flex-row md:items-center justify-between gap-6 mt-8">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center shadow-lg shadow-primary/30">
            <span class="material-icons-round text-white text-2xl">auto_awesome</span>
        </div>
        <div>
            <p class="font-bold text-slate-900 dark:text-white">Smart Verification Assistant</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Our AI has pre-screened {{ $stats['pending_reports'] ?? 0 }} of the pending reports.</p>
        </div>
    </div>
    <button class="whitespace-nowrap px-6 py-2.5 bg-primary text-white font-black text-sm rounded-xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">Run Batch Verification</button>
</div>
@endsection
