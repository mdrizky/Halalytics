<?php

namespace App\Jobs;

use App\Models\ApiHealthLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiHealthCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 120;

    public function handle(): void
    {
        $apis = [
            'gemini' => [
                'url' => 'https://generativelanguage.googleapis.com/v1/models?key=' . config('services.gemini.api_key'),
                'method' => 'GET',
            ],
            'openfoodfacts' => [
                'url' => 'https://world.openfoodfacts.org/api/v2/search?page_size=1',
                'method' => 'GET',
            ],
            'fda' => [
                'url' => 'https://api.fda.gov/drug/label.json?limit=1',
                'method' => 'GET',
            ],
            'openbeautyfacts' => [
                'url' => 'https://world.openbeautyfacts.org/api/v2/search?page_size=1',
                'method' => 'GET',
            ],
        ];

        foreach ($apis as $name => $config) {
            try {
                $start = microtime(true);
                $response = Http::timeout(15)
                    ->withoutVerifying()
                    ->get($config['url']);
                $latency = round((microtime(true) - $start) * 1000, 1);

                $status = 'up';
                if (!$response->successful()) {
                    $status = 'down';
                } elseif ($latency > 3000) {
                    $status = 'slow';
                } elseif ($latency > 1500) {
                    $status = 'degraded';
                }

                ApiHealthLog::create([
                    'api_name' => $name,
                    'status' => $status,
                    'latency_ms' => $latency,
                    'http_status' => $response->status(),
                    'error_details' => $response->successful() ? null : $response->body(),
                    'checked_at' => now(),
                ]);
            } catch (\Exception $e) {
                ApiHealthLog::create([
                    'api_name' => $name,
                    'status' => 'down',
                    'latency_ms' => null,
                    'http_status' => null,
                    'error_details' => $e->getMessage(),
                    'checked_at' => now(),
                ]);

                Log::channel('daily')->warning("API Health Check failed: {$name}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Cleanup logs older than 30 days
        ApiHealthLog::where('checked_at', '<', now()->subDays(30))->delete();
    }
}
