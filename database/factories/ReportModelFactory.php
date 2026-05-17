<?php

namespace Database\Factories;

use App\Models\ProductModel;
use App\Models\ReportModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportModel>
 */
class ReportModelFactory extends Factory
{
    protected $model = ReportModel::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => ProductModel::factory(),
            'laporan' => $this->faker->paragraph(),
            'reason' => 'other',
            'status' => 'pending',
        ];
    }
}
