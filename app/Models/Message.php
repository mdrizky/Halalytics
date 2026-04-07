<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'halocode_messages';

    protected $fillable = [
        'consultation_id',
        'sender_id',
        'message',
        'attachment_path',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id_user');
    }
}
