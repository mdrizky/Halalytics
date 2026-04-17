@extends('admin.layouts.admin_layout')

@section('title', 'Halal Products')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Halal Products</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola database produk halal yang sudah terverifikasi.</p>
    </div>
    <a href="{{ route('halal-products.create') }}" class="px-6 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary-dark transition-all shadow-md shadow-primary/20 flex items-center space-x-2">
        <span class="material-icons-round text-sm">add</span>
        <span>Tambah Produk</span>
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/30 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="px-6 py-4">#</th>
                <th class="px-6 py-4">Produk</th>
                <th class="px-6 py-4">Barcode</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Sertifikasi</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($products as $product)
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                <td class="px-6 py-4 text-sm text-slate-400">{{ $product->id }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                            <img src="{{ $product->image_url }}" alt="{{ $product->product_name }}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='{{ asset('images/placeholders/product-placeholder.svg') }}'">
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 dark:text-white text-sm">{{ $product->product_name }}</div>
                            <div class="text-xs text-slate-500">{{ $product->brand ?? '-' }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="text-xs font-mono text-slate-600 dark:text-slate-400">{{ $product->product_barcode ?? '-' }}</span>
                </td>
                <td class="px-6 py-4">
                    @if($product->halal_status == 'halal')
                        <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold uppercase rounded-md">Halal</span>
                    @elseif($product->halal_status == 'haram')
                        <span class="px-2 py-1 bg-rose-100 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 text-[10px] font-bold uppercase rounded-md">Haram</span>
                    @elseif($product->halal_status == 'syubhat')
                        <span class="px-2 py-1 bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-bold uppercase rounded-md">Syubhat</span>
                    @else
                        <span class="px-2 py-1 bg-slate-100 dark:bg-slate-500/10 text-slate-600 dark:text-slate-400 text-[10px] font-bold uppercase rounded-md">{{ $product->halal_status }}</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="text-xs text-slate-600 dark:text-slate-400">{{ $product->certification_body ?? '-' }}</div>
                    <div class="text-[10px] text-slate-400">{{ $product->halal_certificate_number ?? '' }}</div>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end space-x-1">
                        <a href="{{ route('halal-products.show', $product->id) }}" class="p-2 text-slate-400 hover:text-primary transition-colors" title="Detail">
                            <span class="material-icons-round text-lg">visibility</span>
                        </a>
                        <a href="{{ route('halal-products.edit', $product->id) }}" class="p-2 text-slate-400 hover:text-amber-500 transition-colors" title="Edit">
                            <span class="material-icons-round text-lg">edit</span>
                        </a>
                        <form action="{{ route('halal-products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 transition-colors" title="Hapus">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <span class="material-icons-round text-4xl text-slate-300">inventory_2</span>
                        <p class="text-slate-400 text-sm mt-2">Belum ada produk halal terdaftar.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $products->links() }}
</div>
@endsection
