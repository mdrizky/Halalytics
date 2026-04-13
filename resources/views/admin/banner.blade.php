@extends('admin.layouts.admin_layout')

@section('title', 'Banner Management - Halalytics Admin')
@section('breadcrumb-parent', 'App Management')
@section('breadcrumb-current', 'Banners')

@section('content')
<!-- Header -->
<div class="flex justify-between items-end mb-8">
    <div class="max-w-2xl">
        <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white tracking-tight">Banner Slider</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-2">Manage the promotional banners displayed on the mobile app home screen.</p>
    </div>
    <div class="flex gap-3">
        <button onclick="openModal('addBannerModal')" class="flex items-center gap-2 bg-primary px-4 py-2.5 rounded-lg text-sm font-bold text-white hover:bg-primary-dark transition-all shadow-lg shadow-primary/20">
            <span class="material-icons-round text-[20px]">add_photo_alternate</span>
            Add New Banner
        </button>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Banners -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
            <span class="material-icons-round text-6xl text-primary">view_carousel</span>
        </div>
        <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Total Banners</p>
        <div class="flex items-baseline gap-3 mt-2">
            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($banners->count()) }}</p>
        </div>
    </div>
    
    <!-- Active Banners -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
            <span class="material-icons-round text-6xl text-emerald-500">check_circle</span>
        </div>
        <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Active & Visible</p>
        <div class="flex items-baseline gap-3 mt-2">
            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($banners->where('is_active', 1)->count()) }}</p>
            <span class="text-emerald-500 text-sm font-bold flex items-center gap-0.5">
                <span class="material-icons-round text-xs">visibility</span> Live
            </span>
        </div>
    </div>

    <!-- Estimated Reach -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
            <span class="material-icons-round text-6xl text-primary">people</span>
        </div>
        <p class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Estimated Reach</p>
        <div class="flex items-baseline gap-3 mt-2">
            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($global_user_count ?? 0) }}</p>
            <span class="text-slate-400 text-xs font-semibold">Users</span>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm rounded-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest pl-10">Banner Image</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Title & Description</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Position</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($banners as $banner)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="w-32 h-16 rounded-lg bg-slate-100 dark:bg-slate-800 overflow-hidden border border-slate-200 dark:border-slate-700 relative">
                            @if($banner->image)
                                @php
                                    $bannerImage = str_starts_with((string) $banner->image, 'http')
                                        ? $banner->image
                                        : asset($banner->image);
                                @endphp
                                <img src="{{ $bannerImage }}" alt="Banner" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='{{ asset('images/placeholders/product-placeholder.svg') }}'">
                            @else
                                <img src="{{ asset('images/placeholders/product-placeholder.svg') }}" alt="Banner placeholder" class="w-full h-full object-cover">
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $banner->title }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5">
                                {{ $banner->description ?? 'No description' }}
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300">
                            {{ $banner->position }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($banner->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick='editBanner(@json($banner))' class="p-2 text-slate-400 hover:text-primary transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                                <span class="material-icons-round text-lg">edit</span>
                            </button>
                            <form action="{{ route('admin.banner.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this banner?')">
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
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                        <span class="material-icons-round text-4xl mb-2">view_carousel</span>
                        <p>No banners found. Start by adding one.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add -->
<div id="addBannerModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('addBannerModal')"></div>
        <div class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700">
            <form action="{{ route('admin.banner.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-icons-round text-primary">add_photo_alternate</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-white" id="modal-title">Add New Banner</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Title</label>
                                    <input type="text" name="title" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white focus:ring-primary focus:border-primary sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                                    <textarea name="description" rows="2" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white focus:ring-primary focus:border-primary sm:text-sm"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Image</label>
                                    <input type="file" name="image" required class="block w-full text-sm text-slate-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-primary/10 file:text-primary
                                      hover:file:bg-primary/20
                                    "/>
                                </div>
                                <div class="flex gap-4">
                                    <div class="w-1/3">
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Position</label>
                                        <input type="number" name="position" value="0" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white focus:ring-primary focus:border-primary sm:text-sm">
                                    </div>
                                    <div class="flex items-center mt-6">
                                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 text-primary focus:ring-primary border-slate-300 rounded">
                                        <label for="is_active" class="ml-2 block text-sm text-slate-900 dark:text-slate-300">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100 dark:border-slate-700">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Create Banner
                    </button>
                    <button type="button" onclick="closeModal('addBannerModal')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-700 text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="editBannerModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('editBannerModal')"></div>
        <div class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700">
            <form action="#" method="POST" enctype="multipart/form-data" id="editBannerForm">
                @csrf
                @method('PUT')
                <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-icons-round text-primary">edit</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-white">Edit Banner</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Title</label>
                                    <input type="text" name="title" id="edit_title" required class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white focus:ring-primary focus:border-primary sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                                    <textarea name="description" id="edit_description" rows="2" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white focus:ring-primary focus:border-primary sm:text-sm"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Image (Optional)</label>
                                    <input type="file" name="image" class="block w-full text-sm text-slate-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-primary/10 file:text-primary
                                      hover:file:bg-primary/20
                                    "/>
                                    <p class="text-xs text-slate-400 mt-1">Leave empty to keep current image.</p>
                                </div>
                                <div class="flex gap-4">
                                    <div class="w-1/3">
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Position</label>
                                        <input type="number" name="position" id="edit_position" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white focus:ring-primary focus:border-primary sm:text-sm">
                                    </div>
                                    <div class="flex items-center mt-6">
                                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="h-4 w-4 text-primary focus:ring-primary border-slate-300 rounded">
                                        <label for="edit_is_active" class="ml-2 block text-sm text-slate-900 dark:text-slate-300">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100 dark:border-slate-700">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Update Banner
                    </button>
                    <button type="button" onclick="closeModal('editBannerModal')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-700 text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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

    function editBanner(banner) {
        const form = document.getElementById('editBannerForm');
        form.action = `/admin/banner/${banner.id}`;
        
        document.getElementById('edit_title').value = banner.title;
        document.getElementById('edit_description').value = banner.description;
        document.getElementById('edit_position').value = banner.position;
        document.getElementById('edit_is_active').checked = banner.is_active ? true : false;
        
        openModal('editBannerModal');
    }
</script>
@endsection
