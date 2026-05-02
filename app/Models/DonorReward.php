<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'appointment_id',
        'points_earned',
        'badge_awarded',
        'awarded_at',
    ];

    protected $casts = [
        'awarded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function appointment()
    {
        return $this->belongsTo(DonorAppointment::class, 'appointment_id');
    }
}
