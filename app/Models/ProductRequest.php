<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\DisplayImageService;

class ProductRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'barcode',
        'product_name',
        'image_front',
        'image_back',
        'ocr_text',
        'status',
        'admin_notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function getImageFrontAttribute($value): string
    {
        return app(DisplayImageService::class)->resolve($value, [
            'name' => $this->product_name,
            'barcode' => $this->barcode,
        ], 'product');
    }

    public function getImageBackAttribute($value): string
    {
        return app(DisplayImageService::class)->resolve($value, [
            'name' => $this->product_name,
            'barcode' => $this->barcode,
        ], 'product');
    }
}
