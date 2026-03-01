<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BpomData;
use App\Models\StreetFood;
use App\Models\FoodVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StreetFoodController extends Controller
{
    public function index()
    {
        // Auto-seed awal dari data BPOM pangan jika tabel street foods masih kosong.
        if (StreetFood::count() === 0) {
            $seedCandidates = BpomData::query()
                ->whereIn('kategori', ['pangan', 'makanan', 'food'])
                ->whereNotNull('nama_produk')
                ->limit(20)
                ->get();

            foreach ($seedCandidates as $item) {
                StreetFood::firstOrCreate(
                    ['name' => $item->nama_produk],
                    [
                        'category' => 'produk-bpom',
                        'description' => $item->merk ? "Produk BPOM: {$item->merk}" : 'Produk referensi BPOM',
                        'calories_min' => 150,
                        'calories_max' => 300,
                        'calories_typical' => 200,
                        'protein' => 5,
                        'carbs' => 25,
                        'fat' => 8,
                        'fiber' => 2,
                        'sugar' => 5,
                        'sodium' => 200,
                        'halal_status' => 'halal_umum',
                        'halal_notes' => 'Auto-seed dari referensi BPOM, mohon lengkapi data nutrisi detail.'
                    ]
                );
            }
        }

        $foods = StreetFood::withCount('variants')->paginate(10);
        return view('admin.street-foods.index', compact('foods'));
    }

    public function create()
    {
        return view('admin.street-foods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'calories_typical' => 'required|numeric',
            'halal_status' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('foods', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        StreetFood::create($data);

        return redirect()->route('admin.street-foods.index')->with('success', 'Food created successfully');
    }

    public function edit(StreetFood $streetFood)
    {
        return view('admin.street-foods.edit', compact('streetFood'));
    }

    public function update(Request $request, StreetFood $streetFood)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'calories_typical' => 'required|numeric',
            'halal_status' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Delete old image
            if ($streetFood->image_url) {
                $oldPath = str_replace('/storage/', '', $streetFood->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('image')->store('foods', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $streetFood->update($data);

        return redirect()->route('admin.street-foods.index')->with('success', 'Food updated successfully');
    }

    public function destroy(StreetFood $streetFood)
    {
        if ($streetFood->image_url) {
            $oldPath = str_replace('/storage/', '', $streetFood->image_url);
            Storage::disk('public')->delete($oldPath);
        }
        
        $streetFood->delete();
        return redirect()->route('admin.street-foods.index')->with('success', 'Food deleted successfully');
    }

    public function variants(StreetFood $streetFood)
    {
        $variants = $streetFood->variants;
        return view('admin.street-foods.variants', compact('streetFood', 'variants'));
    }

    public function storeVariant(Request $request, StreetFood $streetFood)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'calories_modifier' => 'required|numeric',
            'halal_modifier' => 'nullable|string'
        ]);

        $streetFood->variants()->create($request->all());

        return back()->with('success', 'Variant added successfully');
    }

    public function destroyVariant(FoodVariant $variant)
    {
        $variant->delete();
        return back()->with('success', 'Variant deleted successfully');
    }
}
