<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReport extends Model
{
    protected $table = 'community_post_reports';

    protected $fillable = [
        'post_id',
        'user_id',
        'reason',
        'description',
        'status',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
