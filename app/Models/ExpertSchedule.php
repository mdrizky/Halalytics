<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertSchedule extends Model
{
    protected $fillable = ['expert_id', 'day_of_week', 'start_time', 'end_time', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function expert() { return $this->belongsTo(Expert::class); }
}
