<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'record_type',
        'record_date',
        'title',
        'description',
        'file_path',
        'hospital_name',
        'doctor_name',
        'tags',
        'is_archived',
    ];

    protected $casts = [
        'tags' => 'array',
        'record_date' => 'date',
        'is_archived' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
