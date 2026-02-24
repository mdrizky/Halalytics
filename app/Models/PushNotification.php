<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    protected $fillable = [
        'title', 'body', 'type',
        'target_type', 'target_data',
        'related_ingredient_id', 'related_product_id',
        'scheduled_at', 'sent_at',
        'sent_count', 'delivered_count', 'opened_count',
        'status'
    ];

    protected $casts = [
        'target_data' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
}
