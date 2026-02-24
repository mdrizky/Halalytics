<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FavoritesAndHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan ada user (misal ID 1) dan produk (misal ID 1, 2)
        $userId = 1; 
        
        // 1. Seed Favorites
        $favorites = [
            [
                'user_id' => $userId,
                'favoritable_id' => 1,
                'favoritable_type' => 'App\Models\ProductModel',
                'product_name' => 'Indomie Goreng',
                'halal_status' => 'halal',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => $userId,
                'favoritable_id' => 2,
                'favoritable_type' => 'App\Models\ProductModel',
                'product_name' => 'Sari Roti Tawar',
                'halal_status' => 'halal',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        try {
            DB::table('favorites')->insertOrIgnore($favorites);
            $this->command->info('Favorites seeded successfully.');
        } catch (\Exception $e) {
            $this->command->error('Error seeding favorites: ' . $e->getMessage());
        }

        // 2. Seed Scan Histories
        $histories = [
            [
                'user_id' => $userId,
                'scannable_id' => 1,
                'scannable_type' => 'App\Models\ProductModel',
                'product_name' => 'Indomie Goreng',
                'barcode' => '8998866200501',
                'halal_status' => 'halal',
                'scan_method' => 'camera',
                'source' => 'android',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'user_id' => $userId,
                'scannable_id' => 2,
                'scannable_type' => 'App\Models\ProductModel',
                'product_name' => 'Sari Roti Tawar',
                'barcode' => '8993416111116',
                'halal_status' => 'halal',
                'scan_method' => 'camera',
                'source' => 'android',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
        ];

        try {
            DB::table('scan_histories')->insertOrIgnore($histories);
            $this->command->info('Scan History seeded successfully.');
        } catch (\Exception $e) {
            $this->command->error('Error seeding history: ' . $e->getMessage());
        }
    }
}
