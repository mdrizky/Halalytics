<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineReminder extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_reminder';
    protected $fillable = [
        'id_user',
        'id_medicine',
        'symptoms',
        'schedule_times',
        'frequency_per_day',
        'start_date',
        'end_date',
        'is_active',
        'notes',
        'taken_times'
    ];

    protected $casts = [
        'schedule_times' => 'array',
        'taken_times' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'id_medicine');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('id_user', $userId);
    }

    // Helper methods
    public function getNextDoseTime()
    {
        if (!$this->is_active || !$this->schedule_times) {
            return null;
        }

        $now = now();
        $today = $now->format('Y-m-d');
        
        foreach ($this->schedule_times as $time) {
            $scheduleTime = \Carbon\Carbon::parse("$today $time");
            
            if ($scheduleTime > $now) {
                return $scheduleTime;
            }
        }
        
        // If all times have passed, return tomorrow's first dose
        $tomorrow = $now->addDay()->format('Y-m-d');
        return \Carbon\Carbon::parse("$tomorrow " . $this->schedule_times[0]);
    }

    public function markAsTaken($time = null)
    {
        $takenTime = $time ?: now()->toISOString();
        $takenTimes = $this->taken_times ?: [];
        $takenTimes[] = $takenTime;
        
        $this->update(['taken_times' => $takenTimes]);
    }
}
