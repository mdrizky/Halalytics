<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportModel extends Model
{
    use HasFactory;

    protected $table = 'reports';
    protected $primaryKey = 'id_report';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'product_id',
        'reason',
        'laporan',
        'evidence_image',
        'status',
        'admin_notes'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    // Relasi ke Produk
    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'id_product');
    }

    public function getImageAttribute(): string
    {
        return app(\App\Services\DisplayImageService::class)->resolve($this->evidence_image, [
            'name' => optional($this->product)->nama_product,
            'reason' => $this->reason,
        ], 'product');
    }
}
