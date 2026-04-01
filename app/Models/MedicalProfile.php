<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalProfile extends Model
{
    protected $fillable = [
        'id_user', 'weight_kg', 'height_cm', 'drug_allergies',
        'chronic_diseases', 'has_gerd', 'blood_type', 'additional_notes',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:1',
        'height_cm' => 'decimal:1',
        'drug_allergies' => 'array',
        'has_gerd' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
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
