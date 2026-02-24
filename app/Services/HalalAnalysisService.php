<?php

namespace App\Services;

use App\Models\Ingredient;

class HalalAnalysisService
{
    /**
     * Analyze ingredients array and determine halal status
     */
    public function analyzeIngredients(array $ingredients): array
    {
        $results = [];
        
        foreach ($ingredients as $ingredientName) {
            $result = $this->analyzeIngredient($ingredientName);
            $results[] = $result;
        }
        
        return $results;
    }

    /**
     * Analyze single ingredient
     */
    public function analyzeIngredient(string $ingredientName): array
    {
        // Normalize ingredient name
        $normalized = $this->normalizeIngredient($ingredientName);
        
        // Try to find exact match first
        $ingredient = Ingredient::where('name', $normalized)->first();
        
        if (!$ingredient) {
            // Try fuzzy matching
            $ingredient = $this->findSimilarIngredient($normalized);
        }
        
        if ($ingredient) {
            return [
                'id' => $ingredient->id,
                'name' => $ingredient->name,
                'original_name' => $ingredientName,
                'halal_status' => $ingredient->halal_status,
                'risk_level' => $ingredient->risk_level ?? 'medium',
                'description' => $ingredient->description,
                'e_number' => $ingredient->e_number,
                'source' => 'database'
            ];
        }
        
        // If not found, analyze based on patterns
        return $this->analyzeUnknownIngredient($normalized, $ingredientName);
    }

    /**
     * Normalize ingredient name for database matching
     */
    private function normalizeIngredient(string $ingredient): string
    {
        // Convert to lowercase
        $ingredient = strtolower(trim($ingredient));
        
        // Remove common prefixes/suffixes
        $ingredient = preg_replace('/^(natural|artificial|food|grade)\s+/i', '', $ingredient);
        $ingredient = preg_replace('/\s+(extract|oil|powder|flavor|color)$/i', '', $ingredient);
        
        // Remove parentheses
        $ingredient = preg_replace('/\([^)]*\)/', '', $ingredient);
        
        // Standardize spacing
        $ingredient = preg_replace('/\s+/', ' ', $ingredient);
        
        return trim($ingredient);
    }

    /**
     * Find similar ingredient using fuzzy matching
     */
    private function findSimilarIngredient(string $normalized)
    {
        // Try removing common variations
        $variations = [
            $normalized,
            preg_replace('/\s+/', '', $normalized), // remove spaces
            preg_replace('/[^a-z0-9]/', '', $normalized), // remove non-alphanumeric
        ];
        
        foreach ($variations as $variation) {
            $ingredient = Ingredient::whereRaw('LOWER(name) LIKE ?', ["%{$variation}%"])->first();
            if ($ingredient) {
                return $ingredient;
            }
        }
        
        return null;
    }

    /**
     * Analyze unknown ingredient based on patterns
     */
    private function analyzeUnknownIngredient(string $normalized, string $originalName): array
    {
        $status = 'unknown';
        $risk = 'medium';
        $description = 'Ingredient not found in database. Manual verification required.';
        
        // Check E-numbers
        if (preg_match('/E(\d+)/i', $normalized, $matches)) {
            $eNumber = $matches[1];
            $status = $this->analyzeENumber($eNumber);
            $risk = $this->getENumberRisk($eNumber);
            $description = "E-number ingredient: E{$eNumber}";
        }
        
        // Check common halal/haram patterns
        if ($this->isHaramPattern($normalized)) {
            $status = 'haram';
            $risk = 'high';
            $description = 'Contains potentially haram components';
        } elseif ($this->isHalalPattern($normalized)) {
            $status = 'halal';
            $risk = 'low';
            $description = 'Generally considered halal';
        }
        
        return [
            'id' => null,
            'name' => $normalized,
            'original_name' => $originalName,
            'halal_status' => $status,
            'risk_level' => $risk,
            'description' => $description,
            'e_number' => $matches[1] ?? null,
            'source' => 'pattern_analysis'
        ];
    }

