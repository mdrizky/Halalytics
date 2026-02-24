<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OpenFoodFactsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-off';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from Open Food Facts API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting Open Food Facts import...');

        $url = 'https://world.openfoodfacts.org/api/v2/search?countries_tags_en=indonesia&page_size=100&json=true';
        
        try {
            $response = \Illuminate\Support\Facades\Http::get($url);
            
            if (!$response->successful()) {
                $this->error('Failed to fetch data from Open Food Facts.');
                return 1;
            }

            $data = $response->json();
            $products = $data['products'] ?? [];

            $barcodes = \App\Models\ProductModel::pluck('barcode')->toArray();
            $importedCount = 0;

            foreach ($products as $offProduct) {
                $barcode = $offProduct['code'] ?? null;
                
                if (!$barcode || in_array($barcode, $barcodes)) {
                    continue;
                }

                $name = $offProduct['product_name'] ?? $offProduct['product_name_id'] ?? 'Unknown Product';
                $ingredients = $offProduct['ingredients_text'] ?? $offProduct['ingredients_text_id'] ?? 'Tidak ada data komposisi.';
                
                // Map Category
                $offCategories = $offProduct['categories_tags'] ?? [];
                $kategoriId = $this->mapCategory($offCategories);

                // Nutrition info
                $nutriments = $offProduct['nutriments'] ?? [];
                $infoGizi = $this->formatNutrition($nutriments);

                \App\Models\ProductModel::create([
                    'nama_product' => $name,
                    'barcode' => $barcode,
                    'komposisi' => $ingredients,
                    'status' => 'syubhat', // Default to syubhat for verification
                    'source' => 'open_food_facts',
                    'info_gizi' => $infoGizi,
                    'kategori_id' => $kategoriId,
                ]);

                $importedCount++;
                $barcodes[] = $barcode;
            }

            $this->info("Successfully imported $importedCount products.");
            return 0;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    private function mapCategory($tags)
    {
        $mapping = [
            'en:snacks' => 'Snack',
            'en:beverages' => 'Minuman',
            'en:meals' => 'Makanan',
            'en:dairies' => 'Produk Susu',
            'en:desserts' => 'Snack',
            'en:frozen-foods' => 'Makanan',
            'en:groceries' => 'Bumbu Masak',
        ];

        $targetCategory = 'Makanan'; // Default

        foreach ($tags as $tag) {
            if (isset($mapping[$tag])) {
                $targetCategory = $mapping[$tag];
                break;
            }
        }

        $kategori = \App\Models\KategoriModel::where('nama_kategori', $targetCategory)->first();
        
        if (!$kategori) {
            $kategori = \App\Models\KategoriModel::create(['nama_kategori' => $targetCategory]);
        }

        return $kategori->id_kategori;
    }

    private function formatNutrition($nutriments)
    {
        $energy = $nutriments['energy-kcal_100g'] ?? $nutriments['energy_100g'] ?? 0;
        $protein = $nutriments['proteins_100g'] ?? 0;
        $fat = $nutriments['fat_100g'] ?? 0;
        $carbs = $nutriments['carbohydrates_100g'] ?? 0;

        return "Energi: {$energy}kcal, Protein: {$protein}g, Lemak: {$fat}g, Karbohidrat: {$carbs}g (per 100g)";
    }
}
