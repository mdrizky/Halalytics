<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'street_food_id',
        'variant_name',
        'variant_type',
        'calories_modifier',
        'protein_modifier',
        'carbs_modifier',
        'fat_modifier',
        'price_modifier',
        'is_default',
        'popularity'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'calories_modifier' => 'decimal:2',
        'protein_modifier' => 'decimal:2',
        'carbs_modifier' => 'decimal:2',
        'fat_modifier' => 'decimal:2',
        'price_modifier' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function streetFood()
    {
        return $this->belongsTo(StreetFood::class);
    }

    public function foodLogs()
    {
        return $this->hasMany(UserFoodLog::class);
    }

    /**
     * Scopes
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('variant_type', $type);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('popularity', 'desc');
    }

    /**
     * Get variant type label in Indonesian
     */
    public function getVariantTypeLabelAttribute()
    {
        return match($this->variant_type) {
            'basic' => 'Dasar',
            'topping' => 'Topping',
            'size' => 'Ukuran',
            'cooking_method' => 'Cara Masak',
            default => $this->variant_type
        };
    }
}
