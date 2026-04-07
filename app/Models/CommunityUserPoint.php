<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityUserPoint extends Model
{
    protected $table = 'community_user_points';

    protected $fillable = [
        'user_id',
        'total_points',
        'level',
    ];

    protected $casts = [
        'total_points' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
