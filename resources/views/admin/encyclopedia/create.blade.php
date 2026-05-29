@extends('admin.layouts.admin_layout')

@section('title', 'Tambah Data Ensiklopedia')
@section('breadcrumb-parent', 'Encyclopedia')
@section('breadcrumb-current', 'Create')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.encyclopedia.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary-dark transition">
        <span class="material-icons-round text-lg">arrow_back</span>
        Kembali ke Daftar
    </a>
</div>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Tambah Data Ensiklopedia</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Lengkapi formulir di bawah untuk menambahkan data baru.</p>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <form action="{{ route('admin.encyclopedia.store') }}" method="POST" class="p-6 sm:p-8">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-4">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tipe Data <span class="text-rose-500">*</span></label>
                <select name="type" required class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-primary focus:border-primary transition shadow-sm">
                    <option value="obat">Obat-obatan</option>
                    <option value="penyakit">Penyakit</option>
                    <option value="hidup_sehat">Hidup Sehat</option>
                    <option value="keluarga">Keluarga</option>
                </select>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Alphabet <span class="text-rose-500">*</span></label>
                <input type="text" name="alphabet" required maxlength="1" placeholder="A-Z" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-primary focus:border-primary transition shadow-sm uppercase text-center font-bold">
            </div>

            <div class="md:col-span-6">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul / Nama <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: Paracetamol" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-primary focus:border-primary transition shadow-sm">
            </div>

            <div class="md:col-span-12">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Ringkasan (Summary)</label>
                <textarea name="summary" rows="2" placeholder="Penjelasan singkat..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-primary focus:border-primary transition shadow-sm"></textarea>
            </div>

            <div class="md:col-span-12">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Konten Lengkap</label>
                <textarea name="content" rows="8" placeholder="Detail informasi lengkap..." class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-primary focus:border-primary transition shadow-sm"></textarea>
            </div>

            <div class="md:col-span-12">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Link Sumber (Source Link)</label>
                <input type="url" name="source_link" placeholder="https://example.com" class="w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-primary focus:border-primary transition shadow-sm">
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
            <button type="reset" class="px-6 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition">Reset</button>
            <button type="submit" class="px-10 py-2.5 rounded-xl bg-primary hover:bg-primary-dark text-white font-bold transition shadow-md shadow-primary/20 flex items-center gap-2">
                <span class="material-icons-round text-lg">save</span>
                Simpan Data
            </button>
        </div>
    </form>
</div>
@endsection
