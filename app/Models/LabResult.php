<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabResult extends Model
{
    protected $fillable = [
        'id_user', 'test_date', 'test_type', 'value', 'unit',
        'normal_range_min', 'normal_range_max', 'status',
        'ai_analysis', 'image_url'
    ];

    protected $casts = [
        'test_date' => 'date',
        'value' => 'decimal:2',
        'normal_range_min' => 'decimal:2',
        'normal_range_max' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
