@extends('admin.master')

@section('title', 'Kategori Produk - Halalytics Admin')

@section('isi')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3 text-white">
        <h1 class="h3 mb-0 fw-bold">Kelola Kategori</h1>
        <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-1"></i> Tambah Kategori
        </button>
    </div>

    <!-- Stats -->
    <div class="glass-card mb-4 p-4">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-info bg-opacity-25 p-3 me-3">
                <i class="fas fa-tags text-info fs-3"></i>
            </div>
            <div>
                <div class="text-white-50 small text-uppercase fw-bold">Total Kategori</div>
                <div class="h3 mb-0 fw-bold text-white">{{ $kategori->total() }}</div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="glass-card p-0 overflow-hidden">
        <div class="p-4 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
            <h6 class="m-0 fw-bold text-white">Daftar Kategori</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-glass mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th>Produk</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kategori as $kat)
                        <tr>
                            <td class="ps-4 text-white-50">#{{ $kat->id_kategori }}</td>
                            <td><strong class="text-white">{{ $kat->nama_kategori }}</strong></td>
                            <td class="text-white-50"><small>{{ $kat->description ?? '-' }}</small></td>
                            <td><span class="badge bg-info bg-opacity-25 text-info rounded-pill px-3">{{ $kat->products_count }} Products</span></td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button onclick="editModal({{ $kat->id_kategori }}, '{{ $kat->nama_kategori }}', '{{ $kat->description }}')" class="btn btn-sm btn-outline-info me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.kategori.destroy', $kat->id_kategori) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $kategori->links() }}
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kategori.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Edit Kategori</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="editNama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" id="editDeskripsi" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editModal(id, nama, desc) {
    document.getElementById('editForm').action = '/admin/kategori/' + id;
    document.getElementById('editNama').value = nama;
    document.getElementById('editDeskripsi').value = desc || '';
    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
</script>
@endsection