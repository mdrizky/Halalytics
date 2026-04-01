<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentalHealthQuizResult extends Model
{
    protected $fillable = [
        'id_user', 'quiz_type', 'total_score', 'severity_level',
        'answers', 'ai_recommendation',
    ];

    protected $casts = [
        'answers' => 'array',
        'total_score' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Interpretasi GAD-7 (kecemasan)
     */
    public static function interpretGad7(int $score): string
    {
        if ($score <= 4) return 'minimal';
        if ($score <= 9) return 'mild';
        if ($score <= 14) return 'moderate';
        return 'severe';
    }

    /**
     * Interpretasi PHQ-9 (depresi)
     */
    public static function interpretPhq9(int $score): string
    {
        if ($score <= 4) return 'minimal';
        if ($score <= 9) return 'mild';
        if ($score <= 14) return 'moderate';
        if ($score <= 19) return 'moderately_severe';
        return 'severe';
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('created_at');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('quiz_type', $type);
    }
}
