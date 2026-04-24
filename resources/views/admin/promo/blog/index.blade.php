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

<!-- Local Articles Grid -->
<div id="panelLocal" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
    @forelse($blogs as $blog)
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all overflow-hidden group flex flex-col">
        <div class="aspect-video relative overflow-hidden bg-slate-100 dark:bg-slate-800">
            @if($blog->image)
                <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20">
                    <span class="material-icons-round text-emerald-500/30 text-6xl">article</span>
                </div>
            @endif
            <div class="absolute top-4 left-4">
                <span class="px-2.5 py-1 rounded-lg bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm text-[10px] font-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-sm uppercase tracking-wider">
                    {{ $blog->category ?: 'Uncategorized' }}
                </span>
            </div>
            <div class="absolute top-4 right-4">
                @if($blog->status == 'published')
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.5)]"></span>
                @else
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                @endif
            </div>
        </div>
        
        <div class="p-6 flex-1 flex flex-col">
            <h4 class="text-base font-extrabold text-slate-800 dark:text-white leading-tight mb-2 line-clamp-2">{{ $blog->title }}</h4>
            <div class="flex items-center gap-4 text-xs text-slate-400 mb-6">
                <div class="flex items-center gap-1">
                    <span class="material-icons-round text-sm">calendar_today</span>
                    {{ $blog->formatted_date }}
                </div>
                <div class="flex items-center gap-1">
                    <span class="material-icons-round text-sm">visibility</span>
                    {{ number_format($blog->views) }}
                </div>
            </div>
            
            <div class="mt-auto pt-4 border-t border-slate-50 dark:border-slate-800 flex justify-between items-center">
                <div class="flex gap-1">
                    <a href="{{ route('admin.promo.blog.edit', $blog->id) }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-primary transition-colors border border-slate-200 dark:border-slate-700">
                        <span class="material-icons-round text-[18px]">edit</span>
                    </a>
                    <form action="{{ route('admin.promo.blog.destroy', $blog->id) }}" method="POST" onsubmit="return confirm('Hapus artikel?');">
                        @csrf @method('DELETE')
                        <button class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-red-500 transition-colors border border-slate-200 dark:border-slate-700">
                            <span class="material-icons-round text-[18px]">delete</span>
                        </button>
                    </form>
                </div>
                <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="px-4 py-1.5 rounded-lg bg-primary/10 text-primary text-xs font-bold hover:bg-primary hover:text-white transition-all">Lihat</a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-20 text-center bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-200 dark:border-slate-800">
        <span class="material-icons-round text-5xl text-slate-200 mb-4">edit_note</span>
        <p class="text-slate-400 font-medium">Belum ada artikel lokal. Mulai tulis sekarang!</p>
    </div>
    @endforelse
</div>
<div class="mb-8" id="paginationLocal">
    {{ $blogs->links() }}
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
        tabLocal.className = 'px-5 py-2.5 rounded-xl text-sm font-bold transition-all bg-primary text-white shadow-lg shadow-emerald-900/10';
        tabExternal.className = 'px-5 py-2.5 rounded-xl text-sm font-bold transition-all text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800';
        panelLocal.classList.remove('hidden');
        document.getElementById('paginationLocal')?.classList.remove('hidden'); // Fix for pagination container
        panelExternal.classList.add('hidden');
    } else {
        tabExternal.className = 'px-5 py-2.5 rounded-xl text-sm font-bold transition-all bg-primary text-white shadow-lg shadow-emerald-900/10';
        tabLocal.className = 'px-5 py-2.5 rounded-xl text-sm font-bold transition-all text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800';
        panelExternal.classList.remove('hidden');
        panelLocal.classList.add('hidden');
        document.getElementById('paginationLocal')?.classList.add('hidden'); // Hide pagination on external tab
        
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
