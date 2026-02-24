<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'favoritable_type', 'favoritable_id',
        'product_name', 'product_image', 'halal_status', 'category',
        'last_known_status', 'has_status_changed', 'status_changed_at',
        'user_notes',
        'firebase_key'
    ];

    protected $casts = [
        'has_status_changed' => 'boolean',
        'status_changed_at' => 'datetime',
    ];

    // Polymorphic relation
    public function favoritable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    // Check if status changed
    public function checkStatusChange()
    {
        $currentStatus = $this->favoritable->status ?? $this->favoritable->halal_status ?? 'unknown';
        
        if ($currentStatus !== $this->last_known_status) {
            $this->update([
                'last_known_status' => $currentStatus,
                'has_status_changed' => true,
                'status_changed_at' => now()
            ]);

            // Send notification to user
            $this->notifyStatusChange($currentStatus);
        }
    }

    private function notifyStatusChange($newStatus)
    {
        Notification::create([
            'user_id' => $this->user_id,
            'title' => '⚠️ Status Produk Favorit Berubah',
            'message' => "Status halal '{$this->product_name}' berubah menjadi {$newStatus}",
            'type' => 'favorite',
            'related_product_id' => $this->favoritable_type === 'App\Models\ProductModel' ? $this->favoritable_id : null,
            'action_type' => 'view_product',
            'action_value' => $this->favoritable_id
        ]);
    }
}
