@extends('admin.layouts.admin_layout')

@section('title', 'Edit Scan - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="text-slate-400">Activity</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Edit Scan</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Edit Scan Record</h3>
            <a href="{{ route('admin.scan.index') }}" class="text-sm font-medium text-primary hover:underline flex items-center">
                <span class="material-icons-round text-sm mr-1">arrow_back</span>
                Back
            </a>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.scan.update', $scan->id_scan) }}" method="POST">
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
                        <option value="syubhat" {{ $sh === 'syubhat' ? 'selected' : '' }}>Syubhat</option>
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
                    <a href="{{ route('admin.scan.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

