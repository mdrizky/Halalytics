<?php

namespace Database\Factories;

use App\Models\OCRProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OCRProduct>
 */
class OCRProductFactory extends Factory
{
    protected $model = OCRProduct::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_name' => $this->faker->words(3, true),
            'brand' => $this->faker->company(),
            'country' => $this->faker->countryCode(),
            'ingredients_raw' => 'Ingredients: Water, Sugar, Salt',
            'ingredients_parsed' => ['Water', 'Sugar', 'Salt'],
            'halal_status' => 'unknown',
            'confidence_level' => $this->faker->randomFloat(2, 40, 99),
            'source' => 'ocr',
            'status' => 'pending_admin_review',
            'ocr_text_hash' => $this->faker->sha256(),
            'language' => 'en',
        ];
    }
}
