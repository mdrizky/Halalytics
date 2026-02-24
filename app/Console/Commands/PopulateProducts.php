<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OpenFoodFactsService;
use App\Models\ProductModel;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\Log;

class PopulateProducts extends Command
{
    protected $signature = 'products:populate {query=indonesia} {limit=50}';
    protected $description = 'Fetch products from Open Food Facts and save to local database';

    public function handle(OpenFoodFactsService $offService)
    {
        $query = $this->argument('query');
        $limit = (int)$this->argument('limit');

        $this->info("Fetching products for query: {$query}...");

        $result = $offService->searchProducts($query, $limit);

        if (!$result['success']) {
            $this->error("Failed to fetch products: " . ($result['message'] ?? 'Unknown error'));
            return 1;
        }

        $products = $result['products'];
        $count = 0;

        foreach ($products as $offProduct) {
            try {
                // Check if already exists
                $existing = ProductModel::where('barcode', $offProduct['barcode'])->first();
                if ($existing) continue;

                // Simple category mapping based on product categories
                $catId = $this->mapCategory($offProduct['categories'] ?? '');

                ProductModel::create([
                    'nama_product' => $offProduct['product_name'] ?? 'Unknown',
                    'barcode' => $offProduct['barcode'],
                    'image' => $offProduct['image_url'] ?? $offProduct['image_front_url'],
                    'komposisi' => json_encode($offProduct['ingredients_list']),
                    'info_gizi' => json_encode($offProduct['nutriments']),
                    'source' => 'open_food_facts',
                    'off_product_id' => $offProduct['_id'] ?? $offProduct['barcode'],
                    'off_last_synced' => now(),
                    'is_imported_from_off' => true,
                    'auto_imported_at' => now(),
                    'verification_status' => 'needs_review',
                    'status' => $offProduct['halal_analysis']['status'] === 'Halal' ? 'halal' : 'syubhat',
                    'halal_analysis' => $offProduct['halal_analysis'],
                    'active' => true,
                    'kategori_id' => $catId,
                    'sugar_g' => $offProduct['nutriments']['sugars_100g'] ?? 0,
                    'calories' => $offProduct['nutriments']['energy-kcal_100g'] ?? 0
                ]);

                $count++;
                $this->output->write('.');
            } catch (\Exception $e) {
                Log::error("Failed to import product {$offProduct['barcode']}: " . $e->getMessage());
            }
        }

        $this->info("\nImported {$count} products successfully!");
        return 0;
    }

    private function mapCategory($offCategories)
    {
        $cats = strtolower($offCategories);
        if (str_contains($cats, 'beverages') || str_contains($cats, 'drinks')) {
            return KategoriModel::where('nama_kategori', 'Minuman')->first()?->id_kategori;
        }
        if (str_contains($cats, 'snacks') || str_contains($cats, 'biscuits')) {
            return KategoriModel::where('nama_kategori', 'Makanan Ringan')->first()?->id_kategori;
        }
        if (str_contains($cats, 'sauces') || str_contains($cats, 'condiments')) {
            return KategoriModel::where('nama_kategori', 'Bumbu Dapur')->first()?->id_kategori;
        }
        return null;
    }
}
