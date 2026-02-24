<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiQueryLog extends Model
{
    protected $fillable = ['id_user', 'query_type', 'input_data', 'ai_response', 'processing_time'];

    protected $casts = [
        'input_data' => 'array',
        'ai_response' => 'array',
        'id_user' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
