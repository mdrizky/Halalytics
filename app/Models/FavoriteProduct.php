<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FavoriteProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_type',
        'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that owns the favorite
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product (polymorphic relationship)
     */
    public function product(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get OCR product relationship
     */
    public function ocrProduct(): BelongsTo
    {
        return $this->belongsTo(OCRProduct::class, 'product_id')
            ->where('product_type', 'ocr');
    }

    /**
     * Get regular product relationship
     */
    public function regularProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id')
            ->where('product_type', 'barcode');
    }

    /**
     * Scope to get favorites by product type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('product_type', $type);
    }

    /**
     * Get product name accessor
     */
    public function getProductNameAttribute()
    {
        return match($this->product_type) {
            'ocr' => $this->ocrProduct?->product_name ?? 'Unknown Product',
            'barcode' => $this->regularProduct?->name ?? 'Unknown Product',
            'manual' => $this->regularProduct?->name ?? 'Unknown Product',
            default => 'Unknown Product'
        };
    }

    /**
     * Get product status accessor
     */
    public function getProductStatusAttribute()
    {
        return match($this->product_type) {
            'ocr' => $this->ocrProduct?->halal_status ?? 'unknown',
            'barcode' => $this->regularProduct?->halal_status ?? 'unknown',
            'manual' => $this->regularProduct?->halal_status ?? 'unknown',
            default => 'unknown'
        };
    }

    /**
     * Get product image accessor
     */
    public function getProductImageAttribute()
    {
        return match($this->product_type) {
            'ocr' => $this->ocrProduct?->front_image_url,
            'barcode' => $this->regularProduct?->image_url,
            'manual' => $this->regularProduct?->image_url,
            default => null
        };
    }
}
