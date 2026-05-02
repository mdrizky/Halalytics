@extends('admin.master')

@section('title', 'AI Prompt Management | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">AI Prompts</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">AI Core Intelligence</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Konfigurasi sistem prompt Gemini AI untuk asisten medis, analisis komposisi, dan fitur cerdas lainnya.</p>
    </div>
    <div>
        <a href="{{ route('admin.ai-prompts.create') }}" class="btn btn-primary">
            <i class="fas fa-brain"></i> Add New Prompt Config
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 24px; padding: 16px; border-radius: 8px; background: rgba(45, 106, 79, 0.1); color: var(--primary-color); border: 1px solid var(--primary-color); display: flex; align-items: center; gap: 12px;">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

<div class="grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(450px, 1fr)); gap: 24px; margin-bottom: 48px;">
    @forelse($prompts as $prompt)
    <div class="card prompt-card" style="border: 1px solid var(--border-color); transition: all 0.3s; position: relative;">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div>
                    <h3 style="margin: 0; font-size: 18px; color: var(--text-main);">{{ $prompt->feature_name }}</h3>
                    <div style="display: flex; gap: 8px; margin-top: 6px;">
                        <span style="font-family: monospace; font-size: 11px; background: var(--bg-light); padding: 2px 8px; border-radius: 4px; color: var(--text-muted);">{{ $prompt->feature_key }}</span>
                        <span style="font-size: 11px; color: var(--primary-color); font-weight: 700;">v{{ $prompt->version }}</span>
                    </div>
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <form action="{{ route('admin.ai-prompts.toggle', $prompt) }}" method="POST">
                        @csrf
                        <button type="submit" class="badge {{ $prompt->is_active ? 'badge-success' : '' }}" style="border: none; cursor: pointer; {{ !$prompt->is_active ? 'background: #eee; color: #777;' : '' }}">
                            {{ $prompt->is_active ? 'ACTIVE' : 'INACTIVE' }}
                        </button>
                    </form>
                </div>
            </div>
            
            <div style="background: #1e1e1e; color: #d4d4d4; padding: 16px; border-radius: 8px; font-family: 'Fira Code', monospace; font-size: 11px; line-height: 1.5; margin-bottom: 16px; height: 120px; overflow-y: auto;">
                <span style="color: #6a9955;">// System Prompt Intelligence</span><br>
                {{ $prompt->system_prompt }}
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 16px;">
                <div style="display: flex; gap: 16px; font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">
                    <span><i class="fas fa-thermometer-half"></i> {{ $prompt->temperature }} temp</span>
                    <span><i class="fas fa-microchip"></i> {{ $prompt->model }}</span>
                    <span><i class="fas fa-coins"></i> {{ number_format($prompt->max_tokens) }} tkn</span>
                </div>
                <div style="display: flex; gap: 12px;">
                    <a href="{{ route('admin.ai-prompts.edit', $prompt) }}" style="color: var(--primary-color); text-decoration: none; font-size: 13px; font-weight: 700;"><i class="fas fa-cog"></i> Config</a>
                    <form action="{{ route('admin.ai-prompts.destroy', $prompt) }}" method="POST" onsubmit="return confirm('Hapus konfigurasi prompt ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: var(--danger); font-size: 13px; font-weight: 700; cursor: pointer;"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column: 1/-1; text-align: center; padding: 100px; background: white; border-radius: 16px; border: 2px dashed var(--border-color);">
        <i class="fas fa-robot" style="font-size: 64px; margin-bottom: 24px; opacity: 0.1; display: block;"></i>
        <div style="font-size: 20px; font-weight: 700; color: var(--text-muted);">No AI Prompts Found</div>
        <p style="margin-top: 8px; color: var(--text-muted);">Mulai tambahkan konfigurasi prompt untuk fitur-fitur AI aplikasi Anda.</p>
    </div>
    @endforelse
</div>

<!-- AI Playground / Testing Section -->
<div class="card" style="border: 1px solid var(--primary-color); background: rgba(45, 106, 79, 0.02);">
    <div class="card-body">
        <h2 style="font-size: 20px; color: var(--primary-color); margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-vial"></i> AI Intelligence Playground (Sandbox)
        </h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Experimental System Prompt</label>
                    <textarea id="testSystemPrompt" rows="6" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: white; outline: none; font-family: monospace; font-size: 12px;" placeholder="Define AI behavior here..."></textarea>
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">User Input Test Case</label>
                    <textarea id="testInput" rows="3" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: white; outline: none; font-size: 13px;" placeholder="Simulate user message..."></textarea>
                </div>
                <button onclick="testPrompt()" id="testBtn" class="btn btn-primary" style="width: 100%; margin-top: 16px; height: 48px;">
                    <i class="fas fa-bolt"></i> Send to Gemini API
                </button>
            </div>
            
            <div>
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Execution Result</label>
                <div id="testResult" style="background: #1e1e1e; color: #569cd6; padding: 20px; border-radius: 8px; height: 310px; overflow-y: auto; font-family: 'Fira Code', monospace; font-size: 12px; border: 4px solid #333;">
                    <div style="color: #6a9955;">// Waiting for execution...</div>
                    <div id="testResultContent" style="margin-top: 12px; color: #d4d4d4; white-space: pre-wrap;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
async function testPrompt() {
    const btn = document.getElementById('testBtn');
    const resultContent = document.getElementById('testResultContent');
    const systemPrompt = document.getElementById('testSystemPrompt').value;
    const testInput = document.getElementById('testInput').value;

    if (!systemPrompt || !testInput) {
        alert('Please fill both system prompt and test input.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Gemini Request...';
    resultContent.innerHTML = '<span style="color: #ce9178;">[SYSTEM] Initializing request to Gemini-1.5-Flash...</span>';

    try {
        const response = await fetch('{{ route("admin.ai-prompts.test") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                system_prompt: systemPrompt,
                test_input: testInput,
            })
        });
        const data = await response.json();
        
        if (data.success) {
            resultContent.innerHTML = `<span style="color: #4ec9b0;">[SUCCESS] Response Received:</span>\n\n${data.response}`;
        } else {
            resultContent.innerHTML = `<span style="color: #f44747;">[ERROR] API Failure:</span>\n\n${data.error}`;
        }
    } catch (e) {
        resultContent.innerHTML = `<span style="color: #f44747;">[FATAL] Network Error:</span>\n\n${e.message}`;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-bolt"></i> Send to Gemini API';
    }
}
</script>
@endpush
