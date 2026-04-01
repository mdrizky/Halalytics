@extends('promo.layout')
@section('title', 'Blog & Edukasi - ' . ($settings['site_name'] ?? 'HalalScan AI'))
@section('description', 'Artikel edukasi halal, keamanan obat, nutrisi, dan tips kesehatan praktis dari HalalScan AI.')
@section('keywords', 'blog halal, edukasi kesehatan, interaksi obat, nutrisi, BPOM')
@section('canonical', route('blog.index', request()->only(['category', 'search'])))

@section('schema')
{
  "@context": "https://schema.org",
  "@type": "Blog",
  "name": "Blog HalalScan AI",
  "description": "Artikel edukasi halal, obat, dan kesehatan",
  "url": "{{ route('blog.index') }}",
  "publisher": {
    "@type": "Organization",
    "name": "{{ $settings['site_name'] ?? 'HalalScan AI' }}"
  }
}
@endsection

@section('styles')
<style>
    .blog-hero {
        background:
            radial-gradient(900px 420px at 100% -10%, rgba(31,79,214,.24), transparent 60%),
            radial-gradient(900px 420px at 0% 0%, rgba(14,165,107,.24), transparent 58%),
            linear-gradient(180deg, #f5f9f7 0%, #f7f9ff 100%);
    }
    .blog-card {
        background: #fff;
        border: 1px solid #dbe3ea;
        border-radius: 20px;
        transition: transform .22s ease, box-shadow .22s ease;
    }
    .blog-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 36px rgba(15, 23, 42, .10);
    }
</style>
@endsection

@section('content')
<section class="blog-hero pt-24 pb-14 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <span class="inline-flex px-4 py-2 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wider">Editorial</span>
            <h1 class="mt-5 text-4xl md:text-5xl font-extrabold text-slate-900">Artikel Edukasi Halal & Kesehatan</h1>
            <p class="mt-4 text-lg text-slate-600">
                Panduan praktis berbasis data untuk bantu kamu mengambil keputusan konsumsi yang lebih aman.
            </p>
        </div>
    </div>
</section>

<section class="py-12 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
            <div class="flex gap-2 overflow-x-auto w-full lg:w-auto pb-2">
                <a href="{{ route('blog.index') }}" class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap {{ !request('category') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('blog.index', ['category' => $cat, 'search' => request('search')]) }}" class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap {{ request('category') == $cat ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
                    {{ $cat }}
                </a>
                @endforeach
            </div>

            <form action="{{ route('blog.index') }}" method="GET" class="w-full lg:w-auto">
                @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel..." class="w-full lg:w-80 pl-4 pr-11 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <button type="submit" class="absolute right-3 top-3 text-slate-400 hover:text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m1.7-4.8a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="py-14 bg-slate-50 min-h-[40vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($blogs->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
            @foreach($blogs as $blog)
            <article class="blog-card overflow-hidden flex flex-col">
                <a href="{{ route('blog.show', $blog->slug) }}" class="block shrink-0">
                    @if($blog->image)
                    <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="w-full h-52 object-cover">
                    @else
                    <div class="w-full h-52 bg-gradient-to-br from-emerald-200 to-blue-200"></div>
                    @endif
                </a>
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-block bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full">
                            {{ $blog->category ?? 'Edukasi' }}
                        </span>
                        <span class="text-slate-400 text-xs">{{ $blog->formatted_date }}</span>
                    </div>

                    <a href="{{ route('blog.show', $blog->slug) }}" class="group">
                        <h2 class="font-extrabold text-slate-900 text-xl leading-snug group-hover:text-emerald-700 transition-colors line-clamp-2">
                            {{ $blog->title }}
                        </h2>
                    </a>
                    <p class="text-slate-600 text-sm mt-2 line-clamp-3">
                        {{ $blog->excerpt }}
                    </p>

                    <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-slate-400 text-xs">{{ $blog->views }}x dibaca</span>
                        <a href="{{ route('blog.show', $blog->slug) }}" class="text-emerald-700 hover:text-emerald-800 font-bold text-sm">Baca</a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <div class="mt-12 flex justify-center">
            {{ $blogs->links() }}
        </div>
        @else
        <div class="text-center py-20 bg-white border border-slate-200 rounded-2xl">
            <h3 class="text-xl font-extrabold text-slate-900 mb-2">Artikel tidak ditemukan</h3>
            <p class="text-slate-500">Ubah kata kunci atau filter kategori untuk hasil lain.</p>
            <a href="{{ route('blog.index') }}" class="inline-flex mt-6 text-emerald-700 font-bold hover:text-emerald-800">Reset pencarian</a>
        </div>
        @endif
    </div>
</section>
@endsection
