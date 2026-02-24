<?php

namespace App\Services;

use App\Models\ForbiddenIngredient;

class SafetyCheckerService
{
    /**
     * Check text or array of ingredients against forbidden list.
     * 
     * @param string|array $input Ingredients text or array
     * @return array Matches found
     */
    public function checkingredients($input)
    {
        if (empty($input)) {
            return [];
        }

        // Normalize input to string for flexibility
        $text = is_array($input) ? implode(' ', $input) : (string)$input;
        $textLower = strtolower($text);
        
        $matches = [];
        $forbiddenList = ForbiddenIngredient::where('is_active', true)->get();

        foreach ($forbiddenList as $forbidden) {
            // Check formatted name
            if ($this->containsWord($textLower, strtolower($forbidden->name))) {
                $matches[] = $forbidden;
                continue;
            }

            // Check aliases
            if ($forbidden->aliases) {
                foreach ($forbidden->aliases as $alias) {
                    if ($this->containsWord($textLower, strtolower($alias))) {
                        $matches[] = $forbidden;
                        break; // Avoid duplicates for same ID
                    }
                }
            }

            // Check code (E120 etc)
            if ($forbidden->code && $this->containsWord($textLower, strtolower($forbidden->code))) {
                 // Check if not already added
                 if (!collect($matches)->contains('id', $forbidden->id)) {
                     $matches[] = $forbidden;
                 }
            }
        }

        return $matches;
    }

    private function containsWord($haystack, $needle)
    {
        // Simple containment for now. 
        // Improvement: Use word boundaries (\b) to avoid partial matches like "grape" matching "rape"
        // But for chemical names, simpler might be better initially.
        // Let's use simple strpos for robustness against OCR errors.
        return strpos($haystack, $needle) !== false;
    }
}
