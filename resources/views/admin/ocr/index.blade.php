@extends('admin.layouts.admin_layout')

@section('title', 'OCR Management - Halalytics Admin')
@section('breadcrumb-parent', 'Expansion Modules')
@section('breadcrumb-current', 'OCR Management')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">OCR Management</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-2 max-w-2xl">
            Review hasil OCR kemasan produk, cek gambar depan-belakang, dan putuskan approval dari satu panel yang sinkron dengan skema data terbaru.
        </p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.ocr.export') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 font-semibold shadow-sm hover:-translate-y-0.5 transition-all">
            <span class="material-icons-round text-lg">download</span>
            Export CSV
        </a>
        <button type="button" onclick="refreshOcrDashboard()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white font-semibold shadow-lg shadow-primary/20 hover:-translate-y-0.5 transition-all">
            <span class="material-icons-round text-lg">refresh</span>
            Refresh
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
        <p class="text-xs font-black uppercase tracking-[0.24em] text-slate-400">Total Scan</p>
        <div class="mt-3 flex items-center justify-between">
            <div class="text-3xl font-extrabold text-slate-900 dark:text-white" id="ocrTotal">0</div>
            <div class="w-11 h-11 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                <span class="material-icons-round">qr_code_scanner</span>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
        <p class="text-xs font-black uppercase tracking-[0.24em] text-slate-400">Pending Review</p>
        <div class="mt-3 flex items-center justify-between">
            <div class="text-3xl font-extrabold text-slate-900 dark:text-white" id="ocrPending">0</div>
            <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
                <span class="material-icons-round">schedule</span>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
        <p class="text-xs font-black uppercase tracking-[0.24em] text-slate-400">Approved Hari Ini</p>
        <div class="mt-3 flex items-center justify-between">
            <div class="text-3xl font-extrabold text-slate-900 dark:text-white" id="ocrApprovedToday">0</div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <span class="material-icons-round">verified</span>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
        <p class="text-xs font-black uppercase tracking-[0.24em] text-slate-400">Rejected Hari Ini</p>
        <div class="mt-3 flex items-center justify-between">
            <div class="text-3xl font-extrabold text-slate-900 dark:text-white" id="ocrRejectedToday">0</div>
            <div class="w-11 h-11 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                <span class="material-icons-round">dangerous</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_420px] gap-6">
    <section class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Queue Produk OCR</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Filter hasil scan dan lakukan approval tanpa reload halaman.</p>
            </div>
            <div class="flex flex-wrap gap-2" id="ocrFilters">
                <button type="button" data-filter="pending" class="ocr-filter-btn px-3 py-2 rounded-full text-xs font-bold bg-primary text-white">Pending</button>
                <button type="button" data-filter="approved" class="ocr-filter-btn px-3 py-2 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-500">Approved</button>
                <button type="button" data-filter="rejected" class="ocr-filter-btn px-3 py-2 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-500">Rejected</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-[11px] font-black uppercase tracking-[0.24em] text-slate-400">
                    <tr>
                        <th class="px-5 py-4">Produk</th>
                        <th class="px-5 py-4">User</th>
                        <th class="px-5 py-4">Confidence</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Waktu</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ocrTableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center text-slate-400">Memuat data OCR...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <aside class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm overflow-hidden min-h-[580px]">
        <div class="p-5 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Detail Review</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Klik salah satu item untuk melihat gambar, teks OCR, dan hasil parsing bahan.</p>
        </div>
        <div id="ocrDetailPanel" class="p-5">
            <div class="rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 px-6 py-16 text-center text-slate-400">
                <span class="material-icons-round text-5xl mb-3 block text-primary/60">document_scanner</span>
                Pilih item OCR dari tabel untuk mulai review.
            </div>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
const ocrBaseUrl = @json(url('/admin/ocr'));
let currentFilter = 'pending';
let currentDetail = null;

document.querySelectorAll('.ocr-filter-btn').forEach((button) => {
    button.addEventListener('click', () => {
        currentFilter = button.dataset.filter;
        document.querySelectorAll('.ocr-filter-btn').forEach((btn) => {
            btn.className = 'ocr-filter-btn px-3 py-2 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-500';
        });
        button.className = 'ocr-filter-btn px-3 py-2 rounded-full text-xs font-bold bg-primary text-white';
        loadOcrProducts();
    });
});

async function refreshOcrDashboard() {
    await Promise.all([loadOcrStatistics(), loadOcrProducts()]);
}

