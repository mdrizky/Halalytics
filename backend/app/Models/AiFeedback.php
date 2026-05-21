<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'ai_log_id', 'is_accurate', 'feedback_text',
    ];

    protected $casts = [
        'is_accurate' => 'boolean',
    ];
}
