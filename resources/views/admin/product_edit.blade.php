@extends('admin.layouts.admin_layout')

@section('title', 'Edit Product - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="text-slate-400">Products</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Edit Product</span>
@endsection

@section('content')
<!-- Page Title -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Edit Product</h2>
        <p class="text-slate-500 text-sm mt-1">Update product information in the halal verification database.</p>
    </div>
    <a href="{{ route('admin.product.index') }}" class="flex items-center space-x-2 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
        <span class="material-icons-round text-lg">arrow_back</span>
        <span class="text-sm font-medium">Back to Products</span>
    </a>
</div>

<!-- Form Card -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <form action="{{ route('admin.product.update', $product->id_product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Product Information</h3>
                    <p class="text-sm text-slate-500 mt-1">Update the details of this product.</p>
                </div>
                <div class="text-right">
                    <span class="block text-xs text-slate-400">ID: {{ $product->id_product }}</span>
                    <span class="block text-xs font-bold text-primary mt-1">Source: {{ $product->source ?? 'local' }}</span>
                </div>
            </div>
        </div>
        
        <div class="p-6 space-y-6">
            <!-- Product Image -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Product Image</label>
                <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-6 text-center hover:border-primary transition-colors">
                    <input type="file" name="image" accept="image/*" class="hidden" id="imageUpload" onchange="previewImage(event)">
                    <label for="imageUpload" class="cursor-pointer">
                        <div id="imagePreview" class="mb-4">
                            @if($product->image)
                                <img src="{{ $product->image }}" class="w-32 h-32 object-cover rounded-lg mx-auto" onerror="this.onerror=null;this.src='{{ asset('images/placeholders/product-placeholder.svg') }}'">
                            @else
                                <span class="material-icons-round text-4xl text-slate-400">image</span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Click to change product image</p>
                        <p class="text-xs text-slate-400 mt-1">JPEG, PNG, JPG, GIF up to 5MB</p>
                    </label>
                </div>
            </div>
            
            <!-- Product Name -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Product Name <span class="text-red-500">*</span></label>
                <input type="text" name="nama_product" value="{{ old('nama_product', $product->nama_product) }}" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent @error('nama_product') border-red-500 @enderror" placeholder="Enter product name">
                @error('nama_product')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Barcode -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Barcode <span class="text-red-500">*</span></label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent @error('barcode') border-red-500 @enderror" placeholder="e.g., 8992388116014">
                    @error('barcode')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Category</label>
                    <select name="kategori_id" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id_kategori }}" {{ old('kategori_id', $product->kategori_id) == $category->id_kategori ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- AI Halal Analysis (Read Only Context) -->
            @if($product->halal_analysis)
            <div class="bg-primary/10 dark:bg-primary/10 rounded-xl p-6 border border-primary/15 dark:border-primary/20 mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-icons-round text-primary">auto_awesome</span>
                    <h3 class="text-sm font-bold text-primary uppercase tracking-wider">AI Halal Analysis Insights</h3>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="px-3 py-1 bg-primary/15 text-primary rounded-full text-xs font-bold uppercase tracking-tighter">
                            Status: {{ $product->halal_analysis['status'] ?? 'Unknown' }}
                        </div>
                        <div class="px-3 py-1 {{ ($product->halal_analysis['is_potentially_halal'] ?? false) ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500' }} rounded-full text-xs font-bold uppercase tracking-tighter">
                            Confidence: {{ ($product->halal_analysis['is_potentially_halal'] ?? false) ? 'Potentially Halal' : 'Potentially Non-Halal' }}
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-primary mb-1">AI Recommendation:</p>
                        <p class="text-sm text-slate-700 dark:text-slate-100/80 italic leading-relaxed">
                            "{{ $product->halal_analysis['recommendation'] ?? 'No specific recommendation provided.' }}"
                        </p>
                    </div>
                    @if(!empty($product->halal_analysis['suspicious_ingredients']))
                    <div>
                        <p class="text-xs font-bold text-rose-600 dark:text-rose-400 mb-1">Suspicious Ingredients Flagged:</p>
                        <ul class="flex flex-wrap gap-2">
                            @foreach($product->halal_analysis['suspicious_ingredients'] as $ingredient)
                            <li class="px-2 py-0.5 bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 rounded text-[10px] font-bold">{{ $ingredient }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Halal Status -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Halal Status <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-4">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="status" value="halal" {{ old('status', $product->status) == 'halal' ? 'checked' : '' }} class="peer sr-only" required>
                        <div class="p-4 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 transition-all">
                            <span class="material-icons-round text-emerald-500 text-2xl mb-2">verified</span>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Halal</p>
                            <p class="text-xs text-slate-400">Certified halal</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="status" value="syubhat" {{ old('status', $product->status) == 'syubhat' ? 'checked' : '' }} class="peer sr-only">
                        <div class="p-4 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-center peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 transition-all">
                            <span class="material-icons-round text-amber-500 text-2xl mb-2">help</span>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Syubhat</p>
                            <p class="text-xs text-slate-400">Needs verification</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="status" value="tidak halal" {{ old('status', $product->status) == 'tidak halal' ? 'checked' : '' }} class="peer sr-only">
                        <div class="p-4 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-center peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 transition-all">
                            <span class="material-icons-round text-red-500 text-2xl mb-2">cancel</span>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Haram</p>
                            <p class="text-xs text-slate-400">Not halal</p>
                        </div>
                    </label>
                </div>
                @error('status')
                <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Verification Status -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Verification Status</label>
                <select name="verification_status" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="needs_review" {{ old('verification_status', $product->verification_status) == 'needs_review' ? 'selected' : '' }}>Needs Review</option>
                    <option value="verified" {{ old('verification_status', $product->verification_status) == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="rejected" {{ old('verification_status', $product->verification_status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            
            <!-- Composition -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Composition / Ingredients</label>
                <textarea name="komposisi" rows="4" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="List the ingredients or composition of the product">{{ old('komposisi', $product->komposisi) }}</textarea>
            </div>
            
            <!-- Nutrition Info -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nutrition Information</label>
                <textarea name="info_gizi" rows="4" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Enter nutrition facts (optional)">{{ old('info_gizi', $product->info_gizi) }}</textarea>
            </div>
        </div>
        
        <div class="p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-end">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.product.index') }}" class="px-6 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-all text-sm font-medium">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all text-sm font-bold flex items-center space-x-2">
                    <span class="material-icons-round text-lg">save</span>
                    <span>Save Changes</span>
                </button>
            </div>
        </div>
    </form>
    
    <!-- Delete Form (Outside main form to prevent conflicts) -->
    <div class="p-6 pt-0 border-t-0 bg-slate-50 dark:bg-slate-800/50">
        <form action="{{ route('admin.product.destroy', $product->id_product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all text-sm font-medium flex items-center space-x-1">
                <span class="material-icons-round text-lg">delete</span>
                <span>Delete Product</span>
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="w-32 h-32 object-cover rounded-lg mx-auto">`;
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
