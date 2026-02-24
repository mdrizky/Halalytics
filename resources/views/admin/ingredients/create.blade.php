@extends('admin.layouts.admin_layout')

@section('title', 'Tambah Bahan Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Tambah Bahan Baru</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Tambahkan referensi bahan baru ke dalam Encyclopedia.</p>
        </div>
        <a href="{{ route('admin.ingredients.index') }}" class="flex items-center space-x-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg transition-colors text-sm font-medium">
            <span class="material-icons-round text-sm">arrow_back</span>
            <span>Batal</span>
        </a>
    </div>

    <form action="{{ route('admin.ingredients.store') }}" method="POST" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        @csrf
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Bahan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('name') ? 'border-rose-500' : 'border-transparent' }} rounded-xl text-sm focus:ring-2 focus:ring-primary/50 dark:text-white transition-all shadow-sm" placeholder="Contoh: Gelatin Babi">
                    @error('name')<p class="text-[10px] text-rose-500 mt-1 font-bold italic">{{ $message }}</p>@enderror
                </div>

                <!-- E-Number -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">E-Number (Opsional)</label>
                    <input type="text" name="e_number" value="{{ old('e_number') }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border {{ $errors->has('e_number') ? 'border-rose-500' : 'border-transparent' }} rounded-xl text-sm focus:ring-2 focus:ring-primary/50 dark:text-white transition-all shadow-sm" placeholder="Contoh: E441">
                    @error('e_number')<p class="text-[10px] text-rose-500 mt-1 font-bold italic">{{ $message }}</p>@enderror
                </div>

                <!-- Halal Status -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status Kehalalan <span class="text-rose-500">*</span></label>
                    <select name="halal_status" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-transparent rounded-xl text-sm focus:ring-2 focus:ring-primary/50 dark:text-white transition-all shadow-sm">
                        <option value="halal" {{ old('halal_status') == 'halal' ? 'selected' : '' }}>Halal ✅</option>
                        <option value="haram" {{ old('halal_status') == 'haram' ? 'selected' : '' }}>Haram ❌</option>
                        <option value="syubhat" {{ old('halal_status') == 'syubhat' ? 'selected' : '' }}>Syubhat ⚠️</option>
                        <option value="unknown" {{ old('halal_status') == 'unknown' ? 'selected' : '' }}>Unknown ❓</option>
                    </select>
                </div>

                <!-- Health Risk -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Risiko Kesehatan <span class="text-rose-500">*</span></label>
                    <select name="health_risk" required class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-transparent rounded-xl text-sm focus:ring-2 focus:ring-primary/50 dark:text-white transition-all shadow-sm">
                        <option value="safe" {{ old('health_risk') == 'safe' ? 'selected' : '' }}>Safe (Aman)</option>
                        <option value="low_risk" {{ old('health_risk') == 'low_risk' ? 'selected' : '' }}>Low Risk (Risiko Rendah)</option>
                        <option value="high_risk" {{ old('health_risk') == 'high_risk' ? 'selected' : '' }}>High Risk (Risiko Tinggi)</option>
                        <option value="dangerous" {{ old('health_risk') == 'dangerous' ? 'selected' : '' }}>Dangerous (Berbahaya)</option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Deskripsi Bahan</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-transparent rounded-xl text-sm focus:ring-2 focus:ring-primary/50 dark:text-white transition-all shadow-sm" placeholder="Jelaskan mengenai bahan ini...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sources -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sumber (Hewan/Tumbuhan/Sintetis)</label>
                    <input type="text" name="sources" value="{{ old('sources') }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-transparent rounded-xl text-sm focus:ring-2 focus:ring-primary/50 dark:text-white transition-all shadow-sm" placeholder="Contoh: Animal (Porcine)">
                </div>

                <!-- Active -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status Aktif</label>
                    <div class="flex items-center space-x-3 mt-1">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" name="active" value="1" {{ old('active', 1) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:width-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary"></div>
                            <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-300">Aktifkan bahan ini</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Catatan Tambahan (Internal Admin)</label>
                <input type="text" name="notes" value="{{ old('notes') }}" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-transparent rounded-xl text-sm focus:ring-2 focus:ring-primary/50 dark:text-white transition-all shadow-sm" placeholder="Catatan internal jika diperlukan...">
            </div>
        </div>

        <div class="p-6 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-end space-x-3">
            <button type="reset" class="px-6 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Reset</button>
            <button type="submit" class="px-8 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/20 transition-all">Simpan Bahan</button>
        </div>
    </form>
</div>
@endsection
