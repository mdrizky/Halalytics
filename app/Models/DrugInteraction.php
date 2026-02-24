<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugInteraction extends Model
{
    protected $fillable = [
        'medicine_a_id', 'medicine_b_id', 'severity', 'description',
        'recommendation', 'ai_verified', 'verified_at'
    ];

    protected $casts = [
        'ai_verified' => 'boolean',
        'verified_at' => 'datetime'
    ];

    public function medicineA()
    {
        return $this->belongsTo(Medicine::class, 'medicine_a_id', 'id_medicine');
    }

    public function medicineB()
    {
        return $this->belongsTo(Medicine::class, 'medicine_b_id', 'id_medicine');
    }
}
