<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActivity extends Model
{
    use HasFactory;

    protected $table = 'admin_activities';
    protected $fillable = [
        'admin_id',
        'action_type',
        'product_id',
        'description'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'id_user');
    }
}
