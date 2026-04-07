<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OcrScanHistory extends Model
{
    protected $table = 'ocr_scan_histories';

    protected $fillable = [
        'user_id', 'product_name', 'raw_text', 'detected_haram', 'severity', 'scanned_at',
    ];

    protected $casts = [
        'detected_haram' => 'array',
        'scanned_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
