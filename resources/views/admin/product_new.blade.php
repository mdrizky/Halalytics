@extends('master')

@section('title', 'Data Products')

@section('isi')
<div class="dashboard-aurora">
    <div class="aurora-blobs">
        <span class="blob blob-1"></span>
        <span class="blob blob-2"></span>
        <span class="blob blob-3"></span>
    </div>

    <div class="container-fluid p-0">
        <!-- Header -->
        <div class="dashboard-header glass-panel">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <p class="eyebrow">Product Management</p>
                    <h1 class="hero-title">Product Database</h1>
                    <p class="hero-subtitle">
                        Kelola database produk halal dengan interface modern dan responsif.
                    </p>

                    <div class="chip-row">
                        <div class="chip">
                            <span>Total Products</span>
                            <strong>{{ $products->count() ?? 0 }}</strong>
                        </div>
                        <div class="chip">
                            <span>Halal</span>
                            <strong>{{ $products->where('status', 'halal')->count() ?? 0 }}</strong>
                        </div>
                        <div class="chip">
                            <span>Non-Halal</span>
                            <strong>{{ $products->where('status', '!=', 'halal')->count() ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-end">
                    <a href="{{ route('admin.product.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Tambah Product
                    </a>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="glass-panel mb-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari produk...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="halal">Halal</option>
                        <option value="tidak halal">Tidak Halal</option>
                        <option value="syubhat">Syubhat</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="categoryFilter">
                        <option value="">Semua Kategori</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id_kategori }}">{{ $category->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="glass-panel">
            <div class="table-responsive">
                <table class="table table-hover" id="productsTable">
                    <thead>
                        <tr>
                            <th>Barcode</th>
                            <th>Nama Product</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($products->count() > 0)
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <code class="barcode-code">{{ $product->barcode }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $product->nama_product }}</strong>
                                        @if($product->komposisi)
                                            <br><small class="text-muted">{{ Str::limit($product->komposisi, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->kategori_id)
                                            <span class="badge bg-info">{{ $product->kategori->nama_kategori ?? 'N/A' }}</span>
                                        @else
                                            <span class="badge bg-secondary">No Category</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->status == 'halal')
                                            <span class="badge bg-success">Halal</span>
                                        @elseif($product->status == 'tidak halal')
                                            <span class="badge bg-danger">Tidak Halal</span>
                                        @else
                                            <span class="badge bg-warning">Diragukan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.product.edit', $product->id_product) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.product.destroy', $product->id_product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="empty-state">
                                        <i class="fas fa-box-open fa-3x mb-3"></i>
                                        <h5>Belum ada produk</h5>
                                        <p>Tambahkan produk pertama untuk memulai.</p>
                                        <a href="{{ route('admin.product.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i> Tambah Product
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

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

.dashboard-aurora {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    z-index: -1;
    overflow: hidden;
}

.aurora-blobs {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(40px);
    opacity: 0.4;
    animation: float 20s infinite ease-in-out;
}

.blob-1 {
    width: 300px;
    height: 300px;
    background: linear-gradient(45deg, #2E8B57, #4CAF50);
    top: -150px;
    left: -150px;
    animation-delay: 0s;
}

.blob-2 {
    width: 200px;
    height: 200px;
    background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
    top: -100px;
    right: -100px;
    animation-delay: 5s;
}

.blob-3 {
    width: 250px;
    height: 250px;
    background: linear-gradient(45deg, #4ECDC4, #45B7D1);
    bottom: -150px;
    left: 50%;
    animation-delay: 10s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-20px) rotate(180deg);
    }
}

.dashboard-header {
    background: var(--bg-card);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid var(--border-color);
    box-shadow: var(--card-shadow);
    position: relative;
    overflow: hidden;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--primary-color), var(--accent-color), var(--primary-color));
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

.eyebrow {
    color: var(--accent-color);
    font-size: 0.875rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-light);
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    color: var(--text-muted);
    font-size: 1.1rem;
    line-height: 1.6;
}

