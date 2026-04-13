<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Services\DisplayImageService;

class OCRProduct extends Model
{
    use HasFactory;

    protected $table = 'ocr_products';

    protected $fillable = [
        'user_id',
        'product_name',
        'brand',
        'country',
        'ingredients_raw',
        'ingredients_parsed',
        'halal_status',
        'confidence_level',
        'source',
        'status',
        'ocr_text_hash',
        'front_image_path',
        'back_image_path',
        'language',
        'ai_analysis',
        'verified_by',
        'verified_at'
    ];

    protected $casts = [
        'ingredients_parsed' => 'array',
        'ai_analysis' => 'array',
        'confidence_level' => 'float',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that created the OCR product
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Get the admin who approved this product
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by', 'id_user');
    }

    /**
     * Get scan history records for this product
     */
    public function scanHistories()
    {
        return $this->hasMany(ScanHistory::class, 'ocr_product_id');
    }

    /**
     * Scope to get only pending products
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'pending_admin_review']);
    }

    /**
     * Scope to get only approved products
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get only rejected products
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Get the front image URL
     */
    public function getFrontImageUrlAttribute()
    {
        return app(DisplayImageService::class)->resolve($this->front_image_path, [
            'name' => $this->product_name,
            'brand' => $this->brand,
            'category' => 'ocr',
        ], 'product');
    }

    /**
     * Get the back image URL
     */
    public function getBackImageUrlAttribute()
    {
        return app(DisplayImageService::class)->resolve($this->back_image_path, [
            'name' => $this->product_name,
            'brand' => $this->brand,
            'category' => 'ocr',
        ], 'product');
    }

    /**
     * Check if product is pending
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'pending_admin_review'], true);
    }

    /**
     * Check if product is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if product is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(): string
    {
        return match($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'pending' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get processing step label
     */
    public function getProcessingStepLabel(): string
    {
        return match (true) {
            filled($this->front_image_path) && filled($this->back_image_path) => 'Lengkap',
            filled($this->front_image_path) => 'Menunggu gambar belakang',
            filled($this->back_image_path) => 'Perlu gambar depan',
            default => 'Belum lengkap',
        };
    }
}
