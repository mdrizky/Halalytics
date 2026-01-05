@extends('master')
@section('isi')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kategori Products - Halalytics</title>
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
            margin: 0;
            padding: 0;
        }
        
        .page-container {
            padding: 20px;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            background-color: var(--bg-card);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            flex: 1;
            display: flex;
            flex-direction: column;
            max-height: calc(100vh - 40px);
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
            flex-shrink: 0;
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
        
        .category-count {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(5px);
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
        
        .btn-add {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            transition: all 0.3s ease;
            height: 42px;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.95rem;
            font-weight: 600;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(46, 139, 87, 0.3);
        }
        
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 139, 87, 0.4);
            background: linear-gradient(135deg, #3CA56D, #1E4A32);
        }
        
        /* Table Container with Fixed Height */
        .table-container {
            overflow: hidden;
            flex: 1;
            display: flex;
            flex-direction: column;
            border-radius: 0 0 15px 15px;
        }
        
        .table-wrapper {
            overflow-y: auto;
            flex: 1;
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
        
        .badge-slug {
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            color: white;
        }
        
        .badge-new {
            background: linear-gradient(135deg, #38b000, #2a8500);
            color: white;
        }
        
        .badge-old {
            background: linear-gradient(135deg, #f8961e, #e07b00);
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
        }
        
        .btn i {
            margin-right: 0.3rem;
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
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.4rem;
        }
        
        /* Category Icon */
        .category-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            box-shadow: 0 2px 8px rgba(46, 139, 87, 0.3);
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
        
        /* Footer */
        .card-footer {
            background: linear-gradient(to bottom, #2A2A2A, #242424);
            color: var(--primary-light);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        
        /* Custom Scrollbar */
        .table-wrapper::-webkit-scrollbar {
            width: 8px;
        }
        
        .table-wrapper::-webkit-scrollbar-track {
            background: var(--bg-dark);
            border-radius: 4px;
        }
        
        .table-wrapper::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }
        
        .table-wrapper::-webkit-scrollbar-thumb:hover {
            background: var(--primary-light);
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
        
        /* Responsive Adjustments */
        @media (max-width: 1200px) {
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
                height: auto;
            }
            
            .card {
                max-height: none;
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
    </style>
</head>
<body>
    <div class="page-container">
        <div class="card">
            <div class="card-header">
                <div class="header-grid">
                    <div class="header-left">
                        <h5 class="card-title"><i class="fas fa-layer-group"></i> Data Kategori Products</h5>
                        <span class="category-count" id="categoryCount">{{ count($kategori) }} Kategori</span>
                    </div>
                    
                    <div class="header-center">
                        <div class="search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control search-input" placeholder="Cari kategori berdasarkan nama atau slug..." id="searchInput">
                        </div>
                        <div class="filter-wrapper position-relative">
                            <button class="btn filter-btn" title="Filter" id="filterBtn">
                                <i class="fas fa-sliders-h"></i>
                            </button>
                            <div class="filter-dropdown" id="filterDropdown">
                                <h6 class="mb-3">Filter Kategori</h6>
                                
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Urutkan Berdasarkan</label>
                                    <div class="filter-option" onclick="toggleFilter('sort', 'newest')">
                                        <input type="radio" name="sortFilter" value="newest" checked> 
                                        <span>Terbaru</span>
                                    </div>
                                    <div class="filter-option" onclick="toggleFilter('sort', 'oldest')">
                                        <input type="radio" name="sortFilter" value="oldest"> 
                                        <span>Terlama</span>
                                    </div>
                                    <div class="filter-option" onclick="toggleFilter('sort', 'name')">
                                        <input type="radio" name="sortFilter" value="name"> 
                                        <span>Nama A-Z</span>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Status</label>
                                    <div class="filter-option" onclick="toggleFilter('status', 'all')">
                                        <input type="radio" name="statusFilter" value="all" checked> 
                                        <span>Semua Kategori</span>
                                    </div>
                                    <div class="filter-option" onclick="toggleFilter('status', 'recent')">
                                        <input type="radio" name="statusFilter" value="recent"> 
                                        <span>Baru Ditambahkan</span>
                                    </div>
                                </div>
                                
                                <button class="btn btn-outline-primary w-100 mt-2" onclick="applyFilters()">
                                    <i class="fas fa-check"></i> Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="header-right">
                        <a href="#" class="btn btn-add">
                            <i class="fas fa-plus-circle"></i> Tambah Kategori
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="table table-hover" id="categoriesTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategori</th>
                                    <th>Slug</th>
                                    <th>Tanggal Dibuat</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="categoriesTableBody">
                                @forelse ($kategori as $index => $row)
                                <tr>
                                    <td data-label="No">{{ $index + 1 }}</td>
                                    <td data-label="Nama Kategori">
                                        <div class="d-flex align-items-center">
                                            <div class="category-icon">
                                                <i class="fas fa-tag"></i>
                                            </div>
                                            <div class="ms-3">
                                                <div class="fw-semibold">{{ $row->nama_kategori }}</div>
                                                @if($row->created_at->diffInDays(now()) <= 7)
                                                    <span class="badge badge-new">Baru</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Slug"><span class="badge badge-slug">{{ \Str::slug($row->nama_kategori, '-') }}</span></td>
                                    <td data-label="Tanggal Dibuat">
                                        <div class="text-muted small">
                                            <div>{{ $row->created_at->format('d M Y') }}</div>
                                            <div>{{ $row->created_at->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td data-label="Aksi">
                                        <div class="d-flex justify-content-center gap-2 action-buttons">
                                            <a href="#" class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="#" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>   
                                @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="fas fa-layer-group"></i>
                                            <h5 class="mt-2">Belum ada data kategori</h5>
                                            <p class="text-muted">Tambahkan kategori pertama Anda untuk mulai mengatur produk</p>
                                            <a href="#" class="btn btn-add mt-2">
                                                <i class="fas fa-plus-circle me-1"></i> Tambah Kategori
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                <div class="text-center" id="footerCount">
                    Total: <strong>{{ count($kategori) }}</strong> kategori
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Current filters state
        let currentFilters = {
            sort: 'newest',
            status: 'all'
        };

        // Enhanced Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase().trim();
            filterAndSearchCategories(searchText, currentFilters);
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
            const sortFilter = document.querySelector('input[name="sortFilter"]:checked').value;
            const statusFilter = document.querySelector('input[name="statusFilter"]:checked').value;
            
            currentFilters = {
                sort: sortFilter,
                status: statusFilter
            };

            const searchText = document.getElementById('searchInput').value.toLowerCase().trim();
            filterAndSearchCategories(searchText, currentFilters);
            
            // Close dropdown
            document.getElementById('filterDropdown').classList.remove('show');
        }

        function filterAndSearchCategories(searchText, filters) {
            const rows = document.querySelectorAll('#categoriesTable tbody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                if (row.querySelector('.empty-state')) {
                    return; // Skip empty state row
                }
                
                const categoryName = row.cells[1].textContent.toLowerCase();
                const slug = row.cells[2].textContent.toLowerCase();
                const dateText = row.cells[3].textContent.toLowerCase();
                const isNew = row.querySelector('.badge-new');
                
                // Search matching
                const searchMatches = searchText === '' || 
                                    categoryName.includes(searchText) || 
                                    slug.includes(searchText) ||
                                    dateText.includes(searchText);
                
                // Filter matching
                const statusMatches = filters.status === 'all' || 
                                    (filters.status === 'recent' && isNew);
                
                const matches = searchMatches && statusMatches;
                
                if (matches) {
                    row.style.display = '';
                    visibleCount++;
                    
                    // Highlight matching text
                    highlightText(row, searchText);
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Sort rows based on filter
            sortCategories(filters.sort);
            
            // Update category count based on search/filter results
            document.getElementById('categoryCount').textContent = `${visibleCount} Kategori`;
            document.getElementById('footerCount').innerHTML = `Total: <strong>${visibleCount}</strong> kategori`;
            
            // Show empty state if no results
            showEmptyState(visibleCount === 0 && (searchText !== '' || filters.status !== 'all'));
        }

        function sortCategories(sortType) {
            const tbody = document.getElementById('categoriesTableBody');
            const rows = Array.from(tbody.querySelectorAll('tr:not([style*="display: none"])'));
            
            if (rows.length === 0) return;
            
            rows.sort((a, b) => {
                const nameA = a.cells[1].textContent.toLowerCase();
                const nameB = b.cells[1].textContent.toLowerCase();
                const dateA = new Date(a.cells[3].textContent);
                const dateB = new Date(b.cells[3].textContent);
                
                switch(sortType) {
                    case 'newest':
                        return dateB - dateA;
                    case 'oldest':
                        return dateA - dateB;
                    case 'name':
                        return nameA.localeCompare(nameB);
                    default:
                        return 0;
                }
            });
            
            // Reorder rows in DOM
            rows.forEach(row => tbody.appendChild(row));
            
            // Update row numbers
            updateRowNumbers();
        }

        function updateRowNumbers() {
            const rows = document.querySelectorAll('#categoriesTable tbody tr:not([style*="display: none"])');
            rows.forEach((row, index) => {
                row.cells[0].textContent = index + 1;
            });
        }

        function highlightText(row, searchText) {
            if (!searchText) return;
            
            const cells = row.querySelectorAll('td');
            cells.forEach(cell => {
                // Skip action cells and cells with badges/avatars
                if (cell.querySelector('.badge') || cell.querySelector('.action-buttons') || cell.querySelector('.category-icon')) {
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
            let emptyRow = document.querySelector('#categoriesTable tbody tr .empty-state');
            
            if (show && !emptyRow) {
                const tbody = document.getElementById('categoriesTableBody');
                const hasOriginalData = {{ count($kategori) > 0 ? 'true' : 'false' }};
                
                if (hasOriginalData) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-search"></i>
                                    <h5>Kategori tidak ditemukan</h5>
                                    <p>Coba ubah kata kunci pencarian atau atur filter yang berbeda</p>
                                    <button class="btn btn-outline-primary mt-2" onclick="clearSearchAndFilters()">
                                        <i class="fas fa-times"></i> Clear Pencarian
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            } else if (!show && emptyRow && {{ count($kategori) > 0 ? 'true' : 'false' }}) {
                // Reload the original content if search is cleared and we have original data
                window.location.reload();
            }
        }

        function clearSearchAndFilters() {
            // Clear search input
            document.getElementById('searchInput').value = '';
            
            // Reset filters
            document.querySelector('input[name="sortFilter"][value="newest"]').checked = true;
            document.querySelector('input[name="statusFilter"][value="all"]').checked = true;
            
            currentFilters = { 
                sort: 'newest', 
                status: 'all' 
            };
            
            // Refresh the display
            filterAndSearchCategories('', currentFilters);
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
                filterAndSearchCategories('', currentFilters);
            }
        });

        // Auto-close dropdown on window resize
        window.addEventListener('resize', function() {
            document.getElementById('filterDropdown').classList.remove('show');
        });
    </script>
</body>
</html>
@endsection