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

    <!-- AI Analysis Section -->
    <div class="mt-8 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
        <div class="flex items-center space-x-3 mb-6">
            <div class="p-2 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg">
                <span class="material-icons-round text-white text-xl">psychology</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">AI-Powered Analysis</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">Let AI analyze nutrition and halal status based on ingredients</p>
            </div>
        </div>

        <form action="{{ route('admin.street-foods.analyze', $streetFood->id) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Ingredients (comma-separated)</label>
                    <input type="text" name="ingredients" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g. nasi, ayam, telur, bawang, minyak goreng" value="{{ $streetFood->common_ingredients ? implode(', ', $streetFood->common_ingredients) : '' }}">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Additional Description (optional)</label>
                    <input type="text" name="description" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g. digoreng dengan minyak kelapa" value="{{ $streetFood->description }}">
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg text-sm font-bold hover:from-blue-600 hover:to-purple-700 transition-all shadow-md shadow-blue-500/20 flex items-center space-x-2">
                    <span class="material-icons-round text-sm">auto_awesome</span>
                    <span>Analyze with AI</span>
                </button>
            </div>
        </form>

        @if($streetFood->halal_notes || $streetFood->health_notes)
        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
            <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Current AI Analysis Results:</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($streetFood->halal_notes)
                <div class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="material-icons-round text-green-600 text-sm">check_circle</span>
                        <span class="text-sm font-bold text-green-800 dark:text-green-400">Halal Analysis</span>
                    </div>
                    <p class="text-xs text-green-700 dark:text-green-300">{{ $streetFood->halal_notes }}</p>
                </div>
                @endif

                @if($streetFood->health_notes)
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="material-icons-round text-blue-600 text-sm">health_and_safety</span>
                        <span class="text-sm font-bold text-blue-800 dark:text-blue-400">Health Notes</span>
                    </div>
                    <p class="text-xs text-blue-700 dark:text-blue-300">{{ $streetFood->health_notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
