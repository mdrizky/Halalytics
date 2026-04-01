<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanOldCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 300;

    public function handle(): void
    {
        $totalCleaned = 0;

        // Clean old Gemini logs (older than 30 days)
        $logPath = storage_path('logs/gemini.log');
        if (file_exists($logPath) && filesize($logPath) > 50 * 1024 * 1024) {
            // Rotate if > 50MB
            $backupPath = storage_path('logs/gemini-' . now()->format('Y-m-d') . '.log');
            rename($logPath, $backupPath);
            Log::info("Rotated gemini.log ({$backupPath})");
        }

        // Clean old temp uploads (older than 7 days)
        $tempFiles = Storage::disk('local')->files('temp');
        foreach ($tempFiles as $file) {
            $lastModified = Storage::disk('local')->lastModified($file);
            if ($lastModified < now()->subDays(7)->timestamp) {
                Storage::disk('local')->delete($file);
                $totalCleaned++;
            }
        }

        // Clean old OCR uploads not approved (older than 30 days)
        $ocrFiles = Storage::disk('public')->files('ocr_submissions');
        foreach ($ocrFiles as $file) {
            $lastModified = Storage::disk('public')->lastModified($file);
            if ($lastModified < now()->subDays(30)->timestamp) {
                Storage::disk('public')->delete($file);
                $totalCleaned++;
            }
        }

        // Clean API health logs older than 60 days
        $deleted = \App\Models\ApiHealthLog::where('checked_at', '<', now()->subDays(60))->delete();
        $totalCleaned += $deleted;

        // Clean old AI usage logs older than 90 days
        $deleted = \App\Models\AiUsageLog::where('created_at', '<', now()->subDays(90))->delete();
        $totalCleaned += $deleted;

        Log::info("Cache cleanup complete. Removed {$totalCleaned} items.");
    }
}
