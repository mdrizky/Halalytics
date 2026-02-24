<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityModel;
use App\Models\ScanModel;
use App\Models\ReportModel;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $timestamps = true;

    protected $fillable = [
        'username',
        'full_name',
        'email',
        'password',
        'phone',
        'blood_type',
        'allergy',
        'medical_history',
        'role',
        'active',
        'last_login',
        'goal',
        'diet_preference',
        'activity_level',
        'address',
        'language',
        'age',
        'height',
        'weight',
        'bmi',
        'notif_enabled',
        'dark_mode',
        'image',
        'weight_kg',
        'has_diabetes',
        'emergency_contact',
        'is_active',
        'avatar_url',
        'birth_date',
        'gender',
        'bio',
        'dietary_preferences',
        'allergies',
        'notifications_enabled',
        'total_scans',
        'halal_products_count',
        'profile_visibility',
        'show_health_tips',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login' => 'datetime',
        'active' => 'boolean',
        'age' => 'integer',
        'height' => 'float',
        'weight' => 'float',
        'bmi' => 'float',
        'notif_enabled' => 'boolean',
        'dark_mode' => 'boolean',
        'image' => 'string',
        'birth_date' => 'date',
        'dietary_preferences' => 'array',
        'allergies' => 'array',
        'notifications_enabled' => 'boolean',
        'total_scans' => 'integer',
        'halal_products_count' => 'integer',
        'show_health_tips' => 'boolean',
    ];

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
        }
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function activities()
    {
        return $this->hasMany(ActivityModel::class, 'id_user', 'id_user');
    }

    public function scans()
    {
        return $this->hasMany(ScanModel::class, 'user_id', 'id_user');
    }

    public function reports()
    {
        return $this->hasMany(ReportModel::class, 'user_id', 'id_user');
    }
}
