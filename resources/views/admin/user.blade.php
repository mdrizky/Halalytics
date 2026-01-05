@extends('master')
@section('isi')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Halalytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2E8B57;
            --primary-light: #3A9D66;
            --primary-dark: #1A5632;
            --accent-color: #4CAF50;
            --bg-dark: #121212;
            --bg-card: #1E1E1E;
            --bg-hover: #2A2A2A;
            --text-light: #E0E0E0;
            --text-muted: #A0A0A0;
            --border-color: #333333;
            --card-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            --glow-effect: 0 0 10px rgba(46, 139, 87, 0.3);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            min-height: 100vh;
        }
        
        .page-container {
            padding: 20px;
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            background-color: var(--bg-card);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow), var(--glow-effect);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 1.25rem 1.5rem;
            border: none;
            border-radius: 15px 15px 0 0 !important;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .card-header:hover::before {
            left: 100%;
        }
        
        .card-header h5 {
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .user-count {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(5px);
        }
        
        /* Search and Filter Styles */
        .search-container {
            position: relative;
            max-width: 450px;
            width: 100%;
        }
        
        .search-input {
            padding-left: 45px;
            border-radius: 50px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(0, 0, 0, 0.3);
            color: white;
            transition: all 0.3s ease;
            height: 42px;
            font-size: 0.95rem;
            width: 100%;
            backdrop-filter: blur(10px);
        }
        
        .search-input:focus {
            background: rgba(0, 0, 0, 0.4);
            box-shadow: 0 0 0 3px rgba(46, 139, 87, 0.3);
            border-color: var(--primary-color);
        }
        
        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            z-index: 5;
        }
        
        .filter-btn {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            color: white;
            padding: 0.5rem;
            transition: all 0.3s ease;
            height: 42px;
            width: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .filter-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }
        
        /* Table Styles */
        .table-container {
            overflow: hidden;
            border-radius: 0 0 15px 15px;
        }
        
        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            color: var(--text-light);
            width: 100%;
        }
        
        .table thead th {
            background: linear-gradient(to bottom, #2A2A2A, #242424);
            color: var(--primary-light);
            padding: 1rem 1.2rem;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .table tbody td {
            padding: 1rem 1.2rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.2s;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background-color: var(--bg-hover);
            transform: scale(1.01);
        }
        
        /* Badge Styles */
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.75rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .badge-admin {
            background: linear-gradient(135deg, #2E8B57, #1A5632);
            color: white;
        }
        
        .badge-user {
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            color: white;
        }
        
        .badge-active {
            background: linear-gradient(135deg, #38b000, #2a8500);
            color: white;
        }
        
        .badge-inactive {
            background: linear-gradient(135deg, #e63946, #c1121f);
            color: white;
        }
        
        /* Button Styles */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            margin: 0.1rem;
        }
        
        .btn i {
            margin-right: 0.3rem;
        }
        
        .btn-outline-danger {
            background-color: transparent;
            color: #e63946;
            border: 1px solid #e63946;
        }
        
        .btn-outline-danger:hover {
            background-color: #e63946;
            color: white;
            box-shadow: 0 0 10px rgba(230, 57, 70, 0.5);
        }
        
        .btn-outline-success {
            background-color: transparent;
            color: #38b000;
            border: 1px solid #38b000;
        }
        
        .btn-outline-success:hover {
            background-color: #38b000;
            color: white;
            box-shadow: 0 0 10px rgba(56, 176, 0, 0.5);
        }
        
        .btn-outline-warning {
            background-color: transparent;
            color: #f8961e;
            border: 1px solid #f8961e;
        }
        
        .btn-outline-warning:hover {
            background-color: #f8961e;
            color: white;
            box-shadow: 0 0 10px rgba(248, 150, 30, 0.5);
        }
        
        .btn-outline-primary {
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 0 10px rgba(46, 139, 87, 0.5);
        }
        
        /* Empty State */
        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
            color: var(--text-muted);
        }
        
        .empty-state i {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
            opacity: 0.5;
        }
        
        .empty-state h5 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.3rem;
        }
        
        .stats-cell {
            font-weight: 600;
            color: var(--primary-light);
        }
        
        .user-id-cell {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: var(--primary-light);
            font-size: 0.85rem;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--bg-dark);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-light);
        }
        
        /* Header Layout */
        .header-grid {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 1.5rem;
            width: 100%;
            position: relative;
            z-index: 2;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .header-center {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.8rem;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            .table-container {
                overflow-x: auto;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-sm {
                width: 100%;
                margin: 0.1rem 0;
            }
        }
        
        @media (max-width: 992px) {
            .header-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .header-left, .header-center, .header-right {
                justify-content: center;
            }
            
            .search-container {
                max-width: 100%;
            }
            
            .header-left {
                order: 1;
            }
            
            .header-center {
                order: 3;
                width: 100%;
            }
            
            .header-right {
                order: 2;
            }
        }
        
        @media (max-width: 768px) {
            .page-container {
                padding: 15px;
            }
            
            .table thead {
                display: none;
            }
            
            .table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: 10px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
                background-color: var(--bg-card);
            }
            
            .table tbody td {
                display: block;
                text-align: right;
                padding: 0.75rem;
                position: relative;
                border-bottom: 1px solid var(--border-color);
            }
            
            .table tbody td:before {
                content: attr(data-label);
                position: absolute;
                left: 0.75rem;
                top: 50%;
                transform: translateY(-50%);
                font-weight: 600;
                color: var(--primary-light);
                text-transform: uppercase;
                font-size: 0.75rem;
            }
            
            .table tbody td:last-child {
                border-bottom: none;
            }
            
            .action-buttons {
                flex-direction: row;
                justify-content: flex-end;
            }
        }
        
        /* Animation for table rows */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .table tbody tr {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .table tbody tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.02);
        }
        
        .table tbody tr:nth-child(even):hover {
            background-color: var(--bg-hover);
        }

        /* Enhanced Search Results */
        .search-highlight {
            background-color: rgba(46, 139, 87, 0.3);
            padding: 2px 4px;
            border-radius: 4px;
            font-weight: 600;
        }

        /* Filter Dropdown */
        .filter-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1rem;
            min-width: 200px;
            box-shadow: var(--card-shadow);
            z-index: 1000;
            display: none;
        }

        .filter-dropdown.show {
            display: block;
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .filter-option:hover {
            background-color: var(--bg-hover);
        }

        .filter-option input {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="card">
            <div class="card-header">
                <div class="header-grid">
                    <div class="header-left">
                        <h5 class="card-title"><i class="fas fa-users"></i> Manajemen Pengguna</h5>
                        <span class="user-count" id="userCount">{{ count($users) }} Pengguna</span>
                    </div>
                    
                    <div class="header-center">
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control search-input" placeholder="Cari pengguna berdasarkan username, email, role, atau status..." id="searchInput">
                        </div>
                        <div class="filter-wrapper position-relative">
                            <button class="btn filter-btn" title="Filter" id="filterBtn">
                                <i class="fas fa-sliders-h"></i>
                            </button>
                            <div class="filter-dropdown" id="filterDropdown">
                                <h6 class="mb-3">Filter Pengguna</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Role</label>
                                    <div class="filter-option" onclick="toggleFilter('role', 'all')">
                                        <input type="radio" name="roleFilter" value="all" checked> 
                                        <span>Semua Role</span>
                                    </div>
                                    <div class="filter-option" onclick="toggleFilter('role', 'admin')">
                                        <input type="radio" name="roleFilter" value="admin"> 
                                        <span>Admin</span>
                                    </div>
                                    <div class="filter-option" onclick="toggleFilter('role', 'user')">
                                        <input type="radio" name="roleFilter" value="user"> 
                                        <span>User</span>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Status</label>
                                    <div class="filter-option" onclick="toggleFilter('status', 'all')">
                                        <input type="radio" name="statusFilter" value="all" checked> 
                                        <span>Semua Status</span>
                                    </div>
                                    <div class="filter-option" onclick="toggleFilter('status', 'active')">
                                        <input type="radio" name="statusFilter" value="active"> 
                                        <span>Aktif</span>
                                    </div>
                                    <div class="filter-option" onclick="toggleFilter('status', 'inactive')">
                                        <input type="radio" name="statusFilter" value="inactive"> 
                                        <span>Non-Aktif</span>
                                    </div>
                                </div>
                                
                                <button class="btn btn-outline-primary w-100 mt-2" onclick="applyFilters()">
                                    <i class="fas fa-check"></i> Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Header Right kosong karena tombol Tambah User dihapus -->
                    <div class="header-right">
                        <!-- Tombol Tambah User dihapus karena route tidak ada -->
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-container">
                    <table class="table table-hover" id="usersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Tanggal Daftar</th>
                                <th>Last Login</th>
                                <th>Jumlah Scan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            @forelse($users as $u)
                                <tr>
                                    <td data-label="ID" class="user-id-cell">#{{ $u->id_user }}</td>
                                    <td data-label="Username">{{ $u->username }}</td>
                                    <td data-label="Email">{{ $u->email }}</td>
                                    <td data-label="Role">
                                        <span class="badge {{ $u->role == 'admin' ? 'badge-admin' : 'badge-user' }}">
                                            <i class="fas {{ $u->role == 'admin' ? 'fa-crown' : 'fa-user' }} me-1"></i>{{ ucfirst($u->role) }}
                                        </span>
                                    </td>
                                    <td data-label="Status">
                                        @if($u->active == 1)
                                            <span class="badge badge-active"><i class="fas fa-check-circle me-1"></i>Active</span>
                                        @else
                                            <span class="badge badge-inactive"><i class="fas fa-times-circle me-1"></i>Non Active</span>
                                        @endif
                                    </td>
                                    <td data-label="Tanggal Daftar">{{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}</td>
                                    <td data-label="Last Login">{{ $u->last_login ? \Carbon\Carbon::parse($u->last_login)->format('d M Y H:i') : '-' }}</td>
                                    <td data-label="Jumlah Scan" class="stats-cell"><i class="fas fa-search me-1"></i>{{ $u->scans_count ?? 0 }}</td>
                                    <td data-label="Aksi">
                                        <div class="action-buttons">
                                            {{-- Toggle --}}
                                            <form action="{{ route('admin.user_toggle', $u->id_user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                    class="btn btn-sm {{ $u->active == 1 ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                                    <i class="fas {{ $u->active == 1 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                                                    {{ $u->active == 1 ? 'Non Active' : 'Activate' }}
                                                </button>
                                            </form>

                                            {{-- Edit --}}
                                            <a href="{{ route('admin.user_edit', $u->id_user) }}" class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>

                                            {{-- Hapus --}}
                                            <form action="{{ route('admin.user_hapus', $u->id_user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Yakin hapus user ini?')">
                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-state">
                                            <i class="fas fa-users"></i>
                                            <h5>Belum ada data user</h5>
                                            <p>Silakan tambahkan user baru untuk mulai mengelola</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Current filters state
        let currentFilters = {
            role: 'all',
            status: 'all'
        };

        // Enhanced Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase().trim();
            filterAndSearchUsers(searchText, currentFilters);
        });

        // Filter dropdown toggle
        document.getElementById('filterBtn').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('filterDropdown');
            dropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('filterDropdown');
            const filterBtn = document.getElementById('filterBtn');
            if (!dropdown.contains(e.target) && !filterBtn.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        function toggleFilter(type, value) {
            // Update radio buttons
            document.querySelector(`input[name="${type}Filter"][value="${value}"]`).checked = true;
        }

        function applyFilters() {
            const roleFilter = document.querySelector('input[name="roleFilter"]:checked').value;
            const statusFilter = document.querySelector('input[name="statusFilter"]:checked').value;
            
            currentFilters = {
                role: roleFilter,
                status: statusFilter
            };

            const searchText = document.getElementById('searchInput').value.toLowerCase().trim();
            filterAndSearchUsers(searchText, currentFilters);
            
            // Close dropdown
            document.getElementById('filterDropdown').classList.remove('show');
        }

        function filterAndSearchUsers(searchText, filters) {
            const rows = document.querySelectorAll('#usersTable tbody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                if (row.querySelector('.empty-state')) {
                    return; // Skip empty state row
                }
                
                const username = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const role = row.cells[3].textContent.toLowerCase();
                const status = row.cells[4].textContent.toLowerCase();
                const joinDate = row.cells[5].textContent.toLowerCase();
                const scanCount = row.cells[7].textContent.toLowerCase();
                
                // Search matching
                const searchMatches = searchText === '' || 
                                    username.includes(searchText) || 
                                    email.includes(searchText) || 
                                    role.includes(searchText) ||
                                    status.includes(searchText) ||
                                    joinDate.includes(searchText) ||
                                    scanCount.includes(searchText);
                
                // Filter matching
                const roleMatches = filters.role === 'all' || 
                                  (filters.role === 'admin' && role.includes('admin')) ||
                                  (filters.role === 'user' && role.includes('user'));
                
                const statusMatches = filters.status === 'all' || 
                                    (filters.status === 'active' && status.includes('active')) ||
                                    (filters.status === 'inactive' && status.includes('non active'));
                
                const matches = searchMatches && roleMatches && statusMatches;
                
                if (matches) {
                    row.style.display = '';
                    visibleCount++;
                    
                    // Highlight matching text
                    highlightText(row, searchText);
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update user count based on search/filter results
            document.getElementById('userCount').textContent = `${visibleCount} Pengguna`;
            
            // Show empty state if no results
            showEmptyState(visibleCount === 0 && (searchText !== '' || filters.role !== 'all' || filters.status !== 'all'));
        }

        function highlightText(row, searchText) {
            if (!searchText) return;
            
            const cells = row.querySelectorAll('td');
            cells.forEach(cell => {
                // Skip action cells and cells with badges
                if (cell.querySelector('.badge') || cell.querySelector('.action-buttons')) {
                    return;
                }
                
                const originalText = cell.textContent;
                const regex = new RegExp(`(${escapeRegex(searchText)})`, 'gi');
                const highlightedText = originalText.replace(regex, '<span class="search-highlight">$1</span>');
                cell.innerHTML = highlightedText;
            });
        }

        function escapeRegex(text) {
            return text.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function showEmptyState(show) {
            let emptyRow = document.querySelector('#usersTable tbody tr .empty-state');
            
            if (show && !emptyRow) {
                const tbody = document.getElementById('usersTableBody');
                const hasOriginalData = {{ count($users) > 0 ? 'true' : 'false' }};
                
                if (hasOriginalData) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-search"></i>
                                    <h5>Pengguna tidak ditemukan</h5>
                                    <p>Coba ubah kata kunci pencarian atau atur filter yang berbeda</p>
                                    <button class="btn btn-outline-primary mt-2" onclick="clearSearchAndFilters()">
                                        <i class="fas fa-times"></i> Clear Pencarian
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            } else if (!show && emptyRow && {{ count($users) > 0 ? 'true' : 'false' }}) {
                // Reload the original content if search is cleared and we have original data
                window.location.reload();
            }
        }

        function clearSearchAndFilters() {
            // Clear search input
            document.getElementById('searchInput').value = '';
            
            // Reset filters
            document.querySelector('input[name="roleFilter"][value="all"]').checked = true;
            document.querySelector('input[name="statusFilter"][value="all"]').checked = true;
            
            currentFilters = { role: 'all', status: 'all' };
            
            // Refresh the display
            filterAndSearchUsers('', currentFilters);
        }

        // Add animation to table rows
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.table tbody tr');
            rows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.05}s`;
            });
        });

        // Clear search when pressing Escape
        document.getElementById('searchInput').addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                filterAndSearchUsers('', currentFilters);
            }
        });
    </script>
</body>
</html>
@endsection