<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\DisplayImageService;

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
        'active_ingredient',
        'dosage_info',
        'frequency_per_day',
        'max_daily_dose',
        'side_effects',
        'warnings',
        'contraindications',
        'route',
        'halal_status',
        'halal_certificate_number',
        'manufacturer',
        'country_origin',
        'dosage_form',
        'category',
        'source',
        'is_imported_from_fda',
        'external_reference',
        'external_payload',
        'is_prescription_required',
        'is_verified_by_admin',
        'active'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'external_payload' => 'array',
        'is_imported_from_fda' => 'boolean',
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

    public function getImageUrlAttribute($value): string
    {
        return app(DisplayImageService::class)->resolve($value, [
            'name' => $this->name,
            'brand' => $this->brand_name,
            'barcode' => $this->barcode,
            'category' => $this->category ?: 'medicine',
        ], 'medicine');
    }

    public function getDescriptionAttribute($value): string
    {
        $description = trim((string) $value);

        if ($description !== '') {
            return $description;
        }

        return 'Deskripsi obat belum tersedia. Gunakan sesuai indikasi, baca kemasan, dan konsultasikan ke tenaga kesehatan bila perlu.';
    }

    public function getIndicationsAttribute($value): string
    {
        $indications = trim((string) $value);

        if ($indications !== '') {
            return $indications;
        }

        return 'Indikasi spesifik belum dicantumkan. Periksa label kemasan atau konsultasikan ke apoteker.';
    }

    public function getWarningsAttribute($value): string
    {
        $warnings = trim((string) $value);

        if ($warnings !== '') {
            return $warnings;
        }

        return 'Gunakan sesuai aturan pakai. Hentikan penggunaan jika muncul reaksi alergi atau keluhan memburuk.';
    }

    public function getSideEffectsAttribute($value): string
    {
        $sideEffects = trim((string) $value);

        if ($sideEffects !== '') {
            return $sideEffects;
        }

        return 'Efek samping spesifik belum tersedia. Bacalah brosur obat dan konsultasi jika ada efek yang mengganggu.';
    }
}
