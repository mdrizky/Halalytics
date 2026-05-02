@extends('admin.master')

@section('title', 'OCR Verification Panel | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">OCR Management</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">OCR Verification Engine</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Review hasil ekstraksi teks dari kemasan produk dan verifikasi akurasi bahan yang terdeteksi.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ route('admin.ocr.export') }}" class="btn btn-outline">
            <i class="fas fa-file-export"></i> Export Data
        </a>
        <button onclick="refreshOcrDashboard()" class="btn btn-primary">
            <i class="fas fa-sync-alt"></i> Refresh Queue
        </button>
    </div>
</div>

<!-- Stats Row -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 32px;">
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Queue Size</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-main);" id="ocrTotal">0</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Pending Review</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--accent-color);" id="ocrPending">0</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Approved Today</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--success);" id="ocrApprovedToday">0</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Rejected Today</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--danger);" id="ocrRejectedToday">0</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 400px; gap: 24px; align-items: start;">
    <!-- Main Table Section -->
    <div class="card">
        <div class="card-body" style="padding: 0;">
            <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 16px; color: var(--text-main);">OCR Scan Queue</h3>
                <div style="display: flex; gap: 8px;" id="ocrFilters">
                    <button data-filter="pending" class="badge active-filter" style="cursor: pointer; border: none; padding: 6px 12px; font-weight: 700;">Pending</button>
                    <button data-filter="approved" class="badge" style="cursor: pointer; border: none; padding: 6px 12px; font-weight: 700; background: #eee; color: #777;">Approved</button>
                    <button data-filter="rejected" class="badge" style="cursor: pointer; border: none; padding: 6px 12px; font-weight: 700; background: #eee; color: #777;">Rejected</button>
                </div>
            </div>
            <div class="table-container" style="max-height: 700px; overflow-y: auto;">
                <table style="border-collapse: collapse; width: 100%;">
                    <thead>
                        <tr style="position: sticky; top: 0; background: white; z-index: 10;">
                            <th style="padding: 16px 24px;">Product Info</th>
                            <th style="padding: 16px;">Confidence</th>
                            <th style="padding: 16px;">User</th>
                            <th style="padding: 16px;">Time</th>
                            <th style="padding: 16px 24px; text-align: right;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="ocrTableBody">
                        <!-- Content will be injected via JS -->
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 100px; color: var(--text-muted);">
                                <i class="fas fa-circle-notch fa-spin" style="font-size: 24px; margin-bottom: 16px;"></i><br>
                                Fetching OCR data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail Panel Section -->
    <div class="card" style="position: sticky; top: 24px;">
        <div class="card-body" id="ocrDetailPanel">
            <div style="text-align: center; padding: 64px 20px; color: var(--text-muted);">
                <i class="fas fa-file-signature" style="font-size: 48px; margin-bottom: 20px; opacity: 0.2;"></i>
                <div style="font-weight: 700;">Select an item</div>
                <div style="font-size: 13px;">Pilih baris di kiri untuk melihat detail scan.</div>
            </div>
        </div>
    </div>
</div>

<style>
    .active-filter {
        background: var(--primary-color) !important;
        color: white !important;
    }
    .ocr-row:hover {
        background: var(--bg-light);
        cursor: pointer;
    }
    .ocr-row.selected {
        background: rgba(45, 106, 79, 0.05);
        border-left: 4px solid var(--primary-color);
    }
    .confidence-bar {
        height: 6px;
        border-radius: 3px;
        background: #eee;
        overflow: hidden;
        margin-top: 4px;
    }
    .confidence-progress {
        height: 100%;
        background: var(--primary-color);
    }
</style>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
const ocrBaseUrl = '{{ url("/admin/ocr") }}';
let currentFilter = 'pending';
let selectedId = null;

