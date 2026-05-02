<?php

namespace App\Observers;

use App\Models\Article;
use App\Services\GeminiService;

class ArticleObserver
{
    /**
     * Handle the Article "saving" event.
     */
    public function saving(Article $article): void
    {
        // If content is dirty and ai_summary is empty or content changed significantly
        if ($article->isDirty('content') && empty($article->ai_summary)) {
            try {
                $geminiService = app(GeminiService::class);
                $summary = $geminiService->summarizeArticle($article->title, strip_tags($article->content));
                $article->ai_summary = $summary;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to auto-generate AI summary for article', [
                    'article_id' => $article->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
