<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\DisplayImageService;
use Illuminate\Support\Str;

class ProductModel extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'id_product';
    public $timestamps = true;

    protected $fillable = [
        'nama_product',
        'barcode',
        'komposisi',
        'status',
        'active',
        'source',
        'info_gizi',
        'price',
        'kategori_id',
        'image',
        'off_product_id',
        'off_last_synced',
        'is_imported_from_off',
        'auto_imported_at',
        'verification_status',
        'data_completeness_score',
        'needs_manual_review',

        // Health & Approval
        'caffeine_mg',
        'sugar_g',
        'volume_ml',
        'calories',
        'protein_g',
        'fat_g',
        'halal_certificate',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'halal_analysis',

        // New metadata fields
        'brand',
        'quantity',
        'packaging',
        'labels',
        'nutriscore_grade',
        'nova_group',
        'stores',
        'countries',
        'manufacture_date',
        'expiry_date',
    ];

    protected $casts = [
        'halal_analysis' => 'array',
        'off_last_synced' => 'datetime',
        'auto_imported_at' => 'datetime',
        'approved_at' => 'datetime',
        'active' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Relasi ke Kategori
    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'id_kategori');
    }

    // Relasi ke Scan
    public function scans()
    {
        return $this->hasMany(ScanModel::class, 'product_id', 'id_product');
    }

    // Relasi ke Report
    public function reports()
    {
        return $this->hasMany(ReportModel::class, 'product_id', 'id_product');
    }

    public function getImageAttribute($value): string
    {
        return app(DisplayImageService::class)->resolve($value, [
            'name' => $this->nama_product,
            'barcode' => $this->barcode,
            'category' => optional($this->kategori)->nama_kategori,
        ], 'product');
    }

    public function getImageFallbackUrlAttribute(): string
    {
        $category = optional($this->kategori)->nama_kategori;
        $searchTerm = match (Str::lower($category ?? '')) {
            'makanan' => 'food',
            'minuman' => 'beverage',
            'kosmetik' => 'cosmetics',
            'obat' => 'medicine',
            default => blank($category) ? urlencode($this->nama_product ?? 'product') : Str::slug($category, '+'),
        };
        return "https://source.unsplash.com/400x400/?{$searchTerm}";
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'open_food_facts' => 'Open Food Facts',
            'open_beauty_facts' => 'Open Beauty Facts',
            'bpom' => 'BPOM',
            'local_cache' => 'Local Cache',
            'internal', null => 'Internal DB',
            default => ucfirst(str_replace('_', ' ', $this->source)),
        };
    }

    public function getCategoryNameAttribute(): string
    {
        return optional($this->kategori)->nama_kategori ?? 'Umum';
    }
}
