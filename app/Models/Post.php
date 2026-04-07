<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'community_posts';

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image_path',
        'category',
        'hashtags',
        'likes_count',
        'comments_count',
        'is_pinned',
        'is_hidden',
    ];

    protected $casts = [
        'hashtags' => 'array',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'is_pinned' => 'boolean',
        'is_hidden' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }

    public function reports()
    {
        return $this->hasMany(PostReport::class, 'post_id');
    }
}
