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

<!-- Data Cards (Grid instead of Table for better visual) -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
    @forelse($banners as $banner)
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all overflow-hidden group">
        <div class="aspect-video relative overflow-hidden bg-slate-100 dark:bg-slate-800">
            @if($banner->image)
                @php $bannerImg = str_starts_with((string)$banner->image, 'http') ? $banner->image : asset($banner->image); @endphp
                <img src="{{ $bannerImg }}" alt="Banner" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.src='https://images.unsplash.com/photo-1505751172107-160fa86f2648?auto=format&fit=crop&q=80&w=800'">
            @else
                <div class="w-full h-full flex items-center justify-center text-slate-300">
                    <span class="material-icons-round text-6xl">leak_add</span>
                </div>
            @endif
            
            <div class="absolute top-4 right-4">
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $banner->is_active ? 'bg-emerald-500 text-white' : 'bg-slate-500 text-white' }} shadow-lg">
                    {{ $banner->is_active ? 'Live' : 'Hidden' }}
                </span>
            </div>
            
            <div class="absolute bottom-4 left-4">
                <span class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs font-bold border border-white/30">
                    #{{ $banner->position }}
                </span>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex justify-between items-start gap-4 mb-4">
                <div>
                    <h4 class="text-xl font-extrabold text-slate-800 dark:text-white tracking-tight">{{ $banner->title }}</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $banner->description ?? 'No description provided.' }}</p>
                </div>
                <div class="flex gap-1">
                    <button onclick='editBanner(@json($banner))' class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-primary transition-colors border border-slate-200 dark:border-slate-700">
                        <span class="material-icons-round text-lg">edit</span>
                    </button>
                    <form action="{{ route('admin.banner.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Delete banner?')">
                        @csrf @method('DELETE')
                        <button class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-red-500 transition-colors border border-slate-200 dark:border-slate-700">
                            <span class="material-icons-round text-lg">delete</span>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="flex items-center gap-4 pt-4 border-t border-slate-50 dark:border-slate-800">
                <div class="flex -space-x-2">
                    @for($i=0; $i<3; $i++)
                        <div class="w-6 h-6 rounded-full border-2 border-white dark:border-slate-900 bg-slate-200 dark:bg-slate-700"></div>
                    @endfor
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Broadcast Visibility Active</p>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-20 text-center bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-200 dark:border-slate-800">
        <span class="material-icons-round text-6xl text-slate-200 mb-4">broken_image</span>
        <p class="text-slate-400 font-medium">No banners designed yet. Click "Add New Banner" to start.</p>
    </div>
    @endforelse
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
