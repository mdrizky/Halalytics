<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\HalalRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HalalRulesManagerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 20);
        $query = HalalRule::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', (string) $request->string('status'));
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
            'rule_type' => ['required', 'string', 'max:50'],
            'keyword' => ['required', 'string', 'max:150'],
            'status' => ['required', 'in:halal,syubhat,haram,unknown'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $rule = HalalRule::create([
            ...$payload,
            'is_active' => $payload['is_active'] ?? true,
        ]);

        return response()->json(['success' => true, 'data' => $rule], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['sometimes', 'string', 'max:150'],
            'rule_type' => ['sometimes', 'string', 'max:50'],
            'keyword' => ['sometimes', 'string', 'max:150'],
            'status' => ['sometimes', 'in:halal,syubhat,haram,unknown'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $rule = HalalRule::query()->findOrFail($id);
        $rule->update($payload);

        return response()->json(['success' => true, 'data' => $rule]);
    }

    public function destroy(int $id): JsonResponse
    {
        $rule = HalalRule::query()->findOrFail($id);
        $rule->delete();

        return response()->json(['success' => true]);
    }

}
