<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BloodEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'latitude',
        'longitude',
        'address',
        'event_date',
        'start_time',
        'end_time',
        'quota',
        'registered_count',
        'organizer',
        'contact_phone',
        'image_url',
        'status',
        'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function appointments()
    {
        return $this->hasMany(DonorAppointment::class, 'event_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('event_date', '>=', Carbon::today());
    }

    public function getQuotaRemainingAttribute()
    {
        return max(0, $this->quota - $this->registered_count);
    }

    public function getIsFullAttribute()
    {
        return $this->registered_count >= $this->quota;
    }
}
