<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyProfile extends Model
{
    use HasFactory;

    protected $table = 'family_profiles';

    protected $fillable = [
        'user_id',
        'name',
        'relationship',
        'age',
        'gender',
        'allergies',
        'medical_history',
        'image_path',
    ];

    /**
     * Get the user that owns the family profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Accessor for full image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset($this->image_path) : null;
    }
}
