<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthEncyclopedia;
use Illuminate\Http\Request;

class HealthEncyclopediaAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = HealthEncyclopedia::query();

        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $items = $query->orderBy('title', 'asc')->paginate(10);

        return view('admin.encyclopedia.index', compact('items'));
    }

    public function create()
    {
        return view('admin.encyclopedia.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:obat,penyakit,hidup_sehat,keluarga',
            'alphabet' => 'required|max:1',
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'source_link' => 'nullable|url',
        ]);

        HealthEncyclopedia::create($validated);

        return redirect()->route('admin.encyclopedia.index')->with('success', 'Data Ensiklopedia berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = HealthEncyclopedia::findOrFail($id);
        return view('admin.encyclopedia.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = HealthEncyclopedia::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:obat,penyakit,hidup_sehat,keluarga',
            'alphabet' => 'required|max:1',
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'source_link' => 'nullable|url',
        ]);

        $item->update($validated);

        return redirect()->route('admin.encyclopedia.index')->with('success', 'Data Ensiklopedia berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = HealthEncyclopedia::findOrFail($id);
        $item->delete();

        return redirect()->route('admin.encyclopedia.index')->with('success', 'Data Ensiklopedia berhasil dihapus.');
    }
}