// Filter buttons
document.querySelectorAll('#ocrFilters button').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('#ocrFilters button').forEach(b => {
            b.classList.remove('active-filter');
            b.style.background = '#eee';
            b.style.color = '#777';
        });
        btn.classList.add('active-filter');
        btn.style.background = 'var(--primary-color)';
        btn.style.color = 'white';
        currentFilter = btn.dataset.filter;
        loadOcrProducts();
    });
});

async function refreshOcrDashboard() {
    await Promise.all([loadOcrStatistics(), loadOcrProducts()]);
}

async function loadOcrStatistics() {
    try {
        const response = await fetch(`${ocrBaseUrl}/statistics`, { headers: { Accept: 'application/json' } });
        const data = await response.json();
        const stats = data.data || {};
        document.getElementById('ocrTotal').innerText = stats.total || 0;
        document.getElementById('ocrPending').innerText = stats.pending || 0;
        document.getElementById('ocrApprovedToday').innerText = stats.today_approved || 0;
        document.getElementById('ocrRejectedToday').innerText = stats.today_rejected || 0;
    } catch (e) { console.error(e); }
}

async function loadOcrProducts() {
    const tbody = document.getElementById('ocrTableBody');
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:50px;"><i class="fas fa-spinner fa-spin"></i></td></tr>';
    
    try {
        const response = await fetch(`${ocrBaseUrl}/${currentFilter}`, { headers: { Accept: 'application/json' } });
        const payload = await response.json();
        const items = payload.data?.data || [];
        
        if (items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:50px; color:var(--text-muted);">Queue empty.</td></tr>';
            return;
        }
        
        tbody.innerHTML = items.map(item => renderRow(item)).join('');
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:50px; color:var(--danger);">Failed to load queue.</td></tr>';
    }
}

