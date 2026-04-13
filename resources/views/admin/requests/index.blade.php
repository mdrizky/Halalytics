@extends('admin.layouts.admin_layout')

@section('title', 'Product Requests - Halalytics Admin')
@section('breadcrumb-parent', 'Crowdsourcing')
@section('breadcrumb-current', 'Product Requests')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Pending Product Requests</h2>
        <p class="text-slate-500 text-sm mt-1">Review kontribusi produk dari user sebelum dipublish.</p>
    </div>
</div>

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm font-medium">{{ session('error') }}</div>
@endif

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="px-5 py-4 w-12">#</th>
                    <th class="px-5 py-4">User</th>
                    <th class="px-5 py-4">Barcode</th>
                    <th class="px-5 py-4">Product</th>
                    <th class="px-5 py-4">Images</th>
                    <th class="px-5 py-4">OCR</th>
                    <th class="px-5 py-4">Date</th>
                    <th class="px-5 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($requests as $request)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                    <td class="px-5 py-4 text-sm text-slate-500">{{ $loop->iteration }}</td>
                    <td class="px-5 py-4 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $request->user->username ?? 'Unknown' }}</td>
                    <td class="px-5 py-4 text-xs font-mono text-slate-600 dark:text-slate-300">{{ $request->barcode }}</td>
                    <td class="px-5 py-4 text-sm text-slate-700 dark:text-slate-300">{{ $request->product_name }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            @if($request->image_front)
                                <a href="{{ $request->image_front }}" target="_blank" class="block w-12 h-12 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700">
                                    <img src="{{ $request->image_front }}" alt="Front" class="w-full h-full object-cover">
                                </a>
                            @endif
                            @if($request->image_back)
                                <a href="{{ $request->image_back }}" target="_blank" class="block w-12 h-12 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700">
                                    <img src="{{ $request->image_back }}" alt="Back" class="w-full h-full object-cover">
                                </a>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4 max-w-xs">
                        <div class="text-xs text-slate-600 dark:text-slate-400 line-clamp-3">{{ $request->ocr_text ?: '-' }}</div>
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-500">{{ $request->created_at->format('d M Y H:i') }}</td>
                    <td class="px-5 py-4">
                        <div class="flex justify-end gap-2">
                            <form action="{{ route('admin.requests.approve', $request->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500 text-white text-xs font-bold hover:bg-emerald-600" onclick="return confirm('Approve this request?')">Approve</button>
                            </form>
                            <form action="{{ route('admin.requests.reject', $request->id) }}" method="POST" class="inline" onsubmit="return confirm('Reject this request?');">
                                @csrf
                                <input type="hidden" name="reason" value="Ditolak admin">
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500 text-white text-xs font-bold hover:bg-red-600">Reject</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-16 text-center">
                        <div class="text-slate-400 text-sm">Belum ada request baru saat ini.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800">
        {{ $requests->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    setInterval(() => {
        const hasModalOpen = document.querySelector('.modal.show');
        if (!hasModalOpen) {
            window.location.reload();
        }
    }, 45000);
</script>
@endpush
