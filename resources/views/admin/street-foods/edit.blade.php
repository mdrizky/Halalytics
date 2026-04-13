@extends('admin.layouts.admin_layout')

@section('title', 'Edit Street Food - Halalytics Admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center space-x-4 mb-8">
        <a href="{{ route('admin.street-foods.index') }}" class="p-2 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-primary transition-all">
            <span class="material-icons-round text-lg">arrow_back</span>
        </a>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Edit Street Food</h2>
    </div>

    <form action="{{ route('admin.street-foods.update', $streetFood->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Food Name</label>
                    <input type="text" name="name" value="{{ $streetFood->name }}" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g. Nasi Goreng">
                </div>

                <!-- Category -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Category</label>
                    <input type="text" name="category" value="{{ $streetFood->category }}" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g. Nasi & Mie">
                </div>

                <!-- Typical Calories -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Typical Calories (per serving)</label>
                    <input type="number" name="calories_typical" value="{{ $streetFood->calories_typical }}" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g. 500">
                </div>

                <!-- Halal Status -->
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Halal Status</label>
                    <select name="halal_status" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                        <option value="halal_umum" {{ $streetFood->halal_status == 'halal_umum' ? 'selected' : '' }}>Halal Umum</option>
                        <option value="tergantung_bahan" {{ $streetFood->halal_status == 'tergantung_bahan' ? 'selected' : '' }}>Tergantung Bahan</option>
                        <option value="syubhat" {{ $streetFood->halal_status == 'syubhat' ? 'selected' : '' }}>Syubhat</option>
                        <option value="haram" {{ $streetFood->halal_status == 'haram' ? 'selected' : '' }}>Haram</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 space-y-2">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Description / Ingredients</label>
                <textarea name="description" required rows="3" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Describe the food and its common ingredients...">{{ $streetFood->description }}</textarea>
            </div>

            <div class="mt-6 space-y-2">
                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Food Image</label>
                @if($streetFood->image_url)
                    <div class="mb-2">
                        <img src="{{ $streetFood->image_url }}" class="h-24 w-auto rounded-lg shadow-sm border border-slate-200" onerror="this.onerror=null;this.src='{{ asset('images/placeholders/product-placeholder.svg') }}'">
                    </div>
                @endif
                <input type="file" name="image" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                <p class="text-[10px] text-slate-500 mt-1">Leave empty to keep current image</p>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4">
            <button type="reset" class="px-6 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-bold text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">Reset Changes</button>
            <button type="submit" class="px-10 py-2 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary-dark transition-all shadow-md shadow-primary/20">Update Food</button>
        </div>
    </form>
</div>
@endsection
