@extends('admin.layouts.admin_layout')

@section('title', 'Detail Produk BPOM')

@section('content')
<div class="space-y-6">
    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.bpom.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 hover:text-primary transition-colors shadow-sm">
                <span class="material-icons-round">arrow_back</span>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $product->nama_produk }}</h2>
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-slate-500 dark:text-slate-400">Nomor Registrasi:</span>
                    <span class="font-mono font-bold text-primary">{{ $product->nomor_reg }}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($product->verification_status != 'verified')
            <form action="{{ route('admin.bpom.verify', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center space-x-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-colors shadow-sm text-sm font-medium" onclick="return confirm('Verifikasi produk ini?')">
                    <span class="material-icons-round text-sm">verified</span>
                    <span>Verifikasi Data</span>
                </button>
            </form>
            @endif
            <form action="{{ route('admin.bpom.destroy', $product->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex items-center space-x-2 px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-lg transition-colors shadow-sm text-sm font-medium" onclick="return confirm('Hapus data ini?')">
                    <span class="material-icons-round text-sm">delete</span>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 dark:text-white">Informasi Produk</h3>
                    <span class="flex items-center gap-1.5 px-3 py-1 rounded-full {{ $product->verification_status == 'verified' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' }} text-[10px] font-bold uppercase tracking-wider border {{ $product->verification_status == 'verified' ? 'border-emerald-100 dark:border-emerald-500/20' : 'border-amber-100 dark:border-amber-500/20' }}">
                        {{ $product->verification_status == 'verified' ? 'Terverifikasi' : 'Pending' }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Merk / Brand</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $product->merk ?: 'N/A' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kategori BPOM</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ strtoupper($product->kategori) }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pendaftar / Perusahaan</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $product->pendaftar ?: 'N/A' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Bentuk Sediaan</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $product->bentuk_sediaan ?: 'N/A' }}</p>
                        </div>
                        <div class="space-y-1 md:col-span-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kemasan</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $product->kemasan ?: 'N/A' }}</p>
                        </div>
                        <div class="space-y-1 md:col-span-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Alamat Produsen</p>
                            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $product->alamat_produsen ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingredients & Analysis -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="font-bold text-slate-800 dark:text-white">Komposisi & Analisis AI</h3>
                </div>
                <div class="p-6 space-y-8">
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Bahan / Ingredien</h4>
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                            {{ $product->ingredients_text ?: 'Tidak ada data komposisi tersedia.' }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Analisis Halal</h4>
                            <div class="p-4 rounded-xl bg-emerald-50/30 dark:bg-emerald-500/5 border border-emerald-100/50 dark:border-emerald-500/10">
                                <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                                    {{ $product->analisis_halal ?: 'Belum ada analisis halal.' }}
                                </p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Analisis Kandungan</h4>
                            <div class="p-4 rounded-xl bg-blue-50/30 dark:bg-blue-500/5 border border-blue-100/50 dark:border-blue-500/10">
                                <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                                    {{ $product->analisis_kandungan ?: 'Belum ada analisis kandungan.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6">
                <h3 class="font-bold text-slate-800 dark:text-white mb-6">Status Keamanan</h3>
                
                <div class="space-y-6">
                    <!-- Halal Score -->
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Status Halal</p>
                            <p class="text-lg font-black {{ $product->status_halal == 'halal' ? 'text-emerald-500' : ($product->status_halal == 'haram' ? 'text-rose-500' : 'text-amber-500') }}">
                                {{ strtoupper($product->status_halal ?: 'SYUBHAT') }}
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $product->status_halal == 'halal' ? 'bg-emerald-50 text-emerald-500' : 'bg-amber-50 text-amber-500' }}">
                            <span class="material-icons-round text-2xl">{{ $product->status_halal == 'halal' ? 'verified' : 'help_outline' }}</span>
                        </div>
                    </div>

                    <!-- Safety Score -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Skor Keamanan</p>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $product->skor_keamanan ?: 0 }}/100</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-1000 {{ $product->skor_keamanan >= 70 ? 'bg-emerald-500' : ($product->skor_keamanan >= 40 ? 'bg-amber-500' : 'bg-rose-500') }}" 
                                 style="width: {{ $product->skor_keamanan ?: 0 }}%"></div>
                        </div>
                    </div>

                    <hr class="border-slate-100 dark:border-slate-800">

                    <!-- Validity -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Terbit</p>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $product->tanggal_terbit ? $product->tanggal_terbit->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Berlaku</p>
                            <p class="text-xs font-semibold {{ $product->isExpired() ? 'text-rose-500' : 'text-slate-700 dark:text-slate-200' }}">
                                {{ $product->masa_berlaku ? $product->masa_berlaku->format('d/m/Y') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Meta Info -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6">
                <h3 class="font-bold text-xs text-slate-400 uppercase tracking-widest mb-4">Metadata</h3>
                <div class="space-y-4">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Sumber Data</span>
                        <span class="font-bold text-slate-700 dark:text-slate-200">{{ strtoupper($product->sumber_data) }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Barcode</span>
                        <span class="font-mono text-slate-700 dark:text-slate-200">{{ $product->barcode ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Dibuat Pada</span>
                        <span class="text-slate-700 dark:text-slate-200">{{ $product->created_at->format('d M Y') }}</span>
                    </div>
                    @if($product->verified_at)
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Diverifikasi</span>
                        <span class="text-slate-700 dark:text-slate-200">{{ $product->verified_at->format('d M Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

