<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthEvent extends Model
{
    protected $fillable = [
        'title', 'type', 'event_date', 'description', 'image',
        'location', 'max_participants', 'zoom_link', 'whatsapp_group',
    ];

    protected $casts = [
        'event_date' => 'date',
        'max_participants' => 'integer',
    ];

    public function tickets()
    {
        return $this->hasMany(EventTicket::class, 'health_event_id');
    }
}
