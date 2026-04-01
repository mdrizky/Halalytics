<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $articles = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => Article::count(),
            'published' => Article::where('is_published', true)->count(),
            'draft' => Article::where('is_published', false)->count(),
            'total_views' => Article::sum('views'),
        ];

        $categories = Article::select('category')->distinct()->pluck('category');

        return view('admin.articles.index', compact('articles', 'stats', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
        ]);

        Article::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'excerpt' => Str::limit(strip_tags($request->content), 200),
            'content' => $request->content,
            'category' => $request->category,
            'author' => $request->author ?? 'Halalytics Team',
            'source' => 'local',
            'is_published' => true,
            'image' => $request->image,
        ]);

        return back()->with('success', 'Artikel berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $article->update($request->only(['title', 'content', 'category', 'author', 'image', 'is_published']));
        return back()->with('success', 'Artikel berhasil diupdate!');
    }

    public function destroy($id)
    {
        Article::findOrFail($id)->delete();
        return back()->with('success', 'Artikel berhasil dihapus!');
    }

    public function togglePublish($id)
    {
        $article = Article::findOrFail($id);
        $article->update(['is_published' => !$article->is_published]);
        return back()->with('success', 'Status artikel diubah!');
    }
}
