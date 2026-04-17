@extends('admin.layouts.admin_layout')

@section('title', 'Manajemen Kosmetik')
@section('breadcrumb-parent', 'Data & Verification')
@section('breadcrumb-current', 'Cosmetics')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="metric-card metric-card--primary">
        <div class="text-sm opacity-80 font-medium">Total Kosmetik</div>
        <div class="text-3xl font-extrabold mt-1">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="metric-card metric-card--accent">
        <div class="text-sm opacity-80 font-medium">Aman</div>
        <div class="text-3xl font-extrabold mt-1">{{ number_format($stats['aman']) }}</div>
    </div>
    <div class="metric-card metric-card--danger">
        <div class="text-sm opacity-80 font-medium">Bahan Berbahaya/Haram</div>
        <div class="text-3xl font-extrabold mt-1">{{ number_format($stats['bahaya'] + $stats['haram']) }}</div>
    </div>
    <div class="metric-card metric-card--soft">
        <div class="text-sm opacity-80 font-medium">Dari OpenBeautyFacts</div>
        <div class="text-3xl font-extrabold mt-1">{{ number_format($stats['from_obf']) }}</div>
    </div>
</div>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Manajemen Kosmetik</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Data kosmetik dari database BPOM dan AI OpenBeautyFacts.</p>
    </div>
    <form action="{{ route('admin.cosmetics.seed') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button span:last-child').textContent='Mengimpor...';">
        @csrf
        <button type="submit" class="inline-flex flex-shrink-0 items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold transition shadow-sm">
            <span class="material-icons-round text-lg">auto_awesome</span>
            <span>Seed dari OpenBeautyFacts</span>
        </button>
    </form>
</div>

<!-- Filter & Search OBF -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
            <span class="material-icons-round text-slate-400">filter_alt</span> Filter Data
        </h3>
        <form action="{{ route('admin.cosmetics.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-12">
                <input type="text" name="search" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white" placeholder="Cari merk atau nama produk kosmetik..." value="{{ request('search') }}">
            </div>
            <div class="md:col-span-5">
                <select name="status_keamanan" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white">
                    <option value="">Semua Status Keamanan</option>
                    <option value="aman" {{ request('status_keamanan') == 'aman' ? 'selected' : '' }}>Aman</option>
                    <option value="waspada" {{ request('status_keamanan') == 'waspada' ? 'selected' : '' }}>Waspada</option>
                    <option value="bahaya" {{ request('status_keamanan') == 'bahaya' ? 'selected' : '' }}>Bahaya</option>
                </select>
            </div>
            <div class="md:col-span-4">
                <select name="sumber_data" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white">
                    <option value="">Semua Sumber</option>
                    <option value="sistem" {{ request('sumber_data') == 'sistem' ? 'selected' : '' }}>BPOM (Sistem)</option>
                    <option value="open_beauty_facts" {{ request('sumber_data') == 'open_beauty_facts' ? 'selected' : '' }}>OpenBeautyFacts</option>
                </select>
            </div>
            <div class="md:col-span-3">
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-primary text-white font-semibold hover:bg-primary-dark transition">Filter</button>
            </div>
        </form>
    </div>

    <!-- OBF Search -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
            <span class="material-icons-round text-primary">travel_explore</span> Live Search OpenBeautyFacts
        </h3>
        <div class="flex gap-2">
            <input type="text" id="obfSearchInput" class="flex-1 rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white" placeholder="Cari barcode / merk kosmetik di API OBF...">
            <button onclick="searchOBF()" type="button" class="px-4 py-2 rounded-lg bg-primary text-white font-semibold hover:bg-primary-dark transition flex items-center gap-2">
                <span class="material-icons-round text-sm">search</span> Cari
            </button>
        </div>
        <div id="obfResults" class="hidden mt-3 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
            <div id="obfResultsContent"></div>
        </div>
    </div>
</div>

