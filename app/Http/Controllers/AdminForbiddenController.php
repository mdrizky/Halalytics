<?php

namespace App\Http\Controllers;

use App\Models\ForbiddenIngredient;
use Illuminate\Http\Request;

class AdminForbiddenController extends Controller
{
    public function index(Request $request)
    {
        $query = ForbiddenIngredient::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        $ingredients = $query->orderBy('name', 'asc')->paginate(10);
        return view('admin.forbidden.index', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:forbidden_ingredients,name',
            'code' => 'nullable|string',
            'type' => 'required|in:halal_haram,health_hazard,allergen',
            'risk_level' => 'required|in:high,medium,low',
            'reason' => 'required|string',
            'aliases' => 'nullable|string', // Comma separated
        ]);

        $aliases = $request->aliases ? array_map('trim', explode(',', $request->aliases)) : [];

        ForbiddenIngredient::create([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'risk_level' => $request->risk_level,
            'reason' => $request->reason,
            'description' => $request->description,
            'aliases' => $aliases,
            'source' => 'Admin'
        ]);

        return redirect()->route('admin.forbidden.index')->with('success', 'Ingredient added successfully');
    }

    public function update(Request $request, $id)
    {
        $ingredient = ForbiddenIngredient::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|unique:forbidden_ingredients,name,'.$id,
            'code' => 'nullable|string',
            'type' => 'required|in:halal_haram,health_hazard,allergen',
            'risk_level' => 'required|in:high,medium,low',
            'reason' => 'required|string',
            'aliases' => 'nullable|string',
        ]);

        $aliases = $request->aliases ? array_map('trim', explode(',', $request->aliases)) : [];

        $ingredient->update([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'risk_level' => $request->risk_level,
            'reason' => $request->reason,
            'description' => $request->description,
            'aliases' => $aliases,
        ]);

        return redirect()->route('admin.forbidden.index')->with('success', 'Ingredient updated successfully');
    }

    public function destroy($id)
    {
        ForbiddenIngredient::findOrFail($id)->delete();
        return redirect()->route('admin.forbidden.index')->with('success', 'Ingredient deleted successfully');
    }
}
