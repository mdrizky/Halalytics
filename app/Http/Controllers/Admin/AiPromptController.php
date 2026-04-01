<?php

namespace App\Http\Controllers\Admin;

use App\Models\AiPrompt;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\GeminiService;

class AiPromptController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $prompts = AiPrompt::orderBy('feature_key')->get();
        return view('admin.ai-prompts.index', compact('prompts'));
    }

    public function create()
    {
        return view('admin.ai-prompts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'feature_key' => 'required|string|unique:ai_prompts|max:100',
            'feature_name' => 'required|string|max:255',
            'system_prompt' => 'required|string',
            'user_prompt_template' => 'nullable|string',
            'temperature' => 'required|numeric|between:0,2',
            'max_tokens' => 'required|integer|min:100|max:8192',
            'model' => 'required|string',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['version'] = 1;

        AiPrompt::create($validated);

        return redirect()->route('admin.ai-prompts.index')
            ->with('success', 'Prompt AI berhasil dibuat.');
    }

    public function edit(AiPrompt $aiPrompt)
    {
        return view('admin.ai-prompts.edit', compact('aiPrompt'));
    }

    public function update(Request $request, AiPrompt $aiPrompt)
    {
        $validated = $request->validate([
            'feature_name' => 'required|string|max:255',
            'system_prompt' => 'required|string',
            'user_prompt_template' => 'nullable|string',
            'temperature' => 'required|numeric|between:0,2',
            'max_tokens' => 'required|integer|min:100|max:8192',
            'model' => 'required|string',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['version'] = $aiPrompt->version + 1;

        $aiPrompt->update($validated);

        return redirect()->route('admin.ai-prompts.index')
            ->with('success', 'Prompt AI berhasil diperbarui (v' . $validated['version'] . ').');
    }

    public function destroy(AiPrompt $aiPrompt)
    {
        $aiPrompt->delete();
        return redirect()->route('admin.ai-prompts.index')
            ->with('success', 'Prompt AI berhasil dihapus.');
    }

    /**
     * Test a prompt directly against Gemini
     */
    public function test(Request $request)
    {
        $request->validate([
            'system_prompt' => 'required|string',
            'test_input' => 'required|string',
            'temperature' => 'nullable|numeric|between:0,2',
            'max_tokens' => 'nullable|integer|min:100|max:8192',
        ]);

        try {
            $gemini = app(GeminiService::class);
            $result = $gemini->generateText(
                $request->system_prompt . "\n\nUser: " . $request->test_input,
                $request->temperature ?? 0.7,
                $request->max_tokens ?? 2048
            );

            return response()->json([
                'success' => true,
                'response' => $result,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle active status
     */
    public function toggle(AiPrompt $aiPrompt)
    {
        $aiPrompt->update(['is_active' => !$aiPrompt->is_active]);
        $status = $aiPrompt->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Prompt '{$aiPrompt->feature_name}' berhasil {$status}.");
    }
}
