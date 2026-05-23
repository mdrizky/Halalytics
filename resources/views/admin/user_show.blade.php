@extends('admin.layouts.admin_layout')

@section('title', 'User Detail - ' . $user->full_name . ' | Halalytics')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="relative">
                <div class="w-24 h-24 rounded-3xl bg-white border-4 border-primary/10 overflow-hidden shadow-2xl flex items-center justify-center">
                    @if($user->image)
                        <img src="{{ $user->image }}" alt="{{ $user->full_name }}" class="w-full h-full object-cover">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=004D40&color=fff&size=128" alt="{{ $user->full_name }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="absolute -bottom-2 -right-2 w-8 h-8 rounded-xl bg-white shadow-lg flex items-center justify-center border-2 border-white">
                    <span class="material-icons-round text-sm {{ $user->active ? 'text-emerald-500' : 'text-red-500' }}">
                        {{ $user->active ? 'verified_user' : 'block' }}
                    </span>
                </div>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">{{ $user->full_name }}</h1>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-slate-500 font-medium">{{ '@' . $user->username }}</span>
                    @php
                        $roleDisplay = match(strtolower($user->role)) {
                            'admin' => 'Admin',
                            'expert', 'nutritionist' => 'Ahli Gizi',
                            default => 'User'
                        };
                        $badgeClass = match(strtolower($user->role)) {
                            'admin' => 'bg-red-50 text-red-600 border border-red-200/60 dark:bg-red-500/10 dark:text-red-400',
                            'expert', 'nutritionist' => 'bg-emerald-50 text-emerald-600 border border-emerald-200/60 dark:bg-emerald-500/10 dark:text-emerald-400',
                            default => 'bg-blue-50 text-blue-600 border border-blue-200/60 dark:bg-blue-500/10 dark:text-blue-400'
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">
                        {{ $roleDisplay }}
                    </span>
                    @if($user->active)
                        <div class="flex items-center gap-1.5 text-emerald-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-tighter">Active User</span>
                        </div>
                    @else
                        <div class="flex items-center gap-1.5 text-red-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            <span class="text-xs font-bold uppercase tracking-tighter">Blocked</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex gap-3">
            <button onclick="window.location.href='{{ route('admin.user.edit', $user->id_user) }}'" class="px-6 py-3 rounded-2xl bg-white border border-slate-200 text-slate-700 font-bold text-sm shadow-sm hover:shadow-md hover:bg-slate-50 transition-all flex items-center gap-2">
                <span class="material-icons-round text-lg">edit</span>
                Edit Profile
            </button>
            @if($user->role !== 'admin')
            <form action="{{ route('admin.users.destroy', $user->id_user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-3 rounded-2xl bg-red-50 border border-red-100 text-red-600 font-bold text-sm shadow-sm hover:shadow-md hover:bg-red-100 transition-all flex items-center gap-2">
                    <span class="material-icons-round text-lg">delete_forever</span>
                    Delete
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Info -->
        <div class="space-y-8">
            <!-- Account Information -->
            <div class="bg-white rounded-[2rem] border border-slate-200/60 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                    <span class="material-icons-round text-primary">account_circle</span>
                    <h3 class="font-bold text-slate-800">Account Information</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div class="group">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Email Address</label>
                        <div class="text-slate-700 font-bold group-hover:text-primary transition-colors flex items-center gap-2">
                            <span class="material-icons-round text-sm text-slate-300">mail</span>
                            {{ $user->email }}
                        </div>
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Phone Number</label>
                        <div class="text-slate-700 font-bold group-hover:text-primary transition-colors flex items-center gap-2">
                            <span class="material-icons-round text-sm text-slate-300">phone</span>
                            {{ $user->phone ?: 'Not provided' }}
                        </div>
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Provider</label>
                        <div class="text-slate-700 font-bold capitalize flex items-center gap-2">
                            <span class="material-icons-round text-sm text-slate-300">login</span>
                            {{ $user->provider ?: 'Direct Email' }}
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-50">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Joined Date</label>
                            <div class="text-slate-600 text-xs font-bold">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Last Activity</label>
                            <div class="text-slate-600 text-xs font-bold">{{ $user->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if(in_array(strtolower($user->role), ['expert', 'nutritionist', 'ahli_gizi']))
                <!-- Nutritionist Credentials -->
                <div class="bg-white rounded-[2rem] border border-slate-200/60 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 flex items-center gap-3 bg-emerald-50/50">
                        <span class="material-icons-round text-emerald-600">verified</span>
                        <h3 class="font-bold text-slate-800">Nutritionist Credentials</h3>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="group">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">STR / License Number</label>
                            <div class="text-slate-700 font-bold flex items-center gap-2">
                                <span class="material-icons-round text-sm text-slate-300">badge</span>
                                STR-{{ substr(md5($user->id_user), 0, 8) }} (Verified)
                            </div>
                        </div>
                        <div class="group">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Specialization</label>
                            <div class="text-slate-700 font-bold flex items-center gap-2">
                                <span class="material-icons-round text-sm text-slate-300">fitness_center</span>
                                Clinical Nutrition & Weight Management
                            </div>
                        </div>
                        <div class="group">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Consultation Status</label>
                            <div class="text-emerald-600 font-bold flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Ready to Consult (Online)
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(in_array(strtolower($user->role), ['user', 'ahli_gizi']))
                <!-- Health Snapshot (User & Ahli Gizi) -->
                <div class="bg-primary rounded-[2rem] shadow-xl shadow-primary/20 overflow-hidden relative group">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all duration-500"></div>
                    <div class="px-8 py-6 flex items-center gap-3 relative">
                        <span class="material-icons-round text-white/80">favorite</span>
                        <h3 class="font-bold text-white">Health Snapshot</h3>
                    </div>
                    <div class="p-8 space-y-6 relative">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-md border border-white/10">
                                <label class="text-[9px] font-black text-white/50 uppercase tracking-widest block mb-1 text-center">BMI Score</label>
                                <div class="text-2xl font-black text-white text-center">{{ $user->bmi ?: '-' }}</div>
                                @if($user->bmi)
                                    <div class="text-[10px] font-bold text-center mt-1 {{ $user->bmi > 25 ? 'text-amber-300' : ($user->bmi < 18.5 ? 'text-blue-300' : 'text-emerald-300') }}">
                                        {{ $user->bmi > 25 ? 'Overweight' : ($user->bmi < 18.5 ? 'Underweight' : 'Normal Weight') }}
                                    </div>
                                @endif
                            </div>
                            <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-md border border-white/10">
                                <label class="text-[9px] font-black text-white/50 uppercase tracking-widest block mb-1 text-center">Blood Type</label>
                                <div class="text-2xl font-black text-white text-center">{{ $user->blood_type ?: '-' }}</div>
                                <div class="text-[10px] font-bold text-center mt-1 text-white/60 uppercase">Emergency</div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="bg-white/10 rounded-2xl p-4 border border-white/5 hover:bg-white/[0.15] transition-all">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-icons-round text-sm text-amber-400">warning</span>
                                    <label class="text-[10px] font-black text-white/70 uppercase tracking-widest">Allergies</label>
                                </div>
                                <p class="text-xs text-white leading-relaxed font-medium">
                                    {{ $user->allergy ?: 'No specific allergies reported by the user.' }}
                                </p>
                            </div>
                            <div class="bg-white/10 rounded-2xl p-4 border border-white/5 hover:bg-white/[0.15] transition-all">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-icons-round text-sm text-blue-400">history_edu</span>
                                    <label class="text-[10px] font-black text-white/70 uppercase tracking-widest">Medical History</label>
                                </div>
                                <p class="text-xs text-white leading-relaxed font-medium">
                                    {{ $user->medical_history ?: 'User has not shared their medical background yet.' }}
                                </p>
                            </div>
                        </div>
                        <div class="pt-4 grid grid-cols-2 gap-4 text-center">
                            <div class="group/stat">
                                <div class="text-[10px] font-black text-white/40 uppercase tracking-widest">Weight</div>
                                <div class="text-white font-black">{{ $user->weight ? $user->weight . ' kg' : '-' }}</div>
                            </div>
                            <div class="group/stat">
                                <div class="text-[10px] font-black text-white/40 uppercase tracking-widest">Height</div>
                                <div class="text-white font-black">{{ $user->height ? $user->height . ' cm' : '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(strtolower($user->role) === 'admin')
                <!-- Admin Statistics Snapshot -->
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-[2rem] shadow-xl shadow-red-500/20 overflow-hidden relative group">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all duration-500"></div>
                    <div class="px-8 py-6 flex items-center gap-3 relative">
                        <span class="material-icons-round text-white/80">admin_panel_settings</span>
                        <h3 class="font-bold text-white">Admin Statistics</h3>
                    </div>
                    <div class="p-8 space-y-6 relative">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-md border border-white/10">
                                <label class="text-[9px] font-black text-white/50 uppercase tracking-widest block mb-1 text-center">Users Managed</label>
                                <div class="text-2xl font-black text-white text-center">{{ \App\Models\User::count() }}</div>
                                <div class="text-[10px] font-bold text-center mt-1 text-white/60 uppercase">Total</div>
                            </div>
                            <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-md border border-white/10">
                                <label class="text-[9px] font-black text-white/50 uppercase tracking-widest block mb-1 text-center">System Status</label>
                                <div class="text-2xl font-black text-white text-center">Active</div>
                                <div class="text-[10px] font-bold text-center mt-1 text-white/60 uppercase">Online</div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="bg-white/10 rounded-2xl p-4 border border-white/5 hover:bg-white/[0.15] transition-all">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-icons-round text-sm text-amber-400">security</span>
                                    <label class="text-[10px] font-black text-white/70 uppercase tracking-widest">Admin Access Level</label>
                                </div>
                                <p class="text-xs text-white leading-relaxed font-medium">
                                    Full system administrator with access to all management features.
                                </p>
                            </div>
                            <div class="bg-white/10 rounded-2xl p-4 border border-white/5 hover:bg-white/[0.15] transition-all">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-icons-round text-sm text-blue-400">verified_user</span>
                                    <label class="text-[10px] font-black text-white/70 uppercase tracking-widest">Account Status</label>
                                </div>
                                <p class="text-xs text-white leading-relaxed font-medium">
                                    {{ $user->active ? 'Active Administrator' : 'Suspended Account' }}
                                </p>
                            </div>
                        </div>
                        <div class="pt-4 grid grid-cols-2 gap-4 text-center">
                            <div class="group/stat">
                                <div class="text-[10px] font-black text-white/40 uppercase tracking-widest">Member Since</div>
                                <div class="text-white font-black">{{ $user->created_at->format('M Y') }}</div>
                            </div>
                            <div class="group/stat">
                                <div class="text-[10px] font-black text-white/40 uppercase tracking-widest">Last Login</div>
                                <div class="text-white font-black">{{ $user->last_login ? $user->last_login->diffForHumans() : 'Never' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Stats Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-8 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col items-center justify-center group hover:border-primary/20 transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-primary/5 text-primary flex items-center justify-center mb-4 group-hover:bg-primary group-hover:text-white transition-all">
                        <span class="material-icons-round">qr_code_scanner</span>
                    </div>
                    <div class="text-3xl font-black text-slate-800 tracking-tighter">{{ number_format($stats['total_scans'] ?? 0) }}</div>
                    <div class="text-xs font-black text-slate-400 uppercase tracking-widest mt-1">Total Scans</div>
                </div>
                <div class="bg-white p-8 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col items-center justify-center group hover:border-emerald-500/20 transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                        <span class="material-icons-round">task_alt</span>
                    </div>
                    <div class="text-3xl font-black text-slate-800 tracking-tighter">{{ number_format($stats['halal_scans'] ?? 0) }}</div>
                    <div class="text-xs font-black text-slate-400 uppercase tracking-widest mt-1">Halal Matches</div>
                </div>
                <div class="bg-white p-8 rounded-[2rem] border border-slate-200/60 shadow-sm flex flex-col items-center justify-center group hover:border-red-500/20 transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mb-4 group-hover:bg-red-500 group-hover:text-white transition-all">
                        <span class="material-icons-round">report_problem</span>
                    </div>
                    <div class="text-3xl font-black text-slate-800 tracking-tighter">{{ number_format($stats['haram_scans'] ?? 0) }}</div>
                    <div class="text-xs font-black text-slate-400 uppercase tracking-widest mt-1">Haram/Warning</div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-[2rem] border border-slate-200/60 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-icons-round text-primary">history</span>
                        <h3 class="font-bold text-slate-800">Recent Health & Halal Activity</h3>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Latest 30 Events</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Product Details</th>
                                <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Type</th>
                                <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Verdict</th>
                                <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @php
                                $combinedScans = $scanHistories->concat($scans)->sortByDesc(function($s) {
                                    return $s->created_at ?? $s->tanggal_scan;
                                })->take(30);
                            @endphp
                            
                            @forelse($combinedScans as $scan)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="font-bold text-slate-700 group-hover:text-primary transition-colors">
                                            {{ $scan->nama_produk ?? $scan->product_name ?? 'Unknown Product' }}
                                        </div>
                                        <div class="text-[10px] font-black text-slate-400 tracking-tighter">{{ $scan->barcode ?: 'No Barcode' }}</div>
                                    </td>
                                    <td class="px-4 py-5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="material-icons-round text-xs text-slate-300">category</span>
                                            <span class="text-xs font-bold text-slate-500 capitalize">{{ $scan->kategori ?: 'Food/Cosm.' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5 text-center">
                                        @php
                                            $hStatus = strtolower($scan->halal_status ?? $scan->status_halal ?? 'unknown');
                                            $badgeClass = 'bg-slate-100 text-slate-500 border-slate-200';
                                            if($hStatus == 'halal') $badgeClass = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                                            elseif($hStatus == 'haram' || $hStatus == 'tidak halal') $badgeClass = 'bg-red-50 text-red-600 border-red-100';
                                            elseif($hStatus == 'syubhat' || $hStatus == 'diragukan') $badgeClass = 'bg-amber-50 text-amber-600 border-amber-100';
                                        @endphp
                                        <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border {{ $badgeClass }}">
                                            {{ $hStatus }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right text-xs font-bold text-slate-400 italic">
                                        {{ ($scan->created_at ?? $scan->tanggal_scan) ? \Carbon\Carbon::parse($scan->created_at ?? $scan->tanggal_scan)->diffForHumans() : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center opacity-30">
                                            <span class="material-icons-round text-6xl mb-2">inventory_2</span>
                                            <p class="font-bold text-slate-500">No activity history found for this user.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($combinedScans->count() > 0)
                <div class="p-6 bg-slate-50/50 border-t border-slate-100 text-center">
                    <button class="text-[10px] font-black text-primary uppercase tracking-widest hover:underline">View Full Activity Log</button>
                </div>
                @endif
            </div>

            <!-- FCM / Technical Status -->
            <div class="bg-white rounded-[2rem] border border-slate-200/60 shadow-sm p-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-icons-round">notifications_active</span>
                    </div>
                    <div>
                        <div class="font-bold text-slate-800">Notification Delivery Status</div>
                        <div class="text-xs font-medium text-slate-500">
                            {{ $user->fcm_token ? 'FCM Token is active and healthy.' : 'FCM Token not found. Notifications may not be delivered.' }}
                        </div>
                    </div>
                </div>
                <div class="px-4 py-2 rounded-xl {{ $user->fcm_token ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} text-[10px] font-black uppercase tracking-widest border border-currentColor/10">
                    {{ $user->fcm_token ? 'Online' : 'Disconnected' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
