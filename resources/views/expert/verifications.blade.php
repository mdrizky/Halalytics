@extends('expert.layouts.expert_layout')

@section('content')
<div class="space-y-8">
    <div>
        <h3 class="text-xl font-black text-slate-800">Verifikasi AI Analysis</h3>
        <p class="text-slate-500 text-sm mt-1">Tinjau dan verifikasi hasil analisis AI untuk memastikan akurasi.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="surface-card p-5 rounded-2xl">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pending</p>
            <h3 class="text-2xl font-black text-amber-600 mt-1">{{ $stats['pending'] }}</h3>
        </div>
        <div class="surface-card p-5 rounded-2xl">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Terverifikasi</p>
            <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['verified'] }}</h3>
        </div>
        <div class="surface-card p-5 rounded-2xl">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Analisis</p>
            <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $stats['total'] }}</h3>
        </div>
        <div class="surface-card p-5 rounded-2xl">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Produk Unik</p>
            <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $stats['unique_products'] }}</h3>
        </div>
    </div>

    <div class="surface-card rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h4 class="font-bold text-slate-800">Daftar Analisis Produk</h4>
            <span class="text-xs text-slate-400 font-bold">{{ $results->total() }} total</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <tr>
                        <th class="text-left p-4">Produk</th>
                        <th class="text-left p-4">User</th>
                        <th class="text-center p-4">Halal</th>
                        <th class="text-center p-4">Health</th>
                        <th class="text-center p-4">Nutri Score</th>
                        <th class="text-center p-4">Verifikasi</th>
                        <th class="text-right p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($results as $result)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-4">
                            <p class="font-bold text-slate-800">{{ $result->product?->nama_product ?? 'Unknown' }}</p>
                            <p class="text-[10px] text-slate-400">{{ $result->input_type }} - {{ $result->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="p-4">
                            <p class="text-sm font-medium text-slate-600">{{ $result->user?->name ?? 'Guest' }}</p>
                        </td>
                        <td class="p-4 text-center">
                            @php
                                $halalColors = ['HALAL' => 'text-emerald-600 bg-emerald-50', 'HARAM' => 'text-red-600 bg-red-50', 'SYUBHAT' => 'text-amber-600 bg-amber-50'];
                                $color = $halalColors[$result->halal_verdict] ?? 'text-slate-600 bg-slate-50';
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black {{ $color }}">
                                {{ $result->halal_verdict }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="text-xs font-bold {{ $result->health_verdict === 'AMAN' ? 'text-emerald-600' : ($result->health_verdict === 'HINDARI' ? 'text-red-600' : 'text-amber-600') }}">
                                {{ $result->health_verdict ?? '-' }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            @if($result->nutri_score)
                                @php
                                    $nutriColors = ['A' => 'text-emerald-600', 'B' => 'text-lime-600', 'C' => 'text-amber-600', 'D' => 'text-orange-600', 'E' => 'text-red-600'];
                                    $nColor = $nutriColors[$result->nutri_score] ?? 'text-slate-600';
                                @endphp
                                <span class="text-lg font-black {{ $nColor }}">{{ $result->nutri_score }}</span>
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            @if($result->is_verified_by_expert)
                                <span class="inline-flex items-center gap-1 text-[10px] font-black text-emerald-600">
                                    <span class="material-icons-round text-sm">verified</span>
                                    Terverifikasi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-black text-amber-600">
                                    <span class="material-icons-round text-sm">hourglass_empty</span>
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openVerifyModal({{ $result->id }}, '{{ $result->halal_verdict }}', '{{ $result->health_verdict ?? '' }}', '{{ $result->nutri_score ?? '' }}', '{{ addslashes($result->product?->nama_product ?? '') }}')"
                                        class="px-4 py-2 rounded-xl text-[10px] font-black {{ $result->is_verified_by_expert ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-primary text-white hover:bg-primary-dark' }} transition-all"
                                        {{ $result->is_verified_by_expert ? 'disabled' : '' }}>
                                    {{ $result->is_verified_by_expert ? 'Selesai' : 'Verifikasi' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center">
                            <span class="material-icons-round text-4xl text-slate-200 mb-4 block">fact_check</span>
                            <p class="text-slate-400 font-bold">Belum ada hasil analisis AI.</p>
                            <p class="text-xs text-slate-300 mt-1">Analisis akan muncul setelah admin menyetujui produk baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($results->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $results->links() }}
        </div>
        @endif
    </div>
</div>

<div id="verify-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white w-full max-w-lg rounded-[2rem] p-8 shadow-2xl border-2 border-slate-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-black text-slate-800">Verifikasi Analisis AI</h3>
            <button onclick="closeVerifyModal()" class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all">
                <span class="material-icons-round text-lg">close</span>
            </button>
        </div>

        <form id="verify-form" method="POST">
            @csrf
            <input type="hidden" name="analysis_id" id="verify-analysis-id">

            <div class="space-y-5">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Produk</p>
                    <p id="verify-product-name" class="text-sm font-bold text-slate-800"></p>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Status Halal</p>
                        <select name="halal_verdict" id="verify-halal" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-sm font-bold focus:border-primary transition-all">
                            <option value="HALAL">HALAL</option>
                            <option value="HALAL_CERTIFIED">HALAL_CERTIFIED</option>
                            <option value="HALAL_UNCERTIFIED">HALAL_UNCERTIFIED</option>
                            <option value="SYUBHAT">SYUBHAT</option>
                            <option value="HARAM">HARAM</option>
                        </select>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Health Status</p>
                        <select name="health_verdict" id="verify-health" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-sm font-bold focus:border-primary transition-all">
                            <option value="">-</option>
                            <option value="AMAN">AMAN</option>
                            <option value="PERHATIAN">PERHATIAN</option>
                            <option value="HINDARI">HINDARI</option>
                        </select>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nutri Score</p>
                        <select name="nutri_score" id="verify-nutri" class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-3 text-sm font-bold focus:border-primary transition-all">
                            <option value="">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                        </select>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Catatan Ahli Gizi</p>
                    <textarea name="expert_notes" rows="3" placeholder="Tambahkan catatan koreksi atau penjelasan..." class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl p-4 text-sm font-medium focus:border-primary transition-all resize-none"></textarea>
                </div>

                <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-black hover:bg-primary-dark transition-all shadow-xl shadow-primary/20 flex items-center justify-center gap-2">
                    <span class="material-icons-round">verified</span>
                    Verifikasi & Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openVerifyModal(id, halal, health, nutri, productName) {
    document.getElementById('verify-analysis-id').value = id;
    document.getElementById('verify-product-name').textContent = productName;
    document.getElementById('verify-halal').value = halal;
    document.getElementById('verify-health').value = health || '';
    document.getElementById('verify-nutri').value = nutri || '';
    document.getElementById('verify-form').action = '/expert/verifications/' + id + '/verify';
    document.getElementById('verify-modal').classList.remove('hidden');
    document.getElementById('verify-modal').classList.add('flex');
}

function closeVerifyModal() {
    document.getElementById('verify-modal').classList.add('hidden');
    document.getElementById('verify-modal').classList.remove('flex');
}

document.getElementById('verify-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeVerifyModal();
});
</script>
@endsection