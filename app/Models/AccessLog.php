<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessLog extends Model
{
    protected $fillable = [
        'user_id',
        'resource_type',
        'resource_id',
        'action',
        'ip_address',
        'user_agent',
        'result',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public static function log(
        ?int $userId,
        string $resourceType,
        ?int $resourceId,
        string $action,
        string $result = 'success',
        ?array $details = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'action' => $action,
            'result' => $result,
            'details' => $details,
            'ip_address' => $ip ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }
}
