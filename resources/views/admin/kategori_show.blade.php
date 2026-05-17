@extends('admin.layouts.admin_layout')

@section('title', 'Kategori: ' . $kategori->nama_kategori)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.kategori.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 hover:text-primary transition-colors shadow-sm">
                <span class="material-icons-round">arrow_back</span>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $kategori->nama_kategori }}</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Total Produk: <span class="font-bold text-primary">{{ $kategori->products_count }}</span></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.kategori.edit', $kategori->id_kategori) }}" class="flex items-center space-x-2 px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm text-sm font-medium">
                <span class="material-icons-round text-sm">edit</span>
                <span>Edit Kategori</span>
            </a>
        </div>
    </div>

    <!-- Category Profile -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="md:flex">
            <div class="md:w-1/3 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center p-8 border-b md:border-b-0 md:border-r border-slate-100 dark:border-slate-800">
                <div class="relative w-48 h-48 rounded-2xl overflow-hidden bg-white dark:bg-slate-900 shadow-lg border border-slate-200 dark:border-slate-700 p-4 group">
                    <img src="{{ $kategori->thumbnail_url }}" 
                         alt="{{ $kategori->nama_kategori }}" 
                         class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500"
                         onerror="this.onerror=null;this.src='/images/placeholders/category-placeholder.svg'">
                </div>

            </div>
            <div class="md:w-2/3 p-8">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Deskripsi Kategori</h3>
                <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 leading-relaxed mb-6">
                    {{ $kategori->description ?: 'Belum ada deskripsi untuk kategori ini.' }}
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Dibuat Pada</p>
                        <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $kategori->created_at ? $kategori->created_at->format('d M Y, H:i') : '-' }}</p>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Update Terakhir</p>
                        <p class="text-sm font-medium text-slate-800 dark:text-white">{{ $kategori->updated_at ? $kategori->updated_at->format('d M Y, H:i') : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products in this category -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Produk dalam Kategori ini</h3>
            <div class="text-xs text-slate-500 bg-white dark:bg-slate-900 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm">
                Menampilkan <span class="font-bold text-primary">{{ $products->count() }}</span> dari <span class="font-bold text-primary">{{ $kategori->products_count }}</span> produk
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @forelse($products as $product)
            <a href="{{ route('admin.product.edit', $product->id_product) }}" class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden hover:shadow-lg transition-all hover:-translate-y-1">
                <div class="aspect-square bg-slate-50 dark:bg-slate-800/50 p-4 flex items-center justify-center relative overflow-hidden">
                    <img src="{{ $product->image ? asset($product->image) : asset('images/placeholders/product-placeholder.svg') }}" 
                         alt="{{ $product->nama_product }}" 
                         class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500"
                         onerror="this.onerror=null;this.src='/images/placeholders/product-placeholder.svg'">
                    
                    <div class="absolute inset-0 bg-primary/80 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <span class="material-icons-round text-white">edit</span>
                    </div>
                </div>
                <div class="p-3">
                    <p class="text-xs font-bold text-slate-800 dark:text-white truncate" title="{{ $product->nama_product }}">{{ $product->nama_product }}</p>
                    <p class="text-[10px] text-slate-400 mt-1 truncate">{{ $product->barcode }}</p>
                </div>
            </a>
            @empty
            <div class="col-span-full py-12 bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center text-center">
                <span class="material-icons-round text-5xl text-slate-200 dark:text-slate-800 mb-4">inventory_2</span>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada produk dalam kategori ini.</p>
                <a href="{{ route('admin.product.create', ['kategori_id' => $kategori->id_kategori]) }}" class="mt-4 text-primary text-sm font-bold hover:underline">Tambah Produk Pertama</a>
            </div>
            @endforelse
        </div>

        @if($products->hasPages())
        <div class="mt-8 bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