<!-- Main Table -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700">
                    <th class="px-5 py-4">Kosmetik</th>
                    <th class="px-5 py-4">Merk / BPOM</th>
                    <th class="px-5 py-4">Status Halal</th>
                    <th class="px-5 py-4">Keamanan</th>
                    <th class="px-5 py-4">Sumber</th>
                    <th class="px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($cosmetics as $item)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-5 py-4 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg overflow-hidden bg-primary/10 text-primary border border-slate-100 dark:border-slate-700 flex items-center justify-center flex-shrink-0">
                            @php
                                $imgSrc = $item->image_url;
                                if (!$imgSrc || str_contains($imgSrc, 'placeholder.svg')) {
                                    $imgSrc = 'https://images.unsplash.com/photo-1596462502278-27bf85033e5a?q=80&w=150'; // Default Cosmetic
                                }
                            @endphp
                            <img src="{{ $imgSrc }}" class="w-full h-full object-cover" alt="" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name='+encodeURIComponent('{{ $item->nama_produk }}')+'&background=random';">
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-800 dark:text-white line-clamp-1" title="{{ $item->nama_produk }}">{{ Str::limit($item->nama_produk, 35) }}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5"><span class="material-icons-round text-[10px] align-middle">qr_code</span> {{ $item->barcode ?: 'Tanpa Barcode' }}</div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="text-xs text-slate-800 dark:text-slate-300 font-medium line-clamp-1">{{ $item->merk ?: '-' }}</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-500 font-mono mt-0.5">{{ $item->nomor_reg ?: 'NO-BPOM' }}</div>
                    </td>
                    <td class="px-5 py-4">
                        @if($item->status_halal == 'halal')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">HALAL</span>
                        @elseif($item->status_halal == 'haram')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">HARAM</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">SYUBHAT</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($item->status_keamanan == 'aman')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">AMAN</span>
                        @elseif($item->status_keamanan == 'bahaya')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100">BAHAYA</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100">WASPADA</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if(in_array($item->sumber_data, ['open_beauty_facts', 'open_beauty_facts_api']))
                            <span class="inline-flex px-2 py-1 rounded text-[10px] font-bold bg-primary/10 text-primary border border-primary/20">OBF</span>
                        @else
                            <span class="inline-flex px-2 py-1 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">Sistem</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <button onclick='showCosmeticDetailModal(@json($item))' class="p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 transition shadow-sm border border-transparent hover:border-primary/20" title="Detail">
                            <span class="material-icons-round text-lg leading-none">visibility</span>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                            <span class="material-icons-round text-5xl mb-3 opacity-50 text-primary">face_retouching_natural</span>
                            <p class="text-sm font-medium">Belum ada data kosmetik lokal/API.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
        {{ $cosmetics->appends(request()->query())->links('pagination::tailwind') }}
    </div>
</div>

<!-- Tailwind Modal -->
<div id="cosmeticModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity opacity-0" id="cosmeticModalBackdrop"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div id="cosmeticModalPanel" class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200 dark:border-slate-700">
                <div class="bg-white dark:bg-slate-900 px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                    <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white flex items-center gap-2" id="modalTitle">
                        <span class="material-icons-round text-primary">face_retouching_natural</span> Detail Kosmetik
                    </h3>
                    <button type="button" onclick="closeCosmeticModal()" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 focus:outline-none">
                        <span class="material-icons-round">close</span>
                    </button>
                </div>
                <div class="px-4 py-5 sm:p-6" id="modalContent"></div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeCosmeticModal()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-900 dark:text-white shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 sm:mt-0 sm:w-auto transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.4); border-radius: 20px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: rgba(71, 85, 105, 0.5); }
