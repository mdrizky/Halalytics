@extends('admin.master')

@section('title', 'Cosmetic Management | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Cosmetics</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">Cosmetic & Skincare</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Review data keamanan dan kehalalan produk kosmetik, skincare, dan personal care.</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <form action="{{ route('admin.cosmetics.seed') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline" style="color: var(--accent-color); border-color: var(--accent-color);">
                <i class="fas fa-sync-alt"></i> Sync OpenBeautyFacts
            </button>
        </form>
        <button class="btn btn-primary" onclick="openSearchModal()">
            <i class="fas fa-search"></i> Import External Product
        </button>
    </div>
</div>

<!-- Stats Row -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 32px;">
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Total Cosmetics</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-main);">{{ number_format($stats['total']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Verified Safe</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--success);">{{ number_format($stats['aman']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Alert / Forbidden</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--danger);">{{ number_format($stats['bahaya'] + $stats['haram']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Global Database</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--primary-color);">{{ number_format($stats['from_obf']) }}</div>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body">
        <form action="{{ route('admin.cosmetics.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 16px; align-items: center;">
            <div class="input-group" style="position: relative; margin-bottom: 0;">
                <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, brand, or barcode..." style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
            </div>
            
            <select name="status" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="">All Safety Status</option>
                <option value="aman" {{ request('status') == 'aman' ? 'selected' : '' }}>Safe Only</option>
                <option value="waspada" {{ request('status') == 'waspada' ? 'selected' : '' }}>Caution Only</option>
                <option value="bahaya" {{ request('status') == 'bahaya' ? 'selected' : '' }}>Danger Only</option>
            </select>
            
            <select name="source" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="">All Sources</option>
                <option value="lokal" {{ request('source') == 'lokal' ? 'selected' : '' }}>Local/BPOM</option>
                <option value="open_beauty_facts" {{ request('source') == 'open_beauty_facts' ? 'selected' : '' }}>OpenBeautyFacts</option>
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
                        <th style="padding: 16px 24px;">Cosmetic Info</th>
                        <th style="padding: 16px;">Brand</th>
                        <th style="padding: 16px;">Safety Status</th>
                        <th style="padding: 16px;">Halal Review</th>
                        <th style="padding: 16px;">Source</th>
                        <th style="padding: 16px 24px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cosmetics as $item)
                    <tr>
                        <td style="padding: 16px 24px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--bg-light); display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid var(--border-color); flex-shrink: 0;">
                                    <img src="{{ $item->image_url }}" alt="" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='https://loremflickr.com/100/100/{{ urlencode($item->nama_produk) }},cosmetic?lock={{ $item->id }}'">
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 14px; line-height: 1.2;">{{ Str::limit($item->nama_produk, 40) }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">{{ $item->nomor_reg ?: $item->barcode }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px; font-size: 13px; color: var(--text-main); font-weight: 700;">{{ $item->merk ?: '--' }}</td>
                        <td style="padding: 16px;">
                            @php
                                $status = strtolower($item->status_keamanan);
                            @endphp
                            @if($status == 'aman')
                                <span style="color: var(--success); font-size: 11px; font-weight: 800; display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-shield-alt"></i> SAFE
                                </span>
                            @elseif($status == 'waspada')
                                <span style="color: var(--accent-color); font-size: 11px; font-weight: 800; display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-exclamation-circle"></i> CAUTION
                                </span>
                            @else
                                <span style="color: var(--danger); font-size: 11px; font-weight: 800; display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-biohazard"></i> DANGER
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $hStatus = strtolower($item->status_halal);
                            @endphp
                            @if($hStatus == 'halal')
                                <span class="badge badge-success">HALAL</span>
                            @elseif($hStatus == 'haram')
                                <span class="badge badge-danger">HARAM</span>
                            @else
                                <span class="badge" style="background: #eee; color: #777; font-size: 10px;">PENDING</span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @if(Str::contains($item->sumber_data, 'open_beauty'))
                                <span style="font-size: 10px; font-weight: 800; color: #e91e63; display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-globe"></i> OBF
                                </span>
                            @else
                                <span style="font-size: 10px; font-weight: 800; color: var(--primary-color); display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-landmark"></i> BPOM
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px 24px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="#" class="btn btn-outline" style="padding: 8px; color: var(--primary-color); border-color: var(--border-color);"><i class="fas fa-edit"></i></a>
                                <form action="#" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--danger); border-color: var(--border-color);" onclick="return confirm('Hapus data kosmetik ini?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 64px; color: var(--text-muted);">No cosmetic products found. Try syncing with OpenBeautyFacts.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer" style="padding: 16px 24px;">
        {{ $cosmetics->links() }}
    </div>
</div>

@endsection
