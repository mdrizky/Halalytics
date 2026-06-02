@extends('expert.layouts.expert_layout')

@section('content')
<div class="surface-card rounded-3xl p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-xl font-black text-slate-800">Daftar Pasien</h3>
            <p class="text-sm text-slate-500 mt-1">Kelola dan pantau pasien Anda.</p>
        </div>
        <div class="relative">
            <input type="text" placeholder="Cari pasien..." class="pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary w-64">
            <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 text-xs uppercase tracking-wider">
                    <th class="pb-3 font-bold">Nama</th>
                    <th class="pb-3 font-bold">Email</th>
                    <th class="pb-3 font-bold">Total Scan</th>
                    <th class="pb-3 font-bold">Terdaftar</th>
                    <th class="pb-3 font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr class="border-t border-slate-100">
                    <td class="py-3 font-medium text-slate-800">{{ $patient->full_name ?? $patient->username }}</td>
                    <td class="py-3 text-slate-500">{{ $patient->email }}</td>
                    <td class="py-3">{{ $patient->total_scan ?? 0 }}</td>
                    <td class="py-3 text-slate-500">{{ $patient->created_at->format('d M Y') }}</td>
                    <td class="py-3">
                        <a href="#" class="text-primary text-xs font-bold hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-slate-400">Belum ada pasien terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $patients->links('vendor.pagination.tailwind-admin') }}
    </div>
</div>
@endsection
