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
        if (
            !$request->filled('search') &&
            (!$request->filled('status') || $request->status === 'all') &&
            Ingredient::count() === 0
        ) {
            $this->seedFallbackIngredients();
        }

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

    private function seedFallbackIngredients(): void
    {
        $items = [
            ['name' => 'Gelatin (Porcine)', 'e_number' => null, 'halal_status' => 'haram', 'health_risk' => 'high_risk', 'description' => 'Gelatin sumber babi, tidak halal.'],
            ['name' => 'Carmine', 'e_number' => 'E120', 'halal_status' => 'syubhat', 'health_risk' => 'low_risk', 'description' => 'Pewarna dari serangga cochineal.'],
            ['name' => 'Monosodium Glutamate', 'e_number' => 'E621', 'halal_status' => 'halal', 'health_risk' => 'safe', 'description' => 'Penguat rasa umum pada makanan olahan.'],
            ['name' => 'Sodium Benzoate', 'e_number' => 'E211', 'halal_status' => 'halal', 'health_risk' => 'low_risk', 'description' => 'Pengawet makanan dan minuman.'],
            ['name' => 'Polysorbate 80', 'e_number' => 'E433', 'halal_status' => 'syubhat', 'health_risk' => 'low_risk', 'description' => 'Emulsifier, perlu verifikasi sumber.'],
            ['name' => 'Lard', 'e_number' => null, 'halal_status' => 'haram', 'health_risk' => 'dangerous', 'description' => 'Lemak babi, tidak halal.'],
            ['name' => 'Carrageenan', 'e_number' => 'E407', 'halal_status' => 'halal', 'health_risk' => 'safe', 'description' => 'Pengental dari rumput laut.'],
            ['name' => 'Tartrazine', 'e_number' => 'E102', 'halal_status' => 'halal', 'health_risk' => 'low_risk', 'description' => 'Pewarna sintetis kuning.'],
            ['name' => 'Rennet', 'e_number' => null, 'halal_status' => 'syubhat', 'health_risk' => 'low_risk', 'description' => 'Enzim untuk keju, sumber hewani perlu dicek.'],
            ['name' => 'Titanium Dioxide', 'e_number' => 'E171', 'halal_status' => 'halal', 'health_risk' => 'high_risk', 'description' => 'Pewarna putih, dibatasi di beberapa negara.'],
        ];

        foreach ($items as $item) {
            Ingredient::firstOrCreate(
                ['name' => $item['name']],
                array_merge($item, [
                    'sources' => 'Demo Seeder',
                    'notes' => 'Data uji admin',
                    'active' => true,
                ])
            );
        }
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
