@extends('admin.layouts.admin_layout')

@section('title', 'Detail Produk Halal')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Detail Produk Halal</h2>
            <p class="text-slate-500 text-sm mt-1">Informasi lengkap produk #{{ $product->id }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('halal-products.edit', $product->id) }}" class="px-5 py-2 bg-amber-500 text-white rounded-lg text-sm font-bold hover:bg-amber-600 transition-all flex items-center space-x-2">
                <span class="material-icons-round text-sm">edit</span>
                <span>Edit</span>
            </a>
            <a href="{{ route('halal-products.index') }}" class="px-5 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-bold hover:bg-slate-300 transition-all">Kembali</a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="col-span-1">
                <div class="w-full aspect-square rounded-2xl overflow-hidden border-4 border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
                    <img src="{{ $product->image_url }}" alt="{{ $product->product_name }}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='{{ asset('images/placeholders/product-placeholder.svg') }}'">
                </div>
            </div>
            <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Produk</label>
                        <p class="text-lg font-bold text-slate-800 dark:text-white mt-1">{{ $product->product_name }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Brand</label>
                        <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ $product->brand ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Barcode</label>
                        <p class="text-sm font-mono text-slate-700 dark:text-slate-300 mt-1">{{ $product->product_barcode ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Halal</label>
                        <div class="mt-2">
                            @if($product->halal_status == 'halal')
                                <span class="px-3 py-1.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 text-sm font-bold rounded-lg">✅ Halal</span>
                            @elseif($product->halal_status == 'haram')
                                <span class="px-3 py-1.5 bg-rose-100 dark:bg-rose-500/10 text-rose-600 text-sm font-bold rounded-lg">❌ Haram</span>
                            @elseif($product->halal_status == 'syubhat')
                                <span class="px-3 py-1.5 bg-amber-100 dark:bg-amber-500/10 text-amber-600 text-sm font-bold rounded-lg">⚠️ Syubhat</span>
                            @else
                                <span class="px-3 py-1.5 bg-slate-100 dark:bg-slate-500/10 text-slate-600 text-sm font-bold rounded-lg">{{ ucfirst($product->halal_status) }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nomor Sertifikat</label>
                        <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ $product->halal_certificate_number ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Lembaga Sertifikasi</label>
                        <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ $product->certification_body ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Berlaku Hingga</label>
                        <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ $product->certificate_valid_until ? $product->certificate_valid_until->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Terdaftar Sejak</label>
                        <p class="text-sm text-slate-700 dark:text-slate-300 mt-1">{{ $product->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-end">
        <form action="{{ route('halal-products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-5 py-2 bg-rose-500 text-white rounded-lg text-sm font-bold hover:bg-rose-600 transition-all flex items-center space-x-2">
                <span class="material-icons-round text-sm">delete</span>
                <span>Hapus Produk</span>
            </button>
        </form>
    </div>
</div>
@endsection
