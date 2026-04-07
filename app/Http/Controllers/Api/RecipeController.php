<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\RecipeSubstitution;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    public function __construct(private GeminiService $gemini) {}

    public function index(Request $request)
    {
        $query = Recipe::with('user:id_user,username,full_name')->latest();

        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->boolean('halal_only')) {
            $query->where('is_halal_verified', true);
        }

        return response()->json(['success' => true, 'data' => $query->paginate(20)]);
    }

    public function show($id)
    {
        $recipe = Recipe::with('user:id_user,username,full_name')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $recipe]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'ingredients' => 'required|array',
            'steps'       => 'required|array',
            'category'    => 'required|string',
        ]);

        $recipe = Recipe::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'steps'       => $request->steps,
            'category'    => $request->category,
            'image_path'  => $request->hasFile('image')
                ? $request->file('image')->store('recipes', 'public') : null,
        ]);

        return response()->json(['success' => true, 'message' => 'Resep berhasil dibuat', 'data' => $recipe]);
    }

    public function getSubstitution($recipeId)
    {
        $recipe = Recipe::findOrFail($recipeId);

        $cached = RecipeSubstitution::where('recipe_id', $recipeId)
            ->where('created_at', '>', now()->subDays(7))
            ->latest()->first();

        if ($cached) {
            return response()->json(['success' => true, 'data' => $cached->substitution_result, 'cached' => true]);
        }

        $ingredientNames = collect($recipe->ingredients)->pluck('name')->toArray();

        try {
            $result = $this->gemini->substituteIngredients($ingredientNames, $recipe->title);

            RecipeSubstitution::create([
                'recipe_id'            => $recipeId,
                'original_ingredients' => $ingredientNames,
                'substitution_result'  => $result,
                'requested_by'         => Auth::id(),
            ]);

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menganalisis: ' . $e->getMessage()], 500);
        }
    }

    public function halalSwitch($recipeId)
    {
        return $this->getSubstitution($recipeId);
    }
}
