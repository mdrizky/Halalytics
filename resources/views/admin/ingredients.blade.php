@extends('admin.master')

@section('title', 'Ingredient Encyclopedia | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Ingredients</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">Ingredient Encyclopedia</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Basis data komposisi bahan makanan dan minuman untuk analisis kehalalan otomatis.</p>
    </div>
    <div>
        <a href="{{ route('admin.ingredients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Ingredient
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 24px; margin-bottom: 32px;">
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Total Ingredients</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-main);">{{ number_format($stats['total']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Safe (Halal)</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--success);">{{ number_format($stats['halal']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Forbidden (Haram)</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--danger);">{{ number_format($stats['haram']) }}</div>
        </div>
    </div>
    
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Doubtful (Syubhat)</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--accent-color);">{{ number_format($stats['syubhat']) }}</div>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body">
        <form action="{{ route('admin.ingredients.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1.5fr auto; gap: 16px; align-items: center;">
            <div class="input-group" style="position: relative; margin-bottom: 0;">
                <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or E-Number..." style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
            </div>
            
            <select name="status" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="all">All Halal Status</option>
                <option value="halal" {{ request('status') == 'halal' ? 'selected' : '' }}>Halal Only</option>
                <option value="haram" {{ request('status') == 'haram' ? 'selected' : '' }}>Haram Only</option>
                <option value="syubhat" {{ request('status') == 'syubhat' ? 'selected' : '' }}>Syubhat Only</option>
                <option value="unknown" {{ request('status') == 'unknown' ? 'selected' : '' }}>Unknown Status</option>
            </select>
            
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                <i class="fas fa-filter"></i> Apply Filter
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
                        <th style="padding: 16px 24px;">Ingredient Info</th>
                        <th style="padding: 16px;">Halal Status</th>
                        <th style="padding: 16px;">Health Risk</th>
                        <th style="padding: 16px;">Sources / Ref</th>
                        <th style="padding: 16px;">Status</th>
                        <th style="padding: 16px 24px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ingredients as $ingredient)
                    <tr>
                        <td style="padding: 16px 24px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--bg-light); display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid var(--border-color); flex-shrink: 0;">
                                    <img src="{{ $ingredient->image_url }}" alt="" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='https://loremflickr.com/100/100/{{ urlencode($ingredient->name) }},chemical,powder?lock={{ $ingredient->id_ingredient }}'">
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 14px;">{{ $ingredient->name }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted); font-family: monospace;">{{ $ingredient->e_number ?: 'No E-Number' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $hStatus = strtolower($ingredient->halal_status);
                            @endphp
                            @if($hStatus == 'halal')
                                <span class="badge badge-success">HALAL</span>
                            @elseif($hStatus == 'haram')
                                <span class="badge badge-danger">HARAM</span>
                            @elseif($hStatus == 'syubhat' || $hStatus == 'diragukan')
                                <span class="badge badge-warning">SYUBHAT</span>
                            @else
                                <span class="badge" style="background: #eee; color: #777;">UNKNOWN</span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $risk = strtolower($ingredient->health_risk);
                            @endphp
                            @if($risk == 'safe' || $risk == 'none')
                                <span style="color: var(--success); font-size: 12px; font-weight: 700;"><i class="fas fa-check-circle"></i> Safe</span>
                            @elseif($risk == 'high_risk' || $risk == 'dangerous')
                                <span style="color: var(--danger); font-size: 12px; font-weight: 700;"><i class="fas fa-exclamation-triangle"></i> Warning</span>
                            @else
                                <span style="color: var(--accent-color); font-size: 12px; font-weight: 700;"><i class="fas fa-info-circle"></i> Caution</span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            <div style="max-width: 200px; font-size: 11px; color: var(--text-muted); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $ingredient->sources }}">
                                {{ $ingredient->sources ?: '--' }}
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            @if($ingredient->active)
                                <span style="display: flex; align-items: center; gap: 6px; color: var(--success); font-size: 11px; font-weight: 800;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--success);"></span> ACTIVE
                                </span>
                            @else
                                <span style="display: flex; align-items: center; gap: 6px; color: var(--text-muted); font-size: 11px; font-weight: 800;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--text-muted);"></span> DISABLED
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px 24px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('admin.ingredients.edit', $ingredient->id_ingredient) }}" class="btn btn-outline" style="padding: 8px; color: var(--primary-color); border-color: var(--primary-color);"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.ingredients.destroy', $ingredient->id_ingredient) }}" method="POST" onsubmit="return confirm('Hapus bahan ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--danger); border-color: var(--danger);"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 64px; color: var(--text-muted);">No ingredients found in our encyclopedia.</td>
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

@endsection
