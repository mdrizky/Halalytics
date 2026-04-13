@extends('promo.layout')

@section('title', $blog->title . ' - ' . ($settings['site_name'] ?? 'HalalScan AI'))
@section('description', $blog->excerpt ?: 'Baca artikel edukasi halal dan kesehatan dari HalalScan AI.')
@section('keywords', ($blog->category ?? 'edukasi') . ', halal, kesehatan, interaksi obat, nutrisi')
@section('canonical', route('blog.show', $blog->slug))
@section('og_image', $blog->image_url ?? asset('images/logo.png'))

@section('schema')
@php
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $blog->title,
        'description' => $blog->excerpt ?: 'Artikel edukasi halal dan kesehatan',
        'image' => $blog->image_url ? [$blog->image_url] : [asset('images/logo.png')],
        'datePublished' => optional($blog->created_at)->toIso8601String(),
        'dateModified' => optional($blog->updated_at)->toIso8601String(),
        'mainEntityOfPage' => route('blog.show', $blog->slug),
        'author' => [
            '@type' => 'Organization',
            'name' => $settings['site_name'] ?? 'HalalScan AI',
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => $settings['site_name'] ?? 'HalalScan AI',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('images/logo.png'),
            ],
        ],
    ];
@endphp
{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
@endsection

@section('styles')
<style>
    .article-hero {
        background:
            radial-gradient(900px 420px at 100% -10%, rgba(0, 187, 194, .22), transparent 60%),
            radial-gradient(900px 420px at 0% 0%, rgba(16, 185, 129, .32), transparent 58%),
            linear-gradient(135deg, #0c1f18 0%, #124734 100%);
    }
    .article-wrap {
        border: 1px solid #dbe3ea;
        border-radius: 24px;
        background: #fff;
    }
    .article-content {
        color: #334155;
        line-height: 1.8;
    }
    .article-content h2,
    .article-content h3,
    .article-content h4 {
        color: #0f172a;
        margin-top: 1.4em;
        margin-bottom: .6em;
        font-weight: 800;
    }
    .article-content p { margin-bottom: 1.05em; }
    .article-content ul,
    .article-content ol {
        margin-left: 1.2rem;
        margin-bottom: 1.05em;
    }
    .related-card {
        border: 1px solid #dbe3ea;
        border-radius: 20px;
        background: #fff;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .related-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 32px rgba(15, 23, 42, .10);
    }
</style>
@endsection

@section('content')
<section class="article-hero text-white pt-24 pb-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <span class="inline-flex bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                {{ $blog->category ?? 'Edukasi' }}
            </span>
            <h1 class="mt-5 text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight">{{ $blog->title }}</h1>
            <div class="mt-5 flex items-center justify-center gap-4 text-white/75 text-sm">
                <span>{{ $blog->formatted_date }}</span>
                <span>•</span>
                <span>{{ $blog->views }}x dibaca</span>
            </div>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <article class="article-wrap overflow-hidden">
            @if($blog->image)
            <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="w-full h-[320px] md:h-[420px] object-cover">
            @endif
            <div class="p-6 md:p-10">
                <div class="article-content">
                    {!! $blog->content !!}
                </div>

                <div class="mt-10 pt-6 border-t border-slate-200 flex flex-col md:flex-row justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="font-semibold text-slate-700 text-sm">Bagikan:</span>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($blog->title) }}&url={{ urlencode(route('blog.show', $blog->slug)) }}" target="_blank" class="text-slate-500 hover:text-primary text-sm font-semibold">X/Twitter</a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $blog->slug)) }}" target="_blank" class="text-slate-500 hover:text-emerald-700 text-sm font-semibold">Facebook</a>
                        <a href="https://wa.me/?text={{ urlencode($blog->title . ' - ' . route('blog.show', $blog->slug)) }}" target="_blank" class="text-slate-500 hover:text-green-600 text-sm font-semibold">WhatsApp</a>
                    </div>
                    <a href="{{ route('blog.index') }}" class="text-emerald-700 hover:text-emerald-800 text-sm font-bold">
                        ← Kembali ke daftar blog
                    </a>
                </div>
            </div>
        </article>
    </div>
</section>

@if($relatedBlogs->count() > 0)
<section class="py-14 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-8">Artikel Terkait</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-7">
            @foreach($relatedBlogs as $related)
            <article class="related-card overflow-hidden">
                <a href="{{ route('blog.show', $related->slug) }}" class="block">
                    @if($related->image)
                    <img src="{{ $related->image_url }}" alt="{{ $related->title }}" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gradient-to-br from-emerald-200 via-teal-100 to-primary/20"></div>
                    @endif
                </a>
                <div class="p-6">
                    <span class="inline-flex px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">{{ $related->category ?? 'Edukasi' }}</span>
                    <a href="{{ route('blog.show', $related->slug) }}" class="block mt-3">
                        <h3 class="font-extrabold text-slate-900 leading-snug hover:text-emerald-700 transition-colors line-clamp-2">{{ $related->title }}</h3>
                    </a>
                    <p class="text-slate-600 text-sm mt-2 line-clamp-2">{{ $related->excerpt }}</p>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
