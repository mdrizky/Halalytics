<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductModel;
use Illuminate\Support\Facades\Http;

class PopulateMissingProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:populate-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate missing product images from OpenFoodFacts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = ProductModel::whereNull('image')->orWhere('image', '')->get();
        $this->info("Found " . $products->count() . " products with missing images.");

        foreach ($products as $product) {
            $this->info("Fetching image for: {$product->nama_product} ({$product->barcode})");
            
            $url = "https://world.openfoodfacts.org/api/v0/product/{$product->barcode}.json";
            try {
                $response = Http::get($url);
                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['status']) && $data['status'] == 1) {
                        $imageUrl = $data['product']['image_url'] ?? null;
                        if ($imageUrl) {
                            $product->image = $imageUrl;
                            $product->save();
                            $this->info("Success: Saved image for {$product->nama_product}");
                        } else {
                            $this->warn("No image found for {$product->nama_product} in API.");
                        }
                    } else {
                        $this->error("Product not found in OpenFoodFacts: {$product->barcode}");
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error fetching data for {$product->barcode}: " . $e->getMessage());
            }
            
            // Avoid rate limiting
            usleep(200000); 
        }

        $this->info('Image population completed.');
        return 0;
    }
}
