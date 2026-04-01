@extends('admin.layouts.admin_layout')

@section('title', 'AI Prompts - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Admin</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">AI Prompt Management</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">AI Prompt Management</h2>
        <p class="text-slate-500 text-sm mt-1">Configure Gemini AI prompts for each feature without redeploying.</p>
    </div>
    <a href="{{ route('admin.ai-prompts.create') }}" class="px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition flex items-center space-x-2">
        <span class="material-icons-round text-sm">add</span>
        <span>New Prompt</span>
    </a>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-xl text-sm font-medium">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @forelse($prompts as $prompt)
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">{{ $prompt->feature_name }}</h3>
                <code class="text-xs text-slate-400 bg-slate-50 dark:bg-slate-800 px-2 py-0.5 rounded mt-1 inline-block">{{ $prompt->feature_key }}</code>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs font-bold text-slate-400">v{{ $prompt->version }}</span>
                <form action="{{ route('admin.ai-prompts.toggle', $prompt) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1 rounded-full text-xs font-bold {{ $prompt->is_active ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                        {{ $prompt->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-slate-50 dark:bg-slate-800 rounded-lg p-3 mb-4 max-h-24 overflow-y-auto">
            <p class="text-xs text-slate-600 dark:text-slate-400 font-mono whitespace-pre-wrap">{{ \Illuminate\Support\Str::limit($prompt->system_prompt, 200) }}</p>
        </div>

        <div class="flex items-center justify-between text-xs text-slate-400">
            <div class="flex items-center space-x-4">
                <span>🌡️ {{ $prompt->temperature }}</span>
                <span>📝 {{ number_format($prompt->max_tokens) }} tokens</span>
                <span>🤖 {{ $prompt->model }}</span>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.ai-prompts.edit', $prompt) }}" class="text-primary font-bold hover:underline">Edit</a>
                <form action="{{ route('admin.ai-prompts.destroy', $prompt) }}" method="POST" onsubmit="return confirm('Delete this prompt?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500 font-bold hover:underline">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="lg:col-span-2 text-center py-16 text-slate-400">
        <span class="material-icons-round text-5xl mb-4">psychology</span>
        <p class="text-lg font-medium mb-2">No AI prompts configured</p>
        <p class="mb-4">Create prompts for features like health_assistant, halal_analysis, etc.</p>
        <a href="{{ route('admin.ai-prompts.create') }}" class="px-6 py-2 bg-primary text-white rounded-lg font-bold">Create First Prompt</a>
    </div>
    @endforelse
</div>

<!-- Test Prompt Section -->
<div class="mt-8 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
    <h4 class="text-lg font-bold text-slate-800 dark:text-white mb-4">🧪 Test Prompt</h4>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-bold text-slate-600 mb-2">System Prompt</label>
            <textarea id="testSystemPrompt" rows="6" class="w-full p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm font-mono" placeholder="Enter system prompt..."></textarea>
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-600 mb-2">Test Input</label>
            <textarea id="testInput" rows="4" class="w-full p-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm" placeholder="Enter test user message..."></textarea>
            <button onclick="testPrompt()" id="testBtn" class="mt-3 px-6 py-2 bg-violet-600 text-white text-sm font-bold rounded-lg hover:bg-violet-700 transition w-full">
                ⚡ Send to Gemini
            </button>
        </div>
    </div>
    <div id="testResult" class="mt-4 hidden">
        <label class="block text-sm font-bold text-slate-600 mb-2">Response</label>
        <div id="testResultContent" class="p-4 bg-slate-50 dark:bg-slate-800 rounded-lg text-sm whitespace-pre-wrap max-h-64 overflow-y-auto"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function testPrompt() {
    const btn = document.getElementById('testBtn');
    const resultDiv = document.getElementById('testResult');
    const resultContent = document.getElementById('testResultContent');

    btn.disabled = true;
    btn.innerHTML = '⏳ Processing...';

    try {
        const response = await fetch('{{ route("admin.ai-prompts.test") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                system_prompt: document.getElementById('testSystemPrompt').value,
                test_input: document.getElementById('testInput').value,
            })
        });
        const data = await response.json();
        resultDiv.classList.remove('hidden');
        resultContent.textContent = data.success ? data.response : 'Error: ' + data.error;
        resultContent.className = data.success
            ? 'p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg text-sm whitespace-pre-wrap max-h-64 overflow-y-auto text-emerald-800'
            : 'p-4 bg-red-50 dark:bg-red-900/20 rounded-lg text-sm whitespace-pre-wrap max-h-64 overflow-y-auto text-red-800';
    } catch (e) {
        resultDiv.classList.remove('hidden');
        resultContent.textContent = 'Network error: ' + e.message;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '⚡ Send to Gemini';
    }
}
</script>
@endpush
