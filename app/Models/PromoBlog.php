<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoBlog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Get formatted created date
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
