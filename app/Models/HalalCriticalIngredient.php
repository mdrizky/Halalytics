<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HalalCriticalIngredient extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_ingredient';
    protected $fillable = [
        'name',
        'status',
        'description',
        'critical_reason',
        'common_sources',
        'alternatives',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeHaram($query)
    {
        return $query->where('status', 'haram');
    }

    public function scopeSyubhat($query)
    {
        return $query->where('status', 'syubhat');
    }

    public function scopeByName($query, $name)
    {
        return $query->where('name', 'LIKE', "%{$name}%");
    }
}
