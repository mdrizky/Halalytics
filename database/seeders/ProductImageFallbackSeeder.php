<?php

namespace Database\Seeders;

use App\Models\ProductImageFallback;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ProductImageFallbackSeeder extends Seeder
{
    public function run(): void
    {
        $fallbacks = [
            [
                'category_key' => 'food',
                'label' => 'Fallback Makanan',
                'image_path' => '/images/default/food.svg',
                'notes' => 'Untuk snack, bakery, frozen food, dairy, dan makanan umum.',
            ],
            [
                'category_key' => 'drink',
                'label' => 'Fallback Minuman',
                'image_path' => '/images/default/drink.svg',
                'notes' => 'Untuk minuman kemasan, kopi, teh, dan susu minum.',
            ],
            [
                'category_key' => 'seasoning',
                'label' => 'Fallback Bumbu',
                'image_path' => '/images/default/seasoning.svg',
                'notes' => 'Untuk saus, sambal, kecap, dan bumbu dapur.',
            ],
            [
                'category_key' => 'cosmetic',
                'label' => 'Fallback Kosmetik',
                'image_path' => '/images/default/cosmetic.svg',
                'notes' => 'Untuk skincare, kosmetik, dan personal care.',
            ],
            [
                'category_key' => 'medicine',
                'label' => 'Fallback Obat',
                'image_path' => '/images/default/medicine.svg',
                'notes' => 'Untuk obat, suplemen, kesehatan, herbal, dan jamu.',
            ],
            [
                'category_key' => 'general',
                'label' => 'Fallback Umum',
                'image_path' => '/images/default/general.svg',
                'notes' => 'Fallback umum untuk kategori yang tidak terpetakan.',
            ],
        ];

        foreach ($fallbacks as $fallback) {
            ProductImageFallback::updateOrCreate(
                ['category_key' => $fallback['category_key']],
                $fallback
            );
        }

        Cache::forget('product_image_fallbacks_map');
    }
}
