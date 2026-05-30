@extends('admin.master')

@section('title', 'Medicine Management | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Medicines</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">Medicine Inventory</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Review status kehalalan obat-obatan, data farmasi, dan sinkronisasi dengan OpenFDA.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <form action="{{ route('admin.medicines.seed') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline" style="color: var(--accent-color); border-color: var(--accent-color);">
                <i class="fas fa-sync-alt"></i> Sync Common Drugs
            </button>
        </form>
        <button class="btn btn-primary" onclick="openImportModal()">
            <i class="fas fa-plus"></i> Import FDA Medicine
        </button>
    </div>
</div>

<!-- Stats Row -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 32px;">
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Total Medicines</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-main);">{{ number_format($stats['total']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Halal Certified</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--success);">{{ number_format($stats['halal']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Syubhat / Review</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--accent-color);">{{ number_format($stats['syubhat']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">From OpenFDA</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--primary-color);">{{ number_format($stats['from_fda']) }}</div>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body">
        <form action="{{ route('admin.medicines.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 16px; align-items: center;">
            <div class="input-group" style="position: relative; margin-bottom: 0;">
                <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, brand, or manufacturer..." style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
            </div>
            
            <select name="halal_status" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="">All Halal Status</option>
                <option value="halal" {{ request('halal_status') == 'halal' ? 'selected' : '' }}>Halal Only</option>
                <option value="syubhat" {{ request('halal_status') == 'syubhat' ? 'selected' : '' }}>Syubhat Only</option>
                <option value="haram" {{ request('halal_status') == 'haram' ? 'selected' : '' }}>Haram Only</option>
            </select>
            
            <select name="source" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="">All Sources</option>
                <option value="openfda" {{ request('source') == 'openfda' ? 'selected' : '' }}>OpenFDA</option>
                <option value="local" {{ request('source') == 'local' ? 'selected' : '' }}>Local/Manual</option>
                <option value="fallback_seed" {{ request('source') == 'fallback_seed' ? 'selected' : '' }}>System Seed</option>
            </select>
            
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                <i class="fas fa-sync-alt"></i>
            </button>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table style="border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="padding: 16px 24px;">Medicine Info</th>
                        <th style="padding: 16px;">Generic Name</th>
                        <th style="padding: 16px;">Manufacturer</th>
                        <th style="padding: 16px;">Source</th>
                        <th style="padding: 16px;">Halal Status</th>
                        <th style="padding: 16px 24px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicines as $medicine)
                    <tr>
                        <td style="padding: 16px 24px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--bg-light); display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid var(--border-color); flex-shrink: 0;">
                                    <img src="{{ $medicine->image_url }}" alt="{{ $medicine->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='https://loremflickr.com/100/100/{{ urlencode($medicine->name) }},medicine,capsule?lock={{ $medicine->id }}'">
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 14px; line-height: 1.2;">{{ Str::limit($medicine->name, 40) }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">{{ $medicine->brand_name ?: 'No Brand' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px; font-size: 13px; color: var(--text-main);">{{ Str::limit($medicine->generic_name ?: '--', 30) }}</td>
                        <td style="padding: 16px; font-size: 13px; color: var(--text-muted);">{{ Str::limit($medicine->manufacturer ?: 'Unknown', 25) }}</td>
                        <td style="padding: 16px;">
                            @if($medicine->source == 'openfda')
                                <span style="font-size: 10px; font-weight: 800; color: #3c8dbc; display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-external-link-alt"></i> OPENFDA
                                </span>
                            @else
                                <span style="font-size: 10px; font-weight: 800; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-database"></i> LOCAL
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $hStatus = strtolower($medicine->halal_status);
                            @endphp
                            @if($hStatus == 'halal')
                                <span class="badge badge-success">HALAL</span>
                            @elseif($hStatus == 'syubhat' || $hStatus == 'diragukan')
                                <span class="badge badge-warning">SYUBHAT</span>
                            @else
                                <span class="badge badge-danger">HARAM</span>
                            @endif
                        </td>
                        <td style="padding: 16px 24px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="#" class="btn btn-outline" style="padding: 8px; color: var(--primary-color); border-color: var(--border-color);"><i class="fas fa-edit"></i></a>
                                <form action="#" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--danger); border-color: var(--border-color);" onclick="return confirm('Hapus data obat ini?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 64px; color: var(--text-muted);">No medicines found in the database. Try syncing with OpenFDA.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer" style="padding: 16px 24px;">
        {{ $medicines->links() }}
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; padding: 32px; border-radius: 16px; width: 100%; max-width: 500px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <h2 style="margin: 0 0 8px; font-size: 22px; color: var(--primary-color);">Import FDA Medicine</h2>
        <p style="margin: 0 0 24px; color: var(--text-muted); font-size: 14px;">Masukkan nama obat untuk mencari dan mengimport data resmi dari database FDA Amerika Serikat.</p>
        
        <form action="{{ route('admin.medicines.import') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 24px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;">Drug Name</label>
                <input type="text" name="drug_name" required placeholder="e.g. Advil, Paracetamol..." style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeImportModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Search & Import</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openImportModal() {
        document.getElementById('importModal').style.display = 'flex';
    }
    
    function closeImportModal() {
        document.getElementById('importModal').style.display = 'none';
    }
</script>
@endpush
