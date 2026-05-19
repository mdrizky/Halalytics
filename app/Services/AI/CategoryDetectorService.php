<?php

namespace App\Services\AI;

class CategoryDetectorService
{
    /**
     * Detect product category: makanan, minuman, kosmetik, obat.
     *
     * @param  array<string, mixed>  $productData
     */
    public function detect(array $productData): string
    {
        $name = strtolower((string) ($productData['name'] ?? $productData['product_name'] ?? ''));
        $ingredients = strtolower((string) ($productData['ingredients'] ?? $productData['ingredients_text'] ?? $productData['komposisi'] ?? ''));
        $category = strtolower((string) ($productData['category'] ?? $productData['categories'] ?? ''));

        if ($this->matchesCosmetic($category, $ingredients, $name)) {
            return 'kosmetik';
        }

        if ($this->matchesMedicine($category, $name, $ingredients)) {
            return 'obat';
        }

        if ($this->matchesBeverage($name, $category)) {
            return 'minuman';
        }

        return 'makanan';
    }

    private function matchesCosmetic(string $category, string $ingredients, string $name): bool
    {
        if (str_contains($category, 'cosmetic') || str_contains($category, 'beaute') || str_contains($category, 'skincare')) {
            return true;
        }

        $cosmeticKeywords = ['serum', 'moisturizer', 'lipstick', 'foundation', 'sunscreen', 'toner', 'cleanser'];
        foreach ($cosmeticKeywords as $keyword) {
            if (str_contains($name, $keyword)) {
                return true;
            }
        }

        return str_contains($ingredients, 'aqua')
            && (str_contains($ingredients, 'glycerin') || str_contains($ingredients, 'glycerol'));
    }

    private function matchesMedicine(string $category, string $name, string $ingredients): bool
    {
        if (str_contains($category, 'drug') || str_contains($category, 'medicine') || str_contains($category, 'pharma')) {
            return true;
        }

        $medicineKeywords = ['tablet', 'kapsul', 'kaplet', 'sirup obat', 'obat', 'mg/', 'antibiotik'];
        foreach ($medicineKeywords as $keyword) {
            if (str_contains($name, $keyword)) {
                return true;
            }
        }

        return str_contains($ingredients, 'paracetamol') || str_contains($ingredients, 'ibuprofen');
    }

    private function matchesBeverage(string $name, string $category): bool
    {
        if (str_contains($category, 'beverage') || str_contains($category, 'minuman') || str_contains($category, 'drink')) {
            return true;
        }

        $beverageKeywords = ['minuman', 'jus', 'juice', 'teh', 'kopi', 'soda', 'cola', 'air mineral', 'beverage', 'drink'];
        foreach ($beverageKeywords as $keyword) {
            if (str_contains($name, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
