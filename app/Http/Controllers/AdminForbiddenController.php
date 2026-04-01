<?php

namespace App\Http\Controllers;

use App\Models\ForbiddenIngredient;
use Illuminate\Http\Request;

class AdminForbiddenController extends Controller
{
    public function index(Request $request)
    {
        if (!ForbiddenIngredient::query()->exists() && !$request->filled('search')) {
            $this->seedFallbackForbidden();
        }

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

    private function seedFallbackForbidden(): void
    {
        $items = [
            ['name' => 'Lard', 'code' => null, 'type' => 'halal_haram', 'risk_level' => 'high', 'reason' => 'Turunan lemak babi.'],
            ['name' => 'Gelatin Porcine', 'code' => null, 'type' => 'halal_haram', 'risk_level' => 'high', 'reason' => 'Gelatin dari babi.'],
            ['name' => 'Boric Acid', 'code' => 'E284', 'type' => 'health_hazard', 'risk_level' => 'high', 'reason' => 'Bahan berisiko kesehatan.'],
            ['name' => 'Sudan Dye', 'code' => null, 'type' => 'health_hazard', 'risk_level' => 'high', 'reason' => 'Pewarna tekstil berbahaya.'],
            ['name' => 'Carmine', 'code' => 'E120', 'type' => 'allergen', 'risk_level' => 'medium', 'reason' => 'Bisa memicu alergi dan isu halal.'],
            ['name' => 'Cyclamate', 'code' => 'E952', 'type' => 'health_hazard', 'risk_level' => 'medium', 'reason' => 'Pemanis sintetis perlu batas aman.'],
            ['name' => 'Sodium Nitrite', 'code' => 'E250', 'type' => 'health_hazard', 'risk_level' => 'medium', 'reason' => 'Pengawet daging berisiko jika berlebih.'],
        ];

        foreach ($items as $item) {
            ForbiddenIngredient::firstOrCreate(
                ['name' => $item['name']],
                array_merge($item, [
                    'aliases' => [],
                    'description' => 'Data uji admin untuk pengujian dashboard.',
                    'source' => 'Seeder',
                    'is_active' => true,
                ])
            );
        }
    }
}
