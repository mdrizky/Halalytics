@extends('admin.layouts.admin_layout')

@section('title', 'Kamus Kesehatan')
@section('breadcrumb-parent', 'Encyclopedia')
@section('breadcrumb-current', 'Kamus Kesehatan')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Ensiklopedia Kesehatan</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Kelola data obat, penyakit, pola hidup sehat, dan informasi keluarga.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.encyclopedia.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold transition shadow-sm">
            <span class="material-icons-round text-lg">add_circle</span>
            <span>Tambah Data</span>
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-6 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 p-4 border border-emerald-200 dark:border-emerald-800 flex items-start gap-3 text-emerald-800 dark:text-emerald-400">
    <span class="material-icons-round text-emerald-500 mt-0.5">check_circle</span>
    <div class="flex-1">
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
</div>
@endif

<!-- Filter -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 mb-6">
    <form action="{{ route('admin.encyclopedia.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3">
        <div class="md:col-span-6">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">
                    <span class="material-icons-round text-lg">search</span>
                </span>
                <input type="text" name="search" class="w-full pl-10 rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white" placeholder="Cari judul atau isi..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="md:col-span-4">
            <select name="type" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white">
                <option value="">Semua Tipe</option>
                <option value="obat" {{ request('type') == 'obat' ? 'selected' : '' }}>Obat-obatan</option>
                <option value="penyakit" {{ request('type') == 'penyakit' ? 'selected' : '' }}>Penyakit</option>
                <option value="hidup_sehat" {{ request('type') == 'hidup_sehat' ? 'selected' : '' }}>Hidup Sehat</option>
                <option value="keluarga" {{ request('type') == 'keluarga' ? 'selected' : '' }}>Keluarga</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <button type="submit" class="w-full h-full px-4 py-2 rounded-lg bg-primary text-white font-semibold hover:bg-primary-dark transition">Filter</button>
        </div>
    </form>
</div>

<!-- Main Table -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700">
                    <th class="px-5 py-4">Alphabet</th>
                    <th class="px-5 py-4 w-4/12">Judul</th>
                    <th class="px-5 py-4">Tipe</th>
                    <th class="px-5 py-4 w-4/12">Ringkasan</th>
                    <th class="px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($items as $item)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold border border-slate-200 dark:border-slate-700">
                            {{ strtoupper($item->alphabet) }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 flex-shrink-0">
                                <img src="https://loremflickr.com/100/100/{{ urlencode($item->type) }},medical?lock={{ $item->id }}" class="w-full h-full object-cover">
                            </div>
                            <div class="text-sm font-bold text-slate-800 dark:text-white">{{ $item->title }}</div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $typeClasses = [
                                'obat' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                'penyakit' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                'hidup_sehat' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                'keluarga' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                            ];
                            $typeLabel = [
                                'obat' => 'OBAT',
                                'penyakit' => 'PENYAKIT',
                                'hidup_sehat' => 'HIDUP SEHAT',
                                'keluarga' => 'KELUARGA',
                            ];
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $typeClasses[$item->type] ?? 'bg-slate-100 text-slate-700' }} border uppercase">
                            {{ $typeLabel[$item->type] ?? $item->type }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2">{{ $item->summary }}</div>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.encyclopedia.edit', $item->id) }}" class="p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 transition border border-transparent hover:border-primary/20" title="Edit">
                                <span class="material-icons-round text-lg leading-none">edit</span>
                            </a>
                            <form action="{{ route('admin.encyclopedia.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 transition border border-transparent hover:border-rose-100 dark:hover:border-rose-800" title="Hapus">
                                    <span class="material-icons-round text-lg leading-none">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                            <span class="material-icons-round text-5xl mb-3 opacity-50 text-primary">auto_stories</span>
                            <p class="text-sm font-medium">Belum ada data ensiklopedia.</p>
                            <p class="text-xs mt-1">Klik tombol <strong>Tambah Data</strong> untuk menambahkan informasi baru.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
        {{ $items->appends(request()->query())->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
