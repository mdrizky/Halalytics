@extends('admin.master')

@section('title', 'Manajemen Pengguna - Halalytics Admin')

@section('isi')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3 text-white">
        <h1 class="h3 mb-0 fw-bold">Manajemen Pengguna</h1>
        <a href="{{ route('admin.user.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-user-plus me-1"></i> Tambah User
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-25 p-3 me-3">
                        <i class="fas fa-users text-primary fs-3"></i>
                    </div>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold">Total Pengguna</div>
                        <div class="h3 mb-0 fw-bold text-white">{{ number_format($totalUsers) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-25 p-3 me-3">
                        <i class="fas fa-user-check text-success fs-3"></i>
                    </div>
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold">User Aktif</div>
                        <div class="h3 mb-0 fw-bold text-white">{{ number_format($activeUsers) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="glass-card p-0 overflow-hidden">
        <div class="p-4 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-white">Daftar Pengguna</h6>
                <div class="input-group w-25">
                    <span class="input-group-text bg-dark border-0 text-white-50"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control bg-dark border-0 text-white" placeholder="Cari user...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-glass mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-white bg-opacity-10 d-flex justify-content-center align-items-center me-3 text-white fw-bold" style="width: 36px; height: 36px">
                                        {{ strtoupper(substr($user->username, 0, 1)) }}
                                    </div>
                                    <strong class="text-white">{{ $user->username }}</strong>
                                </div>
                            </td>
                            <td class="text-white-50">{{ $user->full_name ?? '-' }}</td>
                            <td class="text-white-50">{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'admin')
                                <span class="badge bg-primary bg-opacity-25 text-primary rounded-pill px-3">Admin</span>
                                @else
                                <span class="badge bg-secondary bg-opacity-25 text-secondary rounded-pill px-3">User</span>
                                @endif
                            </td>
                            <td>
                                @if($user->active)
                                <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-3">
                                    <i class="fas fa-check-circle me-1"></i> Aktif
                                </span>
                                @else
                                <span class="badge bg-danger bg-opacity-25 text-danger rounded-pill px-3">
                                    <i class="fas fa-ban me-1"></i> Blocked
                                </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-white-50" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow">
                                        <li><a class="dropdown-item" href="{{ route('admin.user.edit', $user->id_user) }}"><i class="fas fa-edit me-2"></i> Edit</a></li>
                                        <li><button class="dropdown-item" onclick="toggleStatus({{ $user->id_user }})"><i class="fas fa-ban me-2"></i> Toggle Status</button></li>
                                        <li><hr class="dropdown-divider border-secondary"></li>
                                        <li>
                                            <form action="{{ route('admin.user.destroy', $user->id_user) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i> Hapus</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $users->links() }}
        </div>
    </div>
</div>

<script>
function toggleStatus(id) {
    fetch(`/admin/users/${id}/toggle`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => location.reload());
}
</script>
@endsection