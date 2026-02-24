<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('position', 'asc')->get();
        $global_user_count = \App\Models\User::count();
        return view('admin.banner', compact('banners', 'global_user_count'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'nullable|boolean',
            'position' => 'nullable|integer',
        ]);

        $data = $request->except('image');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/banners');
            $data['image'] = str_replace('public/', 'storage/', $path);
        }

        Banner::create($data);

        return redirect()->route('admin.banner')->with('success', 'Banner berhasil ditambahkan');
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'nullable|boolean',
            'position' => 'nullable|integer',
        ]);

        $data = $request->except('image');
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image) {
                Storage::delete(str_replace('storage/', 'public/', $banner->image));
            }
            $path = $request->file('image')->store('public/banners');
            $data['image'] = str_replace('public/', 'storage/', $path);
        }

        $banner->update($data);

        return redirect()->route('admin.banner')->with('success', 'Banner berhasil diperbarui');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::delete(str_replace('storage/', 'public/', $banner->image));
        }
        $banner->delete();

        return redirect()->route('admin.banner')->with('success', 'Banner berhasil dihapus');
    }
}