    /**
     * Analyze E-number halal status
     */
    private function analyzeENumber(string $eNumber): string
    {
        $num = (int) $eNumber;
        
        // Known haram E-numbers
        $haramRanges = [
            [120, 129], // E120-E129 (Cochineal, Carmine)
            [441, 441], // E441 (Gelatin)
        ];
        
        // Known syubhat E-numbers
        $syubhatRanges = [
            [904, 904], // E904 (Shellac)
            [920, 921], // E920-E921 (L-cysteine)
        ];
        
        foreach ($haramRanges as [$start, $end]) {
            if ($num >= $start && $num <= $end) {
                return 'haram';
            }
        }
        
        foreach ($syubhatRanges as [$start, $end]) {
            if ($num >= $start && $num <= $end) {
                return 'syubhat';
            }
        }
        
        // Most E-numbers are syubhat by default
        return 'syubhat';
    }

    /**
     * Get E-number risk level
     */
    private function getENumberRisk(string $eNumber): string
    {
        $num = (int) $eNumber;
        
        // High risk E-numbers
        if (in_array($num, [120, 441, 904, 920, 921])) {
            return 'high';
        }
        
        // Medium risk ranges
        if (($num >= 100 && $num <= 199) || // Colors
            ($num >= 200 && $num <= 299) || // Preservatives
            ($num >= 300 && $num <= 399) || // Antioxidants
            ($num >= 400 && $num <= 499)) {  // Emulsifiers
            return 'medium';
        }
        
        return 'low';
    }

    /**
     * Check if ingredient matches haram patterns
     */
    private function isHaramPattern(string $ingredient): bool
    {
        $haramKeywords = [
            'gelatin', 'pork', 'lard', 'alcohol', 'wine', 'beer',
            'carmine', 'cochineal', 'blood', 'rennet'
        ];
        
        foreach ($haramKeywords as $keyword) {
            if (strpos($ingredient, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if ingredient matches halal patterns
     */
    private function isHalalPattern(string $ingredient): bool
    {
        $halalKeywords = [
            'water', 'salt', 'sugar', 'flour', 'wheat', 'rice',
            'corn', 'potato', 'tomato', 'onion', 'garlic', 'honey',
            'milk', 'butter', 'cheese', 'egg', 'plant', 'fruit',
            'vegetable', 'herb', 'spice', 'vinegar'
        ];
        
        foreach ($halalKeywords as $keyword) {
            if (strpos($ingredient, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get overall product halal status from ingredients
     */
    public function getOverallStatus(array $ingredientResults): string
    {
        $hasHaram = false;
        $hasSyubhat = false;
        $hasUnknown = false;
        
        foreach ($ingredientResults as $result) {
            switch ($result['halal_status']) {
                case 'haram':
                    $hasHaram = true;
                    break;
                case 'syubhat':
                    $hasSyubhat = true;
                    break;
                case 'unknown':
                    $hasUnknown = true;
                    break;
            }
        }
        
        if ($hasHaram) return 'haram';
        if ($hasSyubhat) return 'syubhat';
        if ($hasUnknown) return 'unknown';
        return 'halal';
    }

    /**
     * Generate warnings for ingredients
     */
    public function generateWarnings(array $ingredientResults): array
    {
        $warnings = [];
        
        foreach ($ingredientResults as $result) {
            switch ($result['halal_status']) {
                case 'haram':
                    $warnings[] = "⚠️ Contains haram ingredient: {$result['original_name']}";
                    break;
                case 'syubhat':
                    $warnings[] = "⚠️ Contains doubtful ingredient: {$result['original_name']}";
                    break;
                case 'unknown':
                    $warnings[] = "⚠️ Unknown ingredient status: {$result['original_name']}";
                    break;
            }
        }
        
        return array_unique($warnings);
    }

    /**
     * Calculate overall confidence score
     */
    public function calculateConfidence(array $ingredientResults, int $ocrConfidence): int
    {
        $totalIngredients = count($ingredientResults);
        if ($totalIngredients === 0) return 0;
        
        $knownIngredients = collect($ingredientResults)->filter(function ($result) {
            return $result['source'] === 'database';
        })->count();
        
        $ingredientScore = ($knownIngredients / $totalIngredients) * 50;
        $ocrScore = $ocrConfidence * 0.5;
        
        return min(100, (int) ($ingredientScore + $ocrScore));
    }
}