</style>
<script>
async function searchOBF() {
    const query = document.getElementById('obfSearchInput').value.trim();
    if (query.length < 2) return;

    const resultsDiv = document.getElementById('obfResults');
    const contentDiv = document.getElementById('obfResultsContent');
    contentDiv.innerHTML = '<div class="text-center text-slate-500 py-4 text-sm flex justify-center items-center gap-2"><span class="material-icons-round animate-spin">refresh</span> Mencari di OpenBeautyFacts...</div>';
    resultsDiv.classList.remove('hidden');

    try {
        const response = await fetch(`{{ route('admin.cosmetics.search-external') }}?query=${encodeURIComponent(query)}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (data.success && data.results.length > 0) {
            let html = '<div class="flex flex-col gap-2 mt-2">';
            data.results.forEach(r => {
                const name = r.nama_produk || r.product_name || 'Unknown';
                const merk = r.brands || r.merk || 'Unknown';
                html += `
                <div class="flex justify-between items-center p-3 rounded-lg border border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/30 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <div class="flex items-center gap-3 pr-4">
                        ${r.image_url ? `<img src="${r.image_url}" class="w-10 h-10 rounded-md object-cover border border-slate-200">` : `<div class="w-10 h-10 rounded-md bg-slate-200 flex items-center justify-center"><i class="material-icons-round text-slate-400">image</i></div>`}
                        <div>
                            <div class="text-sm font-bold text-slate-800 dark:text-white">${name}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">${r.barcode ? r.barcode + ' &bull; ' : ''}${merk}</div>
                        </div>
                    </div>
                    <form action="{{ route('admin.cosmetics.import') }}" method="POST" class="flex-shrink-0">
                        @csrf
                        <input type="hidden" name="identifier" value="${r.barcode || name}">
                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-md bg-primary/10 text-primary border border-primary/20 hover:bg-primary/15 transition">
                            <i class="fas fa-download mr-1"></i> Import
                        </button>
                    </form>
                </div>`;
            });
            html += '</div>';
            contentDiv.innerHTML = html;
        } else {
            contentDiv.innerHTML = '<div class="text-center text-slate-500 py-4 text-sm">Tidak ditemukan di OpenBeautyFacts.</div>';
        }
    } catch (error) {
        contentDiv.innerHTML = '<div class="text-center text-rose-500 py-4 text-sm">Error menghubungi API OBF.</div>';
    }
}

// Modal logic
const modal = document.getElementById('cosmeticModal');
const backdrop = document.getElementById('cosmeticModalBackdrop');
const panel = document.getElementById('cosmeticModalPanel');

function showCosmeticDetailModal(item) {
    document.getElementById('modalTitle').innerHTML = `<span class="material-icons-round text-primary">face_retouching_natural</span> ${item.nama_produk || 'Detail Kosmetik'}`;
    
    // Status Badge Logic
    let statusBadge = '';
    if (item.status_keamanan === 'aman') statusBadge = '<span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">AMAN</span>';
    else if (item.status_keamanan === 'bahaya') statusBadge = '<span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">BAHAYA</span>';
    else statusBadge = '<span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">WASPADA</span>';

    document.getElementById('modalContent').innerHTML = `
        <div class="flex flex-col md:flex-row gap-6">
            <div class="w-full md:w-32 flex-shrink-0 flex justify-center">
                ${item.image_url ? 
                    `<img src="${item.image_url}" class="w-24 h-24 md:w-full md:h-auto object-cover rounded-xl shadow-md border border-slate-200 dark:border-slate-700">` : 
                    `<div class="w-24 h-24 md:w-full md:aspect-square flex flex-col items-center justify-center bg-slate-100 dark:bg-slate-800 rounded-xl text-slate-400"><span class="material-icons-round text-4xl">inventory_2</span><span class="text-[10px] mt-2">No Image</span></div>`
                }
            </div>
            <div class="flex-1 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wider">Merk</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white">${item.merk || '-'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wider">Nomor Registrasi (BPOM)</p>
                        <p class="text-sm font-mono text-slate-800 dark:text-white">${item.nomor_reg || 'Tidak/Belum Terdaftar'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wider">Barcode</p>
                        <p class="text-sm font-mono text-slate-600 dark:text-slate-300 flex items-center gap-1"><span class="material-icons-round text-sm">qr_code</span> ${item.barcode || '-'}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wider">Status Keamanan</p>
                        <div class="mt-0.5">${statusBadge}</div>
                    </div>
                </div>
                <div>
                    <p class="text-[11px] uppercase font-bold text-slate-400 mb-1.5 tracking-wider">Komposisi (Ingredients)</p>
                    <div class="p-3 text-sm text-slate-600 dark:text-slate-300 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 leading-relaxed max-h-40 overflow-y-auto custom-scrollbar">
                        ${item.ingredients || 'Data komposisi tidak tersedia.'}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Show Modal
    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        panel.classList.remove('opacity-0', 'translate-y-4', 'sm:scale-95');
        panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
    });
}

function closeCosmeticModal() {
    backdrop.classList.remove('opacity-100');
    backdrop.classList.add('opacity-0');
    panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
    panel.classList.add('opacity-0', 'translate-y-4', 'sm:scale-95');
    
    setTimeout(() => { modal.classList.add('hidden'); }, 300);
}

document.getElementById('obfSearchInput')?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); searchOBF(); }
});
</script>
@endpush
