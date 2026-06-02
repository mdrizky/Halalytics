<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    protected $fillable = [
        'health_event_id', 'user_id', 'ticket_code', 'qr_data', 'status',
    ];

    public function event()
    {
        return $this->belongsTo(HealthEvent::class, 'health_event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
