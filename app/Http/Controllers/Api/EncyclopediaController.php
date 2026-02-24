<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class EncyclopediaController extends Controller
{
    /**
     * Get list of ingredients with search and filters
     */
    public function index(Request $request)
    {
        $query = Ingredient::query(); // Use query instead of active scope to ensure data shows up

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
}
