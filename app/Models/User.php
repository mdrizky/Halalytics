<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityModel;
use App\Models\ScanModel;
use App\Models\ReportModel;
use App\Models\ScanHistory;
use App\Models\Favorite;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected string $guard_name = 'web';

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
        'fcm_token',
        'total_donor_count',
        'total_donor_points',
        'last_donor_date',
        'next_eligible_date',
        'current_streak',
        'longest_streak',
        'last_active_date',
        'google_id',
        'facebook_id',
        'social_provider',
        'email_verified_at',
        'email_verification_token',
        'email_verification_expires_at',
        'onboarding_progress',
        'onboarding_level',
        'onboarding_completed_at',
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
        'total_donor_count' => 'integer',
        'total_donor_points' => 'integer',
        'last_donor_date' => 'date',
        'next_eligible_date' => 'date',
        'current_streak' => 'integer',
        'longest_streak' => 'integer',
        'last_active_date' => 'date',
        'email_verified_at' => 'datetime',
        'email_verification_expires_at' => 'datetime',
        'onboarding_progress' => 'array',
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

    public function scanHistories()
    {
        return $this->hasMany(ScanHistory::class, 'user_id', 'id_user');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id', 'id_user');
    }

    public function reports()
    {
        return $this->hasMany(ReportModel::class, 'user_id', 'id_user');
    }

    public function expertProfile()
    {
        return $this->hasOne(Expert::class, 'user_id', 'id_user');
    }

    public function communityPosts()
    {
        return $this->hasMany(Post::class, 'user_id', 'id_user');
    }

    public function communityPointProfile()
    {
        return $this->hasOne(CommunityUserPoint::class, 'user_id', 'id_user');
    }

    // --- Donor Darah Relationships ---
    public function donorAppointments()
    {
        return $this->hasMany(DonorAppointment::class, 'user_id', 'id_user');
    }

    public function donorRewards()
    {
        return $this->hasMany(DonorReward::class, 'user_id', 'id_user');
    }

    public function getDonorBadgeAttribute()
    {
        $count = $this->total_donor_count ?? 0;
        if ($count >= 50) return 'platinum';
        if ($count >= 25) return 'gold';
        if ($count >= 10) return 'silver';
        if ($count >= 5)  return 'bronze';
        if ($count >= 1)  return 'first_donor';
        return null;
    }
    // ---------------------------------
    // SINKRONISASI FIELD (TINGKAT DEWA)
    // ---------------------------------
    
    // Pastikan weight_kg dan weight sinkron tanpa rekursi accessor
    public function getWeightAttribute($value)
    {
        if ($value !== null && $value !== '' && (float) $value > 0) {
            return $value;
        }
        $rawKg = $this->attributes['weight_kg'] ?? null;
        if ($rawKg !== null && $rawKg !== '' && (float) $rawKg > 0) {
            return $rawKg;
        }

        return $value;
    }

    public function getWeightKgAttribute($value)
    {
        if ($value !== null && $value !== '' && (float) $value > 0) {
            return $value;
        }
        $rawW = $this->attributes['weight'] ?? null;
        if ($rawW !== null && $rawW !== '' && (float) $rawW > 0) {
            return $rawW;
        }

        return $value;
    }

    // Pastikan height selalu terisi
    public function getHeightAttribute($value)
    {
        return $value ?: ($this->medicalProfile->height_cm ?? 0);
    }

    // Sinkronisasi allergy (string) dan allergies (array) — tanpa saling memanggil accessor (hindari rekursi / undefined)
    public function getAllergyAttribute($value)
    {
        if ($value !== null && $value !== '') {
            return $value;
        }
        $rawJson = $this->attributes['allergies'] ?? null;
        if (is_string($rawJson) && $rawJson !== '' && $rawJson !== 'null') {
            $decoded = json_decode($rawJson, true);
            if (is_array($decoded) && $decoded !== []) {
                return implode(', ', $decoded);
            }
        }

        return $value ?? '';
    }

    public function getAllergiesAttribute($value)
    {
        $rawJson = $this->attributes['allergies'] ?? null;
        if (is_string($rawJson) && $rawJson !== '' && $rawJson !== 'null') {
            $decoded = json_decode($rawJson, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        if (is_array($value)) {
            return $value;
        }
        $allergyStr = $this->attributes['allergy'] ?? null;
        if (is_string($allergyStr) && $allergyStr !== '') {
            return array_values(array_filter(array_map('trim', explode(',', $allergyStr))));
        }

        return [];
    }

    // Relationship ke MedicalProfile untuk fallback data
    public function medicalProfile()
    {
        return $this->hasOne(MedicalProfile::class, 'id_user', 'id_user');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('admin');
    }
}
