<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationCampaign extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'image', 'target_amount', 'collected_amount',
        'donor_count', 'category', 'is_active', 'is_urgent', 'deadline', 'created_by',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'collected_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'is_urgent' => 'boolean',
        'deadline' => 'datetime',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'campaign_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(DonationUpdate::class, 'campaign_id');
    }

    public function progressPercent(): float
    {
        if ((float) $this->target_amount <= 0) {
            return 0;
        }

        return min(100, round(((float) $this->collected_amount / (float) $this->target_amount) * 100, 1));
    }
}
