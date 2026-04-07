<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HalalyticsApiTest extends TestCase
{
    public function test_mental_health_topics_endpoint_exists()
    {
        $response = $this->getJson('/api/mental-health/topics');
        $this->assertNotEquals(404, $response->status(), "Mental Health Topics endpoint is missing.");
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
