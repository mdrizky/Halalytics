<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EncyclopediaController extends Controller
{
    /**
     * Get list of ingredients with search and filters
     */
    public function index(Request $request)
    {
        $query = Ingredient::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('e_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('halal_status', $request->status);
        }

        if ($request->has('risk')) {
            $query->where('health_risk', $request->risk);
        }

        $ingredients = $query->orderBy('name')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $ingredients
        ]);
    }

    /**
     * Get detail of a specific ingredient
     */
    public function show($id)
    {
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            return response()->json([
                'success' => false,
                'message' => 'Ingredient not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ingredient
        ]);
    }

    /**
     * Search by E-number specifically
     */
    public function searchByENumber($eNumber)
    {
        $ingredient = Ingredient::where('e_number', $eNumber)
            ->orWhere('e_number', 'E' . $eNumber)
            ->first();

        if (!$ingredient) {
            return response()->json([
                'success' => false,
                'message' => 'E-Number not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ingredient
        ]);
    }

    /**
     * Create a new ingredient (admin API)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:ingredients,name',
            'e_number' => 'nullable|string|unique:ingredients,e_number',
            'halal_status' => 'required|in:halal,haram,syubhat,unknown',
            'health_risk' => 'required|in:safe,low_risk,high_risk,dangerous',
            'description' => 'nullable|string',
            'sources' => 'nullable|string',
            'notes' => 'nullable|string',
            'active' => 'boolean',
            'image_url' => 'nullable|string',
            'category' => 'nullable|string',
            'aliases' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ingredient = Ingredient::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Bahan berhasil ditambahkan ke database.',
            'data' => $ingredient
        ], 201);
    }

    /**
     * Update an existing ingredient (admin API)
     */
    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::find($id);
        if (!$ingredient) {
            return response()->json([
                'success' => false,
                'message' => 'Ingredient not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:ingredients,name,' . $id . ',id_ingredient',
            'e_number' => 'nullable|string|unique:ingredients,e_number,' . $id . ',id_ingredient',
            'halal_status' => 'required|in:halal,haram,syubhat,unknown',
            'health_risk' => 'required|in:safe,low_risk,high_risk,dangerous',
            'description' => 'nullable|string',
            'sources' => 'nullable|string',
            'notes' => 'nullable|string',
            'active' => 'boolean',
            'image_url' => 'nullable|string',
            'category' => 'nullable|string',
            'aliases' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ingredient->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Informasi bahan berhasil diperbarui.',
            'data' => $ingredient->fresh()
        ]);
    }

    /**
     * Delete an ingredient (admin API)
     */
    public function destroy($id)
    {
        $ingredient = Ingredient::find($id);
        if (!$ingredient) {
            return response()->json([
                'success' => false,
                'message' => 'Ingredient not found'
            ], 404);
        }

        $ingredient->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bahan berhasil dihapus dari database.'
        ]);
    }
}
