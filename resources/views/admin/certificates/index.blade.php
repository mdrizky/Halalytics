@extends('admin.layouts.admin_layout')

@section('title', 'Halal Certificates - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Admin</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Halal Certificates</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Halal Certificate Management</h2>
        <p class="text-slate-500 text-sm mt-1">Manage MUI/LPPOM/BPJPH halal certificates.</p>
    </div>
    <div class="flex space-x-3">
        <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-lg hover:bg-slate-200 transition flex items-center space-x-2">
            <span class="material-icons-round text-sm">upload_file</span>
            <span>Import CSV</span>
        </button>
        <a href="{{ route('admin.certificates.create') }}" class="px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition flex items-center space-x-2">
            <span class="material-icons-round text-sm">add</span>
            <span>Add Certificate</span>
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-xl text-sm font-medium">{{ session('success') }}</div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    @foreach([
        ['Total', $stats['total'], 'verified', 'primary'],
        ['Active', $stats['active'], 'check_circle', 'emerald-500'],
        ['Expired', $stats['expired'], 'event_busy', 'red-500'],
        ['Revoked', $stats['revoked'], 'block', 'slate-500'],
        ['Expiring Soon', $stats['expiring_soon'], 'warning', 'amber-500'],
    ] as [$label, $count, $icon, $color])
    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
        <div class="flex items-center space-x-3">
            <span class="material-icons-round text-{{ $color }}">{{ $icon }}</span>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ number_format($count) }}</p>
                <p class="text-xs text-slate-500">{{ $label }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Search & Filter -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm mb-6 p-4">
    <form method="GET" class="flex flex-wrap items-center gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search certificate, product, manufacturer..."
               class="flex-1 min-w-[200px] px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
        <select name="status" class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
            <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Revoked</option>
        </select>
        <select name="issuing_body" class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            <option value="">All Issuers</option>
            <option value="MUI" {{ request('issuing_body') === 'MUI' ? 'selected' : '' }}>MUI</option>
            <option value="LPPOM" {{ request('issuing_body') === 'LPPOM' ? 'selected' : '' }}>LPPOM</option>
            <option value="BPJPH" {{ request('issuing_body') === 'BPJPH' ? 'selected' : '' }}>BPJPH</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg">Search</button>
    </form>
</div>

<!-- Table -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    <th class="px-6 py-3">Certificate #</th>
                    <th class="px-6 py-3">Product</th>
                    <th class="px-6 py-3">Manufacturer</th>
                    <th class="px-6 py-3">Issuer</th>
                    <th class="px-6 py-3">Expires</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($certificates as $cert)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4 text-sm font-mono font-bold text-primary">{{ $cert->certificate_number }}</td>
                    <td class="px-6 py-4 text-sm font-medium">{{ $cert->product_name }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $cert->manufacturer }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded text-xs font-bold">{{ $cert->issuing_body }}</span></td>
                    <td class="px-6 py-4 text-sm {{ $cert->expires_at < now() ? 'text-red-500' : ($cert->expires_at < now()->addDays(30) ? 'text-amber-500' : 'text-slate-500') }}">
                        {{ \Carbon\Carbon::parse($cert->expires_at)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($cert->status === 'active')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-600">Active</span>
                        @elseif($cert->status === 'expired')
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-600">Expired</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">Revoked</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.certificates.edit', $cert) }}" class="text-primary hover:underline text-sm font-bold">Edit</a>
                            <form action="{{ route('admin.certificates.destroy', $cert) }}" method="POST" onsubmit="return confirm('Delete this certificate?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline text-sm font-bold">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                        <span class="material-icons-round text-4xl mb-2">verified</span>
                        <p>No certificates found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
        {{ $certificates->withQueryString()->links() }}
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-slate-900 rounded-xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-lg font-bold mb-4">Import Certificates from CSV</h3>
        <form action="{{ route('admin.certificates.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <p class="text-sm text-slate-500 mb-4">CSV must have columns: certificate_number, product_name, manufacturer, issuing_body, issued_at, expires_at</p>
            <input type="file" name="csv_file" accept=".csv,.txt" required class="w-full p-2 border rounded-lg mb-4">
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2 text-sm text-slate-500">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg">Import</button>
            </div>
        </form>
    </div>
</div>
@endsection
