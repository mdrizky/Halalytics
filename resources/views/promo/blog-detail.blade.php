@extends('promo.layout')

@section('title', $blog->title . ' - ' . ($settings['site_name'] ?? 'HalalScan AI'))
@section('description', $blog->excerpt)

@section('content')
<!-- Blog Header Section -->
<div class="relative bg-gray-900 overflow-hidden pt-24 pb-16 lg:pt-32 lg:pb-24">
    <!-- Background Image with Overlay -->
    @if($blog->image)
    <div class="absolute inset-0">
        <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="w-full h-full object-cover opacity-30">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent"></div>
    </div>
    @else
    <div class="absolute inset-0 bg-gradient-to-br from-green-900 to-blue-900 opacity-80"></div>
    @endif

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full mb-6 uppercase tracking-wider">
            {{ $blog->category ?? 'Edukasi' }}
        </span>
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-6 shadow-sm">
            {{ $blog->title }}
        </h1>
        <div class="flex items-center justify-center space-x-4 text-gray-300 text-sm">
            <span class="flex items-center"><svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> {{ $blog->formatted_date }}</span>
            <span>&bull;</span>
            <span class="flex items-center"><svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg> {{ $blog->views }} Kali Dibaca</span>
        </div>
    </div>
</div>

<!-- Blog Content -->
<div class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="prose prose-lg prose-green mx-auto text-gray-700">
            {!! $blog->content !!}
        </div>

        <!-- Share & Tags -->
        <div class="mt-16 pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <span class="font-semibold text-gray-900">Bagikan artikel ini:</span>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($blog->title) }}&url={{ urlencode(request()->url()) }}" target="_blank" class="text-blue-400 hover:text-blue-500 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="text-blue-600 hover:text-blue-700 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <a href="https://wa.me/?text={{ urlencode($blog->title . ' - ' . request()->url()) }}" target="_blank" class="text-green-500 hover:text-green-600 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                </a>
            </div>
            <a href="{{ route('blog.index') }}" class="text-green-600 font-semibold hover:text-green-700 transition-colors flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Blog
            </a>
        </div>
    </div>
</div>

<!-- Related Blogs -->
@if($relatedBlogs->count() > 0)
<div class="bg-gray-50 py-16 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Artikel Terkait</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedBlogs as $related)
            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
                <a href="{{ route('blog.show', $related->slug) }}" class="block">
                    @if($related->image)
                    <img src="{{ $related->image_url }}" alt="{{ $related->title }}" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center">
                        <span class="text-5xl opacity-50"><i class="bi bi-file-text"></i></span>
                    </div>
                    @endif
                </a>
                <div class="p-6">
                    <span class="text-green-600 text-xs font-semibold uppercase tracking-wider mb-2 block">
                        {{ $related->category ?? 'Edukasi' }}
                    </span>
                    <a href="{{ route('blog.show', $related->slug) }}" class="group">
                        <h3 class="font-bold text-gray-900 text-lg mb-2 group-hover:text-green-600 transition-colors line-clamp-2">
                            {{ $related->title }}
                        </h3>
                    </a>
                    <p class="text-gray-500 text-sm line-clamp-2">
                        {{ $related->excerpt }}
                    </p>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
