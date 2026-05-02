<?php

namespace Database\Seeders;

use App\Models\ProductModel;
use App\Services\ProductImageService;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ProductImageFallbackSeeder::class);

        $imageService = app(ProductImageService::class);
        $products = ProductModel::with('kategori')
            ->where(function ($query) {
                $query->whereNull('image')->orWhere('image', '');
            })
            ->get();

        foreach ($products as $product) {
            $imageService->syncProduct($product);
        }
    }
}
