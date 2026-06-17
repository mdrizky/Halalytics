<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportedProduct extends Model
{
    use HasFactory;

    protected $table = 'reported_products';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'barcode',
        'product_name',
        'image_front_path',
        'image_back_path',
        'notes',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}