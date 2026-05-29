@extends('admin.master')

@section('title', 'User Management | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Users</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">User Management</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Kelola akun pengguna, atur role, dan pantau aktivitas scan komunitas.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('admin.user.export') }}" class="btn btn-outline">
            <i class="fas fa-file-export"></i> Export CSV
        </a>
        <a href="{{ route('admin.user.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Add New User
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 32px;">
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Total Registrations</div>
            <div style="display: flex; align-items: baseline; gap: 12px;">
                <div style="font-size: 28px; font-weight: 800; color: var(--text-main);">{{ number_format($stats['total_users']) }}</div>
                <div style="font-size: 12px; color: var(--success); font-weight: 700;"><i class="fas fa-arrow-up"></i> {{ $stats['user_change'] }}%</div>
            </div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Active Accounts</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--primary-color);">{{ number_format($stats['active_users']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Community Scans</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--accent-color);">{{ number_format($stats['total_scans']) }}</div>
        </div>
    </div>
</div>

<!-- Role Segmentation -->
@php
    $roleTabs = [
        'all' => ['label' => 'Semua Role', 'icon' => 'users', 'count' => $stats['total_users'] ?? 0],
        'user' => ['label' => 'User Biasa', 'icon' => 'user', 'count' => $stats['total_regular_users'] ?? 0],
        'ahli_gizi' => ['label' => 'Ahli Gizi', 'icon' => 'user-md', 'count' => $stats['total_nutritionists'] ?? 0],
        'admin' => ['label' => 'Admin', 'icon' => 'shield-alt', 'count' => $stats['total_admins'] ?? 0],
    ];
    $activeRole = request('role', 'all');
@endphp
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body" style="padding: 12px; display: flex; gap: 10px; flex-wrap: wrap;">
        @foreach($roleTabs as $roleKey => $tab)
            @php
                $tabUrl = route('admin.user.index', array_filter(array_merge(request()->except(['page', 'role']), [
                    'role' => $roleKey === 'all' ? null : $roleKey,
                ]), fn ($value) => $value !== null && $value !== ''));
                $isActive = ($roleKey === 'all' && !$activeRole) || $activeRole === $roleKey;
            @endphp
            <a href="{{ $tabUrl }}"
               class="btn {{ $isActive ? 'btn-primary' : 'btn-outline' }}"
               style="padding: 10px 14px; border-radius: 999px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-{{ $tab['icon'] }}"></i>
                {{ $tab['label'] }}
                <span class="badge" style="background: {{ $isActive ? 'rgba(255,255,255,.2)' : 'rgba(0,0,0,.06)' }}; color: inherit; border: 1px solid currentColor;">
                    {{ number_format($tab['count']) }}
                </span>
            </a>
        @endforeach
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body">
        <form action="{{ url('/admin/user') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 16px; align-items: center;">
            @if(request('role'))
                <input type="hidden" name="role" value="{{ request('role') }}">
            @endif
            <div class="input-group" style="position: relative; margin-bottom: 0;">
                <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, or username..." style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
            </div>
            
            <select name="status" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="all">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
            </select>
            
            <select name="sort" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Sort by Joined Date</option>
                <option value="username" {{ request('sort') == 'username' ? 'selected' : '' }}>Sort by Username</option>
                <option value="scans_count" {{ request('sort') == 'scans_count' ? 'selected' : '' }}>Sort by Scan Count</option>
            </select>
            
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                <i class="fas fa-filter"></i> Apply
            </button>
        </form>
    </div>
</div>

