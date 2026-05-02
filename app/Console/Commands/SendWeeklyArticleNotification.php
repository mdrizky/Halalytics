<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\UserFcmToken;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;

class SendWeeklyArticleNotification extends Command
{
    protected $signature = 'articles:notify-important';
    protected $description = 'Send push notifications for important health articles';

    public function handle(PushNotificationService $pushService)
    {
        $article = Article::where('status', 'published')->latest()->first();

        if (!$article) {
            $this->error('No articles found to notify.');
            return;
        }

        $tokens = UserFcmToken::pluck('token')->toArray();

        if (empty($tokens)) {
            $this->warn('No user tokens found.');
            return;
        }

        $this->info("Sending notification for: {$article->title}");

        $pushService->sendMulticast(
            $tokens,
            "Artikel Penting Hari Ini! 🌟",
            $article->title,
            [
                'type' => 'article',
                'id' => (string) $article->id,
                'slug' => (string) $article->slug,
                'image' => (string) $article->image_url,
            ]
        );

        $this->info('Notifications sent!');
    }
}
