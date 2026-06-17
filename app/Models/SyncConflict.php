<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncConflict extends Model
{
    protected $fillable = [
        'user_id',
        'resource_type',
        'resource_id',
        'mobile_data',
        'server_data',
        'mobile_timestamp',
        'server_timestamp',
        'resolution_strategy',
        'resolution_result',
        'resolved_at',
    ];

    protected $casts = [
        'mobile_data' => 'array',
        'server_data' => 'array',
        'resolution_result' => 'array',
        'mobile_timestamp' => 'datetime',
        'server_timestamp' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
