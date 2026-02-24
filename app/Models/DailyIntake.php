<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyIntake extends Model
{
    use HasFactory;

    protected $table = 'daily_intakes';
    protected $fillable = [
        'user_id',
        'intake_date',
        'total_water_ml',
        'total_caffeine_mg',
        'total_sugar_g',
        'total_calories'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
