<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HalalAlternative extends Model
{
    protected $fillable = [
        'original_medicine_id', 'alternative_medicine_id',
        'reason', 'ai_confidence', 'verified_by_admin'
    ];

    protected $casts = [
        'ai_confidence' => 'decimal:2',
        'verified_by_admin' => 'boolean'
    ];

    public function originalMedicine()
    {
        return $this->belongsTo(Medicine::class, 'original_medicine_id', 'id_medicine');
    }

    public function alternativeMedicine()
    {
        return $this->belongsTo(Medicine::class, 'alternative_medicine_id', 'id_medicine');
    }
}
