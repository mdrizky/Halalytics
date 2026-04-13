<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Services\DisplayImageService;

class StreetFood extends Model
{
    use HasFactory;

    protected $table = 'street_foods';

    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'description',
        'category',
        'calories_min',
        'calories_max',
        'calories_typical',
        'protein',
        'carbs',
        'fat',
        'fiber',
        'sugar',
        'sodium',
        'serving_size_grams',
        'serving_description',
        'halal_status',
        'halal_notes',
        'health_tags',
        'health_notes',
        'ai_keywords',
        'common_ingredients',
        'image_url',
        'is_popular',
        'is_active',
        'search_count'
    ];

    protected $casts = [
        'health_tags' => 'array',
        'ai_keywords' => 'array',
        'common_ingredients' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'calories_min' => 'decimal:2',
        'calories_max' => 'decimal:2',
        'calories_typical' => 'decimal:2',
        'protein' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fat' => 'decimal:2',
        'fiber' => 'decimal:2',
        'sugar' => 'decimal:2',
        'sodium' => 'decimal:2',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    /**
     * Relationships
     */
    public function variants()
    {
        return $this->hasMany(FoodVariant::class);
    }

    public function defaultVariant()
    {
        return $this->hasOne(FoodVariant::class)->where('is_default', true);
    }

    public function foodLogs()
    {
        return $this->hasMany(UserFoodLog::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'LIKE', "%{$keyword}%")
              ->orWhere('name_en', 'LIKE', "%{$keyword}%")
              ->orWhere('category', 'LIKE', "%{$keyword}%")
              ->orWhereJsonContains('ai_keywords', $keyword);
        });
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Increment search count for popularity tracking
     */
    public function incrementSearchCount()
    {
        $this->increment('search_count');
    }

    /**
     * Calculate health score (0-100) based on nutrition
     */
    public function getHealthScoreAttribute()
    {
        $score = 50; // Base score

        // Protein bonus
        if ($this->protein > 20) $score += 20;
        elseif ($this->protein > 15) $score += 15;
        elseif ($this->protein > 10) $score += 10;
        elseif ($this->protein > 5) $score += 5;

        // Fiber bonus
        if ($this->fiber > 10) $score += 15;
        elseif ($this->fiber > 5) $score += 10;
        elseif ($this->fiber > 3) $score += 5;

        // Sugar penalty
        if ($this->sugar > 15) $score -= 20;
        elseif ($this->sugar > 10) $score -= 10;
        elseif ($this->sugar < 5) $score += 10;

        // Calorie penalty
        if ($this->calories_typical > 500) $score -= 20;
        elseif ($this->calories_typical > 400) $score -= 15;
        elseif ($this->calories_typical > 300) $score -= 5;

        // Fat penalty
        if ($this->fat > 25) $score -= 20;
        elseif ($this->fat > 15) $score -= 10;

        return max(0, min(100, $score));
    }

    /**
     * Get human-readable health score category
     */
    public function getHealthCategoryAttribute()
    {
        $score = $this->health_score;

        if ($score >= 80) return 'Sangat Sehat';
        if ($score >= 60) return 'Sehat';
        if ($score >= 40) return 'Cukup Sehat';
        if ($score >= 20) return 'Kurang Sehat';
        return 'Tidak Sehat';
    }

    /**
     * Get halal status in Indonesian
     */
    public function getHalalStatusLabelAttribute()
    {
        return match($this->halal_status) {
            'halal_umum' => 'Halal (Umum)',
            'syubhat' => 'Syubhat',
            'haram' => 'Haram',
            'tergantung_bahan' => 'Tergantung Bahan',
            default => $this->halal_status
        };
    }

    /**
     * Generate health recommendations based on nutrition
     */
    public function getHealthRecommendationsAttribute()
    {
        $recommendations = [];

        if ($this->calories_typical > 400) {
            $recommendations[] = 'Tinggi kalori. Batasi konsumsi jika sedang diet.';
        }

        if ($this->fat > 15) {
            $recommendations[] = 'Tinggi lemak. Seimbangkan dengan sayuran.';
        }

        if ($this->protein > 20) {
            $recommendations[] = 'Sumber protein baik untuk otot.';
        }

        if ($this->fiber > 5) {
            $recommendations[] = 'Kaya serat, baik untuk pencernaan.';
        }

        if ($this->sodium > 800) {
            $recommendations[] = 'Tinggi natrium. Waspadai jika memiliki hipertensi.';
        }

        if ($this->sugar > 15) {
            $recommendations[] = 'Tinggi gula. Tidak disarankan untuk diabetesi.';
        }

        return $recommendations;
    }

    public function getImageUrlAttribute($value): string
    {
        return app(DisplayImageService::class)->resolve($value, [
            'name' => $this->name,
            'category' => $this->category,
        ], 'street_food');
    }
}
