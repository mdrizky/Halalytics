<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertReview extends Model
{
    protected $fillable = ['consultation_id', 'user_id', 'expert_id', 'rating', 'review'];

    public function consultation() { return $this->belongsTo(Consultation::class); }
    public function user() { return $this->belongsTo(User::class, 'user_id', 'id_user'); }
    public function expert() { return $this->belongsTo(Expert::class); }
}
