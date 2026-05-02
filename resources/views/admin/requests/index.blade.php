@extends('admin.layouts.admin_layout')

@section('title', 'Pending Product Requests - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Moderation</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Pending Requests</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Product Requests</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-2xl">
                Review user-contributed products before they enter the public catalog. Verify OCR data and product imagery.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center rounded-full bg-amber-50 dark:bg-amber-900/20 px-4 py-2 text-sm font-bold text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-800">
                <span class="material-icons-round text-sm mr-2">pending_actions</span>
                {{ number_format($requests->total()) }} Pending
            </span>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Queue Stats -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Current Queue</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($requests->total()) }}</h3>
                    <p class="text-sm text-slate-500 mt-1">Products to review</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-icons-round text-2xl">inventory</span>
                </div>
            </div>
        </div>

        <!-- Media Health -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Media Availability</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    @php
                        $withImages = $requests->getCollection()->filter(fn($item) => !empty($item->getRawOriginal('image_front')) || !empty($item->getRawOriginal('image_back')))->count();
                    @endphp
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($withImages) }}</h3>
                    <p class="text-sm text-slate-500 mt-1">With proof photos</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 flex items-center justify-center">
                    <span class="material-icons-round text-2xl">photo_camera</span>
                </div>
            </div>
        </div>

        <!-- OCR Confidence -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">OCR Parsing</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    @php
                        $withOcr = $requests->getCollection()->filter(fn($item) => !empty($item->ocr_text))->count();
                    @endphp
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($withOcr) }}</h3>
                    <p class="text-sm text-slate-500 mt-1">Ready for analysis</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-sky-50 dark:bg-sky-900/20 text-sky-600 flex items-center justify-center">
                    <span class="material-icons-round text-2xl">text_snippet</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main List Section -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        <th class="px-6 py-5">Submitted By</th>
                        <th class="px-6 py-5">Product Info</th>
                        <th class="px-6 py-5">Evidence Photos</th>
                        <th class="px-6 py-5">Extracted Data</th>
                        <th class="px-6 py-5 text-right">Action Hub</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($requests as $item)
                        <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all duration-200">
                            <td class="px-6 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-bold text-lg border border-primary/20 shadow-sm">
                                        {{ strtoupper(substr($item->user->username ?? $item->user->full_name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $item->user->full_name ?? $item->user->username ?? 'Anonymous' }}</p>
                                        <div class="flex items-center mt-1">
                                            <span class="material-icons-round text-[10px] text-slate-400 mr-1">history</span>
                                            <p class="text-[10px] text-slate-400 uppercase font-bold">{{ $item->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="max-w-[200px]">
                                    <p class="text-sm font-extrabold text-slate-900 dark:text-white line-clamp-2">{{ $item->product_name }}</p>
                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-mono text-slate-500 border border-slate-200 dark:border-slate-700">
                                            {{ $item->barcode ?: 'NO_BARCODE' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                @php
                                    $frontImage = $item->image_front;
                                    $backImage = $item->image_back;
                                @endphp
                                <div class="flex items-center gap-3">
                                    <div class="relative group/img cursor-pointer" onclick="viewImage('{{ $frontImage }}', 'Front View')">
                                        <div class="h-16 w-16 overflow-hidden rounded-2xl border-2 border-white dark:border-slate-800 bg-slate-100 dark:bg-slate-800 shadow-sm transition-transform group-hover/img:scale-110">
                                            <img src="{{ $frontImage }}" alt="Front" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='/images/placeholders/product-placeholder.svg'">
                                        </div>
                                        <div class="absolute -top-1 -right-1 h-4 w-4 bg-primary text-white rounded-full flex items-center justify-center border-2 border-white dark:border-slate-900">
                                            <span class="material-icons-round text-[8px]">image</span>
                                        </div>
                                    </div>
                                    <div class="relative group/img cursor-pointer" onclick="viewImage('{{ $backImage }}', 'Back View')">
                                        <div class="h-16 w-16 overflow-hidden rounded-2xl border-2 border-white dark:border-slate-800 bg-slate-100 dark:bg-slate-800 shadow-sm transition-transform group-hover/img:scale-110">
                                            <img src="{{ $backImage }}" alt="Back" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='/images/placeholders/product-placeholder.svg'">
                                        </div>
                                        <div class="absolute -top-1 -right-1 h-4 w-4 bg-slate-700 text-white rounded-full flex items-center justify-center border-2 border-white dark:border-slate-900">
                                            <span class="material-icons-round text-[8px]">image</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="max-w-[250px]">
                                    @if($item->ocr_text)
                                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 text-[11px] leading-relaxed text-slate-600 dark:text-slate-400 italic">
                                            "{{ \Illuminate\Support\Str::limit($item->ocr_text, 100) }}"
                                        </div>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">No OCR Data</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <form action="{{ route('admin.requests.approve', $item->id) }}" method="POST" onsubmit="return confirm('Approve and publish this product?')">
                                        @csrf
                                        <button type="submit" class="h-10 px-5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-dark transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
                                            <span class="material-icons-round text-sm">check_circle</span>
                                            APPROVE
                                        </button>
                                    </form>
                                    <button onclick="openRejectModal({{ $item->id }}, '{{ $item->product_name }}')" class="h-10 w-10 rounded-xl border border-rose-200 dark:border-rose-900/50 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all flex items-center justify-center">
                                        <span class="material-icons-round text-lg">block</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="h-20 w-20 rounded-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center mb-4">
                                        <span class="material-icons-round text-4xl text-slate-300">verified_user</span>
                                    </div>
                                    <h4 class="text-lg font-bold text-slate-800 dark:text-white">All Caught Up!</h4>
                                    <p class="text-sm text-slate-400 mt-1 max-w-xs mx-auto">There are no pending product requests to review at this moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-6 bg-slate-50/50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800">
            {{ $requests->links() }}
        </div>
    </div>
</div>

<!-- Image Viewer Modal -->
<div id="imageViewerModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/90 backdrop-blur-sm p-4">
    <div class="relative w-full max-w-4xl max-h-[90vh] flex flex-col items-center">
        <button onclick="closeImageViewer()" class="absolute -top-12 right-0 text-white hover:text-primary transition-colors flex items-center gap-2 font-bold uppercase tracking-widest text-xs">
            Close <span class="material-icons-round">close</span>
        </button>
        <img id="viewerImage" src="" class="w-full h-full object-contain rounded-2xl shadow-2xl">
        <div id="viewerLabel" class="mt-4 px-4 py-2 bg-white/10 backdrop-blur-md rounded-full text-white font-bold text-sm tracking-wider uppercase"></div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-8 shadow-2xl">
        <div class="flex items-center gap-4 mb-6">
            <div class="h-12 w-12 rounded-2xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 flex items-center justify-center flex-shrink-0">
                <span class="material-icons-round text-2xl">error_outline</span>
            </div>
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Reject Request</h3>
                <p class="text-xs text-slate-500 mt-1">Provide a reason for rejecting <span id="rejectProductName" class="font-bold text-slate-700 dark:text-slate-200"></span></p>
            </div>
        </div>

        <form id="rejectForm" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Rejection Reason</label>
                <textarea name="reason" rows="4" required class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm focus:ring-2 focus:ring-rose-500 transition-all" placeholder="e.g. Photo is blurry or barcode doesn't match product..."></textarea>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 px-6 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                    CANCEL
                </button>
                <button type="submit" class="flex-1 px-6 py-3 rounded-2xl bg-rose-600 text-white text-sm font-bold hover:bg-rose-700 transition-all shadow-lg shadow-rose-600/20">
                    REJECT NOW
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewImage(url, label) {
    const modal = document.getElementById('imageViewerModal');
    const img = document.getElementById('viewerImage');
    const lbl = document.getElementById('viewerLabel');
    
    img.src = url;
    lbl.innerText = label;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImageViewer() {
    const modal = document.getElementById('imageViewerModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openRejectModal(id, name) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    const nameSpan = document.getElementById('rejectProductName');
    
    form.action = `/admin/requests/${id}/reject`;
    nameSpan.innerText = name;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endpush
