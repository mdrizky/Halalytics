@extends('admin.layouts.admin_layout')

@section('title', 'User Management - Halalytics Admin')
@section('breadcrumb-parent', 'Home')
@section('breadcrumb-current', 'User Management')

@section('content')
<!-- Page Heading & Actions -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight leading-none mb-2">User Overview</h2>
        <p class="text-slate-500 dark:text-slate-400 max-w-lg">Monitor verified scan activity, manage account access, and review community contributions across the Halalytics ecosystem.</p>
    </div>
    <div class="flex gap-3">
        <button class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition-all shadow-sm">
            <span class="material-icons-round text-lg">file_download</span>
            Export Data
        </button>
        <a href="#" class="flex items-center gap-2 px-6 py-2.5 bg-primary text-white rounded-lg text-sm font-extrabold hover:brightness-105 transition-all shadow-lg shadow-primary/20">
            <span class="material-icons-round text-lg">person_add</span>
            Add New User
        </a>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Registered Users -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 bg-primary/10 rounded-lg text-primary">
                <span class="material-icons-round">groups</span>
            </div>
            @php
                $userChange = ($stats['user_change'] ?? 12);
            @endphp
            <span class="text-[10px] font-bold px-2 py-1 bg-green-100 text-green-700 rounded-full">+{{ $userChange }}% vs LY</span>
        </div>
        <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Total Registered Users</p>
        <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total_users'] ?? 0) }}</p>
    </div>
    
    <!-- Daily Active Users -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 bg-primary/10 rounded-lg text-primary">
                <span class="material-icons-round">bolt</span>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 bg-primary/10 text-primary rounded-full">Active Now</span>
        </div>
        <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Daily Active Users</p>
        <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['active_users'] ?? 0) }}</p>
    </div>
    
    <!-- Total Products Scanned -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div class="p-2 bg-amber-500/10 rounded-lg text-amber-500">
                <span class="material-icons-round">qr_code</span>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 bg-amber-100 text-amber-700 rounded-full">New Scans</span>
        </div>
        <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Total Products Scanned</p>
        @php
            $scans = $stats['total_scans'] ?? 0;
            if ($scans >= 1000) {
                $scansFormatted = number_format($scans / 1000, 1) . 'k';
            } else {
                $scansFormatted = number_format($scans);
            }
        @endphp
        <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $scansFormatted }}</p>
    </div>
</div>