function renderRow(item) {
    const confidence = item.confidence_score || 0;
    const date = new Date(item.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const isSelected = selectedId == item.id ? 'selected' : '';
    
    return `
        <tr class="ocr-row ${isSelected}" onclick="loadDetail(${item.id})">
            <td style="padding: 16px 24px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: #eee; overflow: hidden; flex-shrink: 0;">
                        <img src="${item.front_image_url || '/placeholder.png'}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <div style="font-weight: 700; color: var(--text-main); font-size: 13px;">${item.product_name || 'OCR Product'}</div>
                        <div style="font-size: 10px; color: var(--text-muted);">${item.brand || 'No Brand'}</div>
                    </div>
                </div>
            </td>
            <td style="padding: 16px;">
                <div style="font-size: 12px; font-weight: 700;">${confidence}%</div>
                <div class="confidence-bar"><div class="confidence-progress" style="width: ${confidence}%; background: ${confidence > 80 ? 'var(--primary-color)' : (confidence > 50 ? 'var(--accent-color)' : 'var(--danger)')}"></div></div>
            </td>
            <td style="padding: 16px; font-size: 12px; color: var(--text-muted); font-weight: 700;">
                ${item.user?.username || 'Guest'}
            </td>
            <td style="padding: 16px; font-size: 12px; color: var(--text-muted);">${date}</td>
            <td style="padding: 16px 24px; text-align: right;">
                <span class="badge" style="background: ${item.status == 'approved' ? 'rgba(45, 106, 79, 0.1)' : (item.status == 'rejected' ? 'rgba(231, 76, 60, 0.1)' : 'rgba(244, 162, 97, 0.1)')}; color: ${item.status == 'approved' ? 'var(--primary-color)' : (item.status == 'rejected' ? 'var(--danger)' : 'var(--accent-color)')}; font-size: 10px;">
                    ${item.status.toUpperCase()}
                </span>
            </td>
        </tr>
    `;
}

async function loadDetail(id) {
    selectedId = id;
    const panel = document.getElementById('ocrDetailPanel');
    panel.innerHTML = '<div style="text-align:center; padding:50px;"><i class="fas fa-spinner fa-spin"></i></div>';
    
    // Refresh row highlights
    document.querySelectorAll('.ocr-row').forEach(r => r.classList.remove('selected'));
    // (In real usage, we'd find the clicked row and add 'selected', but since we re-render we just need the state)
    
    try {
        const response = await fetch(`${ocrBaseUrl}/product/${id}`, { headers: { Accept: 'application/json' } });
        const payload = await response.json();
        const item = payload.data;
        
        renderDetail(item);
    } catch (e) {
        panel.innerHTML = '<div style="color:var(--danger); text-align:center;">Failed to load details.</div>';
    }
}

function renderDetail(item) {
    const panel = document.getElementById('ocrDetailPanel');
    const ingredients = item.ingredients || [];
    
    panel.innerHTML = `
        <div style="margin-bottom: 24px;">
            <h3 style="margin: 0; font-size: 18px; color: var(--primary-color);">${item.product_name || 'Untitled Scan'}</h3>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">ID: #${item.id} • Submitted by ${item.user?.username || 'Guest'}</div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px;">
            <div style="background: #eee; border-radius: 12px; height: 120px; overflow: hidden; cursor: zoom-in;" onclick="window.open('${item.front_image_url}', '_blank')">
                <img src="${item.front_image_url}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="background: #eee; border-radius: 12px; height: 120px; overflow: hidden; cursor: zoom-in;" onclick="window.open('${item.back_image_url}', '_blank')">
                <img src="${item.back_image_url}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        </div>
        
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Extracted Teks</label>
            <div style="background: var(--bg-light); padding: 12px; border-radius: 8px; font-size: 12px; color: var(--text-main); font-family: monospace; line-height: 1.5; max-height: 150px; overflow-y: auto;">
                ${item.extracted_text || 'No text extracted.'}
            </div>
        </div>
        
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Detected Ingredients (${ingredients.length})</label>
            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                ${ingredients.length > 0 ? ingredients.map(ing => `<span class="badge" style="background:rgba(45,106,79,0.1); color:var(--primary-color); font-size:10px;">${ing}</span>`).join('') : '<span style="font-size:12px; color:var(--text-muted);">No ingredients detected.</span>'}
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <button onclick="approveOcr(${item.id})" style="padding: 14px; border-radius: 12px; border: none; background: var(--primary-color); color: white; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fas fa-check-circle"></i> Approve
            </button>
            <button onclick="rejectOcr(${item.id})" style="padding: 14px; border-radius: 12px; border: none; background: var(--danger); color: white; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fas fa-times-circle"></i> Reject
            </button>
        </div>
    `;
}

async function approveOcr(id) {
    if(!confirm('Setujui hasil OCR ini untuk diproses lebih lanjut?')) return;
    try {
        const response = await fetch(`${ocrBaseUrl}/approve/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
            body: JSON.stringify({ notes: 'Verified via Premium OCR Panel' })
        });
        const data = await response.json();
        if(data.success) {
            refreshOcrDashboard();
            document.getElementById('ocrDetailPanel').innerHTML = '<div style="text-align:center; padding:64px; color:var(--success); font-weight:700;"><i class="fas fa-check-circle" style="font-size:48px; margin-bottom:16px;"></i><br>Approved successfully!</div>';
        }
    } catch (e) { alert('Approval failed'); }
}

async function rejectOcr(id) {
    const reason = prompt('Alasan penolakan:');
    if(reason === null) return;
    try {
        const response = await fetch(`${ocrBaseUrl}/reject/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
            body: JSON.stringify({ reason })
        });
        const data = await response.json();
        if(data.success) {
            refreshOcrDashboard();
            document.getElementById('ocrDetailPanel').innerHTML = '<div style="text-align:center; padding:64px; color:var(--danger); font-weight:700;"><i class="fas fa-times-circle" style="font-size:48px; margin-bottom:16px;"></i><br>Rejected successfully.</div>';
        }
    } catch (e) { alert('Rejection failed'); }
}

document.addEventListener('DOMContentLoaded', refreshOcrDashboard);
</script>
@endpush
