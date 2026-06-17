@extends('admin.layouts.admin_layout')

@section('title', 'Tambah Bahan - Halalytics Admin')
@section('breadcrumb-parent', 'Master Data')
@section('breadcrumb-current', 'Tambah Bahan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Tambah Bahan</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Tambahkan referensi bahan baru ke dalam database.</p>
        </div>
        <a href="{{ route('admin.ingredients.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 hover:border-primary hover:text-primary transition">
            <span class="material-icons-round text-lg">arrow_back</span>
            Batal
        </a>
    </div>

    <form action="{{ route('admin.ingredients.store') }}" method="POST" class="surface-card rounded-2xl overflow-hidden">
        @csrf
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Nama Bahan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm">
                    @error('name')<p class="text-[10px] text-rose-500 mt-1 font-bold italic">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">E-Number (Opsional)</label>
                    <input type="text" name="e_number" value="{{ old('e_number') }}" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm" placeholder="Contoh: E441">
                    @error('e_number')<p class="text-[10px] text-rose-500 mt-1 font-bold italic">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Status Kehalalan <span class="text-rose-500">*</span></label>
                    <select name="halal_status" required class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm">
                        <option value="halal" {{ old('halal_status') == 'halal' ? 'selected' : '' }}>Halal</option>
                        <option value="haram" {{ old('halal_status') == 'haram' ? 'selected' : '' }}>Haram</option>
                        <option value="syubhat" {{ old('halal_status') == 'syubhat' ? 'selected' : '' }}>Syubhat</option>
                        <option value="unknown" {{ old('halal_status') == 'unknown' ? 'selected' : '' }}>Unknown</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Risiko Kesehatan <span class="text-rose-500">*</span></label>
                    <select name="health_risk" required class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm">
                        <option value="safe" {{ old('health_risk') == 'safe' ? 'selected' : '' }}>Safe (Aman)</option>
                        <option value="low_risk" {{ old('health_risk') == 'low_risk' ? 'selected' : '' }}>Low Risk (Risiko Rendah)</option>
                        <option value="high_risk" {{ old('health_risk') == 'high_risk' ? 'selected' : '' }}>High Risk (Risiko Tinggi)</option>
                        <option value="dangerous" {{ old('health_risk') == 'dangerous' ? 'selected' : '' }}>Dangerous (Berbahaya)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Deskripsi Bahan</label>
                <textarea name="description" rows="4" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Sumber</label>
                    <input type="text" name="sources" value="{{ old('sources') }}" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm" placeholder="Contoh: Animal (Porcine)">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Image URL (Opsional)</label>
                    <input type="url" name="image_url" value="{{ old('image_url') }}" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm" placeholder="https://example.com/image.jpg">
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Catatan Tambahan (Internal Admin)</label>
                <textarea name="notes" rows="3" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm">{{ old('notes') }}</textarea>
            </div>

            <label class="flex items-center gap-3 rounded-2xl bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                <input type="hidden" name="active" value="0">
                <input type="checkbox" name="active" value="1" {{ old('active', 1) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary">
                Aktifkan bahan ini
            </label>
        </div>

        <div class="p-6 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.ingredients.index') }}" class="rounded-full border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 hover:border-primary hover:text-primary transition">
                Batal
            </a>
            <button type="submit" class="rounded-full bg-primary px-5 py-2 text-sm font-bold text-white hover:bg-primary-dark transition">
                Simpan Bahan
            </button>
        </div>
    </form>
</div>
@endsection
