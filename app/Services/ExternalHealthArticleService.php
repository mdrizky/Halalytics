<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExternalHealthArticleService
{
    private array $defaultQueries = [
        'kesehatan halal',
        'nutrisi halal',
        'keamanan makanan',
        'obat halal',
        'halal food health',
    ];

    private array $topicFeeds = [
        'HEALTH' => 'Topik Kesehatan',
    ];

    private array $newsLocales = [
        ['hl' => 'id', 'gl' => 'ID', 'ceid' => 'ID:id'],
        ['hl' => 'en-US', 'gl' => 'US', 'ceid' => 'US:en'],
    ];

    public function search(string $query = '', int $limit = 12): Collection
    {
        $query = trim($query);
        $cacheKey = 'external_health_articles_v5_' . md5(Str::lower($query . '|' . $limit));

        return Cache::remember($cacheKey, 1800, function () use ($query, $limit) {
            $queries = $this->buildQueries($query);
            $topicArticles = collect($this->topicFeeds)
                ->flatMap(fn (string $label, string $topic) => $this->fetchGoogleNewsTopicFeed($topic, $label));

            $articles = collect($queries)
                ->flatMap(fn (string $term) => $this->fetchGoogleNewsRss($term))
                ->merge($topicArticles)
                ->unique('source_url')
                ->sortByDesc(fn (array $article) => strtotime((string) ($article['published_at'] ?? '')) ?: 0)
                ->take($limit)
                ->map(function (array $article, $index) {
                    // Hydrate all articles. Relies on caching to avoid timeouts on subsequent calls.
                    return $this->hydrateArticleImage($article);
                })
                ->values();

            if ($articles->count() < $limit && $query !== '') {
                return collect($this->defaultQueries)
                    ->flatMap(fn (string $term) => $this->fetchGoogleNewsRss($term))
                    ->merge($topicArticles)
                    ->unique('source_url')
                    ->sortByDesc(fn (array $article) => strtotime((string) ($article['published_at'] ?? '')) ?: 0)
                    ->take($limit)
                    ->map(fn (array $article) => $this->hydrateArticleImage($article))
                    ->values();
            }

            return $articles;
        });
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->search('', 40)->firstWhere('slug', $slug);
    }

    private function fetchGoogleNewsRss(string $query): Collection
    {
        return collect($this->newsLocales)
            ->flatMap(function (array $locale) use ($query) {
                $rssUrl = 'https://news.google.com/rss/search?q=' . urlencode($query)
                    . '&hl=' . urlencode($locale['hl'])
                    . '&gl=' . urlencode($locale['gl'])
                    . '&ceid=' . urlencode($locale['ceid']);

                return $this->fetchGoogleNewsFeed($rssUrl, $query);
            });
    }

    private function fetchGoogleNewsTopicFeed(string $topic, string $label): Collection
    {
        return collect($this->newsLocales)
            ->flatMap(function (array $locale) use ($topic, $label) {
                $rssUrl = 'https://news.google.com/rss/headlines/section/topic/' . urlencode($topic)
                    . '?hl=' . urlencode($locale['hl'])
                    . '&gl=' . urlencode($locale['gl'])
                    . '&ceid=' . urlencode($locale['ceid']);

                return $this->fetchGoogleNewsFeed($rssUrl, $label);
            });
    }

    private function fetchGoogleNewsFeed(string $rssUrl, string $queryLabel): Collection
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'Halalytics/1.0'])
                ->get($rssUrl);

            if (!$response->successful()) {
                return collect();
            }

            $xml = @simplexml_load_string($response->body());
            if (!$xml || !isset($xml->channel->item)) {
                return collect();
            }

            $items = $xml->xpath('//item') ?: [];

            return collect($items)->map(function ($item) use ($queryLabel) {
                $title = trim((string) ($item->title ?? ''));
                $description = trim(strip_tags((string) ($item->description ?? '')));
                $link = trim((string) ($item->link ?? ''));
                $publishedRaw = trim((string) ($item->pubDate ?? ''));
                $publishedAt = $publishedRaw !== '' ? Carbon::parse($publishedRaw)->toIso8601String() : null;
                $source = trim((string) ($item->source ?? 'Google News'));

                return [
                    'id' => 'ext_' . md5($link . $title),
                    'slug' => 'ext_' . md5($link . $title),
                    'title' => $title,
                    'excerpt' => Str::limit($description !== '' ? $description : $title, 180),
                    'content' => $description !== '' ? $description : $title,
                    'category' => 'Berita Kesehatan',
                    'image_url' => null,
                    'published_at' => $publishedAt,
                    'published_label' => $publishedAt ? Carbon::parse($publishedAt)->translatedFormat('d M Y') : null,
                    'source' => $source !== '' ? $source : 'Google News',
                    'source_url' => $link,
                    'is_external' => true,
                    'search_query' => $queryLabel,
                ];
            })->filter(fn (array $article) => $article['title'] !== '' && $article['source_url'] !== '');
        } catch (\Throwable $throwable) {
            Log::warning('ExternalHealthArticleService feed fetch failed', [
                'rss_url' => $rssUrl,
                'query_label' => $queryLabel,
                'error' => $throwable->getMessage(),
            ]);
            return collect();
        }
    }

    private function buildQueries(string $query): array
    {
        if ($query === '') {
            return $this->defaultQueries;
        }

        return collect([
            $query,
            $query . ' kesehatan',
            $query . ' halal',
            $query . ' nutrisi',
            $query . ' health',
            'halal ' . $query,
            'kesehatan ' . $query,
            'halal food health',
        ])
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function extractPreviewImage(string $url): ?string
    {
        if ($url === '') {
            return null;
        }

        return Cache::remember('external_article_image_' . md5($url), 21600, function () use ($url) {
            try {
                $html = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'Halalytics/1.0'])
                    ->get($url)
                    ->body();

                if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $matches)) {
                    return $matches[1];
                }

                if (preg_match('/<meta[^>]+name=["\']twitter:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $matches)) {
                    return $matches[1];
                }
            } catch (\Throwable $throwable) {
                Log::info('ExternalHealthArticleService image scrape failed', [
                    'url' => $url,
                    'error' => $throwable->getMessage(),
                ]);
                return null;
            }

            return null;
        });
    }

    private function hydrateArticleImage(array $article): array
    {
        $article['image_url'] = $this->extractPreviewImage((string) ($article['source_url'] ?? ''));

        return $article;
    }
}
