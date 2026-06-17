<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MealPlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = MealPlan::query();

        if ($user->role === 'ahli_gizi') {
            $query->where('nutritionist_id', $user->id_user);
        } else {
            $query->where('user_id', $user->id_user);
        }

        $mealPlans = $query
            ->with(['nutritionist:id_user,username,full_name', 'user:id_user,username,full_name'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $mealPlans]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $mealPlan = MealPlan::with(['nutritionist', 'user'])->findOrFail($id);

        $isAuthorized = $mealPlan->nutritionist_id === $user->id_user
            || $mealPlan->user_id === $user->id_user
            || $user->role === 'admin';

        if (!$isAuthorized) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json(['success' => true, 'data' => $mealPlan]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'ahli_gizi') {
            return response()->json(['success' => false, 'message' => 'Only nutritionists can create meal plans'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id_user',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'meals' => 'required|array|min:1',
            'meals.*.day' => 'required|integer|min:1',
            'meals.*.meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'meals.*.items' => 'required|array|min:1',
            'meals.*.items.*.name' => 'required|string',
            'meals.*.items.*.quantity' => 'required|string',
            'meals.*.items.*.calories' => 'nullable|numeric',
            'notes' => 'nullable|string|max:2000',
            'duration_days' => 'required|integer|min:1|max:365',
            'start_date' => 'required|date|after:today',
            'target_calories' => 'nullable|integer|min:500|max:5000',
            'nutritional_targets' => 'nullable|array',
        ]);

        $endDate = \Carbon\Carbon::parse($validated['start_date'])->addDays($validated['duration_days'] - 1);

        $mealPlan = MealPlan::create([
            'nutritionist_id' => $user->id_user,
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'meals' => $validated['meals'],
            'notes' => $validated['notes'] ?? null,
            'duration_days' => $validated['duration_days'],
            'start_date' => $validated['start_date'],
            'end_date' => $endDate,
            'target_calories' => $validated['target_calories'] ?? null,
            'nutritional_targets' => $validated['nutritional_targets'] ?? null,
            'status' => 'draft',
        ]);

        return response()->json(['success' => true, 'data' => $mealPlan->load('nutritionist', 'user')], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $mealPlan = MealPlan::findOrFail($id);

        if ($mealPlan->nutritionist_id !== $user->id_user && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'meals' => 'sometimes|array|min:1',
            'meals.*.day' => 'required|integer|min:1',
            'meals.*.meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'meals.*.items' => 'required|array|min:1',
            'meals.*.items.*.name' => 'required|string',
            'meals.*.items.*.quantity' => 'required|string',
            'meals.*.items.*.calories' => 'nullable|numeric',
            'notes' => 'nullable|string|max:2000',
            'status' => 'sometimes|in:draft,active,completed,archived',
            'target_calories' => 'nullable|integer|min:500|max:5000',
            'nutritional_targets' => 'nullable|array',
        ]);

        $mealPlan->update($validated);

        return response()->json(['success' => true, 'data' => $mealPlan->load('nutritionist', 'user')]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $mealPlan = MealPlan::findOrFail($id);

        if ($mealPlan->nutritionist_id !== $user->id_user && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $mealPlan->delete();

        return response()->json(['success' => true, 'message' => 'Meal plan deleted']);
    }

    public function activate(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $mealPlan = MealPlan::findOrFail($id);

        if ($mealPlan->nutritionist_id !== $user->id_user && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $mealPlan->update(['status' => 'active']);

        return response()->json(['success' => true, 'message' => 'Meal plan activated', 'data' => $mealPlan]);
    }
}
