<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalProfile extends Model
{
    protected $fillable = [
        'id_user', 'weight_kg', 'height_cm', 'drug_allergies',
        'food_allergies', 'chronic_diseases', 'has_gerd', 'activity_level',
        'daily_calories_target', 'daily_sugar_limit_g', 'daily_sodium_limit_mg',
        'daily_fat_limit_g', 'blood_type', 'additional_notes',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:1',
        'height_cm' => 'decimal:1',
        'drug_allergies' => 'array',
        'food_allergies' => 'array',
        'has_gerd' => 'boolean',
        'daily_calories_target' => 'integer',
        'daily_sugar_limit_g' => 'decimal:1',
        'daily_sodium_limit_mg' => 'integer',
        'daily_fat_limit_g' => 'decimal:1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Get the nutritional thresholds as an array.
     */
    public function getThresholdsAttribute(): array
    {
        return [
            'calories' => $this->daily_calories_target ?? 2000,
            'sugar_g' => (float) ($this->daily_sugar_limit_g ?? 50.0),
            'sodium_mg' => $this->daily_sodium_limit_mg ?? 2300,
            'fat_g' => (float) ($this->daily_fat_limit_g ?? 67.0),
        ];
    }

    public function getBmiAttribute(): ?float
    {
        if (!$this->weight_kg || !$this->height_cm) return null;
        $heightM = $this->height_cm / 100;
        return round($this->weight_kg / ($heightM * $heightM), 1);
    }

    public function getBmiCategoryAttribute(): ?string
    {
        $bmi = $this->bmi;
        if (!$bmi) return null;
        if ($bmi < 18.5) return 'underweight';
        if ($bmi < 23) return 'normal';
        if ($bmi < 25) return 'overweight';
        return 'obese';
    }

    public function hasAllergyTo(string $drug): bool
    {
        $allergies = $this->drug_allergies ?? [];
        return in_array(strtolower($drug), array_map('strtolower', $allergies));
    }
}
