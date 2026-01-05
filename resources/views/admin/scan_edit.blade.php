@extends('master')
@section('isi')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Data Scan</h5>
            <a href="{{ route('scan.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('scan.update', $scan->id_scan) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">User</label>
                    <input type="text" class="form-control" value="{{ $scan->user->username ?? 'User' }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Produk</label>
                    <input type="text" class="form-control" value="{{ $scan->nama_produk }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Barcode</label>
                    <input type="text" class="form-control" value="{{ $scan->barcode }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status Halal</label>
                    @php $sh = strtolower($scan->status_halal); @endphp
                    <select name="status_halal" class="form-select" required>
                        <option value="halal" {{ $sh === 'halal' ? 'selected' : '' }}>Halal</option>
                        <option value="tidak halal" {{ $sh === 'tidak halal' ? 'selected' : '' }}>Tidak Halal</option>
                        <option value="diragukan" {{ $sh === 'diragukan' ? 'selected' : '' }}>Diragukan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status Kesehatan</label>
                    @php $sk = strtolower($scan->status_kesehatan); @endphp
                    <select name="status_kesehatan" class="form-select" required>
                        <option value="sehat" {{ $sk === 'sehat' ? 'selected' : '' }}>Sehat</option>
                        <option value="tidak sehat" {{ $sk === 'tidak sehat' ? 'selected' : '' }}>Tidak Sehat</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Expired</label>
                    <input type="date" name="tanggal_expired" class="form-control" value="{{ $scan->tanggal_expired ? \Carbon\Carbon::parse($scan->tanggal_expired)->format('Y-m-d') : '' }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Scan</label>
                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($scan->tanggal_scan)->format('d M Y H:i') }}" disabled>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('scan.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

