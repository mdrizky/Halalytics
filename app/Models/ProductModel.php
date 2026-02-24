<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'kategori_id',
        'image',
        'off_product_id',
        'off_last_synced',
        'is_imported_from_off',
        'auto_imported_at',
        'verification_status',
        'data_completeness_score',
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
        'halal_analysis'
    ];

    protected $casts = [
        'halal_analysis' => 'array',
        'off_last_synced' => 'datetime',
        'auto_imported_at' => 'datetime',
        'approved_at' => 'datetime',
        'active' => 'boolean'
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
}
