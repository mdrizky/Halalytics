<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\RecipeSubstitution;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    public function __construct(private GeminiService $gemini) {}

    public function index(Request $request)
    {
        $query = Recipe::with('user:id_user,username,full_name')->latest();

        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->boolean('halal_only') || $request->boolean('halal_verified')) {
            $query->where('is_halal_verified', true);
        }

        $recipes = $query->limit(50)->get()
            ->map(fn (Recipe $recipe) => $this->recipePayload($recipe))
            ->values();

        return $this->successResponse($recipes, 'Daftar resep berhasil diambil.');
    }

    public function show($id)
    {
        $recipe = Recipe::with('user:id_user,username,full_name')->findOrFail($id);

        return $this->successResponse(
            $this->recipePayload($recipe),
            'Detail resep berhasil diambil.'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'required|array',
            'steps'       => 'required|array',
            'category'    => 'required|string',
            'image'       => 'nullable|image|max:5120',
        ]);

        $recipe = Recipe::create([
            'user_id'     => $request->user()->id_user,
            'title'       => $request->title,
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'steps'       => $request->steps,
            'category'    => $request->category,
            'image_path'  => $request->hasFile('image')
                ? $request->file('image')->store('recipes', 'public') : null,
        ]);

        return $this->successResponse(
            $this->recipePayload($recipe->load('user:id_user,username,full_name')),
            'Resep berhasil dibuat.'
        );
    }

    public function getSubstitution($recipeId)
    {
        $recipe = Recipe::findOrFail($recipeId);

        $cached = RecipeSubstitution::where('recipe_id', $recipeId)
            ->where('created_at', '>', now()->subDays(7))
            ->latest()->first();

        if ($cached) {
            return $this->successResponse(
                $cached->substitution_result,
                'Hasil substitusi resep berhasil diambil.',
                200,
                ['cached' => true]
            );
        }

        $ingredientNames = collect($recipe->ingredients)->pluck('name')->toArray();

        try {
            $result = $this->gemini->substituteIngredients($ingredientNames, $recipe->title);

            RecipeSubstitution::create([
                'recipe_id'            => $recipeId,
                'original_ingredients' => $ingredientNames,
                'substitution_result'  => $result,
                'requested_by'         => request()->user()->id_user,
            ]);

            return $this->successResponse($result, 'Substitusi bahan halal berhasil dibuat.');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menganalisis bahan: ' . $e->getMessage(), 500);
        }
    }

    public function halalSwitch($recipeId)
    {
        return $this->getSubstitution($recipeId);
    }

    private function recipePayload(Recipe $recipe): array
    {
        return [
            'id' => $recipe->id,
            'title' => $recipe->title,
            'description' => $recipe->description,
            'ingredients' => $recipe->ingredients ?? [],
            'steps' => $recipe->steps ?? [],
            'category' => $recipe->category,
            'is_halal_verified' => (bool) $recipe->is_halal_verified,
            'image_path' => $recipe->image_path ? Storage::disk('public')->url($recipe->image_path) : null,
            'user' => $recipe->user ? [
                'id_user' => $recipe->user->id_user,
                'username' => $recipe->user->username,
                'full_name' => $recipe->user->full_name,
            ] : null,
            'created_at' => optional($recipe->created_at)?->toIso8601String(),
        ];
    }
}
