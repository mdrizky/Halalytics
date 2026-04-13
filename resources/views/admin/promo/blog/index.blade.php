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

<!-- Tabs -->
<div class="flex gap-2 mb-6">
    <button onclick="showTab('local')" id="tabLocal" class="px-4 py-2 rounded-lg text-sm font-bold transition bg-primary text-white shadow-sm">
        Artikel Lokal
    </button>
    <button onclick="showTab('external')" id="tabExternal" class="px-4 py-2 rounded-lg text-sm font-bold transition bg-primary/10 text-primary">
        Artikel Eksternal
    </button>
</div>

<!-- Local Articles Tab -->
<div id="panelLocal">
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
</div>

<!-- External Articles Tab -->
<div id="panelExternal" class="hidden">
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 mb-6">
        <div class="flex gap-3">
            <input type="text" id="externalSearchInput" class="flex-1 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" placeholder="Cari berita kesehatan, halal, gizi..." value="halal food health">
            <button onclick="fetchExternalArticles()" class="px-4 py-2 rounded-lg bg-primary text-white font-semibold hover:bg-primary-dark transition">
                <span class="material-icons-round text-lg">search</span>
            </button>
        </div>
    </div>
    
    <div id="externalLoading" class="hidden text-center py-8 text-slate-400">
        <span class="material-icons-round animate-spin text-4xl mb-2">refresh</span>
        <p>Mengambil artikel dari sumber eksternal...</p>
    </div>

    <div id="externalArticlesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach(($externalArticles ?? collect()) as $article)
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden hover:shadow-md transition-shadow" data-external-card="1">
                @if(!empty($article['image_url']))
                    <img src="{{ $article['image_url'] }}" alt="{{ $article['title'] }}" class="w-full h-40 object-cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                @endif
                <div class="h-40 w-full bg-gradient-to-br from-[#E0F2F1] via-white to-[#F4F9F8] items-center justify-center {{ !empty($article['image_url']) ? 'hidden' : 'flex' }}">
                    <span class="material-icons-round text-primary text-4xl">article</span>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-icons-round text-primary">public</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-primary bg-primary/10 px-2 py-0.5 rounded-full">{{ $article['source'] ?? 'external' }}</span>
                        <span class="text-[10px] text-slate-400 ml-auto">{{ $article['published_label'] ?? '-' }}</span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-2 line-clamp-2">{{ $article['title'] ?? '-' }}</h3>
                    <p class="text-xs text-slate-500 mb-4 line-clamp-3">{{ $article['excerpt'] ?? '-' }}</p>
                    <a href="{{ $article['source_url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-primary-dark transition">
                        Baca Selengkapnya <span class="material-icons-round text-sm">open_in_new</span>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div id="externalEmpty" class="{{ !($externalArticles ?? collect())->count() ? '' : 'hidden' }} text-center py-12 text-slate-400">
        <span class="material-icons-round text-4xl mb-2">article</span>
        <p>Klik tombol search untuk mengambil artikel dari sumber eksternal.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showTab(tab) {
    const tabLocal = document.getElementById('tabLocal');
    const tabExternal = document.getElementById('tabExternal');
    const panelLocal = document.getElementById('panelLocal');
    const panelExternal = document.getElementById('panelExternal');

    if (tab === 'local') {
        tabLocal.className = 'px-4 py-2 rounded-lg text-sm font-bold transition bg-primary text-white shadow-sm';
        tabExternal.className = 'px-4 py-2 rounded-lg text-sm font-bold transition bg-primary/10 text-primary';
        panelLocal.classList.remove('hidden');
        panelExternal.classList.add('hidden');
    } else {
        tabExternal.className = 'px-4 py-2 rounded-lg text-sm font-bold transition bg-primary text-white shadow-sm';
        tabLocal.className = 'px-4 py-2 rounded-lg text-sm font-bold transition bg-primary/10 text-primary';
        panelExternal.classList.remove('hidden');
        panelLocal.classList.add('hidden');
        
        // Auto-fetch on first tab open
        const grid = document.getElementById('externalArticlesGrid');
        if (!grid.querySelector('[data-external-card]') && grid.children.length === 0) {
            fetchExternalArticles();
        }
    }
}

async function fetchExternalArticles() {
    const query = document.getElementById('externalSearchInput').value.trim() || 'halal food health';
    const grid = document.getElementById('externalArticlesGrid');
    const loading = document.getElementById('externalLoading');
    const empty = document.getElementById('externalEmpty');

    grid.innerHTML = '';
    loading.classList.remove('hidden');
    empty.classList.add('hidden');

    try {
        const response = await fetch(`/api/articles?include_external=1&limit=24&q=${encodeURIComponent(query)}`);
        const payload = await response.json();
        const articles = (payload?.data || []).filter(item => item && item.source !== 'halalytics');

        loading.classList.add('hidden');

        if (articles.length === 0) {
            empty.classList.remove('hidden');
            empty.querySelector('p').textContent = 'Tidak ada artikel ditemukan. Coba kata kunci lain.';
            return;
        }

        articles.forEach(article => {
            const sourceName = article.source || 'external';
            const sourceUrl = article.source_url || '#';
            const safeTitle = escapeHtml(article.title || '-');
            const safeExcerpt = escapeHtml(article.excerpt || article.content || '-');
            const safeSourceName = escapeHtml(sourceName);
            const safeImageUrl = escapeHtml(article.image_url || '');
            const safeSourceUrl = escapeHtml(sourceUrl);
            const dateText = article.published_at ? new Date(article.published_at).toLocaleDateString('id-ID') : '-';
            const imageHtml = article.image_url
                ? `<img src="${safeImageUrl}" alt="${safeTitle}" class="w-full h-40 object-cover">`
                : `<div class="h-40 w-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                        <span class="material-icons-round text-slate-400 text-4xl">article</span>
                   </div>`;
            grid.innerHTML += `
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden hover:shadow-md transition-shadow" data-external-card="1">
                    ${imageHtml}
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-icons-round text-primary">public</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-primary bg-primary/10 px-2 py-0.5 rounded-full">${safeSourceName}</span>
                            <span class="text-[10px] text-slate-400 ml-auto">${dateText}</span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-2 line-clamp-2">${safeTitle}</h3>
                        <p class="text-xs text-slate-500 mb-4 line-clamp-3">${safeExcerpt}</p>
                        <a href="${safeSourceUrl}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-primary-dark transition">
                            Baca Selengkapnya <span class="material-icons-round text-sm">open_in_new</span>
                        </a>
                    </div>
                </div>
            `;
        });

    } catch (error) {
        loading.classList.add('hidden');
        grid.innerHTML = '<div class="col-span-3 text-center text-red-400 py-8">Error mengambil artikel. Silakan coba lagi.</div>';
        console.error('External articles error:', error);
    }
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

document.getElementById('externalSearchInput')?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); fetchExternalArticles(); }
});
</script>
@endpush
