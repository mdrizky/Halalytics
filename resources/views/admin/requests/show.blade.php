@extends('admin.layouts.admin_layout')

@section('title', 'Detail Permintaan: ' . $request->product_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.requests.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 hover:text-primary transition-colors shadow-sm">
                <span class="material-icons-round">arrow_back</span>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $request->product_name }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    @php
                        $statusColors = [
                            'pending' => 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                            'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
                            'rejected' => 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
                        ];
                        $statusColor = $statusColors[$request->status] ?? 'bg-slate-100 text-slate-500';
                    @endphp
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusColor }} uppercase tracking-wider">
                        {{ $request->status }}
                    </span>
                    <span class="text-slate-400 dark:text-slate-500 text-xs font-medium">Diajukan pada {{ $request->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>

        @if($request->status === 'pending')
        <div class="flex items-center gap-3">
            <button type="button" onclick="openRejectModal()" class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-rose-600 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors shadow-sm text-sm font-medium flex items-center gap-2">
                <span class="material-icons-round text-sm">close</span>
                <span>Tolak</span>
            </button>
            <form action="{{ route('admin.requests.approve', $request->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-all shadow-lg shadow-emerald-500/20 text-sm font-bold flex items-center gap-2">
                    <span class="material-icons-round text-sm">check</span>
                    <span>Setujui & Verifikasi</span>
                </button>
            </form>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Content Area -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Product Images -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6">
                <h3 class="font-bold text-slate-800 dark:text-white mb-6">Bukti Visual Produk</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Tampilan Depan</p>
                        <div class="aspect-[3/4] bg-slate-50 dark:bg-slate-800/50 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-800 flex items-center justify-center p-4">
                            <img src="{{ $request->image_front }}" alt="Front" class="w-full h-full object-contain hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://loremflickr.com/400/600/{{ urlencode($request->product_name) }},product?lock={{ $request->id }}'">
                        </div>
                    </div>
                    <div class="space-y-3">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest text-center">Tampilan Belakang (Komposisi)</p>
                        <div class="aspect-[3/4] bg-slate-50 dark:bg-slate-800/50 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-800 flex items-center justify-center p-4">
                            <img src="{{ $request->image_back }}" alt="Back" class="w-full h-full object-contain hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://loremflickr.com/400/600/{{ urlencode($request->product_name) }},ingredients?lock={{ $request->id + 100 }}'">
                        </div>
                    </div>
                </div>
            </div>

            <!-- OCR Result -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-slate-800 dark:text-white">Hasil Ekstraksi OCR (Bahan)</h3>
                    <span class="flex items-center gap-1.5 px-2 py-1 bg-primary/10 text-primary rounded-lg text-[10px] font-bold uppercase tracking-wider border border-primary/20">
                        <span class="material-icons-round text-xs">auto_awesome</span>
                        AI Generated
                    </span>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-100 dark:border-slate-800">
                    <p class="text-slate-700 dark:text-slate-200 leading-relaxed font-medium">
                        {{ $request->ocr_text ?: 'Tidak ada data OCR yang terdeteksi.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Submitter Info -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6">
                <h3 class="font-bold text-slate-800 dark:text-white mb-6">Pengirim</h3>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-lg">
                        {{ strtoupper(substr($request->user->full_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 dark:text-white">{{ $request->user->full_name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $request->user->email }}</p>
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400">Barcode</span>
                        <span class="font-mono font-bold text-slate-800 dark:text-white">{{ $request->barcode }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400">ID Permintaan</span>
                        <span class="font-mono text-xs text-slate-500">REQ-{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            </div>

            <!-- Notes if rejected/processed -->
            @if($request->admin_notes)
            <div class="bg-rose-50 dark:bg-rose-900/10 rounded-2xl border border-rose-100 dark:border-rose-900/20 shadow-sm p-6">
                <h3 class="font-bold text-rose-800 dark:text-rose-400 text-sm mb-3">Catatan Admin</h3>
                <p class="text-sm text-rose-700 dark:text-rose-300 italic">
                    "{{ $request->admin_notes }}"
                </p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
        <div class="p-6">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-4">Tolak Permintaan</h3>
            <form action="{{ route('admin.requests.reject', $request->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-2">Alasan Penolakan</label>
                        <textarea name="reason" rows="4" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-rose-500/50 dark:text-white" placeholder="Sebutkan alasan penolakan agar pengguna dapat memperbaiki data..." required></textarea>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-8">
                    <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-bold text-sm hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-rose-500 text-white rounded-xl font-bold text-sm hover:bg-rose-600 shadow-lg shadow-rose-500/20 transition-all">Tolak Permanen</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endsection
