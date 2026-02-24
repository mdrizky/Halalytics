<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $table = 'admin_notifications';
    
    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at'
    ];
    
    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];
    
    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
    
    /**
     * Get notification icon based on type
     */
    public function getIconAttribute()
    {
        return match($this->type) {
            'report' => 'fas fa-flag',
            'user' => 'fas fa-user',
            'product' => 'fas fa-box',
            'scan' => 'fas fa-barcode',
            'system' => 'fas fa-cog',
            default => 'fas fa-bell'
        };
    }
    
    /**
     * Get notification color based on type
     */
    public function getColorAttribute()
    {
        return match($this->type) {
            'report' => 'warning',
            'user' => 'info',
            'product' => 'success',
            'scan' => 'primary',
            'system' => 'secondary',
            default => 'light'
        };
    }
}
