<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutritionScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'product_image_path',
        'product_name',
        'barcode',
        'ai_nutrition_analysis',
        'halal_status',
        'health_score',
        'is_flagged',
        'admin_verification',
    ];

    protected $casts = [
        'ai_nutrition_analysis' => 'array',
        'is_flagged' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
