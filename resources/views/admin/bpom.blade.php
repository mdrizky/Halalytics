@extends('admin.layouts.admin_layout')

@section('title', 'BPOM Raw Data Management | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">BPOM Data</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">BPOM Raw Data Repository</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Master data produk obat, makanan, dan kosmetik hasil sinkronisasi API eksternal dan kontribusi pengguna.</p>
    </div>
    <div>
        <form action="{{ route('admin.bpom.sync') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('.btn-text').textContent='Syncing...';">
            @csrf
            <button type="submit" class="btn btn-primary" style="background: var(--accent-color); border-color: var(--accent-color);">
                <i class="fas fa-sync-alt"></i> <span class="btn-text">Sync Global Data</span>
            </button>
        </form>
    </div>
</div>

<!-- Stats Row -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 32px;">
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Total Indexed</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-main);">{{ number_format($stats['total']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Verified Items</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--success);">{{ number_format($stats['verified']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">AI Analyzed</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--primary-color);">{{ number_format($stats['ai_generated']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="card-body">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Safety Alerts</div>
            <div style="font-size: 28px; font-weight: 800; color: var(--danger);">{{ number_format($stats['dangerous']) }}</div>
        </div>
    </div>
</div>

<!-- Search & Filters -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body">
        <form action="{{ route('admin.bpom.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 16px; align-items: center;">
            <div class="input-group" style="position: relative; margin-bottom: 0;">
                <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, brand, or registration number..." style="width: 100%; padding: 12px 12px 12px 48px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light); outline: none;">
            </div>
            
            <select name="kategori" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="">All Categories</option>
                <option value="obat" {{ request('kategori') == 'obat' ? 'selected' : '' }}>Obat-obatan</option>
                <option value="kosmetik" {{ request('kategori') == 'kosmetik' ? 'selected' : '' }}>Kosmetik & Skincare</option>
                <option value="pangan" {{ request('kategori') == 'pangan' ? 'selected' : '' }}>Pangan Olahan</option>
                <option value="suplemen" {{ request('kategori') == 'suplemen' ? 'selected' : '' }}>Suplemen</option>
            </select>
            
            <select name="status" style="padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-light);">
                <option value="">All Safety Status</option>
                <option value="aman" {{ request('status') == 'aman' ? 'selected' : '' }}>Safe Only</option>
                <option value="waspada" {{ request('status') == 'waspada' ? 'selected' : '' }}>Caution Only</option>
                <option value="bahaya" {{ request('status') == 'bahaya' ? 'selected' : '' }}>Danger Only</option>
            </select>
            
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">
                <i class="fas fa-filter"></i>
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
                        <th style="padding: 16px 24px;">Product Info</th>
                        <th style="padding: 16px;">Category</th>
                        <th style="padding: 16px;">Safety Status</th>
                        <th style="padding: 16px;">Data Source</th>
                        <th style="padding: 16px 24px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bpom_data as $data)
                    <tr>
                        <td style="padding: 16px 24px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--bg-light); display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid var(--border-color); flex-shrink: 0;">
                                    @php
                                        $imgSrc = $data->image_url;
                                        if (!$imgSrc || str_contains($imgSrc, 'placeholder.svg')) {
                                            $imgSrc = null;
                                        }
                                    @endphp
                                    @if($imgSrc)
                                        <img src="{{ $imgSrc }}" alt="" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none';">
                                    @endif
                                    <i class="fas fa-box" style="font-size: 18px; color: var(--text-muted);"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 14px; line-height: 1.2;">{{ Str::limit($data->nama_produk, 45) }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">{{ $data->merk ?: 'No Brand' }} • <span style="font-family: monospace;">{{ $data->nomor_reg ?: 'NO REG' }}</span></div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            <span class="badge" style="background: rgba(45, 106, 79, 0.1); color: var(--primary-color);">{{ strtoupper($data->kategori) }}</span>
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $status = strtolower($data->status_keamanan);
                            @endphp
                            @if($status == 'aman')
                                <span style="color: var(--success); font-size: 11px; font-weight: 800; display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-check-circle"></i> SAFE
                                </span>
                            @elseif($status == 'waspada')
                                <span style="color: var(--accent-color); font-size: 11px; font-weight: 800; display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-exclamation-circle"></i> CAUTION
                                </span>
                            @else
                                <span style="color: var(--danger); font-size: 11px; font-weight: 800; display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-times-circle"></i> DANGER
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @if($data->sumber_data == 'ai')
                                <span style="font-size: 10px; font-weight: 800; color: #673ab7; display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-robot"></i> AI GENERATED
                                </span>
                            @elseif(Str::contains($data->sumber_data, 'open_'))
                                <span style="font-size: 10px; font-weight: 800; color: #e91e63; display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-globe"></i> GLOBAL SYNC
                                </span>
                            @else
                                <span style="font-size: 10px; font-weight: 800; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-database"></i> SYSTEM RAW
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px 24px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('admin.bpom.show', $data->id) }}" class="btn btn-outline" style="padding: 8px; color: var(--primary-color); border-color: var(--border-color);"><i class="fas fa-eye"></i></a>
                                
                                @if($data->verification_status != 'verified')
                                <form action="{{ route('admin.bpom.verify', $data->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--success); border-color: var(--success);" title="Verifikasi Data" onclick="return confirm('Verifikasi kebenaran data ini?')">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                </form>
                                @endif
                                
                                <form action="{{ route('admin.bpom.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Hapus data raw ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--danger); border-color: var(--border-color);"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 64px; color: var(--text-muted);">No BPOM raw data available. Click Sync to fetch from external APIs.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer" style="padding: 16px 24px;">
        {{ $bpom_data->links() }}
    </div>
</div>

@endsection
