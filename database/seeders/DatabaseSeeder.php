<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\KategoriModel;
use App\Models\ProductModel;

use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            HalalCriticalIngredientSeeder::class,
            IngredientSeeder::class,
            MedicineSeeder::class,
            ProductSeeder::class,
            StreetFoodSeeder::class,
            HalalyticsFeatureSeeder::class,
            HalalDatabaseSeeder::class,
            BannerSeeder::class,
        ]);
    }
}
