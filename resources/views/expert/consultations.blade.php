@extends('expert.layouts.expert_layout')

@section('content')
<div class="surface-card rounded-3xl p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-xl font-black text-slate-800">Konsultasi</h3>
            <p class="text-sm text-slate-500 mt-1">Riwayat konsultasi kesehatan pasien.</p>
        </div>
        <div class="relative">
            <input type="text" placeholder="Cari konsultasi..." class="pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary w-64">
            <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 text-xs uppercase tracking-wider">
                    <th class="pb-3 font-bold">Pasien</th>
                    <th class="pb-3 font-bold">Tipe</th>
                    <th class="pb-3 font-bold">Ringkasan</th>
                    <th class="pb-3 font-bold">Tanggal</th>
                    <th class="pb-3 font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($consultations as $consultation)
                <tr class="border-t border-slate-100">
                    <td class="py-3 font-medium text-slate-800">{{ $consultation->user_full_name ?? $consultation->user?->full_name ?? 'Guest' }}</td>
                    <td class="py-3">
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-primary/10 text-primary font-bold uppercase">{{ str_replace('_', ' ', $consultation->event_type) }}</span>
                    </td>
                    <td class="py-3 text-slate-500 max-w-xs truncate">{{ $consultation->summary ?? '-' }}</td>
                    <td class="py-3 text-slate-500">{{ $consultation->created_at->format('d M Y H:i') }}</td>
                    <td class="py-3">
                        <a href="#" class="text-primary text-xs font-bold hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-slate-400">Belum ada konsultasi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $consultations->links('vendor.pagination.tailwind-admin') }}
    </div>
</div>
@endsection
