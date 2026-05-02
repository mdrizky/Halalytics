<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodEmergencyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_name',
        'blood_type_needed',
        'bags_needed',
        'urgency_level',
        'contact_person',
        'contact_phone',
        'notes',
        'is_fulfilled',
        'fulfilled_at',
        'created_by',
    ];

    protected $casts = [
        'is_fulfilled' => 'boolean',
        'fulfilled_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }
}
