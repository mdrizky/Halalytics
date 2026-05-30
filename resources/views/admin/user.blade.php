@extends('admin.layouts.admin_layout')

@section('title', 'User Intelligence Center | Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Administration</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">User Management</span>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Sophisticated Header -->
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
        <div class="space-y-2">
            <h2 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">User Management</h2>
            <p class="text-slate-500 dark:text-slate-400 font-medium max-w-2xl">
                Kelola ekosistem pengguna Halalytics. Pantau aktivitas scan, atur hak akses profesional, dan analisis pertumbuhan komunitas secara realtime.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.user.export') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-5 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 shadow-sm hover:bg-slate-50 transition-all">
                <span class="material-icons-round text-lg">file_download</span>
                EXPORT DATA
            </a>
            <a href="{{ route('admin.user.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 dark:bg-white px-6 py-3 text-sm font-bold text-white dark:text-slate-900 shadow-xl hover:opacity-90 transition-all transform hover:-translate-y-1">
                <span class="material-icons-round text-lg">person_add</span>
                REGISTER NEW USER
            </a>
        </div>
    </div>

    <!-- Analytics Dashboard Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-7 border border-slate-200/60 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 h-32 w-32 bg-primary/5 rounded-full group-hover:scale-125 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="h-12 w-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                    <span class="material-icons-round">groups</span>
                </div>
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Total Registrations</p>
                <div class="flex items-baseline gap-3">
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_users']) }}</h3>
                    <span class="flex items-center text-xs font-bold text-emerald-500">
                        <span class="material-icons-round text-sm">trending_up</span>
                        {{ $stats['user_change'] }}%
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-7 border border-slate-200/60 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 h-32 w-32 bg-emerald-500/5 rounded-full group-hover:scale-125 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="h-12 w-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mb-5">
                    <span class="material-icons-round">how_to_reg</span>
                </div>
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Active Accounts</p>
                <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['active_users']) }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-7 border border-slate-200/60 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 h-32 w-32 bg-amber-500/5 rounded-full group-hover:scale-125 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="h-12 w-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center mb-5">
                    <span class="material-icons-round">qr_code_scanner</span>
                </div>
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Community Scans</p>
                <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_scans']) }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-7 border border-slate-200/60 dark:border-slate-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 h-32 w-32 bg-blue-500/5 rounded-full group-hover:scale-125 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="h-12 w-12 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center mb-5">
                    <span class="material-icons-round">medication</span>
                </div>
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Health Experts</p>
                <h3 class="text-3xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total_nutritionists']) }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter & Search Control Center -->
    <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-6 border border-slate-200/60 dark:border-slate-800 shadow-xl shadow-slate-200/20">
        <form action="{{ route('admin.user.index') }}" method="GET" class="flex flex-col xl:flex-row gap-6">
            <div class="flex-1 relative">
                <span class="material-icons-round absolute left-5 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="w-full rounded-2xl border-none bg-slate-50 dark:bg-slate-800 pl-14 pr-6 py-4 text-sm font-bold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-primary/20 transition-all"
                    placeholder="Search by identity, email, or username..."
                >
            </div>
            
            <div class="flex flex-wrap items-center gap-4">
                <select name="role" class="rounded-2xl border-none bg-slate-50 dark:bg-slate-800 px-6 py-4 text-sm font-bold text-slate-600 dark:text-slate-300 focus:ring-2 focus:ring-primary/20 cursor-pointer">
                    <option value="">All Roles</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Regular Users</option>
                    <option value="ahli_gizi" {{ request('role') == 'ahli_gizi' ? 'selected' : '' }}>Nutritionists</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrators</option>
                </select>

                <select name="status" class="rounded-2xl border-none bg-slate-50 dark:bg-slate-800 px-6 py-4 text-sm font-bold text-slate-600 dark:text-slate-300 focus:ring-2 focus:ring-primary/20 cursor-pointer">
                    <option value="all">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked Only</option>
                </select>

                <button type="submit" class="rounded-2xl bg-primary px-8 py-4 text-sm font-black text-white shadow-lg shadow-primary/20 hover:opacity-90 transition-all">
                    APPLY FILTERS
                </button>
            </div>
        </form>
    </div>

    <!-- Main User Registry -->
    <div class="bg-white dark:bg-slate-900 rounded-[3rem] border border-slate-200/60 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-slate-800/50 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                        <th class="px-8 py-6">User Profile</th>
                        <th class="px-8 py-6">Security & Role</th>
                        <th class="px-8 py-6">Medical Insights</th>
                        <th class="px-8 py-6">Activity Metrics</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $user)
                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all duration-300">
                            <td class="px-8 py-7">
                                <div class="flex items-center gap-5">
                                    <div class="h-14 w-14 rounded-2xl bg-slate-100 dark:bg-slate-800 border-2 border-white dark:border-slate-700 shadow-sm overflow-hidden group-hover:scale-105 transition-transform duration-500">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=00bbc2&color=fff&bold=true" class="h-full w-full object-cover">
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-sm font-black text-slate-900 dark:text-white">{{ $user->full_name }}</p>
                                        <p class="text-xs font-bold text-slate-400 flex items-center gap-1.5">
                                            <span class="material-icons-round text-xs">alternate_email</span>
                                            {{ $user->username }}
                                        </p>
                                        <p class="text-[10px] font-bold text-slate-400/80">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-7">
                                @php
                                    $roleKey = strtolower($user->role ?? 'user');
                                    $roleMeta = match($roleKey) {
                                        'admin' => ['label' => 'ADMINISTRATOR', 'color' => 'text-rose-600', 'bg' => 'bg-rose-50', 'border' => 'border-rose-100'],
                                        'ahli_gizi', 'nutritionist', 'expert' => ['label' => 'HEALTH EXPERT', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-100'],
                                        default => ['label' => 'COMMUNITY USER', 'color' => 'text-primary', 'bg' => 'bg-primary/5', 'border' => 'border-primary/10'],
                                    };
                                @endphp
                                <div class="space-y-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg {{ $roleMeta['bg'] }} {{ $roleMeta['color'] }} {{ $roleMeta['border'] }} border text-[10px] font-black tracking-widest uppercase">
                                        {{ $roleMeta['label'] }}
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-2 rounded-full {{ $user->active ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                                        <span class="text-[10px] font-black {{ $user->active ? 'text-emerald-600' : 'text-rose-600' }} uppercase">{{ $user->active ? 'Verified & Active' : 'Account Blocked' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-7">
                                <div class="flex flex-wrap gap-2">
                                    @if($user->bmi)
                                        <div class="px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-center">
                                            <p class="text-[10px] font-bold text-slate-400 leading-none mb-1">BMI</p>
                                            <p class="text-xs font-black text-slate-700 dark:text-slate-200">{{ $user->bmi }}</p>
                                        </div>
                                    @endif
                                    @if($user->blood_type)
                                        <div class="px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-100 text-center">
                                            <p class="text-[10px] font-bold text-rose-300 leading-none mb-1">BLOOD</p>
                                            <p class="text-xs font-black text-rose-600">{{ $user->blood_type }}</p>
                                        </div>
                                    @endif
                                    @if(!$user->bmi && !$user->blood_type)
                                        <span class="text-xs font-bold text-slate-300 italic">No health data</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-7">
                                <div class="flex items-center gap-4">
                                    <div class="text-center">
                                        <p class="text-xl font-black text-primary">{{ number_format(($user->scans_count ?? 0) + ($user->scan_histories_count ?? 0)) }}</p>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Total Scans</p>
                                    </div>
                                    <div class="h-8 w-px bg-slate-100 dark:bg-slate-800"></div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400">Joined On</p>
                                        <p class="text-xs font-black text-slate-700 dark:text-slate-200">{{ $user->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-7 text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('admin.user.show', $user->id_user) }}" class="h-10 w-10 rounded-xl flex items-center justify-center text-slate-400 hover:bg-primary/10 hover:text-primary transition-all" title="Full Analytics">
                                        <span class="material-icons-round">analytics</span>
                                    </a>
                                    <a href="{{ route('admin.user.edit', $user->id_user) }}" class="h-10 w-10 rounded-xl flex items-center justify-center text-slate-400 hover:bg-primary/10 hover:text-primary transition-all" title="Edit Profile">
                                        <span class="material-icons-round">manage_accounts</span>
                                    </a>
                                    <div class="h-6 w-px bg-slate-100 dark:bg-slate-800 mx-1"></div>
                                    <form action="{{ route('admin.user.toggle', $user->id_user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="h-10 w-10 rounded-xl flex items-center justify-center {{ $user->active ? 'text-rose-400 hover:bg-rose-50 hover:text-rose-600' : 'text-emerald-400 hover:bg-emerald-50 hover:text-emerald-600' }} transition-all" title="{{ $user->active ? 'Suspend Account' : 'Activate Account' }}">
                                            <span class="material-icons-round">{{ $user->active ? 'block' : 'check_circle' }}</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="h-20 w-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                        <span class="material-icons-round text-4xl">person_search</span>
                                    </div>
                                    <p class="text-slate-400 font-bold">No users found matching your search criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
