<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFoodLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'street_food_id',
        'food_variant_id',
        'input_method',
        'ai_confidence',
        'portion_multiplier',
        'total_calories',
        'total_protein',
        'total_carbs',
        'total_fat',
        'meal_type',
        'consumed_at',
        'user_notes'
    ];

    protected $casts = [
        'ai_confidence' => 'decimal:2',
        'portion_multiplier' => 'decimal:2',
        'total_protein' => 'decimal:2',
        'total_carbs' => 'decimal:2',
        'total_fat' => 'decimal:2',
        'consumed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function streetFood()
    {
        return $this->belongsTo(StreetFood::class);
    }

    public function foodVariant()
    {
        return $this->belongsTo(FoodVariant::class);
    }

    /**
     * Scopes
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('consumed_at', today());
    }

    public function scopeByMealType($query, $mealType)
    {
        return $query->where('meal_type', $mealType);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('consumed_at', 'desc')->limit($limit);
    }

    /**
     * Get meal type label in Indonesian
     */
    public function getMealTypeLabelAttribute()
    {
        return match($this->meal_type) {
            'breakfast' => 'Sarapan',
            'lunch' => 'Makan Siang',
            'dinner' => 'Makan Malam',
            'snack' => 'Camilan',
            default => $this->meal_type
        };
    }

    /**
     * Get input method label
     */
    public function getInputMethodLabelAttribute()
    {
        return match($this->input_method) {
            'photo' => 'Scan Foto',
            'text' => 'Cari Teks',
            'manual' => 'Input Manual',
            default => $this->input_method
        };
    }

    /**
     * Create log with calculated nutrition
     */
    public static function createWithCalculation($data)
    {
        $streetFood = StreetFood::findOrFail($data['street_food_id']);
        $variant = isset($data['food_variant_id']) 
            ? FoodVariant::find($data['food_variant_id']) 
            : null;

        $portionMultiplier = $data['portion_multiplier'] ?? 1.0;

        // Base nutrition
        $calories = $streetFood->calories_typical;
        $protein = $streetFood->protein;
        $carbs = $streetFood->carbs;
        $fat = $streetFood->fat;

        // Add variant modifiers
        if ($variant) {
            $calories += $variant->calories_modifier;
            $protein += $variant->protein_modifier;
            $carbs += $variant->carbs_modifier;
            $fat += $variant->fat_modifier;
        }

        // Apply portion multiplier
        $data['total_calories'] = round($calories * $portionMultiplier);
        $data['total_protein'] = round($protein * $portionMultiplier, 2);
        $data['total_carbs'] = round($carbs * $portionMultiplier, 2);
        $data['total_fat'] = round($fat * $portionMultiplier, 2);

        return self::create($data);
    }
}
