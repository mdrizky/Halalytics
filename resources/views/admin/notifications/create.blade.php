@extends('admin.layouts.admin_layout')

@section('title', 'Send Push Notification')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Kirim Push Notification</h2>
        <p class="text-slate-500 text-sm mt-1">Gunakan Firebase untuk mengirim pesan ke aplikasi mobile.</p>
    </div>
    
    <form action="{{ route('admin.notifications.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Konten Notifikasi</h3>
                
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Judul Notifikasi *</label>
                        <input type="text" name="title" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="⚠️ Peringatan Bahan Berbahaya">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pesan Notifikasi *</label>
                        <textarea name="body" required rows="4" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Bahan E123 terdeteksi memiliki risiko kesehatan..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe Notifikasi *</label>
                            <select name="type" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                                <option value="general">Umum (Berita/Update)</option>
                                <option value="ingredient_alert">Peringatan Bahan</option>
                                <option value="product_reminder">Pengingat Produk</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Target Pengguna *</label>
                            <select name="target_type" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                                <option value="all">Semua Pengguna</option>
                                <option value="specific_users">Pengguna Tertentu (Coming Soon)</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jadwal Kirim (Opsional)</label>
                        <input type="datetime-local" name="scheduled_at" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <p class="text-[10px] text-slate-400">Biarkan kosong untuk mengirim notifikasi secara instan.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end space-x-4">
            <a href="{{ route('admin.notifications.index') }}" class="px-6 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-800 transition-all">Batal</a>
            <button type="submit" class="px-8 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary-dark transition-all shadow-md shadow-primary/20 flex items-center space-x-2">
                <span class="material-icons-round text-sm">send</span>
                <span>Kirim Sekarang</span>
            </button>
        </div>
    </form>
</div>
@endsection
