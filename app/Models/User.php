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
    ];

    /**
     * 🔐 Hash password otomatis setiap kali diisi.
     */
    public function setPasswordAttribute($value)
    {
        if ($value && !Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    /**
     * 🔑 Override login pakai 'username' (bukan email).
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * 📊 Relasi ke aktivitas pengguna.
     */
    public function activities()
    {
        return $this->hasMany(ActivityModel::class, 'id_user', 'id_user');
    }

    /**
     * 📷 Relasi ke hasil scan.
     */
    public function scans()
    {
        return $this->hasMany(ScanModel::class, 'user_id', 'id_user');
    }

    /**
     * 🧾 Relasi ke laporan pengguna.
     */
    public function reports()
    {
        return $this->hasMany(ReportModel::class, 'user_id', 'id_user');
    }
}
