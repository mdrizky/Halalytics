<?php

namespace Database\Factories;

use App\Models\KategoriModel;
use App\Models\ProductModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductModel>
 */
class ProductModelFactory extends Factory
{
    protected $model = ProductModel::class;

    public function definition(): array
    {
        return [
            'nama_product' => $this->faker->words(3, true),
            'barcode' => (string) $this->faker->unique()->numerify('899###########'),
            'komposisi' => 'Water, Sugar, Salt',
            'status' => 'halal',
            'active' => true,
            'kategori_id' => KategoriModel::factory(),
        ];
    }
}
