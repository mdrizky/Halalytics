<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanIngredient extends Model
{
    protected $fillable = [
        'scan_history_id',
        'ingredient_name',
        'status',
        'warning',
        'e_code',
        'category',
        'source_type',
    ];

    public function scanHistory()
    {
        return $this->belongsTo(ScanHistory::class);
    }
}
