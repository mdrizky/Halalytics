<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotificationPreference extends Model
{
    protected $fillable = [
        'user_id', 'medication_reminders', 'promo_deals',
        'weekly_report', 'favorite_updates', 'new_products',
        'watchlist_alerts', 'security_alerts',
    ];

    protected $casts = [
        'medication_reminders' => 'boolean',
        'promo_deals'          => 'boolean',
        'weekly_report'        => 'boolean',
        'favorite_updates'     => 'boolean',
        'new_products'         => 'boolean',
        'watchlist_alerts'     => 'boolean',
        'security_alerts'      => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /** Get or create preferences for a user (all defaults = true). */
    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'medication_reminders' => true,
                'promo_deals'          => true,
                'weekly_report'        => true,
                'favorite_updates'     => true,
                'new_products'         => true,
                'watchlist_alerts'     => true,
                'security_alerts'      => true,
            ]
        );
    }

    /** Check if a specific channel is enabled. */
    public function isEnabled(string $channel): bool
    {
        return (bool) ($this->{$channel} ?? false);
    }
}
