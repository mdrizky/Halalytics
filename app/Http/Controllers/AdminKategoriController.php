<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriModel;

class AdminKategoriController extends Controller
{
    public function index()
    {
        $kategori = KategoriModel::withCount('products')->orderBy('id_kategori', 'desc')->paginate(10); // Changed sort to standard ID
        return view('admin.kategori-redesign', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
            'description' => 'nullable|string|max:500',
        ]);

        KategoriModel::create($request->only(['nama_kategori', 'description']));

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
        ]);

        $kategori->update($request->only(['nama_kategori', 'description']));

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
