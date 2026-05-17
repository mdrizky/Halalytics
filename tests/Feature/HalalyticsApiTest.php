<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class HalalyticsApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Hindari false negative bila `php artisan route:cache` pernah dijalankan lokal/CI
        if ($this->app->routesAreCached()) {
            $this->artisan('route:clear');
        }
    }

    public function test_mental_health_topics_endpoint_exists()
    {
        $response = $this->getJson('/api/mental-health/topics');
        $this->assertNotEquals(404, $response->status(), "Mental Health Topics endpoint is missing.");
    }

    public function test_sync_scan_logs_route_exists_and_syncs_for_authenticated_user(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'active' => true,
        ]);

        $token = $user->createToken('phpunit')->plainTextToken;

        $payload = [
            'logs' => [
                [
                    'barcode' => '1234567890123',
                    'product_name' => 'PHPUnit Offline Product',
                    'halal_status' => 'halal',
                    'ai_analysis' => null,
                    'scanned_at' => (int) round(microtime(true) * 1000),
                ],
            ],
        ];

        $response = $this->postJson('/api/sync/scan-logs', $payload, [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'message', 'count']);
    }

    public function test_help_categories_endpoint_exists()
    {
        $response = $this->getJson('/api/help/categories');
        $this->assertNotEquals(404, $response->status(), "Help Categories endpoint is missing.");
    }

    public function test_popular_products_endpoint_exists()
    {
        $response = $this->getJson('/api/v1/products/popular');
        $this->assertNotEquals(404, $response->status(), "Popular Products endpoint is missing.");
    }
}
