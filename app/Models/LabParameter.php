<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_result_id',
        'parameter_name',
        'user_value',
        'normal_range',
        'status',
        'explanation',
    ];

    public function labResult()
    {
        // Assuming LabResult model exists
        return $this->belongsTo(LabResult::class, 'lab_result_id', 'id');
    }
}
