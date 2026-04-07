<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    protected $table = 'community_comment_likes';

    protected $fillable = [
        'comment_id',
        'user_id',
    ];
}
