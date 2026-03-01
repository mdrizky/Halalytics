@extends('admin.layouts.admin_layout')

@section('title', 'Articles - Halalytics Admin')
@section('breadcrumb-parent', 'Content')
@section('breadcrumb-current', 'Articles')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Article Management</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola artikel edukasi kesehatan dan update aplikasi.</p>
    </div>
    <a href="{{ route('admin.promo.blog.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-semibold hover:bg-primary-dark transition">
        <span class="material-icons-round text-lg">add</span>
        Artikel Baru
    </a>
</div>

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="px-5 py-4 w-16">#</th>
                    <th class="px-5 py-4">Artikel</th>
                    <th class="px-5 py-4">Kategori</th>
                    <th class="px-5 py-4">Views</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($blogs as $key => $blog)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                    <td class="px-5 py-4 text-sm text-slate-500">{{ $blogs->firstItem() + $key }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 rounded-lg bg-slate-100 dark:bg-slate-800 overflow-hidden flex items-center justify-center">
                                @if($blog->image)
                                    <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-icons-round text-slate-400">article</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ Str::limit($blog->title, 72) }}</div>
                                <div class="text-xs text-slate-500">{{ $blog->formatted_date }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-300">{{ $blog->category ?: '-' }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm text-slate-700 dark:text-slate-300">{{ number_format($blog->views) }}</td>
                    <td class="px-5 py-4">
                        @if($blog->status == 'published')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600">TERBIT</span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-600">DRAFT</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="p-2 rounded-lg text-slate-500 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-800" title="Lihat">
                                <span class="material-icons-round text-lg">visibility</span>
                            </a>
                            <a href="{{ route('admin.promo.blog.edit', $blog->id) }}" class="p-2 rounded-lg text-slate-500 hover:text-primary hover:bg-slate-100 dark:hover:bg-slate-800" title="Edit">
                                <span class="material-icons-round text-lg">edit</span>
                            </a>
                            <form action="{{ route('admin.promo.blog.toggle', $blog->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20" title="Toggle Status">
                                    <span class="material-icons-round text-lg">sync</span>
                                </button>
                            </form>
                            <form action="{{ route('admin.promo.blog.destroy', $blog->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus artikel ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20" title="Hapus">
                                    <span class="material-icons-round text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center">
                        <div class="text-slate-400 text-sm">Belum ada artikel. Klik <strong>Artikel Baru</strong> untuk menambah konten.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
        {{ $blogs->links() }}
    </div>
</div>
@endsection
