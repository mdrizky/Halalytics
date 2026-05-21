<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'prompt_type', 'input_data', 'ai_response', 'response_time_ms', 'is_accurate', 'feedback_text',
    ];

    protected $casts = [
        'is_accurate' => 'boolean',
        'response_time_ms' => 'integer',
    ];
}
