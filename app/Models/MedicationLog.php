<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicationLog extends Model
{
    protected $fillable = ['id_reminder', 'id_user', 'taken_at', 'status', 'notes'];

    protected $casts = [
        'taken_at' => 'datetime'
    ];

    public function reminder()
    {
        return $this->belongsTo(MedicationReminder::class, 'id_reminder', 'id_reminder');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
