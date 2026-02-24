<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'emergency_type',
        'location_latitude',
        'location_longitude',
        'ai_guidance',
        'was_helpful',
        'user_feedback',
    ];

    protected $casts = [
        'ai_guidance' => 'array',
        'was_helpful' => 'boolean',
        'location_latitude' => 'decimal:8',
        'location_longitude' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
