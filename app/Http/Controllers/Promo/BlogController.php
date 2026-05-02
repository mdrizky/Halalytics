<?php

namespace App\Http\Controllers\Promo;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\PromoSetting;
use App\Services\ExternalHealthArticleService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(
        private readonly ExternalHealthArticleService $externalArticles
    ) {
    }

    public function index(Request $request)
    {
        $settings = PromoSetting::getAllSettings();
        
        $query = Article::where('status', 'published')->where(function($q) {
            $q->where('category', 'Promo')->orWhere('category', 'article');
        });
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // simple search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        $blogs = $query->orderBy('created_at', 'desc')->paginate(9)->withQueryString();
        $categories = Article::where('status', 'published')->select('category')->distinct()->pluck('category');
        $externalArticles = $this->externalArticles->search((string) $request->get('search', ''), 6);

        return view('promo.blog', compact('settings', 'blogs', 'categories', 'externalArticles'));
    }

    public function show($slug)
    {
        $settings = PromoSetting::getAllSettings();
        $blog = Article::where('slug', $slug)->where('status', 'published')->firstOrFail();
        
        // Increment views
        $blog->increment('views');
        
        $relatedBlogs = Article::where('status', 'published')
            ->where('id', '!=', $blog->id)
            ->where('category', $blog->category)
            ->inRandomOrder()
            ->take(3)
            ->get();
            
        return view('promo.blog-detail', compact('settings', 'blog', 'relatedBlogs'));
    }
}
