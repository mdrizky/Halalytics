<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiPrompt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIPromptManagerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 20);
        $query = AiPrompt::query()->latest();

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
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:100'],
            'prompt' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $prompt = AiPrompt::create([
            ...$payload,
            'version' => 1,
            'is_active' => $payload['is_active'] ?? true,
        ]);

        return response()->json(['success' => true, 'data' => $prompt], 201);
    }
}
