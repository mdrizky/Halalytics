<?php

namespace Database\Factories;

use App\Models\ProductModel;
use App\Models\ScanModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScanModel>
 */
class ScanModelFactory extends Factory
{
    protected $model = ScanModel::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => ProductModel::factory(),
            'nama_produk' => $this->faker->words(2, true),
            'barcode' => (string) $this->faker->numerify('899###########'),
            'kategori' => 'food',
            'status_halal' => 'halal',
            'status_kesehatan' => 'good',
            'tanggal_expired' => null,
            'tanggal_scan' => now(),
        ];
    }
}
