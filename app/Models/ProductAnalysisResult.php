<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAnalysisResult extends Model
{
    use HasFactory;

    protected $table = 'product_analysis_results';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'product_id',
        'user_id',
        'input_type',
        'raw_input',
        'halal_verdict',
        'halal_data',
        'health_verdict',
        'health_data',
        'nutri_score',
        'is_verified_by_expert',
        'expert_id',
        'expert_notes',
    ];

    protected $casts = [
        'halal_data' => 'array',
        'health_data' => 'array',
        'is_verified_by_expert' => 'boolean',
        'expert_id' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'id_product');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'expert_id', 'id_user');
    }
}