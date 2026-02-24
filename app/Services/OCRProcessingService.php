<?php

namespace App\Services;

class OCRProcessingService
{
    /**
     * Parse OCR text into structured data
     */
    public function parseOCRText(string $ocrText): array
    {
        $data = [
            'product_name' => null,
            'brand' => null,
            'country' => null,
            'ingredients' => []
        ];

        // Clean and normalize text
        $text = $this->cleanText($ocrText);
        $lines = array_filter(array_map('trim', explode("\n", $text)));

        foreach ($lines as $line) {
            // Extract product name (usually first line or before ingredients)
            if (!$data['product_name'] && !$this->isIngredientLine($line) && !$this->isMetadataLine($line)) {
                $data['product_name'] = $this->extractProductName($line);
                continue;
            }

            // Extract brand
            if (!$data['brand'] && $this->isBrandLine($line)) {
                $data['brand'] = $this->extractBrand($line);
                continue;
            }

            // Extract country
            if (!$data['country'] && $this->isCountryLine($line)) {
                $data['country'] = $this->extractCountry($line);
                continue;
            }

            // Extract ingredients
            if ($this->isIngredientLine($line)) {
                $ingredients = $this->extractIngredients($line);
                $data['ingredients'] = array_merge($data['ingredients'], $ingredients);
            }
        }

        // If no product name found, try to extract from first few words
        if (!$data['product_name'] && !empty($lines)) {
            $data['product_name'] = $this->extractProductName($lines[0]);
        }

        return $data;
    }

    /**
     * Clean OCR text
     */
    private function cleanText(string $text): string
    {
        // Remove common OCR errors
        $text = preg_replace('/[^\w\s\-\.\,\(\)\/\&]/', ' ', $text);
        
        // Fix common character substitutions
        $replacements = [
            'l' => '1', // lowercase L to number 1
            'O' => '0', // uppercase O to number 0
            'S' => '5', // uppercase S to number 5
            'I' => '1', // uppercase I to number 1
        ];

        foreach ($replacements as $from => $to) {
            // Only replace in contexts where it makes sense (like E-numbers)
            $text = preg_replace("/E{$from}/", "E{$to}", $text);
        }

        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }

    /**
     * Check if line contains ingredients
     */
    private function isIngredientLine(string $line): bool
    {
        $ingredientKeywords = [
            'ingredients', 'ingredient', 'composition', 'contains',
            'bahan', 'kandungan', 'mengandung'
        ];

        foreach ($ingredientKeywords as $keyword) {
            if (stripos($line, $keyword) !== false) {
                return true;
            }
        }

        // Check for common ingredient patterns
        if (preg_match('/(flour|sugar|oil|salt|water|E\d+)/i', $line)) {
            return true;
        }

        return false;
    }

