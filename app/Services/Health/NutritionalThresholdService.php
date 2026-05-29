<?php

namespace App\Services\Health;

use App\Models\MedicalProfile;
use App\Models\User;

class NutritionalThresholdService
{
    /**
     * Calculate personalized nutritional thresholds for a user or family member.
     */
    public function calculateThresholds($data): array
    {
        $weight = (float) ($data['weight_kg'] ?? 0);
        $height = (float) ($data['height_cm'] ?? 0);
        $age = (int) ($data['age'] ?? 0);
        $gender = $data['gender'] ?? 'male';
        $activityLevel = $data['activity_level'] ?? 'sedentary'; // sedentary, light, moderate, active, very_active
        $conditions = $data['conditions'] ?? []; // diabetes, hypertension, etc.

        if ($weight <= 0 || $height <= 0 || $age <= 0) {
            return $this->getDefaultLimits();
        }

        // 1. Calculate BMR (Mifflin-St Jeor Equation)
        if ($gender === 'male') {
            $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
        } else {
            $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
        }

        // 2. Calculate TDEE based on activity level
        $multipliers = [
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'active' => 1.725,
            'very_active' => 1.9,
        ];
        $tdee = $bmr * ($multipliers[$activityLevel] ?? 1.2);

        // 3. Set Base Limits (WHO/AHA Guidelines)
        // Sugar: < 10% of total calories (ideally < 5%)
        $sugarLimitG = ($tdee * 0.10) / 4; 
        
        // Sodium: < 2300mg (WHO recommends < 2000mg)
        $sodiumLimitMg = 2300;

        // Fat: 20-35% of total calories
        $fatLimitG = ($tdee * 0.30) / 9;

        // 4. Adjust based on medical conditions
        if ($this->hasCondition($conditions, ['diabetes', 'diabet'])) {
            $sugarLimitG = ($tdee * 0.05) / 4; // Stricter sugar limit
        }

        if ($this->hasCondition($conditions, ['hypertension', 'hipertensi', 'darah tinggi'])) {
            $sodiumLimitMg = 1500; // Stricter sodium limit
        }

        if ($this->hasCondition($conditions, ['cholesterol', 'kolesterol', 'jantung'])) {
            $fatLimitG = ($tdee * 0.20) / 9; // Lower fat limit
        }

        return [
            'calories' => round($tdee),
            'sugar_g' => round($sugarLimitG, 1),
            'sodium_mg' => round($sodiumLimitMg),
            'fat_g' => round($fatLimitG, 1),
            'bmr' => round($bmr),
        ];
    }

    private function hasCondition($conditions, array $keywords): bool
    {
        if (is_string($conditions)) {
            $conditions = explode(',', strtolower($conditions));
        }
        
        if (!is_array($conditions)) {
            return false;
        }

        foreach ($keywords as $keyword) {
            foreach ($conditions as $condition) {
                if (str_contains(strtolower(trim($condition)), $keyword)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getDefaultLimits(): array
    {
        return [
            'calories' => 2000,
            'sugar_g' => 50.0,
            'sodium_mg' => 2300.0,
            'fat_g' => 67.0,
            'bmr' => 1500,
        ];
    }
}
