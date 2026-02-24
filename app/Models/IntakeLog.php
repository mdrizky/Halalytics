<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntakeLog extends Model
{
    protected $table = 'intake_logs';
    protected $fillable = [
        'user_id', 'product_id', 'product_name', 'sugar_g', 'sodium_mg', 'calories', 'logged_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
