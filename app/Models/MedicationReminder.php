<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicationReminder extends Model
{
    protected $table = 'medicine_reminders';
    protected $primaryKey = 'id_reminder';

    protected $fillable = [
        'id_user', 'id_medicine', 'dosage', 'frequency_per_day', 'schedule_times',
        'start_date', 'end_date', 'is_active', 'notes', 'taken_times'
    ];

    protected $casts = [
        'schedule_times' => 'array',
        'taken_times' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'id_medicine', 'id_medicine');
    }

    public function logs()
    {
        return $this->hasMany(MedicationLog::class, 'id_reminder', 'id_reminder');
    }
}