.chip-row {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.chip {
    background: var(--bg-hover);
    border-radius: 25px;
    padding: 0.5rem 1rem;
    display: flex;
    align-items: center;
    border: 1px solid var(--border-color);
}

.chip strong {
    color: var(--primary-color);
    font-weight: 600;
}

.glass-panel {
    background: var(--bg-card);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid var(--border-color);
    box-shadow: var(--card-shadow);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.table {
    background: transparent !important;
    color: var(--text-light) !important;
}

.table th {
    background: var(--bg-hover) !important;
    color: var(--primary-color) !important;
    border: none !important;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.875rem;
    letter-spacing: 0.05em;
}

.table td {
    border: 1px solid var(--border-color) !important;
    vertical-align: middle;
}

.table-hover tbody tr:hover {
    background: var(--bg-hover) !important;
}

.barcode-code {
    background: var(--bg-dark);
    color: var(--accent-color);
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
}

.badge {
    padding: 0.35rem 0.65rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.bg-success {
    background: var(--accent-color) !important;
    color: var(--bg-dark) !important;
}

.bg-danger {
    background: #DC3545 !important;
    color: white !important;
}

.bg-warning {
    background: #FFC107 !important;
    color: var(--bg-dark) !important;
}

.bg-info {
    background: #17A2B8 !important;
    color: white !important;
}

.bg-secondary {
    background: #6C757D !important;
    color: white !important;
}

.btn {
    border-radius: 10px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    transition: all 0.3s ease;
    border: none;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(46, 139, 87, 0.3);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
}

.btn-outline-primary {
    background: transparent;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}

.btn-outline-primary:hover {
    background: var(--primary-color);
    color: white;
}

.btn-outline-danger {
    background: transparent;
    color: #DC3545;
    border: 2px solid #DC3545;
}

.btn-outline-danger:hover {
    background: #DC3545;
    color: white;
}

.empty-state {
    padding: 3rem;
    text-align: center;
    color: var(--text-muted);
}

.empty-state i {
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.text-muted {
    color: var(--text-muted) !important;
}

.input-group {
    position: relative;
}

.input-group-text {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    z-index: 10;
}

.form-control, .form-select {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    color: var(--text-light);
    border-radius: 10px;
    padding: 0.75rem 1rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 5px rgba(46, 139, 87, 0.2);
    outline: none;
}

.form-control::placeholder {
    color: var(--text-muted);
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}

.pagination .page-link {
    background: var(--bg-card);
    color: var(--text-light);
    border: 1px solid var(--border-color);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background: var(--primary-color);
    color: white;
}

.pagination .page-item.active .page-link {
    background: var(--primary-color);
    color: white;
}
</style>

<script>
// Search functionality
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchValue = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#productsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});

// Filter functionality
document.getElementById('statusFilter')?.addEventListener('change', function(e) {
    const statusValue = e.target.value;
    const rows = document.querySelectorAll('#productsTable tbody tr');
    
    rows.forEach(row => {
        if (statusValue === '') {
            row.style.display = '';
        } else {
            const statusBadge = row.querySelector('.badge');
            if (statusBadge) {
                const statusText = statusBadge.textContent.toLowerCase();
                row.style.display = statusText.includes(statusValue) ? '' : 'none';
            }
        }
    });
});

// Real-time updates
setInterval(() => {
    fetch('/admin/api/realtime-stats')
        .then(response => response.json())
        .then(data => {
            if (data.response_code === 200) {
                // Update statistics
                const chips = document.querySelectorAll('.chip strong');
                if (chips[0]) chips[0].textContent = data.data.total_scans_today || 0;
                if (chips[1]) chips[1].textContent = data.data.halal_stats?.halal || 0;
                if (chips[2]) chips[2].textContent = (data.data.halal_stats?.haram || 0) + (data.data.halal_stats?.syubhat || 0);
            }
        })
        .catch(error => console.error('Error updating stats:', error));
}, 5000); // Update every 5 seconds
</script>
@endsection
