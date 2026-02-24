<?php

namespace App\Http\Controllers\Admin\Promo;

use App\Http\Controllers\Controller;
use App\Models\PromoBlog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PromoBlogController extends Controller
{
    public function index()
    {
        $blogs = PromoBlog::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.promo.blog.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.promo.blog.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('promo_blogs', 'public');
        }

        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug;
        $counter = 1;

        while (PromoBlog::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        PromoBlog::create([
            'title' => $request->title,
            'slug' => $slug,
            'excerpt' => Str::limit(strip_tags($request->content), 150),
            'content' => $request->content,
            'category' => $request->category,
            'status' => $request->status,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.promo.blog.index')->with('success', 'Artikel berhasil dibuat');
    }

    public function edit($id)
    {
        $blog = PromoBlog::findOrFail($id);
        return view('admin.promo.blog.edit', compact('blog'));
    }

    public function update(Request $request, $id)
    {
        $blog = PromoBlog::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $data = [
            'title' => $request->title,
            'excerpt' => Str::limit(strip_tags($request->content), 150),
            'content' => $request->content,
            'category' => $request->category,
            'status' => $request->status,
        ];

        // Only update slug if title changed significantly
        if ($blog->title !== $request->title) {
            $baseSlug = Str::slug($request->title);
            $slug = $baseSlug;
            $counter = 1;
            while (PromoBlog::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $data['slug'] = $slug;
        }

        if ($request->hasFile('image')) {
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = $request->file('image')->store('promo_blogs', 'public');
        }

        $blog->update($data);

        return redirect()->route('admin.promo.blog.index')->with('success', 'Artikel berhasil diperbarui');
    }

    public function toggle($id)
    {
        $blog = PromoBlog::findOrFail($id);
        $blog->status = $blog->status === 'published' ? 'draft' : 'published';
        $blog->save();

        return redirect()->back()->with('success', 'Status artikel diubah');
    }

    public function destroy($id)
    {
        $blog = PromoBlog::findOrFail($id);
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }
        $blog->delete();

        return redirect()->route('admin.promo.blog.index')->with('success', 'Artikel berhasil dihapus');
    }
}
