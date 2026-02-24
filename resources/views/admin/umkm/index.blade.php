@extends('admin.layouts.admin_layout')

@section('title', 'Manajemen UMKM')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Manajemen Produk UMKM</h2>
        <p class="text-slate-500 text-sm mt-1">Daftar pelaku usaha kecil yang terintegrasi dengan Halalytics.</p>
    </div>
    <a href="{{ route('admin.umkm.create') }}" class="px-6 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary-dark transition-all shadow-md shadow-primary/20 flex items-center space-x-2">
        <span class="material-icons-round text-sm">add</span>
        <span>Daftarkan UMKM</span>
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/30 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="px-6 py-4">QR / ID</th>
                <th class="px-6 py-4">Produk</th>
                <th class="px-6 py-4">UMKM</th>
                <th class="px-6 py-4">Status Halal</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($products as $product)
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-1 bg-white rounded border border-slate-100 shadow-sm">
                            {!! file_get_contents(storage_path('app/public/' . str_replace('/storage/', '', $product->qr_code_image_path))) !!}
                            {{-- Assuming SVG output directly --}}
                        </div>
                        <span class="text-[10px] font-mono font-bold text-slate-400">{{ $product->qr_code_unique_id }}</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="font-bold text-slate-800 dark:text-white text-sm">{{ $product->product_name }}</div>
                    <div class="text-xs text-slate-500">{{ $product->product_category }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-slate-700 dark:text-slate-300">{{ $product->umkm_name }}</div>
                    <div class="text-[10px] text-slate-400">{{ $product->umkm_owner }}</div>
                </td>
                <td class="px-6 py-4">
                    @if($product->halal_status == 'halal_mui')
                        <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold uppercase rounded-md">Halal MUI</span>
                    @elseif($product->halal_status == 'self_declared')
                        <span class="px-2 py-1 bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-bold uppercase rounded-md">Self Declared</span>
                    @else
                        <span class="px-2 py-1 bg-sky-100 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 text-[10px] font-bold uppercase rounded-md">In Process</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end space-x-2">
                        <a href="{{ route('admin.umkm.download-qr', $product->id) }}" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-all" title="Download QR">
                            <span class="material-icons-round text-lg">download</span>
                        </a>
                        <form action="{{ route('admin.umkm.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus data UMKM ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 transition-all">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-400">Belum ada produk UMKM terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $products->links() }}
</div>
@endsection

@push('styles')
<style>
    /* Scale down the SVG for thumbnail */
    td svg {
        width: 32px;
        height: 32px;
    }
</style>
@endpush
