@extends('admin.master')

@section('title', 'Data Kategori')

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
                    <p class="eyebrow">Category Management</p>
                    <h1 class="hero-title">Product Categories</h1>
                    <p class="hero-subtitle">
                        Kelola kategori produk halal dengan interface yang intuitif.
                    </p>

                    <div class="chip-row">
                        <div class="chip">
                            <span>Total Kategori</span>
                            <strong>{{ count($kategori) }}</strong>
                        </div>
                        <div class="chip">
                            <span>Aktif</span>
                            <strong>{{ count($kategori) }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus me-2"></i> Tambah Kategori
                    </button>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="glass-panel">
            @if(count($kategori) > 0)
                <div class="row">
                    @foreach($kategori as $kat)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="category-card">
                                <div class="category-header">
                                    <div class="category-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <div class="category-info">
                                        <h5>{{ $kat->nama_kategori }}</h5>
                                        <span class="badge bg-success">
                                            Aktif
                                        </span>
                                    </div>
                                </div>
                                </div>
                                <div class="category-body">
                                    <p class="category-description">
                                        {{ $kat->deskripsi ?? 'Kategori produk halal' }}
                                    </p>
                                    <div class="category-stats">
                                        <small class="text-muted">
                                            <i class="fas fa-box me-1"></i>
                                            {{ \App\Models\ProductModel::where('kategori_id', $kat->id_kategori)->count() }} produk
                                        </small>
                                    </div>
                                </div>
                                <div class="category-footer">
                                    <div class="btn-group" role="group">
                                        <button type="button" onclick="editCategory({{ $kat->id_kategori }}, '{{ $kat->nama_kategori }}', '{{ addslashes($kat->deskripsi) }}')" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.kategori.destroy', $kat->id_kategori) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-tags fa-3x mb-3"></i>
                    <h5>Belum ada kategori</h5>
                    <p>Tambahkan kategori pertama untuk memulai.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus me-2"></i> Tambah Kategori Pertama
                    </button>
                </div>
            @endif
        </div>

        <!-- Add Category Modal -->
        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">
                            <i class="fas fa-tag me-2"></i> Tambah Kategori Baru
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.kategori.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama_kategori" class="form-label">Nama Kategori</label>
                                <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi kategori..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Simpan Kategori
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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

.category-card {
    background: var(--bg-card);
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(46, 139, 87, 0.2);
}

.category-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.category-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-info h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.category-info .badge {
    background: rgba(255, 255, 255, 0.3);
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.category-body {
    padding: 1.5rem;
}

.category-description {
    color: var(--text-light);
    margin-bottom: 1rem;
    line-height: 1.5;
}

.category-stats {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.category-footer {
    background: var(--bg-hover);
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn {
    border-radius: 10px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    transition: all 0.3s ease;
    border: none;
    padding: 0.5rem 1rem;
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

.btn-secondary {
    background: #6C757D;
    color: white;
}

.empty-state {
    padding: 4rem 2rem;
    text-align: center;
    color: var(--text-muted);
}

.empty-state i {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
}

.text-muted {
    color: var(--text-muted) !important;
}

.modal-content {
    border: 1px solid var(--border-color);
    border-radius: 15px;
    background: var(--bg-card);
}

.modal-header {
    background: var(--bg-hover);
    border-bottom: 1px solid var(--border-color);
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    color: var(--text-light);
    margin: 0;
    font-weight: 600;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-muted);
    cursor: pointer;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    background: var(--bg-hover);
    border-top: 1px solid var(--border-color);
    padding: 1rem 1.5rem;
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.form-label {
    color: var(--text-light);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.form-control {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    color: var(--text-light);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 5px rgba(46, 139, 87, 0.2);
    outline: none;
}

.form-select {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    color: var(--text-light);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 5px rgba(46, 139, 87, 0.2);
    outline: none;
}
</style>

<script>
// Edit category function
function editCategory(id, name, description) {
    const form = document.getElementById('editCategoryForm');
    form.action = `/admin/kategori/${id}`;
    document.getElementById('editCategoryId').value = id;
    document.getElementById('editCategoryName').value = name;
    document.getElementById('editDeskripsi').value = description;
    document.getElementById('editCategoryModalLabel').textContent = 'Edit Kategori: ' + name;
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}

// Real-time updates
setInterval(() => {
    fetch('/admin/api/realtime-stats')
        .then(response => response.json())
        .then(data => {
            if (data.response_code === 200) {
                // Update category count if exists
                const categoryCount = document.querySelector('.chip strong');
                if (categoryCount) {
                    categoryCount.textContent = data.data.total_categories || 0;
                }
            }
        })
        .catch(error => console.error('Error updating stats:', error));
}, 5000);
</script>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">
                    <i class="fas fa-edit me-2"></i> Edit Kategori
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kategori.update', '') }}" method="POST" id="editCategoryForm">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" id="editCategoryId" name="id_kategori">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editCategoryName" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="editCategoryName" name="nama_kategori" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDeskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select class="form-select" id="editStatus" name="status" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non-Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Update Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
