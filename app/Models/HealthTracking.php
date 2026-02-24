<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthTracking extends Model
{
    protected $table = 'health_trackings';

    protected $fillable = ['id_user', 'metric_type', 'value', 'recorded_at', 'notes'];

    protected $casts = [
        'recorded_at' => 'datetime',
        'id_user' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