<!-- User Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table style="border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="padding: 16px 24px;">User Information</th>
                        <th style="padding: 16px;">Contact</th>
                        <th style="padding: 16px;">Role</th>
                        <th style="padding: 16px;">Status</th>
                        <th style="padding: 16px; text-align: center;">Medical</th>
                        <th style="padding: 16px; text-align: center;">Scans</th>
                        <th style="padding: 16px;">Joined Date</th>
                        <th style="padding: 16px 24px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td style="padding: 16px 24px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--bg-light); display: flex; align-items: center; justify-content: center; border: 1px solid var(--border-color); overflow: hidden;">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=random&color=fff" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main);">{{ $user->full_name }}</div>
                                    <div style="font-size: 12px; color: var(--text-muted);">{{ '@' . $user->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-size: 13px; color: var(--text-main);">{{ $user->email }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">{{ $user->phone ?: 'No phone' }}</div>
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $roleKey = strtolower($user->role ?? 'user');
                                $roleMeta = match($roleKey) {
                                    'admin' => ['label' => 'ADMIN', 'color' => '#DC2626', 'bg' => 'rgba(220,38,38,.08)', 'border' => 'rgba(220,38,38,.24)'],
                                    'ahli_gizi', 'nutritionist', 'expert' => ['label' => 'AHLI GIZI', 'color' => '#059669', 'bg' => 'rgba(5,150,105,.08)', 'border' => 'rgba(5,150,105,.24)'],
                                    default => ['label' => 'USER', 'color' => 'var(--primary-color)', 'bg' => 'rgba(0,77,64,.08)', 'border' => 'rgba(0,77,64,.20)'],
                                };
                            @endphp
                            <span class="badge" style="background: {{ $roleMeta['bg'] }}; color: {{ $roleMeta['color'] }}; border: 1px solid {{ $roleMeta['border'] }};">
                                {{ $roleMeta['label'] }}
                            </span>
                        </td>
                        <td style="padding: 16px;">
                            @if($user->active)
                                <span style="display: flex; align-items: center; gap: 6px; color: var(--success); font-size: 12px; font-weight: 700;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--success);"></span> Active
                                </span>
                            @else
                                <span style="display: flex; align-items: center; gap: 6px; color: var(--danger); font-size: 12px; font-weight: 700;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--danger);"></span> Blocked
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px; text-align: center;">
                            @if($user->bmi)
                                <div style="font-weight: 700; color: {{ $user->bmi > 25 ? '#F59E0B' : 'var(--primary-color)' }}; font-size: 13px;">
                                    {{ $user->bmi }} <span style="font-size: 10px; font-weight: 500; color: var(--text-muted);">BMI</span>
                                </div>
                            @endif
                            @if($user->blood_type)
                                <span class="badge" style="background: rgba(231, 76, 60, 0.1); color: #E74C3C; border: 1px solid #E74C3C; font-size: 10px; margin-top: 4px;">
                                    Type {{ $user->blood_type }}
                                </span>
                            @endif
                            @if(!$user->bmi && !$user->blood_type)
                                <span style="color: var(--text-muted); font-size: 11px;">-</span>
                            @endif
                        </td>
                        <td style="padding: 16px; text-align: center;">
                            <div style="font-weight: 800; color: var(--primary-color);">{{ number_format(($user->scans_count ?? 0) + ($user->scan_histories_count ?? 0)) }}</div>
                        </td>
                        <td style="padding: 16px; font-size: 12px; color: var(--text-muted);">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td style="padding: 16px 24px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('admin.user.show', $user->id_user) }}" class="btn btn-outline" style="padding: 8px; color: var(--accent-color); border-color: var(--accent-color);" title="View Detail"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.user.edit', $user->id_user) }}" class="btn btn-outline" style="padding: 8px; color: var(--primary-color); border-color: var(--primary-color);"><i class="fas fa-user-edit"></i></a>
                                <form action="{{ route('admin.user.role', $user->id_user) }}" method="POST" style="display: inline-flex;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" title="Ganti Role"
                                            style="width: 104px; padding: 8px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); font-size: 11px; font-weight: 700;">
                                        <option value="user" {{ $roleKey === 'user' ? 'selected' : '' }}>User</option>
                                        <option value="ahli_gizi" {{ in_array($roleKey, ['ahli_gizi', 'nutritionist', 'expert']) ? 'selected' : '' }}>Ahli Gizi</option>
                                        <option value="admin" {{ $roleKey === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                                
                                <form action="{{ route('admin.user.toggle', $user->id_user) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: {{ $user->active ? 'var(--danger)' : 'var(--success)' }}; border-color: var(--border-color);" title="{{ $user->active ? 'Block User' : 'Unblock User' }}">
                                        <i class="fas fa-{{ $user->active ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>
                                
                                @if($user->role != 'admin')
                                <form action="{{ route('admin.user.destroy', $user->id_user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini selamanya?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--danger); border-color: var(--danger);"><i class="fas fa-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 48px; color: var(--text-muted);">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer" style="padding: 16px 24px;">
        {{ $users->links() }}
    </div>
</div>

@endsection