<!-- Search & Filters -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm mb-8">
    <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex flex-wrap gap-4 items-center justify-between bg-slate-50/50 dark:bg-slate-800/20">
        <form action="{{ route('admin.user') }}" method="GET" class="w-full flex flex-wrap gap-4 items-center justify-between">
            <div class="flex-1 min-w-[300px]">
                <div class="relative group">
                    <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm outline-none" placeholder="Search by name, email, or UID...">
                </div>
            </div>
            <div class="flex gap-2">
                <select name="status" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm px-4 py-2 focus:ring-primary/20 focus:border-primary outline-none font-medium" onchange="this.form.submit()">
                    <option value="">Status: All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
                <select name="sort" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm px-4 py-2 focus:ring-primary/20 focus:border-primary outline-none font-medium" onchange="this.form.submit()">
                    <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Sort by: Newest</option>
                    <option value="username" {{ request('sort') == 'username' ? 'selected' : '' }}>Alphabetical</option>
                    <option value="scans_count" {{ request('sort') == 'scans_count' ? 'selected' : '' }}>Most Active</option>
                </select>
            </div>
        </form>
    </div>
    
    <!-- Data Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-400 text-[11px] uppercase tracking-widest font-bold">
                    <th class="px-6 py-4">User Details</th>
                    <th class="px-6 py-4">Registration</th>
                    <th class="px-6 py-4 text-center">Total Scans</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-full bg-primary/20 overflow-hidden ring-2 ring-white dark:ring-slate-900 shadow-sm flex items-center justify-center text-primary font-bold">
                                @if($user->profile_image)
                                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->username }}" class="w-full h-full object-cover">
                                @else
                                {{ strtoupper(substr($user->username ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white leading-none mb-1">{{ $user->full_name ?? $user->username }}</p>
                                <p class="text-xs text-slate-500 font-medium tracking-tight">{{ $user->email }} • ID: {{ $user->id_user }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-slate-700 dark:text-slate-300 font-medium">{{ $user->created_at ? $user->created_at->format('M d, Y') : '-' }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $user->created_at ? $user->created_at->format('H:i A') : '' }}</p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $userScanTotal = (int) ($user->scans_count ?? 0) + (int) ($user->scan_histories_count ?? 0);
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">{{ number_format($userScanTotal) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $status = (int)($user->active ?? 1) === 1 ? 'active' : 'blocked';
                            $statusClass = match($status) {
                                'active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200/50',
                                'blocked' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 border-rose-200/50',
                                'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200/50',
                                default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-slate-200/50'
                            };
                            $showPulse = $status === 'active';
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[11px] font-black uppercase tracking-wider {{ $statusClass }} border">
                            @if($showPulse)
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            @else
                            <span class="w-1.5 h-1.5 rounded-full bg-current opacity-50"></span>
                            @endif
                            {{ ucfirst($status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.user.edit', $user->id_user) }}" class="px-3 py-1.5 text-xs font-bold text-primary hover:bg-primary/5 rounded-lg transition-colors">Details</a>
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-1.5 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                                    <span class="material-icons-round text-lg">more_horiz</span>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <span class="material-icons-round text-5xl text-slate-300 mb-3">group_off</span>
                            <p class="text-slate-500 font-medium">No users found</p>
                            <p class="text-slate-400 text-sm">Try adjusting your search or filter criteria</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination Footer -->
    <div class="p-4 bg-slate-50/50 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">
            Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ number_format($users->total()) }} users
        </p>
        <div class="flex items-center gap-1">
            {{ $users->links('vendor.pagination.tailwind-admin') }}
        </div>
    </div>
</div>

<!-- Bottom Cards -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Community Verification Status -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 p-6 rounded-2xl text-white relative overflow-hidden shadow-xl shadow-slate-900/10">
        <div class="relative z-10 flex flex-col h-full justify-between">
            <div>
                <h3 class="text-xl font-extrabold mb-2 tracking-tight">Community Verification Status</h3>
                <p class="text-slate-300 text-sm leading-relaxed mb-4">Halalytics relies on trusted contributors. Review the top 1% of users who have suggested verified halal certificates this month.</p>
                <div class="space-y-2 mb-4">
                    @forelse(($topContributors ?? []) as $contributor)
                    @php
                        $contributorScans = (int) ($contributor->scans_count ?? 0) + (int) ($contributor->scan_histories_count ?? 0);
                    @endphp
                    <div class="flex items-center justify-between text-xs bg-white/10 rounded-lg px-3 py-2">
                        <span>{{ $contributor->username }}</span>
                        <span class="font-bold">{{ number_format($contributorScans) }} scans</span>
                    </div>
                    @empty
                    <div class="text-xs text-slate-400">Belum ada kontribusi pengguna.</div>
                    @endforelse
                </div>
            </div>
            <div class="flex items-center gap-4">
                <button class="px-4 py-2 bg-primary rounded-lg text-sm font-extrabold hover:brightness-110 transition-all">Review Contributors</button>
                <a href="#" class="text-sm font-bold text-slate-400 hover:text-white transition-colors">Learn about Tier System</a>
            </div>
        </div>
        <!-- Decorative Background -->
        <div class="absolute -right-12 -bottom-12 opacity-10">
            <span class="material-icons-round text-[160px]">verified</span>
        </div>
    </div>
    
    <!-- Recent Scan Trends -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-sm">
        <h3 class="text-lg font-extrabold text-slate-900 dark:text-white mb-4 tracking-tight">Recent Scan Trends</h3>
        <div class="space-y-4">
            @forelse($scan_trends ?? [] as $trend)
            <div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full" style="background-color: {{ $trend['color'] ?? '#00bbc2' }}"></div>
                        <span class="text-sm font-bold text-slate-600 dark:text-slate-400">{{ $trend['category'] ?? 'Unknown' }}</span>
                    </div>
                    <span class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $trend['percentage'] ?? 0 }}%</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                    <div class="h-full rounded-full" style="width: {{ $trend['percentage'] ?? 0 }}%; background-color: {{ $trend['color'] ?? '#00bbc2' }}"></div>
                </div>
            </div>
            @empty
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-primary"></div>
                    <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Dairy & Poultry</span>
                </div>
                <span class="text-sm font-extrabold text-slate-900 dark:text-white">42%</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                <div class="bg-primary h-full rounded-full" style="width: 42%"></div>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                    <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Processed Snacks</span>
                </div>
                <span class="text-sm font-extrabold text-slate-900 dark:text-white">28%</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                <div class="bg-amber-400 h-full rounded-full" style="width: 28%"></div>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-primary"></div>
                    <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Beauty & Cosmetics</span>
                </div>
                <span class="text-sm font-extrabold text-slate-900 dark:text-white">15%</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                <div class="bg-primary h-full rounded-full" style="width: 15%"></div>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
