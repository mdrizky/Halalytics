@extends('admin.layouts.admin_layout')

@section('title', 'Edit User - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="text-slate-400">Users</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Edit User</span>
@endsection

@section('content')
<!-- Page Title -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Edit User</h2>
        <p class="text-slate-500 text-sm mt-1">Update user account information and access permissions.</p>
    </div>
    <a href="{{ route('admin.user.index') }}" class="flex items-center space-x-2 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
        <span class="material-icons-round text-lg">arrow_back</span>
        <span class="text-sm font-medium">Back to Users</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- User Profile Card -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 text-center">
            <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center text-primary text-3xl font-bold mx-auto mb-4">
                {{ strtoupper(substr($user->username ?? 'U', 0, 1)) }}
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ $user->full_name ?? $user->username }}</h3>
            <p class="text-sm text-slate-500">{{ $user->email }}</p>
            <div class="mt-4">
                @if($user->active)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span>
                    Active
                </span>
                @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 dark:bg-red-900/30 text-red-600">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                    Blocked
                </span>
                @endif
            </div>
            
            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($user->scans_count ?? 0) }}</p>
                        <p class="text-xs text-slate-400">Total Scans</p>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ $user->role ?? 'user' }}</p>
                        <p class="text-xs text-slate-400">Role</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 text-left">
                <p class="text-xs text-slate-400 mb-2">Member Since</p>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ \Carbon\Carbon::parse($user->created_at)->format('F d, Y') }}</p>
                
                @if($user->last_login)
                <p class="text-xs text-slate-400 mt-4 mb-2">Last Login</p>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ \Carbon\Carbon::parse($user->last_login)->diffForHumans() }}</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Edit Form -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <form action="{{ route('admin.user.update', $user->id_user) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Account Information</h3>
                    <p class="text-sm text-slate-500 mt-1">Update user details and permissions.</p>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Full Name</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Enter full name">
                        </div>
                        
                        <!-- Username -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Enter username">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Enter email">
                        </div>
                        
                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Enter phone number">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Role</label>
                            <select name="role" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Account Status</label>
                            <select name="active" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="1" {{ old('active', $user->active) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('active', $user->active) == 0 ? 'selected' : '' }}>Blocked</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                    <form action="{{ route('admin.user.destroy', $user->id_user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all text-sm font-medium flex items-center space-x-1">
                            <span class="material-icons-round text-lg">delete</span>
                            <span>Delete User</span>
                        </button>
                    </form>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.user.index') }}" class="px-6 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-all text-sm font-medium">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all text-sm font-bold flex items-center space-x-2">
                            <span class="material-icons-round text-lg">save</span>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Recent Scans -->
        @if(isset($userScans) && count($userScans) > 0)
        <div class="mt-6 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Recent Scans</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/30 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($userScans as $scan)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-3 text-sm text-slate-800 dark:text-white">{{ $scan->nama_produk }}</td>
                            <td class="px-6 py-3">
                                @if($scan->status_halal == 'halal')
                                <span class="text-xs font-bold text-emerald-600">Halal</span>
                                @elseif($scan->status_halal == 'syubhat')
                                <span class="text-xs font-bold text-amber-600">Syubhat</span>
                                @else
                                <span class="text-xs font-bold text-red-600">Haram</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-xs text-slate-400">{{ \Carbon\Carbon::parse($scan->tanggal_scan)->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection