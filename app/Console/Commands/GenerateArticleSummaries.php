<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Services\GeminiService;
use Illuminate\Console\Command;

class GenerateArticleSummaries extends Command
{
    protected $signature = 'articles:summarize';
    protected $description = 'Generate AI summaries for all articles without one';

    public function handle(GeminiService $gemini)
    {
        $articles = Article::whereNull('ai_summary')->orWhere('ai_summary', '')->get();

        if ($articles->isEmpty()) {
            $this->info('All articles are already summarized.');
            return;
        }

        $this->info("Summarizing {$articles->count()} articles...");

        foreach ($articles as $article) {
            $prompt = "Tolong berikan ringkasan singkat dan menarik (maksimal 2 kalimat) untuk artikel berikut:\n\nJudul: {$article->title}\nKonten: " . strip_tags($article->content);
            
            try {
                $summary = $gemini->generateText($prompt);
                $article->update(['ai_summary' => trim($summary)]);
                $this->info("Summarized: {$article->title}");
            } catch (\Exception $e) {
                $this->error("Failed to summarize {$article->title}: " . $e->getMessage());
            }
        }

        $this->info('Summarization complete!');
    }
}
