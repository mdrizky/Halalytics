@extends('admin.layouts.admin_layout')

@section('title', 'Manajemen Obat-Obatan')
@section('breadcrumb-parent', 'Data & Verification')
@section('breadcrumb-current', 'Medicines')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="metric-card metric-card--primary">
        <div class="text-sm opacity-80 font-medium">Total Obat</div>
        <div class="text-3xl font-extrabold mt-1">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="metric-card metric-card--accent">
        <div class="text-sm opacity-80 font-medium">Status Halal</div>
        <div class="text-3xl font-extrabold mt-1">{{ number_format($stats['halal']) }}</div>
    </div>
    <div class="metric-card metric-card--soft">
        <div class="text-sm opacity-80 font-medium">Status Syubhat</div>
        <div class="text-3xl font-extrabold mt-1">{{ number_format($stats['syubhat']) }}</div>
    </div>
    <div class="metric-card metric-card--primary">
        <div class="text-sm opacity-80 font-medium">Dari OpenFDA</div>
        <div class="text-3xl font-extrabold mt-1">{{ number_format($stats['from_fda']) }}</div>
    </div>
</div>

<!-- Page Title & Main Action -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Manajemen Obat-Obatan</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Kelola data obat lokal dan terintegrasi dari OpenFDA.</p>
    </div>
    <form action="{{ route('admin.medicines.seed') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button span:last-child').textContent='Mengimpor...';">
        @csrf
        <button type="submit" class="inline-flex flex-shrink-0 items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg font-semibold transition shadow-lg shadow-primary/20">
            <span class="material-icons-round text-lg">cloud_download</span>
            <span>Seed dari OpenFDA</span>
        </button>
    </form>
</div>

<!-- Filter & Search FDA -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Local Filter -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
            <span class="material-icons-round text-slate-400">filter_alt</span> Filter Data Lokal
        </h3>
        <form action="{{ route('admin.medicines.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-12">
                <input type="text" name="search" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white" placeholder="Cari nama obat, generic name..." value="{{ request('search') }}">
            </div>
            <div class="md:col-span-5">
                <select name="halal_status" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="halal" {{ request('halal_status') == 'halal' ? 'selected' : '' }}>Halal</option>
                    <option value="syubhat" {{ request('halal_status') == 'syubhat' ? 'selected' : '' }}>Syubhat</option>
                    <option value="haram" {{ request('halal_status') == 'haram' ? 'selected' : '' }}>Haram</option>
                </select>
            </div>
            <div class="md:col-span-4">
                <select name="source" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white">
                    <option value="">Semua Sumber</option>
                    <option value="openfda" {{ request('source') == 'openfda' ? 'selected' : '' }}>OpenFDA</option>
                    <option value="local" {{ request('source') == 'local' ? 'selected' : '' }}>Lokal</option>
                </select>
            </div>
            <div class="md:col-span-3">
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-primary text-white font-semibold hover:bg-primary-dark transition">Filter</button>
            </div>
        </form>
    </div>

    <!-- FDA Search -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
            <span class="material-icons-round text-primary">travel_explore</span> Live Search OpenFDA
        </h3>
        <div class="flex gap-2">
            <input type="text" id="fdaSearchInput" class="flex-1 rounded-lg border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white" placeholder="Ketik nama obat untuk ditarik...">
            <button onclick="searchFDA()" type="button" class="px-4 py-2 rounded-lg bg-primary text-white font-semibold hover:bg-primary-dark transition flex items-center gap-2">
                <span class="material-icons-round text-sm">search</span> Cari
            </button>
        </div>
        <div id="fdaResults" class="hidden mt-3 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
            <div id="fdaResultsContent"></div>
        </div>
    </div>
</div>

