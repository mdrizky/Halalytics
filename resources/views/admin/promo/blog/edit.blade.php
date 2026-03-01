@extends('admin.layouts.admin_layout')

@section('title', 'Edit Article - Halalytics Admin')
@section('breadcrumb-parent', 'Content')
@section('breadcrumb-current', 'Edit Article')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Edit Artikel</h2>
    <p class="text-slate-500 text-sm mt-1">Perbarui konten artikel agar tetap relevan.</p>
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
    <form action="{{ route('admin.promo.blog.update', $blog->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold mb-1">Judul Artikel</label>
            <input type="text" name="title" value="{{ old('title', $blog->title) }}" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Kategori</label>
            <input type="text" name="category" value="{{ old('category', $blog->category) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Status</label>
            <select name="status" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800">
                <option value="draft" {{ old('status', $blog->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $blog->status) == 'published' ? 'selected' : '' }}>Published</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Konten Artikel</label>
            <textarea name="content" rows="12" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800">{{ old('content', $blog->content) }}</textarea>
        </div>

        @if($blog->image)
        <div>
            <label class="block text-sm font-semibold mb-2">Gambar Saat Ini</label>
            <img src="{{ $blog->image_url }}" alt="Current image" class="w-40 h-28 rounded-lg object-cover border border-slate-200 dark:border-slate-700">
        </div>
        @endif

        <div>
            <label class="block text-sm font-semibold mb-1">Ganti Gambar Sampul</label>
            <input type="file" name="image" accept="image/*" class="w-full text-sm">
            <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ingin mengganti gambar.</p>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-semibold hover:bg-primary-dark transition">Simpan Perubahan</button>
            <a href="{{ route('admin.promo.blog.index') }}" class="px-4 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-semibold">Batal</a>
        </div>
    </form>
</div>
@endsection
