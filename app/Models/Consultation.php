<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $fillable = [
        'user_id', 'expert_id', 'status', 'payment_token',
        'payment_status', 'amount', 'started_at', 'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'consultation_id');
    }

    public function review()
    {
        return $this->hasOne(ExpertReview::class);
    }
}