<!-- Main Table -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <th class="px-5 py-4">Nama Obat</th>
                    <th class="px-5 py-4">Generic Name / Mfg</th>
                    <th class="px-5 py-4">Bentuk</th>
                    <th class="px-5 py-4">Status Halal</th>
                    <th class="px-5 py-4">Sumber</th>
                    <th class="px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($medicines as $med)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-5 py-4 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl overflow-hidden bg-[#E0F2F1] border border-slate-200 dark:border-slate-700 flex items-center justify-center flex-shrink-0">
                            <img src="{{ $med->image_url }}" alt="{{ $med->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="material-icons-round hidden text-[#004D40]">medication</span>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-800 dark:text-white line-clamp-1" title="{{ $med->name }}">{{ Str::limit($med->name, 40) }}</div>
                            @if($med->brand_name)
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-mono line-clamp-1">{{ $med->brand_name }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="text-xs text-slate-800 dark:text-slate-300 font-medium line-clamp-1" title="{{ $med->generic_name }}">{{ Str::limit($med->generic_name, 30) ?: '-' }}</div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-500 line-clamp-1">{{ Str::limit($med->manufacturer, 25) ?: '-' }}</div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-medium border border-slate-200 dark:border-slate-700">
                            {{ $med->dosage_form ?: $med->route ?: '-' }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        @if($med->halal_status == 'halal')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> HALAL
                            </span>
                        @elseif($med->halal_status == 'haram')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800/50">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> HARAM
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> SYUBHAT
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($med->source == 'openfda')
                            <span class="inline-flex px-2 py-1 rounded-md bg-primary/10 text-primary text-[11px] font-bold border border-primary/20">OpenFDA</span>
                        @else
                            <span class="inline-flex px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[11px] font-bold border border-slate-200 dark:border-slate-700">Lokal</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <button onclick='showMedicineDetailModal(@json($med))' class="p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 transition shadow-sm border border-transparent hover:border-primary/20" title="Lihat Detail">
                            <span class="material-icons-round text-lg leading-none">visibility</span>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                            <span class="material-icons-round text-5xl mb-3 opacity-50 text-primary">medication</span>
                            <p class="text-sm font-medium">Belum ada data obat lokal atau API.</p>
                            <p class="text-xs mt-1">Gunakan tombol <strong>Seed dari OpenFDA</strong> untuk mengisi data awal.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
        {{ $medicines->appends(request()->query())->links('pagination::tailwind') }}
    </div>
</div>

<!-- Tailwind Modal -->
<div id="medicineModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity opacity-0" id="medicineModalBackdrop"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div id="medicineModalPanel" class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200 dark:border-slate-700">
                <div class="bg-white dark:bg-slate-900 px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                    <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white flex items-center gap-2" id="modalTitle">
                        <span class="material-icons-round text-primary">info</span> Detail Obat
                    </h3>
                    <button type="button" onclick="closeMedicineModal()" class="text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 focus:outline-none">
                        <span class="material-icons-round">close</span>
                    </button>
                </div>
                <div class="px-4 py-5 sm:p-6" id="modalContent">
                    <!-- Dynamic content goes here -->
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeMedicineModal()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-900 dark:text-white shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 sm:mt-0 sm:w-auto transition">
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
async function searchFDA() {
    const query = document.getElementById('fdaSearchInput').value.trim();
    if (query.length < 2) return;

    const resultsDiv = document.getElementById('fdaResults');
    const contentDiv = document.getElementById('fdaResultsContent');
    contentDiv.innerHTML = '<div class="text-center text-slate-500 py-4 text-sm flex justify-center items-center gap-2"><span class="material-icons-round animate-spin">refresh</span> Mencari di OpenFDA...</div>';
    resultsDiv.classList.remove('hidden');

    try {
        const response = await fetch(`{{ route('admin.medicines.search-external') }}?query=${encodeURIComponent(query)}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (data.success && data.results.length > 0) {
            let html = '<div class="flex flex-col gap-2 mt-2">';
            data.results.forEach(r => {
                const name = r.nama_produk || r.brand_name || 'Unknown';
                const manf = r.manufacturer || 'Unknown';
                const act  = r.default_action || 'Import';
                html += `
                <div class="flex justify-between items-center p-3 rounded-lg border border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/30 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <div class="flex-1 pr-4">
                        <div class="text-sm font-bold text-slate-800 dark:text-white">${name}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">${r.generic_name || ''} &bull; ${manf}</div>
                    </div>
                    <form action="{{ route('admin.medicines.import') }}" method="POST" class="flex-shrink-0">
                        @csrf
                        <input type="hidden" name="drug_name" value="${r.nama_produk || r.brand_name || query}">
                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-md bg-primary/10 text-primary border border-primary/20 hover:bg-primary/15 transition">
                            <i class="fas fa-plus mr-1"></i> ${act}
                        </button>
                    </form>
                </div>`;
            });
            html += '</div>';
            contentDiv.innerHTML = html;
        } else {
            contentDiv.innerHTML = '<div class="text-center text-slate-500 py-4 text-sm">Tidak ditemukan di OpenFDA.</div>';
        }
    } catch (error) {
        contentDiv.innerHTML = '<div class="text-center text-rose-500 py-4 text-sm">Error saat mencari data API.</div>';
    }
}

// Modal logic
const modal = document.getElementById('medicineModal');
const backdrop = document.getElementById('medicineModalBackdrop');
const panel = document.getElementById('medicineModalPanel');

function showMedicineDetailModal(med) {
    document.getElementById('modalTitle').innerHTML = `<span class="material-icons-round text-primary">medication</span> ${med.name || 'Detail Obat'}`;
    
    // Status Badge Logic
    let statusBadge = '';
    if (med.halal_status === 'halal') statusBadge = '<span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">HALAL</span>';
    else if (med.halal_status === 'haram') statusBadge = '<span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">HARAM</span>';
    else statusBadge = '<span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">SYUBHAT</span>';

    document.getElementById('modalContent').innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                <p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wider">Generic Name</p>
                <p class="text-sm font-semibold text-slate-800 dark:text-white">${med.generic_name || '-'}</p>
            </div>
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                <p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wider">Manufacturer</p>
                <p class="text-sm font-semibold text-slate-800 dark:text-white">${med.manufacturer || '-'}</p>
            </div>
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                <p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wider">Route & Form</p>
                <p class="text-sm font-semibold text-slate-800 dark:text-white">${med.route || '-'} / ${med.dosage_form || '-'}</p>
            </div>
            <div class="p-3 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                <p class="text-[10px] uppercase font-bold text-slate-400 mb-1 tracking-wider">Status & Source</p>
                <div class="flex items-center gap-2 mt-0.5">
                    ${statusBadge}
                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 uppercase">${med.source || 'Lokal'}</span>
                </div>
            </div>
        </div>
        
        <div class="space-y-3">
            <div>
                <p class="text-[11px] uppercase font-bold text-slate-400 mb-1.5 tracking-wider">Indications</p>
                <div class="p-3 text-sm text-slate-600 dark:text-slate-300 rounded-lg bg-primary/10 border border-primary/15 leading-relaxed">
                    ${med.indications || 'Tidak ada data indikasi.'}
                </div>
            </div>
            <div>
                <p class="text-[11px] uppercase font-bold text-slate-400 mb-1.5 tracking-wider">Dosage</p>
                <div class="p-3 text-sm text-slate-600 dark:text-slate-300 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 leading-relaxed">
                    ${med.dosage_info || 'Tidak ada panduan dosis spesifik.'}
                </div>
            </div>
            <div>
                <p class="text-[11px] uppercase font-bold text-rose-400 dark:text-rose-500 mb-1.5 tracking-wider">Side Effects & Warnings</p>
                <div class="p-3 text-sm text-slate-600 dark:text-slate-300 rounded-lg bg-rose-50/50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30 leading-relaxed">
                    ${med.side_effects || 'Tidak ada data efek samping dominan.'}
                </div>
            </div>
        </div>
    `;
    
    // Show Modal
    modal.classList.remove('hidden');
    // Trigger animations
    requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        panel.classList.remove('opacity-0', 'translate-y-4', 'sm:scale-95');
        panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
    });
}

function closeMedicineModal() {
    backdrop.classList.remove('opacity-100');
    backdrop.classList.add('opacity-0');
    panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
    panel.classList.add('opacity-0', 'translate-y-4', 'sm:scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300); // match transition duration
}

document.getElementById('fdaSearchInput')?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); searchFDA(); }
});
</script>
@endpush
