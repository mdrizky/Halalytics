<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoSetting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Helper to get all settings as a key-value array
    public static function getAllSettings()
    {
        return self::pluck('value', 'key')->toArray();
    }
}
