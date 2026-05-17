<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriModel;
use Illuminate\Support\Facades\Storage;

class AdminKategoriController extends Controller
{
    public function index()
    {
        $query = KategoriModel::withCount('products')
            ->with([
                'products' => fn ($query) => $query
                    ->select('id_product', 'kategori_id', 'image', 'nama_product')
                    ->latest('id_product')
                    ->limit(1),
            ]);

        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($builder) use ($search) {
                $builder->where('nama_kategori', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $kategori = $query
            ->orderBy('nama_kategori')
            ->paginate(10)
            ->withQueryString();

        return view('admin.kategori-redesign', compact('kategori'));
    }

    public function show($id)
    {
        $kategori = KategoriModel::withCount('products')->findOrFail($id);
        $products = \App\Models\ProductModel::where('kategori_id', $id)
            ->latest('id_product')
            ->paginate(12);
            
        return view('admin.kategori_show', compact('kategori', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data = $request->only(['nama_kategori', 'description']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/categories');
            $data['image'] = str_replace('public/', 'storage/', $path);
        }

        KategoriModel::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan!'
            ]);
        }

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function create()
    {
        return view('admin.kategori_new');
    }

    public function edit($id)
    {
        $kategori = KategoriModel::findOrFail($id);
        
        if (request()->ajax()) {
            return response()->json($kategori);
        }
        
        return view('admin.kategori_edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriModel::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,'.$id.',id_kategori',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $data = $request->only(['nama_kategori', 'description']);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($kategori->image) {
                Storage::delete(str_replace('storage/', 'public/', $kategori->image));
            }
            $path = $request->file('image')->store('public/categories');
            $data['image'] = str_replace('public/', 'storage/', $path);
        }

        $kategori->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diupdate!'
            ]);
        }

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diupdate!');
    }

    public function destroy($id)
    {
        $kategori = KategoriModel::findOrFail($id);
        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
