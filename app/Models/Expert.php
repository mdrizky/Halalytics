<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expert extends Model
{
    protected $fillable = [
        'user_id', 'specialization', 'bio', 'certificate_path',
        'is_verified', 'is_online', 'price_per_session', 'rating', 'total_reviews',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_online'   => 'boolean',
        'rating'      => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function wallet()
    {
        return $this->hasOne(ExpertWallet::class);
    }

    public function reviews()
    {
        return $this->hasMany(ExpertReview::class);
    }

    public function schedules()
    {
        return $this->hasMany(ExpertSchedule::class);
    }
}
