@extends('admin.layouts.admin_layout')

@section('title', 'Manajemen Artikel')
@section('breadcrumb-parent', 'Content & Activity')
@section('breadcrumb-current', 'Articles')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="rounded-xl p-4 bg-primary text-white shadow-sm border border-primary/20">
        <div class="text-sm opacity-80 font-medium">Total Artikel</div>
        <div class="text-3xl font-extrabold mt-1">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="rounded-xl p-4 bg-emerald-500 text-white shadow-sm border border-emerald-600/20">
        <div class="text-sm opacity-80 font-medium">Published</div>
        <div class="text-3xl font-extrabold mt-1">{{ number_format($stats['published']) }}</div>
    </div>
    <div class="rounded-xl p-4 bg-amber-500 text-white shadow-sm border border-amber-600/20">
        <div class="text-sm opacity-80 font-medium">Draft</div>
        <div class="text-3xl font-extrabold mt-1">{{ number_format($stats['draft']) }}</div>
    </div>
    <div class="rounded-xl p-4 bg-primary text-white shadow-sm border border-primary/20">
        <div class="text-sm opacity-80 font-medium">Total Views</div>
        <div class="text-3xl font-extrabold mt-1">{{ number_format($stats['total_views']) }}</div>
    </div>
</div>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Manajemen Artikel</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Kelola konten edukasi halal, kesehatan, dan nutrisi.</p>
    </div>
    <button type="button" onclick="document.getElementById('addArticleModal').classList.remove('hidden')" class="inline-flex flex-shrink-0 items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold transition shadow-sm">
        <span class="material-icons-round text-lg">add_circle</span>
        <span>Tambah Artikel</span>
    </button>
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
    <form action="{{ route('admin.articles.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3">
        <div class="md:col-span-6">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">
                    <span class="material-icons-round text-lg">search</span>
                </span>
                <input type="text" name="search" class="w-full pl-10 rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white" placeholder="Cari judul artikel atau author..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="md:col-span-4">
            <select name="category" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                @endforeach
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
                    <th class="px-5 py-4 w-5/12">Judul Artikel</th>
                    <th class="px-5 py-4">Kategori</th>
                    <th class="px-5 py-4">Author & Views</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($articles as $article)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex-shrink-0 flex items-center justify-center">
                                @if($article->image)
                                    <img src="{{ $article->image }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <span class="material-icons-round text-slate-400 text-2xl hidden">newspaper</span>
                                @else
                                    <span class="material-icons-round text-slate-400 text-2xl">newspaper</span>
                                @endif
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-800 dark:text-white line-clamp-1" title="{{ $article->title }}">{{ $article->title }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1" title="{{ $article->excerpt }}">{{ $article->excerpt }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex px-2 py-1 rounded bg-primary/10 dark:bg-primary/15 text-primary dark:text-emerald-300 text-xs font-bold uppercase border border-primary/20 dark:border-primary/30">
                            {{ $article->category }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $article->author }}</div>
                        <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1"><span class="material-icons-round text-[11px]">visibility</span> {{ number_format($article->views) }} views</div>
                    </td>
                    <td class="px-5 py-4">
                        @if($article->is_published)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> PUBLISHED
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> DRAFT
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('admin.articles.toggle', $article->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition border border-transparent hover:border-amber-100 dark:hover:border-amber-800" title="{{ $article->is_published ? 'Jadikan Draft' : 'Publish' }}">
                                    <span class="material-icons-round text-lg leading-none">{{ $article->is_published ? 'visibility_off' : 'visibility' }}</span>
                                </button>
                            </form>
                            <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus artikel ini secara permanen?')">
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
                            <span class="material-icons-round text-5xl mb-3 opacity-50 text-primary">newspaper</span>
                            <p class="text-sm font-medium">Belum ada artikel.</p>
                            <p class="text-xs mt-1">Klik tombol <strong>Tambah Artikel</strong> untuk membuat konten baru.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
        {{ $articles->appends(request()->query())->links('pagination::tailwind') }}
    </div>
</div>

<!-- Tailwind Modal for Add Article -->
<div id="addArticleModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all sm:my-8 w-full sm:max-w-3xl border border-slate-200 dark:border-slate-700">
                <form action="{{ route('admin.articles.store') }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-slate-900 px-4 py-4 sm:px-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="material-icons-round text-primary">post_add</span> Tambah Artikel Baru
                        </h3>
                        <button type="button" onclick="document.getElementById('addArticleModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300">
                            <span class="material-icons-round">close</span>
                        </button>
                    </div>
                    
                    <div class="px-4 py-5 sm:p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Judul Artikel</label>
                            <input type="text" name="title" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori</label>
                                <select name="category" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-primary focus:border-primary">
                                    <option value="health">Kesehatan (Health)</option>
                                    <option value="halal">Halal & Syariat</option>
                                    <option value="medicine">Obat & Farmasi</option>
                                    <option value="cosmetic">Kosmetik & Skincare</option>
                                    <option value="nutrition">Nutrisi & Diet</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Author</label>
                                <input type="text" name="author" value="Halalytics Team" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-primary focus:border-primary">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">URL Gambar Cover (Opsional)</label>
                            <input type="url" name="image" placeholder="https://" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Konten Artikel</label>
                            <textarea name="content" rows="8" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-primary focus:border-primary"></textarea>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100 dark:border-slate-800">
                        <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark sm:ml-3 sm:w-auto">Simpan Artikel</button>
                        <button type="button" onclick="document.getElementById('addArticleModal').classList.add('hidden')" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-900 dark:text-white shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 sm:mt-0 sm:w-auto">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Artikel Eksternal</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Feed Google News kesehatan/halal yang bisa dipakai admin untuk referensi konten dan halaman promosi.</p>
        </div>
        <form action="{{ route('admin.articles.index') }}" method="GET" class="flex items-center gap-2 w-full lg:w-auto">
            <input type="text" name="external_q" value="{{ $externalQuery }}" placeholder="Cari artikel eksternal..." class="w-full lg:w-80 rounded-xl border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-primary focus:border-primary">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white font-semibold hover:bg-primary-dark transition">
                <span class="material-icons-round text-lg">travel_explore</span>
                Cari
            </button>
        </form>
    </div>
    <div class="p-5">
        @if(($externalArticles ?? collect())->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 px-6 py-14 text-center text-slate-400">
                <span class="material-icons-round text-5xl mb-3 block text-primary/60">rss_feed</span>
                Belum ada artikel eksternal yang cocok untuk pencarian ini.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($externalArticles as $article)
                    <article class="rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden bg-white dark:bg-slate-950 shadow-sm hover:-translate-y-1 transition-all">
                        <div class="relative h-44 bg-slate-100 dark:bg-slate-800">
                            @if(!empty($article['image_url']))
                                <img src="{{ $article['image_url'] }}" alt="{{ $article['title'] }}" class="w-full h-full object-cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="absolute inset-0 items-center justify-center {{ empty($article['image_url']) ? 'flex' : 'hidden' }} bg-gradient-to-br from-primary/10 to-emerald-50 dark:from-primary/10 dark:to-slate-900">
                                <span class="material-icons-round text-4xl text-primary/70">newspaper</span>
                            </div>
                            <span class="absolute top-3 left-3 inline-flex items-center px-2.5 py-1 rounded-full bg-primary text-white text-[10px] font-black uppercase tracking-[0.18em]">
                                {{ $article['source'] ?? 'Google News' }}
                            </span>
                        </div>
                        <div class="p-4">
                            <div class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">
                                {{ $article['published_label'] ?? 'Terbaru' }}
                            </div>
                            <h4 class="mt-2 text-base font-extrabold text-slate-900 dark:text-white line-clamp-2">{{ $article['title'] }}</h4>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 line-clamp-3">{{ $article['excerpt'] ?? 'Ringkasan artikel tidak tersedia.' }}</p>
                            <div class="mt-4 flex items-center justify-between gap-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-primary/10 text-primary text-[11px] font-bold">
                                    External Feed
                                </span>
                                <a href="{{ $article['source_url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:text-primary-dark">
                                    Buka Sumber
                                    <span class="material-icons-round text-base">open_in_new</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
