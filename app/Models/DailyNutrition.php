<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyNutrition extends Model
{
    protected $fillable = [
        'user_id',
        'scan_date',
        'total_calories',
        'total_protein',
        'total_carbs',
        'total_fat',
        'total_fiber',
        'total_sugar',
        'total_sodium',
        'total_scans',
        'total_halal',
        'total_syubhat',
        'total_haram',
        'health_score_avg',
    ];

    protected $casts = [
        'scan_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOnDate($query, $date)
    {
        return $query->where('scan_date', $date);
    }

    public function scopeToday($query)
    {
        return $query->where('scan_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('scan_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }
}
