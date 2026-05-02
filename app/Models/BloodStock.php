<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_type',
        'volume_ml',
        'bags_count',
        'source_appointment_id',
        'event_id',
        'collected_date',
        'expiry_date',
        'location',
        'status',
    ];

    protected $casts = [
        'collected_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function sourceAppointment()
    {
        return $this->belongsTo(DonorAppointment::class, 'source_appointment_id');
    }

    public function event()
    {
        return $this->belongsTo(BloodEvent::class, 'event_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('expiry_date', '>', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('blood_type', $type);
    }
}
