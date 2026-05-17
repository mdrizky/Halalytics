<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\OCRService;
use App\Models\User;
use App\Models\OCRProduct;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class OCRServiceTest extends TestCase
{
    private OCRService $ocrService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ocrService = app(OCRService::class);
        Storage::fake('public');
    }

    /**
     * 📷 Test extractTextFromImage with mock response
     */
    public function test_extract_text_from_image_with_mock(): void
    {
        // Mock HTTP response for Google Vision API
        Http::fake([
            'vision.googleapis.com/*' => Http::response([
                'responses' => [
                    [
                        'fullTextAnnotation' => [
                            'text' => 'Ingredients: Water, Sugar, Salt, Natural Flavors'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $user = User::factory()->create();
        $image = UploadedFile::fake()->create('product.jpg', 120, 'image/jpeg');

        $result = $this->ocrService->extractTextFromImage($image, $user);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('text', $result);
        $this->assertArrayHasKey('ingredients', $result);
        $this->assertArrayHasKey('confidence', $result);
        $this->assertArrayHasKey('method', $result);
        $this->assertEquals('google_vision', $result['method']);
        $this->assertStringContainsString('Water, Sugar, Salt', $result['text']);
    }

    /**
     * 📷 Test extractTextFromImage fallback to Gemini
     */
    public function test_extract_text_from_image_fallback_to_gemini(): void
    {
        // Mock Google Vision API failure
        Http::fake([
            'https://vision.googleapis.com/*' => Http::response(['error' => 'API Error'], 500),
            // Mock Gemini API success
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => 'Ingredients: Water, Sugar, Salt, Natural Flavors'
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $user = User::factory()->create();
        $image = UploadedFile::fake()->create('product.jpg', 120, 'image/jpeg');

        $result = $this->ocrService->extractTextFromImage($image, $user);

        $this->assertIsArray($result);
        $this->assertEquals('gemini', $result['method']);
        $this->assertStringContainsString('Water, Sugar, Salt', $result['text']);
    }

    /**
     * 📷 Test extractTextFromImage with invalid image
     */
    public function test_extract_text_from_image_with_invalid_image(): void
    {
        $user = User::factory()->create();
        $image = UploadedFile::fake()->create('invalid.txt', 100);

        $result = $this->ocrService->extractTextFromImage($image, $user);

        $this->assertIsArray($result);
        $this->assertEquals('mock', $result['method']);
        $this->assertArrayHasKey('text', $result);
    }

    /**
     * 🔍 Test analyzeIngredients with halal ingredients
     */
    public function test_analyze_ingredients_halal(): void
    {
        $ingredients = ['Water', 'Sugar', 'Salt', 'Natural Flavors'];
        $text = 'Ingredients: Water, Sugar, Salt, Natural Flavors';

        $analysis = $this->ocrService->analyzeIngredients($ingredients, $text);

        $this->assertIsArray($analysis);
        $this->assertArrayHasKey('overall_status', $analysis);
        $this->assertArrayHasKey('confidence', $analysis);
        $this->assertArrayHasKey('recommendation', $analysis);
        $this->assertArrayHasKey('ingredients', $analysis);
        $this->assertEquals('halal', $analysis['overall_status']);
        $this->assertGreaterThan(70, $analysis['confidence']);
    }

    /**
     * 🔍 Test analyzeIngredients with haram ingredients
     */
    public function test_analyze_ingredients_haram(): void
    {
        $ingredients = ['Pork', 'Gelatin', 'Alcohol'];
        $text = 'Ingredients: Pork, Gelatin, Alcohol';

        $analysis = $this->ocrService->analyzeIngredients($ingredients, $text);

        $this->assertIsArray($analysis);
        $this->assertEquals('haram', $analysis['overall_status']);
        $this->assertGreaterThan(70, $analysis['confidence']);
    }

    /**
     * 🔍 Test analyzeIngredients with mixed ingredients
     */
    public function test_analyze_ingredients_mixed(): void
    {
        $ingredients = ['Water', 'Sugar', 'Gelatin', 'Salt'];
        $text = 'Ingredients: Water, Sugar, Gelatin, Salt';

        $analysis = $this->ocrService->analyzeIngredients($ingredients, $text);

        $this->assertIsArray($analysis);
        $this->assertEquals('diragukan', $analysis['overall_status']);
        $this->assertGreaterThan(0, $analysis['confidence']);
    }

    /**
     * 🔍 Test analyzeIngredients with empty ingredients
     */
    public function test_analyze_ingredients_empty(): void
    {
        $ingredients = [];
        $text = '';

        $analysis = $this->ocrService->analyzeIngredients($ingredients, $text);

        $this->assertIsArray($analysis);
        $this->assertEquals('diragukan', $analysis['overall_status']);
        $this->assertEquals(0, $analysis['confidence']);
    }

    /**
     * 🔄 Test processOCRImage complete flow
     */
    public function test_process_ocr_image_complete_flow(): void
    {
        // Mock both APIs
        Http::fake([
            'https://vision.googleapis.com/*' => Http::response([
                'responses' => [
                    [
                        'fullTextAnnotation' => [
                            'text' => 'Ingredients: Water, Sugar, Salt, Natural Flavors'
                        ]
                    ]
                ]
            ], 200),
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => 'Based on the ingredients, this product appears to be halal.'
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $user = User::factory()->create();
        $image = UploadedFile::fake()->create('product.jpg', 120, 'image/jpeg');
        $productName = 'Test Product';
        $brand = 'Test Brand';

        $result = $this->ocrService->processOCRImage($image, $productName, $brand, $user);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('text', $result);
        $this->assertArrayHasKey('ingredients', $result);
        $this->assertArrayHasKey('halal_analysis', $result);
        $this->assertArrayHasKey('confidence', $result);
        $this->assertArrayHasKey('processing_time', $result);
        $this->assertGreaterThan(0, $result['processing_time']);
    }

    /**
     * 📊 Test getOCRStatistics
     */
    public function test_get_ocr_statistics(): void
    {
        // Create OCR products with different statuses
        OCRProduct::factory()->count(5)->create(['status' => 'pending_admin_review']);
        OCRProduct::factory()->count(3)->create(['status' => 'approved']);
        OCRProduct::factory()->count(2)->create(['status' => 'rejected']);

        $stats = $this->ocrService->getOCRStatistics();

        $this->assertIsArray($stats);
        $this->assertEquals(10, $stats['total']);
        $this->assertEquals(5, $stats['pending']);
        $this->assertEquals(3, $stats['approved']);
        $this->assertEquals(2, $stats['rejected']);
        $this->assertArrayHasKey('accuracy', $stats);
        $this->assertArrayHasKey('avg_confidence', $stats);
    }

    /**
     * 📊 Test getUserOCRStatistics
     */
    public function test_get_user_ocr_statistics(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // Create OCR products for specific user
        OCRProduct::factory()->count(3)->create(['user_id' => $user->id_user]);
        OCRProduct::factory()->count(2)->create(['user_id' => $otherUser->id_user]);

        $stats = $this->ocrService->getUserOCRStatistics($user);

        $this->assertIsArray($stats);
        $this->assertEquals(3, $stats['total']);
        $this->assertArrayHasKey('pending', $stats);
        $this->assertArrayHasKey('approved', $stats);
        $this->assertArrayHasKey('rejected', $stats);
        $this->assertArrayHasKey('accuracy', $stats);
    }

    /**
     * 🧹 Test cleanupOldOCRProducts
     */
    public function test_cleanup_old_ocr_products(): void
    {
        // Create OCR products with different ages
        $oldProduct = OCRProduct::factory()->create(['created_at' => now()->subDays(35)]);
        $recentProduct = OCRProduct::factory()->create(['created_at' => now()->subDays(10)]);

        $deleted = $this->ocrService->cleanupOldOCRProducts(30);

        $this->assertEquals(1, $deleted);
        $this->assertDatabaseMissing('ocr_products', ['id' => $oldProduct->id]);
        $this->assertDatabaseHas('ocr_products', ['id' => $recentProduct->id]);
    }

    /**
     * 🔍 Test validateOCRResult
     */
    public function test_validate_ocr_result(): void
    {
        $validResult = [
            'text' => 'Ingredients: Water, Sugar, Salt',
            'ingredients' => ['Water', 'Sugar', 'Salt'],
            'confidence' => 85,
            'method' => 'google_vision'
        ];

        $invalidResult = [
            'text' => '',
            'ingredients' => [],
            'confidence' => 0,
            'method' => 'mock'
        ];

        $this->assertTrue($this->ocrService->validateOCRResult($validResult));
        $this->assertFalse($this->ocrService->validateOCRResult($invalidResult));
    }

    /**
     * 📝 Test extractIngredientsFromText
     */
    public function test_extract_ingredients_from_text(): void
    {
        $text1 = 'Ingredients: Water, Sugar, Salt, Natural Flavors';
        $text2 = 'Contains: Water, Sugar, Salt';
        $text3 = 'Water Sugar Salt Natural Flavors';

        $ingredients1 = $this->ocrService->extractIngredientsFromText($text1);
        $ingredients2 = $this->ocrService->extractIngredientsFromText($text2);
        $ingredients3 = $this->ocrService->extractIngredientsFromText($text3);

        $this->assertIsArray($ingredients1);
        $this->assertContains('Water', $ingredients1);
        $this->assertContains('Sugar', $ingredients1);
        $this->assertContains('Salt', $ingredients1);

        $this->assertIsArray($ingredients2);
        $this->assertContains('Water', $ingredients2);

        $this->assertIsArray($ingredients3);
        $this->assertNotEmpty($ingredients3);
    }

    /**
     * 📊 Test getProcessingMetrics
     */
    public function test_get_processing_metrics(): void
    {
        $metrics = $this->ocrService->getProcessingMetrics();

        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('total_processed', $metrics);
        $this->assertArrayHasKey('avg_processing_time', $metrics);
        $this->assertArrayHasKey('success_rate', $metrics);
        $this->assertArrayHasKey('method_distribution', $metrics);
    }
}
