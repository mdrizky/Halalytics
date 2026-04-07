<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    protected $table = 'community_point_transactions';

    protected $fillable = [
        'user_id',
        'points',
        'reason',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'points' => 'integer',
        'reference_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
