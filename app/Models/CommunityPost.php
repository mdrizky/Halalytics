<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    protected $fillable = [
        'user_id', 'title', 'content', 'image_path', 'category',
        'hashtags', 'likes_count', 'comments_count', 'is_pinned', 'is_hidden',
    ];

    protected $casts = [
        'hashtags'  => 'array',
        'is_pinned' => 'boolean',
        'is_hidden' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class, 'user_id', 'id_user'); }
    public function comments() { return $this->hasMany(CommunityComment::class, 'post_id'); }
    public function likes() { return $this->hasMany(CommunityPostLike::class, 'post_id'); }
}
