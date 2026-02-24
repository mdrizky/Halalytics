<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngredientManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Ingredient::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('e_number', 'like', "%{$search}%");
        }

        if ($request->has('status') && $request->status != 'all') {
            $query->where('halal_status', $request->status);
        }

        $ingredients = $query->latest()->paginate(15);
        
        $stats = [
            'total' => Ingredient::count(),
            'halal' => Ingredient::where('halal_status', 'halal')->count(),
            'haram' => Ingredient::where('halal_status', 'haram')->count(),
            'syubhat' => Ingredient::where('halal_status', 'syubhat')->count(),
        ];

        return view('admin.ingredients.index', compact('ingredients', 'stats'));
    }

    public function create()
    {
        return view('admin.ingredients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:ingredients,name',
            'e_number' => 'nullable|string|unique:ingredients,e_number',
            'halal_status' => 'required|in:halal,haram,syubhat,unknown',
            'health_risk' => 'required|in:safe,low_risk,high_risk,dangerous',
            'description' => 'nullable|string',
            'sources' => 'nullable|string',
            'notes' => 'nullable|string',
            'active' => 'boolean'
        ]);

        Ingredient::create($validated);

        return redirect()->route('admin.ingredients.index')->with('success', 'Bahan berhasil ditambahkan ke database.');
    }

    public function edit($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        return view('admin.ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|unique:ingredients,name,' . $id . ',id_ingredient',
            'e_number' => 'nullable|string|unique:ingredients,e_number,' . $id . ',id_ingredient',
            'halal_status' => 'required|in:halal,haram,syubhat,unknown',
            'health_risk' => 'required|in:safe,low_risk,high_risk,dangerous',
            'description' => 'nullable|string',
            'sources' => 'nullable|string',
            'notes' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $ingredient->update($validated);

        return redirect()->route('admin.ingredients.index')->with('success', 'Informasi bahan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Ingredient::findOrFail($id)->delete();
        return redirect()->route('admin.ingredients.index')->with('success', 'Bahan berhasil dihapus dari database.');
    }
}