    /**
     * Check if line is metadata (country, made in, etc)
     */
    private function isMetadataLine(string $line): bool
    {
        $metadataKeywords = [
            'made in', 'product of', 'imported from', 'manufactured in',
            'dibuat di', 'produk dari', 'net weight', 'weight', 'berat'
        ];

        foreach ($metadataKeywords as $keyword) {
            if (stripos($line, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if line contains brand information
     */
    private function isBrandLine(string $line): bool
    {
        // Usually brand is in uppercase or has specific patterns
        if (preg_match('/^[A-Z][A-Z\s]{2,}$/', $line)) {
            return true;
        }

        // Common brand indicators
        if (preg_match('/(brand|by|©|™|®)/i', $line)) {
            return true;
        }

        return false;
    }

    /**
     * Check if line contains country information
     */
    private function isCountryLine(string $line): bool
    {
        $countries = [
            'indonesia', 'malaysia', 'singapore', 'thailand', 'philippines',
            'usa', 'america', 'united states', 'china', 'japan', 'korea',
            'india', 'pakistan', 'bangladesh', 'saudi arabia', 'uae',
            'germany', 'france', 'italy', 'spain', 'netherlands',
            'australia', 'new zealand', 'canada', 'mexico', 'brazil'
        ];

        foreach ($countries as $country) {
            if (stripos($line, $country) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract product name from line
     */
    private function extractProductName(string $line): string
    {
        // Remove common prefixes
        $line = preg_replace('/^(product|name|nama produk)\s*:?\s*/i', '', $line);
        
        // Remove suffixes like "with", "flavored", etc.
        $line = preg_replace('/\s+(with|flavored|style|type)\s+.*/i', '', $line);
        
        // Clean up and return first reasonable length
        $name = trim($line);
        
        if (strlen($name) > 50) {
            // Take first 50 characters
            $name = substr($name, 0, 50);
        }

        return $name;
    }

    /**
     * Extract brand from line
     */
    private function extractBrand(string $line): string
    {
        // Remove common brand prefixes
        $line = preg_replace('/^(brand|by|©|™|®)\s*:?\s*/i', '', $line);
        
        return trim($line);
    }

    /**
     * Extract country from line
     */
    private function extractCountry(string $line): string
    {
        // Remove common prefixes
        $line = preg_replace('/^(made in|product of|imported from|dibuat di|produk dari)\s*:?\s*/i', '', $line);
        
        // Extract country name
        if (preg_match('/([A-Za-z\s]+)/', $line, $matches)) {
            return trim($matches[1]);
        }
        
        return trim($line);
    }

    /**
     * Extract ingredients from line
     */
    private function extractIngredients(string $line): array
    {
        $ingredients = [];
        
        // Remove ingredient keywords
        $line = preg_replace('/^(ingredients|ingredient|composition|contains|bahan|kandungan|mengandung)\s*:?\s*/i', '', $line);
        
        // Split by common separators
        $parts = preg_split('/[,;\/\&\n]/', $line);
        
        foreach ($parts as $part) {
            $ingredient = $this->cleanIngredient(trim($part));
            
            if ($ingredient && strlen($ingredient) > 1) {
                $ingredients[] = $ingredient;
            }
        }
        
        return array_unique($ingredients);
    }

    /**
     * Clean individual ingredient
     */
    private function cleanIngredient(string $ingredient): string
    {
        // Remove parentheses content (usually percentages or descriptions)
        $ingredient = preg_replace('/\([^)]*\)/', '', $ingredient);
        
        // Remove common additives descriptors
        $ingredient = preg_replace('/(food|natural|artificial|flavor|color|preservative)\s+/i', '', $ingredient);
        
        // Normalize E-numbers
        $ingredient = preg_replace('/E\s*(\d+)/i', 'E$1', $ingredient);
        
        // Convert to lowercase for consistency
        $ingredient = strtolower(trim($ingredient));
        
        // Skip if too short or contains only numbers
        if (strlen($ingredient) < 2 || is_numeric($ingredient)) {
            return '';
        }
        
        return $ingredient;
    }

    /**
     * Generate hash for OCR text to detect duplicates
     */
    public function generateHash(string $ocrText): string
    {
        // Normalize text for hashing
        $normalized = strtolower($this->cleanText($ocrText));
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        
        return md5($normalized);
    }

    /**
     * Check OCR confidence based on text quality
     */
    public function calculateConfidence(string $ocrText): int
    {
        $confidence = 0;
        
        // Length check
        if (strlen($ocrText) > 50) $confidence += 20;
        
        // Contains ingredients
        if ($this->isIngredientLine($ocrText)) $confidence += 30;
        
        // Contains E-numbers
        if (preg_match('/E\d+/i', $ocrText)) $confidence += 20;
        
        // Contains common ingredient words
        if (preg_match('/(flour|sugar|oil|salt|water)/i', $ocrText)) $confidence += 15;
        
        // Text quality (ratio of alphanumeric characters)
        $alphanumeric = preg_replace('/[^a-zA-Z0-9\s]/', '', $ocrText);
        if (strlen($alphanumeric) / strlen($ocrText) > 0.8) $confidence += 15;
        
        return min(100, $confidence);
    }
}
