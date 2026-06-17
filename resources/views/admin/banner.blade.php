@extends('admin.layouts.admin_layout')

@section('title', 'Banner Slider - Halalytics Admin')
@section('breadcrumb-parent', 'Content')
@section('breadcrumb-current', 'Banner Slider')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Banner Slider</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-2xl">
                Kelola banner promosi dan informasi utama yang tampil di halaman home. Urutan tampil, status aktif, dan visual poster sekarang dipisahkan dengan lebih rapi.
            </p>
        </div>
        <button onclick="openCreateModal()" class="inline-flex items-center gap-2 rounded-full bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark transition">
            <span class="material-icons-round text-lg">add_photo_alternate</span>
            Tambah Banner
        </button>
    </div>

    @if($errors->any())
        <div class="rounded-2xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 p-4">
            <div class="flex items-center gap-3">
                <span class="material-icons-round text-rose-500">error_outline</span>
                <div>
                    <p class="text-sm font-bold text-rose-700 dark:text-rose-300">Terjadi kesalahan</p>
                    <ul class="mt-1 text-sm text-rose-600 dark:text-rose-400 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="surface-card rounded-2xl p-5">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Total Banner</p>
            <div class="mt-3 flex items-end justify-between">
                <div>
                    <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($banners->count()) }}</p>
                    <p class="text-sm text-slate-500">Seluruh campaign visual</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-icons-round">view_carousel</span>
                </div>
            </div>
        </div>
        <div class="surface-card rounded-2xl p-5">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Active Banner</p>
            <div class="mt-3 flex items-end justify-between">
                <div>
                    <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($banners->where('is_active', true)->count()) }}</p>
                    <p class="text-sm text-slate-500">Tampil ke user</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-icons-round">campaign</span>
                </div>
            </div>
        </div>
        <div class="surface-card rounded-2xl p-5">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Inactive Draft</p>
            <div class="mt-3 flex items-end justify-between">
                <div>
                    <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($banners->where('is_active', false)->count()) }}</p>
                    <p class="text-sm text-slate-500">Masih bisa direvisi</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                    <span class="material-icons-round">draft</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($banners as $banner)
            <article class="surface-card overflow-hidden rounded-3xl">
                <div class="relative h-56 bg-slate-100 dark:bg-slate-800">
                    <img src="{{ $banner->image_url }}{{ str_contains($banner->image_url, '?') ? '&' : '?' }}v={{ strtotime($banner->updated_at) }}" alt="{{ $banner->title }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='https://loremflickr.com/800/400/{{ urlencode($banner->title) }},banner,advertising?lock={{ $banner->id }}'">
                    <div class="absolute left-4 top-4 flex items-center gap-2">
                        <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-bold uppercase {{ $banner->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-900/80 text-white' }}">
                            {{ $banner->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="inline-flex rounded-full bg-white/90 px-3 py-1 text-[11px] font-bold text-slate-700">
                            Posisi {{ $banner->position }}
                        </span>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $banner->title }}</h3>
                    <p class="mt-2 min-h-[48px] text-sm leading-6 text-slate-500 dark:text-slate-400">
                        {{ $banner->description ?: 'Belum ada deskripsi. Tambahkan konteks campaign agar admin lain mudah memahaminya.' }}
                    </p>
                    <div class="mt-5 flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.16em] text-slate-400">Last update</p>
                            <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $banner->updated_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button 
                                data-id="{{ $banner->id }}"
                                data-title="{{ $banner->title }}"
                                data-desc="{{ $banner->description }}"
                                data-pos="{{ (int) $banner->position }}"
                                data-active="{{ $banner->is_active ? 'true' : 'false' }}"
                                data-image="{{ $banner->image }}"
                                onclick="openEditModal(this.dataset.id, this.dataset.title, this.dataset.desc, this.dataset.pos, this.dataset.active, this.dataset.image)" 
                                class="inline-flex items-center rounded-full border border-slate-200 dark:border-slate-700 px-3 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:border-primary hover:text-primary transition">
                                Edit
                            </button>
                            <form action="{{ route('admin.banner.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Hapus banner ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center rounded-full border border-rose-200 px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="surface-card col-span-full rounded-3xl px-6 py-16 text-center">
                <div class="flex flex-col items-center text-slate-400">
                    <span class="material-icons-round text-5xl mb-3">view_carousel</span>
                    <p class="text-sm font-medium">Belum ada banner yang tersimpan.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4">
    <div class="w-full max-w-2xl rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-6 shadow-2xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Tambah Banner Baru</h3>
                <p class="mt-1 text-sm text-slate-500">Unggah banner dengan deskripsi, urutan tampil, dan status aktif.</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                <span class="material-icons-round">close</span>
            </button>
        </div>

        <form action="{{ route('admin.banner.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Judul banner</label>
                <input type="text" name="title" required class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm">
            </div>
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Posisi urutan</label>
                    <input type="number" name="position" min="1" value="1" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Gambar banner</label>
                    <input type="file" name="image" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm">
                    <p class="mt-1 text-xs text-slate-400">Maksimal 10MB. Format: JPG, PNG, WebP.</p>
                </div>
            </div>
            <label class="flex items-center gap-3 rounded-2xl bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                <input type="checkbox" name="is_active" checked class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary">
                Aktifkan banner setelah disimpan
            </label>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCreateModal()" class="rounded-full border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300">
                    Batal
                </button>
                <button type="submit" class="rounded-full bg-primary px-5 py-2 text-sm font-bold text-white hover:bg-primary-dark transition">
                    Simpan Banner
                </button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4">
    <div class="w-full max-w-2xl rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-6 shadow-2xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Edit Banner</h3>
                <p class="mt-1 text-sm text-slate-500">Perbarui judul, deskripsi, gambar, dan status banner.</p>
            </div>
            <button type="button" onclick="closeEditModal()" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                <span class="material-icons-round">close</span>
            </button>
        </div>

        <form id="editForm" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Judul banner</label>
                <input type="text" name="title" id="edit_title" required class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm">
            </div>
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Deskripsi</label>
                <textarea name="description" id="edit_desc" rows="3" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Posisi urutan</label>
                    <input type="number" name="position" id="edit_pos" min="1" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Ganti gambar</label>
                    <input type="file" name="image" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm">
                    <p class="mt-1 text-xs text-slate-400">Maksimal 10MB. Format: JPG, PNG, WebP. Biarkan kosong jika tidak ingin mengganti.</p>
                    <div id="edit_image_preview" class="mt-3 hidden">
                        <img id="edit_image_preview_img" src="" alt="Preview" class="h-32 w-full rounded-xl object-cover border border-slate-200 dark:border-slate-700">
                        <p class="mt-1 text-xs text-slate-400">Gambar saat ini</p>
                    </div>
                </div>
            </div>
            <label class="flex items-center gap-3 rounded-2xl bg-slate-50 dark:bg-slate-800 px-4 py-3 text-sm font-semibold text-slate-700 dark:text-slate-200">
                <input type="checkbox" name="is_active" id="edit_active" class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary">
                Banner aktif
            </label>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="rounded-full border border-slate-200 dark:border-slate-700 px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300">
                    Batal
                </button>
                <button type="submit" class="rounded-full bg-primary px-5 py-2 text-sm font-bold text-white hover:bg-primary-dark transition">
                    Update Banner
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    document.getElementById('createModal').classList.add('flex');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.getElementById('createModal').classList.remove('flex');
}

function openEditModal(id, title, desc, pos, active, image) {
    document.getElementById('edit_title').value = title || '';
    document.getElementById('edit_desc').value = desc || '';
    document.getElementById('edit_pos').value = pos || 1;
    document.getElementById('edit_active').checked = active === 'true';
    document.getElementById('editForm').action = `/admin/banner/${id}`;
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');

    let preview = document.getElementById('edit_image_preview');
    let previewImg = document.getElementById('edit_image_preview_img');
    if (image) {
        preview.classList.remove('hidden');
        previewImg.src = '{{ asset("") }}' + image;
    } else {
        preview.classList.add('hidden');
    }
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}
</script>
@endpush
