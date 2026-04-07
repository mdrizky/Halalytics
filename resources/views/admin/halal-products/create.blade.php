@extends('admin.layouts.admin_layout')

@section('title', 'Tambah Produk Halal')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Tambah Produk Halal</h2>
        <p class="text-slate-500 text-sm mt-1">Daftarkan produk baru ke database halal Halalytics.</p>
    </div>

    <form action="{{ route('halal-products.store') }}" method="POST">
        @csrf

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center">
                <span class="material-icons-round text-primary mr-2">verified</span>
                Informasi Produk
            </h3>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Produk *</label>
                        <input type="text" name="product_name" required value="{{ old('product_name') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Contoh: Indomie Goreng">
                        @error('product_name') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Brand</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Contoh: Indofood">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Barcode</label>
                        <input type="text" name="product_barcode" value="{{ old('product_barcode') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="8991234567890">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Halal *</label>
                        <select name="halal_status" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                            <option value="halal" {{ old('halal_status') == 'halal' ? 'selected' : '' }}>Halal</option>
                            <option value="haram" {{ old('halal_status') == 'haram' ? 'selected' : '' }}>Haram</option>
                            <option value="syubhat" {{ old('halal_status') == 'syubhat' ? 'selected' : '' }}>Syubhat</option>
                            <option value="non_halal" {{ old('halal_status') == 'non_halal' ? 'selected' : '' }}>Non Halal</option>
                            <option value="unknown" {{ old('halal_status') == 'unknown' ? 'selected' : '' }}>Belum Diketahui</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor Sertifikat</label>
                        <input type="text" name="halal_certificate_number" value="{{ old('halal_certificate_number') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="LPPOM-xxxxx">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Lembaga Sertifikasi</label>
                        <input type="text" name="certification_body" value="{{ old('certification_body') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="LPPOM MUI">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sertifikat Berlaku Hingga</label>
                    <input type="date" name="certificate_valid_until" value="{{ old('certificate_valid_until') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end space-x-4">
            <a href="{{ route('halal-products.index') }}" class="px-6 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-800 transition-all">Batal</a>
            <button type="submit" class="px-8 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary-dark transition-all shadow-md shadow-primary/20 flex items-center space-x-2">
                <span class="material-icons-round text-sm">save</span>
                <span>Simpan Produk</span>
            </button>
        </div>
    </form>
</div>
@endsection
