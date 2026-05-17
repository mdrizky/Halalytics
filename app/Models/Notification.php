<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'message', 'type',
        'related_product_id', 'related_umkm_id', 'extra_data', 'data',
        'action_type', 'action_value',
        'is_read', 'read', 'read_at',
        'firebase_key', 'is_sent_fcm', 'sent_at'
    ];

    protected $casts = [
        'extra_data' => 'array',
        'is_read' => 'boolean',
        'is_sent_fcm' => 'boolean',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function relatedProduct()
    {
        return $this->belongsTo(ProductModel::class, 'related_product_id');
    }

    /**
     * Legacy UMKM relation removed — module was deleted.
     * Kept as safe stub returning null to avoid breaking serialization.
     */
    public function relatedUmkm()
    {
        return $this->belongsTo(ProductModel::class, 'related_umkm_id');
    }

    // Mark as read
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    // Scope for unread
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Scope for specific user
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereNull('user_id'); // Include broadcasts
        });
    }

    /**
     * Alias atribut untuk kompatibilitas API / tes (kolom DB: extra_data, is_read).
     *
     * @return array<string, mixed>
     */
    public function getDataAttribute(): array
    {
        $parsed = $this->extra_data;

        return is_array($parsed) ? $parsed : [];
    }

    public function setDataAttribute(mixed $value): void
    {
        $this->extra_data = is_array($value) ? $value : [];
    }

    public function getReadAttribute(): bool
    {
        return (bool) ($this->attributes['is_read'] ?? false);
    }

    public function setReadAttribute(mixed $value): void
    {
        $this->attributes['is_read'] = $value ? 1 : 0;
    }
}
