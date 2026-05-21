<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\NutritionRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NutritionRulesManagerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 20);
        $query = NutritionRule::query()->latest();

        if ($request->filled('severity')) {
            $query->where('severity', (string) $request->string('severity'));
        }
        if ($request->filled('q')) {
            $q = (string) $request->string('q');
            $query->where('name', 'like', "%{$q}%");
        }

        return response()->json([
            'success' => true,
            'data' => $query->paginate(max(1, min(100, $perPage))),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'metric' => ['required', 'string', 'max:80'],
            'threshold' => ['required', 'numeric'],
            'unit' => ['required', 'string', 'max:20'],
            'severity' => ['required', 'in:low,moderate,high,critical'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $rule = NutritionRule::create([
            ...$payload,
            'is_active' => $payload['is_active'] ?? true,
        ]);

        return response()->json(['success' => true, 'data' => $rule], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['sometimes', 'string', 'max:150'],
            'metric' => ['sometimes', 'string', 'max:80'],
            'threshold' => ['sometimes', 'numeric'],
            'unit' => ['sometimes', 'string', 'max:20'],
            'severity' => ['sometimes', 'in:low,moderate,high,critical'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $rule = NutritionRule::query()->findOrFail($id);
        $rule->update($payload);

        return response()->json(['success' => true, 'data' => $rule]);
    }

    public function destroy(int $id): JsonResponse
    {
        $rule = NutritionRule::query()->findOrFail($id);
        $rule->delete();

        return response()->json(['success' => true]);
    }

}
