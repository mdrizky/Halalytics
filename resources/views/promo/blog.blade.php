@extends('promo.layout')
@section('title', 'Blog & Edukasi - ' . ($settings['site_name'] ?? 'HalalScan AI'))

@section('content')
<div class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl mb-4">Artikel Edukasi</h1>
            <p class="text-xl text-gray-500 max-w-2xl mx-auto">
                Temukan panduan, berita terbaru, dan informasi mendalam tentang makanan halal, bahan aktif obat, dan gaya hidup sehat.
            </p>
        </div>

        <!-- Filter & Search -->
        <div class="mb-12 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex gap-2 mb-4 md:mb-0 pb-2 overflow-x-auto w-full md:w-auto">
                <a href="{{ route('blog.index') }}" class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ !request('category') ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-green-50' }}">
                    Semua
                </a>
                @foreach($categories as $cat)
                <a href="{{ route('blog.index', ['category' => $cat]) }}" class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ request('category') == $cat ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-green-50' }}">
                    {{ $cat }}
                </a>
                @endforeach
            </div>

            <form action="{{ route('blog.index') }}" method="GET" class="w-full md:w-auto">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel..." class="w-full md:w-64 pl-4 pr-10 py-2 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Blog Grid -->
        @if($blogs->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($blogs as $blog)
            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover flex flex-col">
                <a href="{{ route('blog.show', $blog->slug) }}" class="block shrink-0">
                    @if($blog->image)
                    <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center">
                        <span class="text-5xl text-white opacity-50"><i class="bi bi-file-text"></i></span>
                    </div>
                    @endif
                </a>
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                            {{ $blog->category ?? 'Edukasi' }}
                        </span>
                        <span class="text-gray-400 text-xs">{{ $blog->formatted_date }}</span>
                    </div>
                    <a href="{{ route('blog.show', $blog->slug) }}" class="group">
                        <h3 class="font-bold text-gray-900 text-xl mb-2 group-hover:text-green-600 transition-colors line-clamp-2">{{ $blog->title }}</h3>
                    </a>
                    <p class="text-gray-500 text-sm mb-4 line-clamp-3">
                        {{ $blog->excerpt }}
                    </p>
                    <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-gray-400 text-xs"><i class="bi bi-eye mr-1"></i> {{ $blog->views }}x dibaca</span>
                        <a href="{{ route('blog.show', $blog->slug) }}" class="text-green-600 hover:text-green-700 font-semibold text-sm">Baca Selengkapnya &#8594;</a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-12 flex justify-center">
            {{ $blogs->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <div class="text-gray-300 text-6xl mb-4"><i class="bi bi-search"></i></div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Artikel Ditemukan</h3>
            <p class="text-gray-500">Maaf, kami tidak dapat menemukan artikel yang cocok dengan pencarian Anda.</p>
            @if(request('search') || request('category'))
            <div class="mt-6">
                <a href="{{ route('blog.index') }}" class="text-green-600 hover:underline">Tampilkan Semua Artikel</a>
            </div>
            @endif
        </div>
        @endif

    </div>
</div>
@endsection
