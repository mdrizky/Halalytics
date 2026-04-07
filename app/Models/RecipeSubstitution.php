<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeSubstitution extends Model
{
    protected $fillable = [
        'recipe_id', 'original_ingredients', 'substitution_result', 'requested_by',
    ];

    protected $casts = [
        'original_ingredients' => 'array',
        'substitution_result'  => 'array',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
