@extends('admin.master')

@section('title', 'Forbidden Ingredients | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Forbidden Database</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--danger);">Forbidden & High-Risk Ingredients</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Review and manage ingredients that are haram, doubtful, or hazardous to health.</p>
    </div>
    <div>
        <button class="btn btn-primary" style="background: var(--danger); border-color: var(--danger);" onclick="openAddModal()">
            <i class="fas fa-ban"></i> Add Forbidden Item
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 24px; padding: 16px; border-radius: 8px; background: rgba(45, 106, 79, 0.1); color: var(--primary-color); border: 1px solid var(--primary-color); display: flex; align-items: center; gap: 12px;">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body">
        <form action="{{ route('admin.forbidden.index') }}" method="GET" style="display: grid; grid-template-columns: 3fr auto; gap: 16px; align-items: center;">
            <div class="input-group" style="position: relative; margin-bottom: 0;">
                <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, code, or alias..." style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
            </div>
            
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                <i class="fas fa-filter"></i> Search
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table style="border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="padding: 16px 24px;">Ingredient</th>
                        <th style="padding: 16px;">Type</th>
                        <th style="padding: 16px;">Risk Level</th>
                        <th style="padding: 16px;">Reason / Source</th>
                        <th style="padding: 16px 24px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ingredients as $ing)
                    <tr>
                        <td style="padding: 16px 24px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(231, 76, 60, 0.1); display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid rgba(231, 76, 60, 0.2); flex-shrink: 0;">
                                    <i class="fas fa-skull-crossbones" style="font-size: 18px; color: var(--danger);"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 14px;">{{ $ing->name }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                                        @if($ing->code) <span style="font-family: monospace; background: #eee; padding: 1px 4px; border-radius: 3px;">{{ $ing->code }}</span> @endif
                                        @if($ing->aliases && count($ing->aliases) > 0)
                                            • {{ implode(', ', array_slice($ing->aliases, 0, 2)) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            @if($ing->type == 'halal_haram')
                                <span class="badge" style="background: rgba(231, 76, 60, 0.1); color: var(--danger);">HALAL/HARAM</span>
                            @elseif($ing->type == 'health_hazard')
                                <span class="badge" style="background: rgba(244, 162, 97, 0.1); color: var(--accent-color);">HEALTH HAZARD</span>
                            @else
                                <span class="badge" style="background: rgba(45, 106, 79, 0.1); color: var(--primary-color);">ALLERGEN</span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @if($ing->risk_level == 'high')
                                <div style="display: flex; align-items: center; gap: 6px; color: var(--danger); font-size: 12px; font-weight: 800;">
                                    <i class="fas fa-exclamation-triangle"></i> HIGH RISK
                                </div>
                            @elseif($ing->risk_level == 'medium')
                                <div style="display: flex; align-items: center; gap: 6px; color: var(--accent-color); font-size: 12px; font-weight: 800;">
                                    <i class="fas fa-exclamation-circle"></i> MEDIUM
                                </div>
                            @else
                                <div style="display: flex; align-items: center; gap: 6px; color: var(--primary-color); font-size: 12px; font-weight: 800;">
                                    <i class="fas fa-info-circle"></i> LOW RISK
                                </div>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            <div style="font-size: 13px; font-weight: 700; color: var(--text-main);">{{ $ing->reason }}</div>
                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px; max-width: 250px;">{{ Str::limit($ing->description, 60) }}</div>
                        </td>
                        <td style="padding: 16px 24px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <button onclick="openEditModal({{ $ing->id }}, '{{ addslashes($ing->name) }}', '{{ $ing->code }}', '{{ $ing->type }}', '{{ $ing->risk_level }}', '{{ addslashes($ing->reason) }}', '{{ addslashes($ing->description) }}', '{{ $ing->aliases ? implode(', ', $ing->aliases) : '' }}')" class="btn btn-outline" style="padding: 8px; color: var(--primary-color); border-color: var(--border-color);"><i class="fas fa-edit"></i></button>
                                <form action="{{ route('admin.forbidden.destroy', $ing->id) }}" method="POST" onsubmit="return confirm('Hapus item ini dari database forbidden?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--danger); border-color: var(--border-color);"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 64px; color: var(--text-muted);">No forbidden items found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer" style="padding: 16px 24px;">
        {{ $ingredients->links() }}
    </div>
</div>

<!-- Modals for Add/Edit -->
<div id="itemModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; padding: 32px; border-radius: 16px; width: 100%; max-width: 600px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">
        <h2 id="modalTitle" style="margin: 0 0 8px; font-size: 22px; color: var(--danger);">Add Forbidden Item</h2>
        <p style="margin: 0 0 24px; color: var(--text-muted); font-size: 14px;">Konfigurasi parameter risiko dan alasan pelarangan bahan.</p>
        
        <form id="itemForm" method="POST">
            @csrf
            <div id="methodField"></div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Ingredient Name *</label>
                    <input type="text" name="name" id="field_name" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">E-Number Code</label>
                    <input type="text" name="code" id="field_code" placeholder="e.g. E120" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Aliases (Comma separated)</label>
                <input type="text" name="aliases" id="field_aliases" placeholder="Cochineal, Carmine, CI 75470..." style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Classification Type</label>
                    <select name="type" id="field_type" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                        <option value="halal_haram">Halal/Haram Critical</option>
                        <option value="health_hazard">Health Hazard / Poison</option>
                        <option value="allergen">Common Allergen</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Risk Level</label>
                    <select name="risk_level" id="field_risk" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                        <option value="high">High Risk / Forbidden</option>
                        <option value="medium">Medium Risk / Doubtful</option>
                        <option value="low">Low Risk / Caution</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Primary Reason *</label>
                <input type="text" name="reason" id="field_reason" required placeholder="e.g. Derived from non-halal animal source" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
            </div>
            
            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Detailed Description</label>
                <textarea name="description" id="field_description" rows="3" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none; resize: none;"></textarea>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Save Ingredient</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const modal = document.getElementById('itemModal');
    const form = document.getElementById('itemForm');
    const modalTitle = document.getElementById('modalTitle');
    const methodField = document.getElementById('methodField');
    const submitBtn = document.getElementById('submitBtn');
    
    function openAddModal() {
        form.reset();
        form.action = '{{ route("admin.forbidden.store") }}';
        methodField.innerHTML = '';
        modalTitle.innerText = 'Add Forbidden Item';
        modalTitle.style.color = 'var(--danger)';
        submitBtn.style.background = 'var(--danger)';
        submitBtn.style.borderColor = 'var(--danger)';
        modal.style.display = 'flex';
    }
    
    function openEditModal(id, name, code, type, risk, reason, desc, aliases) {
        form.action = '/admin/forbidden/' + id;
        methodField.innerHTML = '@method("PUT")';
        modalTitle.innerText = 'Edit Forbidden Item';
        modalTitle.style.color = 'var(--primary-color)';
        submitBtn.style.background = 'var(--primary-color)';
        submitBtn.style.borderColor = 'var(--primary-color)';
        
        document.getElementById('field_name').value = name;
        document.getElementById('field_code').value = code || '';
        document.getElementById('field_type').value = type;
        document.getElementById('field_risk').value = risk;
        document.getElementById('field_reason').value = reason;
        document.getElementById('field_description').value = desc || '';
        document.getElementById('field_aliases').value = aliases || '';
        
        modal.style.display = 'flex';
    }
    
    function closeModal() {
        modal.style.display = 'none';
    }
    
    window.onclick = function(event) {
        if (event.target == modal) closeModal();
    }
</script>
@endpush
