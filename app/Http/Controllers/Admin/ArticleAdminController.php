<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ExternalHealthArticleService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ArticleAdminController extends Controller
{
    public function __construct(private ExternalHealthArticleService $externalArticles)
    {
    }

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
            'published' => Article::where('status', 'published')->count(),
            'draft' => Article::where('status', 'draft')->count(),
            'total_views' => Article::sum('views'),
        ];

        $categories = Article::select('category')->distinct()->pluck('category');
        $externalQuery = trim((string) ($request->query('external_q') ?: $request->query('search') ?: 'halal food health'));
        $externalArticles = $this->externalArticles->search($externalQuery, 9);

        return view('admin.articles', compact('articles', 'stats', 'categories', 'externalArticles', 'externalQuery'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'status' => 'nullable|in:draft,published',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $status = $request->status ?? 'published';
        $imageData = $request->image;

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('public/articles');
            $imageData = asset(str_replace('public/', 'storage/', $path));
        }

        Article::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'excerpt' => Str::limit(strip_tags($request->content), 200),
            'content' => $request->content,
            'category' => $request->category,
            'author' => $request->author ?? 'Halalytics Team',
            'source' => 'local',
            'status' => $status,
            'is_published' => $status === 'published',
            'image' => $imageData,
        ]);

        return back()->with('success', 'Artikel berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data = $request->only(['title', 'content', 'category', 'author', 'image', 'is_published', 'status']);
        
        if ($request->hasFile('image_file')) {
            // Delete old image if it's a local one
            if ($article->image && str_contains($article->image, '/storage/articles/')) {
                $oldPath = str_replace(asset('storage/'), 'public/', $article->image);
                Storage::delete($oldPath);
            }
            $path = $request->file('image_file')->store('public/articles');
            $data['image'] = asset(str_replace('public/', 'storage/', $path));
        }

        if (isset($data['status'])) {
            $data['is_published'] = $data['status'] === 'published';
        } elseif (isset($data['is_published'])) {
            $data['status'] = $data['is_published'] ? 'published' : 'draft';
        }

        $article->update($data);
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
        $newPublished = !$article->is_published;
        $article->update([
            'is_published' => $newPublished,
            'status' => $newPublished ? 'published' : 'draft'
        ]);
        return back()->with('success', 'Status artikel diubah!');
    }
}
