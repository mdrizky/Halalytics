<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
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

        $localArticles = Article::query()
            ->where('is_published', true)
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
            ->map(function (Article $article) {
                return [
                    'id' => (string) $article->id,
                    'slug' => (string) $article->slug,
                    'title' => (string) $article->title,
                    'excerpt' => (string) ($article->excerpt ?: Str::limit(strip_tags((string) $article->content), 170)),
                    'content' => (string) $article->content,
                    'ai_summary' => (string) $article->ai_summary,
                    'category' => (string) ($article->category ?: 'Kesehatan'),
                    'image_url' => $article->resolved_image,
                    'published_at' => optional($article->created_at)->toIso8601String(),
                    'source' => 'halalytics',
                    'source_url' => $article->source_url ?: route('blog.show', $article->slug),
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
        $article = Article::query()
            ->where('is_published', true)
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug)->orWhere('id', $slug);
            })
            ->first();

        if ($article) {
            // Trigger AI summary generation if missing
            if (!$article->ai_summary) {
                \Illuminate\Support\Facades\Artisan::queue('articles:summarize');
            }

            $payload = $this->normalizeArticlePayload([
                'id' => (string) $article->id,
                'slug' => (string) $article->slug,
                'title' => (string) $article->title,
                'excerpt' => (string) ($article->excerpt ?: Str::limit(strip_tags((string) $article->content), 170)),
                'content' => (string) $article->content,
                'ai_summary' => (string) $article->ai_summary,
                'category' => (string) ($article->category ?: 'Kesehatan'),
                'image_url' => $article->image,
                'published_at' => optional($article->created_at)->toIso8601String(),
                'source' => 'halalytics',
                'source_url' => $article->source_url ?: route('blog.show', $article->slug),
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

    public function recommended(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user();
        $limit = min(max((int) $request->query('limit', 5), 1), 15);

        // Get user's most scanned categories
        $favoriteCategories = [];
        if ($user) {
            $favoriteCategories = \App\Models\ScanHistory::where('user_id', $user->id_user)
                ->whereNotNull('category')
                ->select('category')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('category')
                ->orderByDesc('count')
                ->limit(3)
                ->pluck('category')
                ->toArray();
        }

        // If no user or no history, get random published articles
        $query = Article::where('is_published', true);

        if (!empty($favoriteCategories)) {
            $query->where(function ($q) use ($favoriteCategories) {
                foreach ($favoriteCategories as $cat) {
                    $q->orWhere('category', 'like', "%{$cat}%")
                      ->orWhere('title', 'like', "%{$cat}%")
                      ->orWhere('excerpt', 'like', "%{$cat}%");
                }
            });
        }

        $recommendations = $query->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(fn (Article $article) => $this->normalizeArticlePayload([
                'id' => (string) $article->id,
                'slug' => (string) $article->slug,
                'title' => (string) $article->title,
                'excerpt' => (string) ($article->excerpt ?: Str::limit(strip_tags((string) $article->content), 170)),
                'content' => (string) $article->content,
                'ai_summary' => (string) $article->ai_summary,
                'category' => (string) ($article->category ?: 'Kesehatan'),
                'image_url' => $article->image,
                'published_at' => optional($article->created_at)->toIso8601String(),
                'source' => 'halalytics',
                'source_url' => $article->source_url ?: route('blog.show', $article->slug),
                'is_external' => false,
            ]))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Rekomendasi artikel untuk Anda',
            'data' => $recommendations,
        ]);
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
            'ai_summary' => (string) data_get($article, 'ai_summary'),
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
