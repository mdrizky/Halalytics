<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CacheService;
use App\Models\User;
use App\Models\ProductModel;
use App\Models\ScanModel;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\Cache;

class CacheServiceTest extends TestCase
{
    private CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheService = app(CacheService::class);
    }

    /**
     * 📊 Test getUserStats caching
     */
    public function test_get_user_stats_caches_data(): void
    {
        $user = User::factory()->create();
        
        // First call should cache the data
        $stats1 = $this->cacheService->getUserStats($user->id_user);
        
        // Second call should return cached data
        $stats2 = $this->cacheService->getUserStats($user->id_user);
        
        $this->assertEquals($stats1, $stats2);
        
        // Verify cache key exists
        $cacheKey = "user_stats:{$user->id_user}";
        $this->assertTrue(Cache::has($cacheKey));
    }

    /**
     * 📊 Test getDashboardStats caching
     */
    public function test_get_dashboard_stats_caches_data(): void
    {
        // First call should cache the data
        $stats1 = $this->cacheService->getDashboardStats();
        
        // Second call should return cached data
        $stats2 = $this->cacheService->getDashboardStats();
        
        $this->assertEquals($stats1, $stats2);
        
        // Verify cache key exists
        $this->assertTrue(Cache::has('dashboard_stats'));
    }

    /**
     * 🔍 Test getProductSearch caching
     */
    public function test_get_product_search_caches_data(): void
    {
        ProductModel::factory()->create(['nama_product' => 'Test Product']);
        
        $query = 'Test';
        $page = 1;
        
        // First call should cache the data
        $search1 = $this->cacheService->getProductSearch($query, $page);
        
        // Second call should return cached data
        $search2 = $this->cacheService->getProductSearch($query, $page);
        
        $this->assertEquals($search1, $search2);
        
        // Verify cache key exists
        $cacheKey = "product_search:" . md5($query) . ":page:{$page}";
        $this->assertTrue(Cache::has($cacheKey));
    }

    /**
     * 🏷️ Test getCategories caching
     */
    public function test_get_categories_caches_data(): void
    {
        KategoriModel::factory()->create(['nama_kategori' => 'Test Category']);
        
        // First call should cache the data
        $categories1 = $this->cacheService->getCategories();
        
        // Second call should return cached data
        $categories2 = $this->cacheService->getCategories();
        
        $this->assertEquals($categories1, $categories2);
        
        // Verify cache key exists
        $this->assertTrue(Cache::has('categories_all'));
    }

    /**
     * 🌟 Test getPopularProducts caching
     */
    public function test_get_popular_products_caches_data(): void
    {
        ProductModel::factory()->count(10)->create();
        ScanModel::factory()->count(20)->create();
        
        $limit = 5;
        
        // First call should cache the data
        $products1 = $this->cacheService->getPopularProducts($limit);
        
        // Second call should return cached data
        $products2 = $this->cacheService->getPopularProducts($limit);
        
        $this->assertEquals($products1, $products2);
        
        // Verify cache key exists
        $cacheKey = "popular_products:{$limit}";
        $this->assertTrue(Cache::has($cacheKey));
    }

    /**
     * 🗄️ Test clearCache functionality
     */
    public function test_clear_cache_removes_cache_entries(): void
    {
        // Create some cached data
        $this->cacheService->getDashboardStats();
        $this->cacheService->getCategories();
        
        // Verify cache exists
        $this->assertTrue(Cache::has('dashboard_stats'));
        $this->assertTrue(Cache::has('categories_all'));
        
        // Clear cache
        $cleared = $this->cacheService->clearCache();
        
        // Verify cache is cleared
        $this->assertFalse(Cache::has('dashboard_stats'));
        $this->assertFalse(Cache::has('categories_all'));
        $this->assertGreaterThan(0, $cleared);
    }

    /**
     * 🔄 Test warmUpCache functionality
     */
    public function test_warm_up_cache_creates_cache_entries(): void
    {
        // Clear all cache first
        Cache::flush();
        
        // Warm up cache
        $results = $this->cacheService->warmUpCache();
        
        // Verify cache entries are created
        $this->assertTrue(Cache::has('dashboard_stats'));
        $this->assertTrue(Cache::has('categories_all'));
        $this->assertTrue(Cache::has('popular_products:10'));
        $this->assertTrue(Cache::has('forbidden_ingredients'));
        
        // Verify results contain expected keys
        $expectedKeys = ['dashboard_stats', 'categories', 'popular_products', 'forbidden_ingredients'];
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $results);
        }
    }

    /**
     * 📊 Test getCacheStats returns data
     */
    public function test_get_cache_stats_returns_data(): void
    {
        $stats = $this->cacheService->getCacheStats();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('driver', $stats);
        $this->assertArrayHasKey('prefix', $stats);
    }

    /**
     * 📱 Test cacheApiResponse functionality
     */
    public function test_cache_api_response(): void
    {
        $endpoint = 'test-endpoint';
        $params = ['param1' => 'value1'];
        $data = ['result' => 'test_data'];
        
        // Cache data
        $this->cacheService->cacheApiResponse($endpoint, $params, $data);
        
        // Retrieve cached data
        $cached = $this->cacheService->cacheApiResponse($endpoint, $params);
        
        $this->assertEquals($data, $cached);
    }
}
