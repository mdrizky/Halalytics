@extends('admin.layouts.admin_layout')

@section('title', $apiName . ' History - API Monitor')

@section('breadcrumb')
<span class="text-slate-400">Admin</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<a href="{{ route('admin.api-monitor.index') }}" class="text-slate-400 hover:text-primary">API Monitor</a>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">{{ strtoupper($apiName) }}</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white">{{ strtoupper($apiName) }} History</h2>
        <p class="text-slate-500 text-sm mt-1">Uptime: <span class="font-bold {{ $uptime >= 99 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $uptime }}%</span> (last {{ $days }} days)</p>
    </div>
    <div class="flex items-center space-x-2">
        @foreach([1, 7, 30] as $d)
        <a href="{{ route('admin.api-monitor.history', [$apiName, 'days' => $d]) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-lg {{ $d == $days ? 'bg-primary text-white' : 'text-slate-500 hover:bg-slate-100' }}">
            {{ $d }}d
        </a>
        @endforeach
    </div>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/30 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="px-6 py-3">Checked At</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3">HTTP</th>
                <th class="px-6 py-3">Latency</th>
                <th class="px-6 py-3">Error</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($logs as $log)
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                <td class="px-6 py-3 text-sm">{{ \Carbon\Carbon::parse($log->checked_at)->format('d M H:i:s') }}</td>
                <td class="px-6 py-3">
                    @php $c = ['up'=>'emerald','down'=>'red','slow'=>'amber','degraded'=>'orange']; @endphp
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-{{ $c[$log->status] ?? 'slate' }}-100 text-{{ $c[$log->status] ?? 'slate' }}-600">{{ strtoupper($log->status) }}</span>
                </td>
                <td class="px-6 py-3 text-sm font-mono">{{ $log->http_status ?? '—' }}</td>
                <td class="px-6 py-3 text-sm font-bold {{ ($log->latency_ms ?? 0) > 1000 ? 'text-amber-600' : '' }}">{{ $log->latency_ms ? $log->latency_ms . 'ms' : '—' }}</td>
                <td class="px-6 py-3 text-xs text-red-500 max-w-xs truncate">{{ $log->error_details ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 border-t">{{ $logs->withQueryString()->links() }}</div>
</div>
@endsection
