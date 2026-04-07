<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionGoal extends Model
{
    protected $fillable = [
        'user_id', 'daily_calories', 'daily_carbs',
        'daily_protein', 'daily_fat', 'goal_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
