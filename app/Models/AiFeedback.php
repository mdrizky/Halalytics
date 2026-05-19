<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiFeedback extends Model
{
    protected $fillable = ['user_id', 'ai_log_id', 'is_accurate', 'feedback_text'];

    protected $casts = ['is_accurate' => 'boolean'];

    public function log(): BelongsTo
    {
        return $this->belongsTo(AiLog::class, 'ai_log_id');
    }
}
