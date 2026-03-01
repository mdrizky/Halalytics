<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoBlog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HealthArticleController extends Controller
{
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

        $externalArticles = collect();
        if ($includeExternal) {
            $externalArticles = $this->fetchExternalRss($query)->take($limit);
        }

        $articles = $localArticles
            ->concat($externalArticles)
            ->sortByDesc(function ($item) {
                $time = data_get($item, 'published_at');
                return $time ? strtotime((string) $time) : 0;
            })
            ->take($limit)
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

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Artikel tidak ditemukan',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail artikel',
            'data' => [
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
            ],
        ]);
    }

    private function fetchExternalRss(string $query = '')
    {
        $feeds = [
            'https://sehatnegeriku.kemkes.go.id/feed/',
            'https://rss.detik.com/health',
        ];

        $result = collect();
        foreach ($feeds as $feedUrl) {
            try {
                $response = Http::timeout(5)->get($feedUrl);
                if (!$response->successful()) {
                    continue;
                }

                $xml = @simplexml_load_string($response->body());
                if (!$xml || !isset($xml->channel->item)) {
                    continue;
                }

                foreach ($xml->channel->item as $item) {
                    $title = trim((string) ($item->title ?? ''));
                    $description = trim(strip_tags((string) ($item->description ?? '')));
                    if ($query !== '') {
                        $haystack = Str::lower($title . ' ' . $description);
                        if (!Str::contains($haystack, Str::lower($query))) {
                            continue;
                        }
                    }

                    $link = trim((string) ($item->link ?? ''));
                    $publishedAtRaw = (string) ($item->pubDate ?? '');
                    $publishedAt = null;
                    if ($publishedAtRaw !== '') {
                        try {
                            $publishedAt = Carbon::parse($publishedAtRaw)->toIso8601String();
                        } catch (\Throwable $e) {
                            $publishedAt = null;
                        }
                    }

                    $result->push([
                        'id' => 'ext_' . md5($link . $title),
                        'slug' => 'ext_' . md5($link . $title),
                        'title' => $title,
                        'excerpt' => Str::limit($description, 180),
                        'content' => $description,
                        'category' => 'Berita Kesehatan',
                        'image_url' => null,
                        'published_at' => $publishedAt,
                        'source' => parse_url($feedUrl, PHP_URL_HOST) ?: 'rss',
                        'source_url' => $link,
                    ]);
                }
            } catch (\Throwable $e) {
                // Ignore external feed errors; local CMS articles still returned.
            }
        }

        return $result;
    }
}

