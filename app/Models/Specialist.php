<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'specialty',
        'avatar_url',
        'bio',
        'is_online',
        'is_available',
        'rating',
        'total_consultations',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'is_available' => 'boolean',
        'rating' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function sessions()
    {
        return $this->hasMany(ConsultationSession::class, 'specialist_id');
    }
}