async function loadOcrStatistics() {
    try {
        const response = await fetch(`${ocrBaseUrl}/statistics`, { headers: { Accept: 'application/json' } });
        const payload = await response.json();
        const stats = payload?.data || {};
        document.getElementById('ocrTotal').textContent = stats.total_scans ?? stats.total ?? 0;
        document.getElementById('ocrPending').textContent = stats.pending_review ?? stats.pending ?? 0;
        document.getElementById('ocrApprovedToday').textContent = stats.approved_today ?? stats.today_approved ?? 0;
        document.getElementById('ocrRejectedToday').textContent = stats.rejected_today ?? stats.today_rejected ?? 0;
    } catch (error) {
        console.error('OCR statistics error', error);
    }
}

async function loadOcrProducts() {
    const routeMap = {
        pending: `${ocrBaseUrl}/pending`,
        approved: `${ocrBaseUrl}/approved`,
        rejected: `${ocrBaseUrl}/rejected`,
    };

    const tbody = document.getElementById('ocrTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="px-5 py-16 text-center text-slate-400">Memuat data OCR...</td></tr>';

    try {
        const response = await fetch(routeMap[currentFilter], { headers: { Accept: 'application/json' } });
        const payload = await response.json();
        const rows = payload?.data?.data || [];

        if (rows.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="px-5 py-16 text-center text-slate-400">Belum ada produk OCR pada filter ${escapeHtml(currentFilter)}.</td></tr>`;
            document.getElementById('ocrDetailPanel').innerHTML = `
                <div class="rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 px-6 py-16 text-center text-slate-400">
                    <span class="material-icons-round text-5xl mb-3 block text-primary/60">inbox</span>
                    Tidak ada data untuk filter saat ini.
                </div>
            `;
            return;
        }

        tbody.innerHTML = rows.map((row) => renderOcrRow(row)).join('');
        currentDetail = rows[0];
        renderDetail(rows[0]);
    } catch (error) {
        console.error('OCR products error', error);
        tbody.innerHTML = '<tr><td colspan="6" class="px-5 py-16 text-center text-rose-500">Gagal memuat data OCR.</td></tr>';
    }
}

function renderOcrRow(product) {
    const image = product.front_image_url || product.back_image_url;
    const confidence = Number(product.confidence_level || product.confidence_score || 0);
    const statusClass = product.status === 'approved'
        ? 'bg-emerald-100 text-emerald-700'
        : product.status === 'rejected'
            ? 'bg-rose-100 text-rose-700'
            : 'bg-amber-100 text-amber-700';

    return `
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors cursor-pointer" onclick="loadOcrDetail(${product.id})">
            <td class="px-5 py-4">
                <div class="flex items-center gap-3">
                    <img src="${escapeHtml(image)}" alt="${escapeHtml(product.product_name || 'OCR Product')}" class="w-12 h-12 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div>
                        <div class="text-sm font-bold text-slate-900 dark:text-white">${escapeHtml(product.product_name || 'Produk OCR')}</div>
                        <div class="text-[11px] text-slate-400">${escapeHtml(product.brand || 'Tanpa brand')} · ${escapeHtml(product.processing_step || 'Belum lengkap')}</div>
                    </div>
                </div>
            </td>
            <td class="px-5 py-4">
                <div class="text-sm font-semibold text-slate-800 dark:text-slate-200">${escapeHtml(product.user?.full_name || product.user?.username || 'Unknown')}</div>
                <div class="text-[11px] text-slate-400">${escapeHtml(product.user?.username || '-')}</div>
            </td>
            <td class="px-5 py-4">
                <div class="text-sm font-bold text-slate-900 dark:text-white">${confidence.toFixed(1)}%</div>
                <div class="mt-2 h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                    <div class="h-full bg-primary rounded-full" style="width:${Math.min(100, Math.max(confidence, 4))}%"></div>
                </div>
            </td>
            <td class="px-5 py-4">
                <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-[0.18em] ${statusClass}">
                    ${escapeHtml(product.status_label || product.status)}
                </span>
            </td>
            <td class="px-5 py-4 text-sm text-slate-500">${formatDate(product.created_at)}</td>
            <td class="px-5 py-4 text-right">
                <div class="flex justify-end gap-2">
                    ${product.status !== 'approved' ? `<button type="button" onclick="event.stopPropagation(); approveOcr(${product.id})" class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-emerald-500 text-white text-xs font-bold shadow-sm hover:bg-emerald-600">Approve</button>` : ''}
                    ${product.status !== 'rejected' ? `<button type="button" onclick="event.stopPropagation(); rejectOcr(${product.id})" class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-rose-500 text-white text-xs font-bold shadow-sm hover:bg-rose-600">Reject</button>` : ''}
                </div>
            </td>
        </tr>
    `;
}

async function loadOcrDetail(id) {
    try {
        const response = await fetch(`${ocrBaseUrl}/product/${id}`, { headers: { Accept: 'application/json' } });
        const payload = await response.json();
        currentDetail = payload?.data || null;
        if (currentDetail) {
            renderDetail(currentDetail);
        }
    } catch (error) {
        console.error('OCR detail error', error);
    }
}

function renderDetail(product) {
    const panel = document.getElementById('ocrDetailPanel');
    const images = [product.front_image_url, product.back_image_url].filter(Boolean);
    const ingredients = (product.ingredients || []).slice(0, 20);
    const adminNote = product.admin_note ? `<div class="rounded-2xl bg-amber-50 text-amber-700 px-4 py-3 text-sm font-medium">${escapeHtml(product.admin_note)}</div>` : '';

    panel.innerHTML = `
        <div class="space-y-5">
            <div>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h4 class="text-xl font-extrabold text-slate-900 dark:text-white">${escapeHtml(product.product_name || 'Produk OCR')}</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">${escapeHtml(product.brand || 'Tanpa brand')} · ${escapeHtml(product.user?.full_name || product.user?.username || 'Unknown')}</p>
                    </div>
                    <span class="inline-flex px-3 py-1.5 rounded-full text-[11px] font-black uppercase tracking-[0.18em] ${product.status === 'approved' ? 'bg-emerald-100 text-emerald-700' : product.status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700'}">
                        ${escapeHtml(product.status_label || product.status)}
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                ${images.map((image) => `<img src="${escapeHtml(image)}" alt="OCR Image" class="w-full aspect-[4/3] object-cover rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">`).join('')}
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl bg-slate-50 dark:bg-slate-800 p-4">
                    <p class="text-xs font-black uppercase tracking-[0.24em] text-slate-400">Confidence</p>
                    <p class="mt-2 text-2xl font-extrabold text-slate-900 dark:text-white">${Number(product.confidence_level || product.confidence_score || 0).toFixed(1)}%</p>
                </div>
                <div class="rounded-2xl bg-slate-50 dark:bg-slate-800 p-4">
                    <p class="text-xs font-black uppercase tracking-[0.24em] text-slate-400">Waktu Submit</p>
                    <p class="mt-2 text-sm font-bold text-slate-900 dark:text-white">${formatDate(product.created_at, true)}</p>
                </div>
            </div>
            ${adminNote}
            <div>
                <p class="text-xs font-black uppercase tracking-[0.24em] text-slate-400 mb-3">Extracted Text</p>
                <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-4 text-sm text-slate-700 dark:text-slate-300 leading-7">${escapeHtml(product.extracted_text || '-')}</div>
            </div>
            <div>
                <p class="text-xs font-black uppercase tracking-[0.24em] text-slate-400 mb-3">Detected Ingredients</p>
                <div class="flex flex-wrap gap-2">
                    ${ingredients.length > 0
                        ? ingredients.map((item) => `<span class="inline-flex px-3 py-1.5 rounded-full bg-primary/10 text-primary text-xs font-bold">${escapeHtml(item)}</span>`).join('')
                        : '<span class="text-sm text-slate-400">Belum ada bahan yang berhasil diparsing.</span>'}
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                ${product.status !== 'approved' ? `<button type="button" onclick="approveOcr(${product.id})" class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-3 rounded-2xl bg-emerald-500 text-white font-bold hover:bg-emerald-600">Approve</button>` : ''}
                ${product.status !== 'rejected' ? `<button type="button" onclick="rejectOcr(${product.id})" class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-3 rounded-2xl bg-rose-500 text-white font-bold hover:bg-rose-600">Reject</button>` : ''}
            </div>
        </div>
    `;
}

async function approveOcr(id) {
    if (!confirm('Setujui hasil OCR ini?')) {
        return;
    }

    try {
        const response = await fetch(`${ocrBaseUrl}/approve/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ notes: 'Approved via admin OCR dashboard' })
        });

        const payload = await response.json();
        if (!payload.success) {
            throw new Error(payload.message || 'Approval gagal');
        }

        await refreshOcrDashboard();
        await loadOcrDetail(id);
    } catch (error) {
        alert(error.message || 'Gagal approve OCR');
    }
}

async function rejectOcr(id) {
    const reason = prompt('Masukkan alasan penolakan OCR:');
    if (reason === null) {
        return;
    }

    try {
        const response = await fetch(`${ocrBaseUrl}/reject/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason })
        });

        const payload = await response.json();
        if (!payload.success) {
            throw new Error(payload.message || 'Reject gagal');
        }

        await refreshOcrDashboard();
        await loadOcrDetail(id);
    } catch (error) {
        alert(error.message || 'Gagal reject OCR');
    }
}

function formatDate(value, withTime = false) {
    if (!value) return '-';
    const date = new Date(value);
    return new Intl.DateTimeFormat('id-ID', withTime ? {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    } : {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    }).format(date);
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

document.addEventListener('DOMContentLoaded', refreshOcrDashboard);
</script>
@endpush
