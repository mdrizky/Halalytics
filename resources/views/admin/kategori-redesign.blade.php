@extends('admin.layouts.admin_layout')

@section('title', 'Category Management - Halalytics Admin')
@section('breadcrumb-parent', 'Management')
@section('breadcrumb-current', 'Categories')

@section('content')
<!-- Header -->
<div class="flex justify-between items-end mb-8">
    <div class="max-w-2xl">
        <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">Category Executive</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-2">Manage product categories and organize your inventory structure efficiently.</p>
    </div>
    <div class="flex gap-3">
        <button onclick="openModal('addCategoryModal')" class="flex items-center gap-2 bg-primary px-4 py-2.5 rounded-lg text-sm font-bold text-white hover:bg-primary-dark transition-all shadow-lg shadow-primary/20">
            <span class="material-icons-round text-[20px]">add</span>
            Add New Category
        </button>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Categories -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
            <span class="material-icons-round text-6xl text-primary">category</span>
        </div>
        <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Total Categories</p>
        <div class="flex items-baseline gap-3 mt-2">
            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($kategori->total()) }}</p>
        </div>
    </div>
    
    <!-- Active Categories -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
            <span class="material-icons-round text-6xl text-emerald-500">check_circle</span>
        </div>
        <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Active Status</p>
        <div class="flex items-baseline gap-3 mt-2">
            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($kategori->count()) }}</p>
            <span class="text-emerald-500 text-sm font-bold flex items-center gap-0.5">
                <span class="material-icons-round text-xs">verified</span> Live
            </span>
        </div>
    </div>

    <!-- Products Linked -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
            <span class="material-icons-round text-6xl text-primary">inventory_2</span>
        </div>
        <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Products Linked</p>
        <div class="flex items-baseline gap-3 mt-2">
            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">
                {{ number_format($kategori->sum('products_count')) }}
            </p>
        </div>
    </div>
</div>

<!-- Filter & Toolbar -->
<div class="bg-white dark:bg-slate-900 rounded-t-xl border-x border-t border-slate-200 dark:border-slate-800 p-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('admin.kategori.index') }}" method="GET" class="flex-1">
            <div class="relative w-full max-w-sm">
                <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary/50 transition-all placeholder:text-slate-400" placeholder="Search categories...">
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm rounded-b-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest pl-10">Category Name</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Description</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Products</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($kategori as $kat)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @php
                                $catImages = [
                                    'Obat' => 'https://images.unsplash.com/photo-1584308666744-24d5e478ac5c?q=80&w=200',
                                    'Kosmetik' => 'https://images.unsplash.com/photo-1596462502278-27bf85033e5a?q=80&w=200',
                                    'Pangan Olahan' => 'https://images.unsplash.com/photo-1606859191214-25806e8e2423?q=80&w=200',
                                    'Suplemen' => 'https://images.unsplash.com/photo-1550508003-8833cb9137d2?q=80&w=200',
                                ];
                                $defaultImg = 'https://images.unsplash.com/photo-1563240619-44ec0047592c?q=80&w=200';
                                $imgSrc = $kat->thumbnail_url;
                                if (!$imgSrc || str_contains($imgSrc, 'placeholder')) {
                                    $imgSrc = $catImages[$kat->nama_kategori] ?? $defaultImg;
                                }
                            @endphp
                            <div class="h-12 w-12 rounded-2xl overflow-hidden bg-primary/10 flex items-center justify-center text-primary font-bold text-lg border border-slate-200 dark:border-slate-700">
                                <img src="{{ $imgSrc }}" alt="{{ $kat->nama_kategori }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name='+encodeURIComponent('{{ $kat->nama_kategori }}')+'&background=random';">
                            </div>
                            <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $kat->nama_kategori }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-1">
                            {{ $kat->description ?? 'No description provided' }}
                        </p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary dark:bg-primary/15 dark:text-emerald-300">
                            {{ $kat->products_count }} Products
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editCategory({{ $kat->id_kategori }}, '{{ addslashes($kat->nama_kategori) }}', '{{ addslashes($kat->description) }}')" class="p-2 text-slate-400 hover:text-primary transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <form action="{{ route('admin.kategori.destroy', $kat->id_kategori) }}" method="POST" onsubmit="return confirm('Delete this category? Products linked to it might lose their categorization.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-500 transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                                    <span class="material-icons-round text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                        <span class="material-icons-round text-4xl mb-2">category</span>
                        <p>No categories found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <p class="text-sm text-slate-500 font-medium">
            Showing <span class="text-slate-900 dark:text-white">{{ $kategori->firstItem() ?? 0 }}</span> 
            to <span class="text-slate-900 dark:text-white">{{ $kategori->lastItem() ?? 0 }}</span> 
            of <span class="text-slate-900 dark:text-white">{{ number_format($kategori->total()) }}</span> categories
        </p>
        <div class="flex items-center gap-2">
           {{ $kategori->links('vendor.pagination.tailwind-admin') }}
        </div>
    </div>
</div>

<!-- Modal Add -->
<div id="addCategoryModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('addCategoryModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700">
            <form action="{{ route('admin.kategori.store') }}" method="POST">
                @csrf
                <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-icons-round text-primary">category</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-white" id="modal-title">Add New Category</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="nama_kategori" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Category Name</label>
                                    <input type="text" name="nama_kategori" id="nama_kategori" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white focus:ring-primary focus:border-primary sm:text-sm">
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                                    <textarea name="description" id="description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white focus:ring-primary focus:border-primary sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100 dark:border-slate-700">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Create Category
                    </button>
                    <button type="button" onclick="closeModal('addCategoryModal')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-700 text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="editCategoryModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('editCategoryModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700">
            <form action="#" method="POST" id="editCategoryForm">
                @csrf
                @method('PUT')
                <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-icons-round text-primary">edit</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-white">Edit Category</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="edit_nama_kategori" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Category Name</label>
                                    <input type="text" name="nama_kategori" id="edit_nama_kategori" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white focus:ring-primary focus:border-primary sm:text-sm">
                                </div>
                                <div>
                                    <label for="edit_description" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                                    <textarea name="description" id="edit_description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white focus:ring-primary focus:border-primary sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100 dark:border-slate-700">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Save Changes
                    </button>
                    <button type="button" onclick="closeModal('editCategoryModal')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-700 text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function editCategory(id, name, description) {
        const form = document.getElementById('editCategoryForm');
        form.action = `/admin/kategori/${id}`;
        
        document.getElementById('edit_nama_kategori').value = name;
        document.getElementById('edit_description').value = description;
        
        openModal('editCategoryModal');
    }
</script>
@endsection
