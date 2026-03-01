@extends('admin.layouts.admin_layout')

@section('title', 'Create Article - Halalytics Admin')
@section('breadcrumb-parent', 'Content')
@section('breadcrumb-current', 'Create Article')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Tulis Artikel Baru</h2>
    <p class="text-slate-500 text-sm mt-1">Buat artikel edukasi kesehatan untuk user aplikasi.</p>
</div>

@if ($errors->any())
<div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
    <ul class="list-disc pl-5 space-y-1">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
    <form action="{{ route('admin.promo.blog.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-semibold mb-1">Judul Artikel</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" placeholder="Contoh: 5 Bahaya Gula Buatan Pada Minuman Kemasan">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Kategori</label>
            <input type="text" name="category" value="{{ old('category', 'Edukasi Halal') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" placeholder="Edukasi, Update, Tips">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Status</label>
            <select name="status" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800">
                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Konten Artikel</label>
            <textarea name="content" rows="12" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" placeholder="Tulis isi artikel di sini...">{{ old('content') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Gambar Sampul</label>
            <input type="file" name="image" accept="image/*" class="w-full text-sm">
            <p class="text-xs text-slate-500 mt-1">Format: JPG/PNG/WEBP (max 2MB)</p>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-semibold hover:bg-primary-dark transition">Simpan Artikel</button>
            <a href="{{ route('admin.promo.blog.index') }}" class="px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold">Batal</a>
        </div>
    </form>
</div>
@endsection
