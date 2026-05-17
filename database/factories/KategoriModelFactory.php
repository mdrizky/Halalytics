<?php

namespace Database\Factories;

use App\Models\KategoriModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KategoriModel>
 */
class KategoriModelFactory extends Factory
{
    protected $model = KategoriModel::class;

    public function definition(): array
    {
        return [
            'nama_kategori' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
        ];
    }
}
