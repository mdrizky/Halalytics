<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $table = 'ingredients';
    protected $primaryKey = 'id_ingredient';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'e_number',
        'halal_status',
        'health_risk',
        'description',
        'sources',
        'notes',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Scope for active ingredients
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope by halal status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('halal_status', $status);
    }
}
