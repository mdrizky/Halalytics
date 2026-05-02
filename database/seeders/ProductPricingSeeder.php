<?php

namespace Database\Seeders;

use App\Models\ProductModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductPricingSeeder extends Seeder
{
    public function run(): void
    {
        ProductModel::query()->each(function (ProductModel $product) {
            $price = $product->price;

            if ($price !== null && (float) $price > 0) {
                return;
            }

            $categoryName = Str::lower(optional($product->kategori)->nama_kategori ?? '');
            $productName = Str::lower($product->nama_product);

            $resolvedPrice = match (true) {
                str_contains($productName, 'indomie') => 4500,
                str_contains($productName, 'oreo') => 12000,
                str_contains($productName, 'silverqueen') => 18500,
                str_contains($productName, 'kitkat') => 11000,
                str_contains($productName, 'pocari') => 9000,
                str_contains($productName, 'ultra milk') => 7500,
                str_contains($productName, 'teh botol') => 5000,
                str_contains($productName, 'kecap') => 14000,
                str_contains($productName, 'sambal') => 9500,
                str_contains($productName, 'cetaphil') => 118000,
                str_contains($productName, 'vaseline') => 32000,
                str_contains($productName, 'wardah') => 42000,
                str_contains($productName, 'pixy') => 39000,
                str_contains($productName, 'panadol') => 18000,
                str_contains($categoryName, 'kosmetik') || str_contains($categoryName, 'skincare') => 45000,
                str_contains($categoryName, 'obat') || str_contains($categoryName, 'kesehatan') || str_contains($categoryName, 'suplemen') => 22000,
                str_contains($categoryName, 'minuman') || str_contains($categoryName, 'kopi') || str_contains($categoryName, 'teh') => 8000,
                str_contains($categoryName, 'saus') || str_contains($categoryName, 'bumbu') => 12000,
                default => 15000,
            };

            $product->update(['price' => $resolvedPrice]);
        });
    }
}
