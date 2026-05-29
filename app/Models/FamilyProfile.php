<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'relationship',
        'age',
        'gender',
        'weight_kg',
        'height_cm',
        'activity_level',
        'daily_calories_target',
        'daily_sugar_limit_g',
        'daily_sodium_limit_mg',
        'daily_fat_limit_g',
        'allergies',
        'medical_history',
        'image_path',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:1',
        'height_cm' => 'decimal:1',
        'daily_calories_target' => 'integer',
        'daily_sugar_limit_g' => 'decimal:1',
        'daily_sodium_limit_mg' => 'integer',
        'daily_fat_limit_g' => 'decimal:1',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
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
}
