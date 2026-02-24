<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_medicine';
    protected $fillable = [
        'name',
        'generic_name',
        'brand_name',
        'barcode',
        'image_url',
        'description',
        'indications',
        'ingredients',
        'dosage_info',
        'frequency_per_day',
        'max_daily_dose',
        'side_effects',
        'contraindications',
        'route',
        'halal_status',
        'halal_certificate_number',
        'manufacturer',
        'country_origin',
        'dosage_form',
        'category',
        'source',
        'is_prescription_required',
        'is_verified_by_admin',
        'active'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'is_prescription_required' => 'boolean',
        'is_verified_by_admin' => 'boolean',
        'active' => 'boolean'
    ];

    // Relationships
    public function reminders()
    {
        return $this->hasMany(MedicineReminder::class, 'id_medicine');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeHalal($query)
    {
        return $query->where('halal_status', 'halal');
    }

    public function scopeByBarcode($query, $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('generic_name', 'LIKE', "%{$term}%")
              ->orWhere('brand_name', 'LIKE', "%{$term}%");
        });
    }
}
