<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'community_comments';

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
        'likes_count',
        'is_hidden',
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'is_hidden' => 'boolean',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
