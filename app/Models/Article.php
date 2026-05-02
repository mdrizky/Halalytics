<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\DisplayImageService;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'ai_summary', 'image',
        'category', 'source', 'source_url', 'author',
        'is_published', 'status', 'views',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'status' => 'string',
    ];

    public function getImageAttribute($value): ?string
    {
        return app(DisplayImageService::class)->resolve($value, [
            'name' => $this->title,
            'category' => $this->category,
        ], 'article');
    }
}
