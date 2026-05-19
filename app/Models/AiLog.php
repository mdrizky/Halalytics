<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiLog extends Model
{
    protected $fillable = [
        'user_id', 'prompt_type', 'input_data', 'ai_response',
        'response_time_ms', 'is_accurate', 'feedback_text',
    ];

    protected $casts = [
        'is_accurate' => 'boolean',
        'response_time_ms' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(AiFeedback::class, 'ai_log_id');
    }
}
