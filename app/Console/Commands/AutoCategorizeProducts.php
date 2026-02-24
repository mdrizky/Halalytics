<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductModel;
use App\Models\KategoriModel;
use Illuminate\Support\Str;

class AutoCategorizeProducts extends Command
{
    protected $signature = 'products:auto-categorize {--force : Recategorize even if already categorized}';
    protected $description = 'Automatically categorize products based on their names';

    public function handle()
    {
        $products = ProductModel::all();
        $this->info("Scanning " . $products->count() . " products...");

        $rules = [
            12 => ['vitamin', 'suplemen', 'supplement', 'tablet', 'sirup obat', 'panadol', 'paracetamol', 'betadine', 'bodrex', 'obat batuk'],
            11 => ['sabun', 'soap', 'shampoo', 'sampo', 'lotion', 'parfum', 'perfume', 'lulur', 'makeup', 'body wash', 'lipstick', 'moisturizer', 'sunscreen', 'wardah', 'kahf', 'biore', 'ponds'],
            2 => ['drink', 'minuman', 'botol', 'can ', 'kaleng', 'teh', 'tea', 'kopi', 'coffee', 'jus', 'juice', 'soda', ' cola', 'water', 'air mineral', 'sparkling', 'syrup', 'sirup', 'pocari', 'mizone', 'adem sari', 'tehbotol', 'pucuk', 'nescafe', 'torabika', 'kapas tembak'],
            5 => ['susu', 'milk', 'cheese', 'keju', 'yogurt', 'butter', 'mentega', 'dairy', 'creamer', 'krimer', 'indomilk', 'frisian flag', 'ultra milk', 'anlene', 'hi-lo', 'dancow'],
            3 => ['snack', 'keripik', 'chips', 'wafer', 'makanan ringan', 'biskuit', 'chiki', 'oreo', 'biscuits', 'taro', 'chitatos', 'chitato', 'lay\'s', 'twist', 'malkist', 'roma', 'pringles', 'kitkat', 'silverqueen', 'cadbury', 'cheetos', 'beng-beng', 'chocolate', 'cokelat', 'candy', 'permen'],
            4 => ['bumbu', 'seasoning', 'kecap', 'sauce', 'saus', 'garam', 'salt', 'gula', 'sugar', 'penyedap', 'masako', 'royco', 'ladaku', 'terasi', 'ajinomoto', 'sasa'],
            1 => ['mie', 'noodle', 'nasi', 'rice', 'roti', 'bread', 'sereal', 'cereal', 'pasta', 'spaghetti', 'macaroni', 'indomie', 'sarimi', 'sedap', 'pop mie', 'mie instant'],
        ];

        $categories = KategoriModel::all()->pluck('nama_kategori', 'id_kategori')->toArray();
        $changedCount = 0;

        foreach ($products as $product) {
            $name = ' ' . Str::lower($product->nama_product) . ' '; // Add spaces for better boundary matching
            $foundId = null;

            // Specialized checks for "Cream" to avoid Cosmetics match "Full Cream", "Creamy" dairy, or "Cream" snacks
            if (Str::contains($name, 'cream') && !Str::contains($name, 'full cream') && !Str::contains($name, 'creamy')) {
                if (!Str::contains($name, 'biscuit') && !Str::contains($name, 'wafer') && !Str::contains($name, 'snack') && !Str::contains($name, 'indomilk') && !Str::contains($name, 'pringles')) {
                    $foundId = 11; // Cosmetics
                }
            }
            
            if (!$foundId) {
                // Check rules
                foreach ($rules as $id => $keywords) {
                    foreach ($keywords as $keyword) {
                        if (Str::contains($name, $keyword)) {
                            $foundId = $id;
                            break 2;
                        }
                    }
                }
            }

            if ($foundId && ($this->option('force') || is_null($product->kategori_id) || $product->kategori_id == 10)) { // 10 is Food & Beverages (too generic)
                if ($product->kategori_id != $foundId) {
                    $oldCat = $categories[$product->kategori_id] ?? 'None';
                    $newCat = $categories[$foundId] ?? 'Unknown';
                    
                    $product->kategori_id = $foundId;
                    $product->save();
                    
                    $this->info("Updated: {$product->nama_product} | {$oldCat} -> {$newCat}");
                    $changedCount++;
                }
            }
        }

        $this->info("Categorization completed. Total updated: {$changedCount}");
        return 0;
    }
}
