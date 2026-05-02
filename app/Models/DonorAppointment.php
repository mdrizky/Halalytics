<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DonorAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'qr_code',
        'queue_number',
        'status',
        'screening_passed',
        'screening_notes',
        'admin_notes',
        'weight_kg',
        'hemoglobin',
        'blood_pressure',
        'checked_in_at',
        'approved_at',
    ];

    protected $casts = [
        'screening_passed' => 'boolean',
        'checked_in_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->qr_code)) {
                $model->qr_code = Str::uuid()->toString();
            }
            if (empty($model->queue_number)) {
                $maxQueue = static::where('event_id', $model->event_id)->max('queue_number');
                $model->queue_number = $maxQueue ? $maxQueue + 1 : 1;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function event()
    {
        return $this->belongsTo(BloodEvent::class, 'event_id');
    }

    public function bloodStock()
    {
        return $this->hasOne(BloodStock::class, 'source_appointment_id');
    }

    public function reward()
    {
        return $this->hasOne(DonorReward::class, 'appointment_id');
    }
}
