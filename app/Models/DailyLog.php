<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyLog extends Model
{
    protected $table = 'daily_nutrition_logs';

    protected $fillable = [
        'user_id', 'meal_type', 'food_items', 'total_calories',
        'total_carbs', 'total_protein', 'total_fat',
        'image_path', 'gemini_response', 'logged_at',
    ];

    protected $casts = [
        'food_items' => 'array',
        'logged_at'  => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
