@extends('admin.layouts.admin_layout')

@section('title', 'Daftarkan UMKM')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Daftarkan Produk UMKM</h2>
        <p class="text-slate-500 text-sm mt-1">Sertifikasi halal mandiri untuk pelaku usaha kecil dan menengah.</p>
    </div>
    
    <form action="{{ route('admin.umkm.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-6">
            <!-- UMKM Info -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center">
                    <span class="material-icons-round text-primary mr-2">store</span>
                    Informasi Bisnis UMKM
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama UMKM *</label>
                        <input type="text" name="umkm_name" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Contoh: Kripik Tempe Barokah">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Pemilik *</label>
                        <input type="text" name="umkm_owner" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Nama lengkap pemilik">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor HP</label>
                        <input type="text" name="umkm_phone" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="0812xxxxxx">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Lengkap</label>
                        <input type="text" name="umkm_address" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Alamat produksi/toko">
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center">
                    <span class="material-icons-round text-emerald-500 mr-2">inventory_2</span>
                    Detail Produk
                </h3>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Produk *</label>
                            <input type="text" name="product_name" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Contoh: Keripik Tempe 250g">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori Produk *</label>
                            <select name="product_category" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                                <option value="Makanan Ringan">Makanan Ringan</option>
                                <option value="Makanan Berat">Makanan Berat</option>
                                <option value="Minuman">Minuman</option>
                                <option value="Bumbu/Bahan Masakan">Bumbu/Bahan Masakan</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Deskripsi Singkat</label>
                        <textarea name="product_description" rows="3" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Ceritakan sedikit tentang keunggulan produk ini..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Halal Status -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 flex items-center">
                    <span class="material-icons-round text-amber-500 mr-2">verified_user</span>
                    Status Sertifikasi Halal
                </h3>
                
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Halal Saat Ini *</label>
                        <select name="halal_status" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                            <option value="halal_mui">Bersertifikat Halal MUI</option>
                            <option value="self_declared">Self-Declared (Pernyataan Mandiri)</option>
                            <option value="in_process">Sedang Dalam Proses</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor Sertifikat (Jika Ada)</label>
                            <input type="text" name="halal_cert_number" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="ID3211000xxxxxx">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Masa Berlaku</label>
                            <input type="date" name="halal_cert_expiry" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Foto Sertifikat (Pilihan)</label>
                        <input type="file" name="halal_cert_image" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm">
                        <p class="text-[10px] text-slate-400">Scan atau foto sertifikat halal untuk meningkatkan kepercayaan konsumen.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end space-x-4">
            <a href="{{ route('admin.umkm.index') }}" class="px-6 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-800 transition-all">Batal</a>
            <button type="submit" class="px-8 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary-dark transition-all shadow-md shadow-primary/20 flex items-center space-x-2">
                <span class="material-icons-round text-sm">qr_code_2</span>
                <span>Daftarkan & Generate QR</span>
            </button>
        </div>
    </form>
</div>
@endsection
