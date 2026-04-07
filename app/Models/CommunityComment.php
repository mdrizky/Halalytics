<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityComment extends Model
{
    protected $fillable = ['post_id', 'user_id', 'parent_id', 'content', 'likes_count', 'is_hidden'];
    protected $casts = ['is_hidden' => 'boolean'];

    public function post() { return $this->belongsTo(CommunityPost::class, 'post_id'); }
    public function user() { return $this->belongsTo(User::class, 'user_id', 'id_user'); }
    public function parent() { return $this->belongsTo(self::class, 'parent_id'); }
    public function replies() { return $this->hasMany(self::class, 'parent_id'); }
}
