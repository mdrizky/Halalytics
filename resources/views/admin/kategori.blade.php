@extends('admin.master')

@section('title', 'Category Executive - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Catalog</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Categories</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Category Executive</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 max-w-2xl">
                Organize your product hierarchy, manage visual thumbnails, and monitor item distribution across categories.
            </p>
        </div>
        <button onclick="openModal('addCategoryModal')" class="inline-flex items-center gap-2 rounded-2xl bg-primary px-6 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all transform hover:-translate-y-1">
            <span class="material-icons-round text-lg">add</span>
            NEW CATEGORY
        </button>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Structure</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($kategori->total()) }}</h3>
                    <p class="text-sm text-slate-500 mt-1">Active categories</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-icons-round text-2xl">category</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Inventory</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($kategori->sum('products_count')) }}</h3>
                    <p class="text-sm text-slate-500 mt-1">Mapped products</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 flex items-center justify-center">
                    <span class="material-icons-round text-2xl">inventory_2</span>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Visuals</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($kategori->getCollection()->filter(fn($item) => filled($item->thumbnail_url))->count()) }}</h3>
                    <p class="text-sm text-slate-500 mt-1">Thumbnails active</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 flex items-center justify-center">
                    <span class="material-icons-round text-2xl">photo_library</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form action="{{ route('admin.kategori.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <span class="material-icons-round absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 pl-12 pr-4 py-3.5 text-sm focus:ring-2 focus:ring-primary transition-all"
                    placeholder="Search category name or description..."
                >
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="rounded-2xl bg-slate-900 dark:bg-white dark:text-slate-900 px-6 py-3.5 text-sm font-bold text-white hover:opacity-90 transition-all">
                    FILTER
                </button>
                <a href="{{ route('admin.kategori.index') }}" class="rounded-2xl border border-slate-200 dark:border-slate-700 px-6 py-3.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                    RESET
                </a>
            </div>
        </form>
    </div>

    <!-- Category Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($kategori as $item)
            <article class="group bg-white dark:bg-slate-900 rounded-[2.5rem] p-6 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 relative overflow-hidden">
                <!-- Visual Background Glow -->
                <div class="absolute -top-12 -right-12 h-32 w-32 bg-primary/5 blur-3xl rounded-full group-hover:bg-primary/10 transition-colors"></div>

                <div class="flex items-start justify-between relative z-10">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="h-20 w-20 overflow-hidden rounded-[2rem] border-2 border-white dark:border-slate-800 bg-slate-100 dark:bg-slate-800 shadow-lg group-hover:scale-105 transition-transform duration-500">
                            <img src="{{ $item->thumbnail_url }}" alt="{{ $item->nama_kategori }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='https://loremflickr.com/200/200/{{ urlencode($item->nama_kategori) }},product,category?lock={{ $item->id_kategori }}'">
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white truncate">{{ $item->nama_kategori }}</h3>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ number_format($item->products_count) }} products</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button 
                            data-id="{{ $item->id_kategori }}"
                            data-name="{{ $item->nama_kategori }}"
                            data-desc="{{ $item->description }}"
                            onclick="editCategory(this.dataset.id, this.dataset.name, this.dataset.desc)" 
                            class="h-10 w-10 rounded-full text-slate-400 hover:bg-primary/10 hover:text-primary transition-all">
                            <span class="material-icons-round text-lg">edit</span>
                        </button>
                        <form action="{{ route('admin.kategori.destroy', $item->id_kategori) }}" method="POST" onsubmit="return confirm('Delete this category and all its product mappings?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="h-10 w-10 rounded-full text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all">
                                <span class="material-icons-round text-lg">delete_outline</span>
                            </button>
                        </form>
                    </div>
                </div>

                <p class="mt-6 text-sm leading-7 text-slate-500 dark:text-slate-400 line-clamp-2 min-h-[3.5rem]">
                    {{ $item->description ?: 'No description provided. Add a brief summary to help organize the product catalog more effectively.' }}
                </p>

                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="inline-flex items-center rounded-full bg-primary/5 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-primary border border-primary/10">
                        ACTIVE CATALOG
                    </span>
                    <a href="{{ route('admin.product.index', ['category' => $item->id_kategori]) }}" class="group/btn flex items-center gap-2 text-sm font-bold text-slate-700 dark:text-slate-300 hover:text-primary transition-all">
                        View Products
                        <span class="material-icons-round text-sm group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white dark:bg-slate-900 rounded-[3rem] px-6 py-20 text-center border border-dashed border-slate-200 dark:border-slate-800">
                <div class="flex flex-col items-center">
                    <div class="h-20 w-20 rounded-full bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center mb-4">
                        <span class="material-icons-round text-4xl text-slate-300">category</span>
                    </div>
                    <h4 class="text-xl font-bold text-slate-800 dark:text-white">No Categories Found</h4>
                    <p class="text-sm text-slate-400 mt-2">Start by creating a new category to organize your products.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl px-6 py-4 border border-slate-200 dark:border-slate-800">
        {{ $kategori->links() }}
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-xl rounded-[2.5rem] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-8 shadow-2xl overflow-hidden relative">
        <!-- Decor -->
        <div class="absolute top-0 right-0 h-32 w-32 bg-primary/5 blur-3xl rounded-full"></div>

        <div class="flex items-start justify-between gap-4 relative z-10">
            <div>
                <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white">Create Category</h3>
                <p class="mt-1 text-sm text-slate-500">Define a new segment for your product catalog.</p>
            </div>
            <button type="button" onclick="closeModal('addCategoryModal')" class="rounded-full h-10 w-10 flex items-center justify-center bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-slate-600">
                <span class="material-icons-round">close</span>
            </button>
        </div>

        <form action="{{ route('admin.kategori.store') }}" method="POST" class="mt-8 space-y-6 relative z-10">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Category Name</label>
                    <input type="text" name="nama_kategori" required class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-5 py-4 text-sm focus:ring-2 focus:ring-primary transition-all" placeholder="e.g. Frozen Food">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Description</label>
                    <textarea name="description" rows="4" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-5 py-4 text-sm focus:ring-2 focus:ring-primary transition-all" placeholder="Briefly describe what kind of products belong here..."></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('addCategoryModal')" class="px-8 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">
                    CANCEL
                </button>
                <button type="submit" class="px-8 py-3.5 rounded-2xl bg-primary text-white text-sm font-bold hover:bg-primary-dark transition-all shadow-lg shadow-primary/20">
                    SAVE CATEGORY
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
    <div class="w-full max-w-xl rounded-[2.5rem] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-8 shadow-2xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white">Modify Category</h3>
                <p class="mt-1 text-sm text-slate-500">Update the core attributes of this category.</p>
            </div>
            <button type="button" onclick="closeModal('editCategoryModal')" class="rounded-full h-10 w-10 flex items-center justify-center bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-slate-600">
                <span class="material-icons-round">close</span>
            </button>
        </div>

        <form id="editCategoryForm" method="POST" class="mt-8 space-y-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Category Name</label>
                    <input type="text" name="nama_kategori" id="edit_nama_kategori" required class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-5 py-4 text-sm focus:ring-2 focus:ring-primary transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 ml-1">Description</label>
                    <textarea name="description" id="edit_description" rows="4" class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-5 py-4 text-sm focus:ring-2 focus:ring-primary transition-all"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('editCategoryModal')" class="px-8 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">
                    CANCEL
                </button>
                <button type="submit" class="px-8 py-3.5 rounded-2xl bg-slate-900 dark:bg-white dark:text-slate-900 text-white text-sm font-bold hover:opacity-90 transition-all shadow-lg">
                    UPDATE CATEGORY
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}

function editCategory(id, name, description) {
    const form = document.getElementById('editCategoryForm');
    form.action = `/admin/kategori/${id}`;
    document.getElementById('edit_nama_kategori').value = name || '';
    document.getElementById('edit_description').value = description || '';
    openModal('editCategoryModal');
}
</script>
@endpush

