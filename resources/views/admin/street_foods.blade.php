@extends('admin.master')

@section('title', 'Street Food Management | Halalytics')

@section('breadcrumb-items')
    <i class="fas fa-chevron-right" style="font-size: 10px; color: var(--text-muted);"></i>
    <span style="color: var(--primary-color); font-weight: 700;">Street Foods</span>
@endsection

@section('content')
<div class="dashboard-header" style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="margin: 0; font-size: 28px; color: var(--primary-color);">Street Food Management</h1>
        <p style="margin: 4px 0 0; color: var(--text-muted); font-size: 14px;">Kelola data makanan tidak kemasan, informasi kalori, dan status kehalalan umum.</p>
    </div>
    <div>
        <a href="{{ route('admin.street-foods.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Street Food
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table style="border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="padding: 16px 24px;">Food Item</th>
                        <th style="padding: 16px;">Category</th>
                        <th style="padding: 16px;">Calorie Estimate</th>
                        <th style="padding: 16px;">Halal Status</th>
                        <th style="padding: 16px;">Variants</th>
                        <th style="padding: 16px 24px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($foods as $food)
                    <tr>
                        <td style="padding: 16px 24px;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--bg-light); display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid var(--border-color); flex-shrink: 0;">
                                    @if($food->image_url)
                                        <img src="{{ $food->image_url }}" alt="" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none';">
                                    @endif
                                    <i class="fas fa-utensils" style="font-size: 18px; color: var(--text-muted);"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: var(--text-main); font-size: 14px;">{{ $food->name }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">{{ Str::limit($food->description, 30) }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            <span class="badge" style="background: rgba(45, 106, 79, 0.1); color: var(--primary-color);">{{ strtoupper($food->category) }}</span>
                        </td>
                        <td style="padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; color: var(--text-main);">
                                <i class="fas fa-fire-alt" style="color: var(--accent-color);"></i>
                                {{ number_format($food->calories_typical) }} kcal
                            </div>
                        </td>
                        <td style="padding: 16px;">
                            @php
                                $hStatus = strtolower($food->halal_status);
                            @endphp
                            @if($hStatus == 'halal_umum' || $hStatus == 'halal')
                                <span class="badge badge-success">HALAL UMUM</span>
                            @elseif($hStatus == 'syubhat' || $hStatus == 'tergantung_bahan')
                                <span class="badge badge-warning">SYUBHAT</span>
                            @else
                                <span class="badge badge-danger">NON-HALAL</span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            <a href="{{ route('admin.street-foods.variants', $food->id) }}" style="display: flex; align-items: center; gap: 6px; text-decoration: none; color: var(--primary-color); font-weight: 700; font-size: 13px;">
                                <i class="fas fa-layer-group"></i>
                                {{ $food->variants_count }} Variants
                            </a>
                        </td>
                        <td style="padding: 16px 24px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('admin.street-foods.edit', $food->id) }}" class="btn btn-outline" style="padding: 8px; color: var(--primary-color); border-color: var(--border-color);"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.street-foods.destroy', $food->id) }}" method="POST" onsubmit="return confirm('Hapus data makanan ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 8px; color: var(--danger); border-color: var(--border-color);"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 64px; color: var(--text-muted);">No street foods data available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer" style="padding: 16px 24px;">
        {{ $foods->links() }}
    </div>
</div>

@endsection
