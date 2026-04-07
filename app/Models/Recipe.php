<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'ingredients',
        'steps', 'category', 'is_halal_verified', 'image_path',
    ];

    protected $casts = [
        'ingredients'       => 'array',
        'steps'             => 'array',
        'is_halal_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function substitutions()
    {
        return $this->hasMany(RecipeSubstitution::class);
    }
}
