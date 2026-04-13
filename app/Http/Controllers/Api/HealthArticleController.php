<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoBlog;
use App\Services\DisplayImageService;
use App\Services\ExternalHealthArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class HealthArticleController extends Controller
{
    public function __construct(
        private readonly ExternalHealthArticleService $externalArticles,
        private readonly DisplayImageService $displayImageService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 20), 1), 50);
        $query = trim((string) $request->query('q', ''));
        $includeExternal = $request->boolean('include_external', true);

        $localArticles = PromoBlog::query()
            ->where('status', 'published')
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($qq) use ($query) {
                    $qq->where('title', 'like', "%{$query}%")
                        ->orWhere('excerpt', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%")
                        ->orWhere('category', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (PromoBlog $blog) {
                return [
                    'id' => (string) $blog->id,
                    'slug' => (string) $blog->slug,
                    'title' => (string) $blog->title,
                    'excerpt' => (string) ($blog->excerpt ?: Str::limit(strip_tags((string) $blog->content), 170)),
                    'content' => (string) $blog->content,
                    'category' => (string) ($blog->category ?: 'Kesehatan'),
                    'image_url' => $blog->image ? asset('storage/' . $blog->image) : null,
                    'published_at' => optional($blog->created_at)->toIso8601String(),
                    'source' => 'halalytics',
                    'source_url' => route('blog.show', $blog->slug),
                ];
            })
            ->values();

        try {
            $externalArticles = $includeExternal
                ? $this->externalArticles->search($query, $limit)
                : collect();
        } catch (\Throwable $throwable) {
            Log::warning('HealthArticleController external article fetch failed', [
                'query' => $query,
                'limit' => $limit,
                'error' => $throwable->getMessage(),
            ]);
            $externalArticles = collect();
        }

        $articles = $localArticles
            ->concat($externalArticles)
            ->sortByDesc(function ($item) {
                $time = data_get($item, 'published_at');
                return $time ? strtotime((string) $time) : 0;
            })
            ->take($limit)
            ->map(fn (array $article) => $this->normalizeArticlePayload($article))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Artikel kesehatan berhasil dimuat',
            'data' => $articles,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $blog = PromoBlog::query()
            ->where('status', 'published')
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug)->orWhere('id', $slug);
            })
            ->first();

        if ($blog) {
            $payload = $this->normalizeArticlePayload([
                'id' => (string) $blog->id,
                'slug' => (string) $blog->slug,
                'title' => (string) $blog->title,
                'excerpt' => (string) ($blog->excerpt ?: Str::limit(strip_tags((string) $blog->content), 170)),
                'content' => (string) $blog->content,
                'category' => (string) ($blog->category ?: 'Kesehatan'),
                'image_url' => $blog->image ? asset('storage/' . $blog->image) : null,
                'published_at' => optional($blog->created_at)->toIso8601String(),
                'source' => 'halalytics',
                'source_url' => route('blog.show', $blog->slug),
                'is_external' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detail artikel',
                'data' => $payload,
            ]);
        }

        try {
            $externalArticle = $this->externalArticles->findBySlug($slug);
        } catch (\Throwable $throwable) {
            Log::warning('HealthArticleController external article detail failed', [
                'slug' => $slug,
                'error' => $throwable->getMessage(),
            ]);
            $externalArticle = null;
        }
        if ($externalArticle) {
            return response()->json([
                'success' => true,
                'message' => 'Detail artikel eksternal',
                'data' => $this->normalizeArticlePayload($externalArticle),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Artikel tidak ditemukan',
            'data' => null,
        ], 404);
    }

    private function normalizeArticlePayload(array $article): array
    {
        $title = trim((string) data_get($article, 'title', 'Artikel Kesehatan Halalytics'));
        $excerpt = trim((string) data_get($article, 'excerpt', ''));
        $content = trim((string) data_get($article, 'content', ''));

        return [
            'id' => (string) data_get($article, 'id', Str::slug($title)),
            'slug' => (string) data_get($article, 'slug', Str::slug($title)),
            'title' => $title,
            'excerpt' => $excerpt !== '' ? $excerpt : Str::limit(strip_tags($content !== '' ? $content : $title), 170),
            'content' => $content !== '' ? $content : 'Konten artikel sedang diperbarui. Silakan buka sumber artikel untuk membaca detail lengkap.',
            'category' => (string) data_get($article, 'category', 'Kesehatan'),
            'image_url' => $this->displayImageService->resolve(
                data_get($article, 'image_url'),
                [
                    'name' => $title,
                    'category' => data_get($article, 'category', 'article'),
                ],
                'article'
            ),
            'published_at' => data_get($article, 'published_at'),
            'source' => (string) data_get($article, 'source', 'Halalytics'),
            'source_url' => data_get($article, 'source_url'),
            'is_external' => (bool) data_get($article, 'is_external', false),
        ];
    }
}
