@extends('admin.master')

@section('title', 'Refine Asset - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Catalog</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<a href="{{ route('admin.product.index') }}" class="text-slate-400 hover:text-primary transition-colors">Product Hub</a>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Refine Asset</span>
@endsection

@section('content')
<div class="max-w-5xl">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Refine Asset</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Update verified product data and AI analysis insights.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.product.index') }}" class="h-12 px-6 flex items-center gap-2 rounded-2xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all font-bold text-sm">
                <span class="material-icons-round text-lg">arrow_back</span>
                BACK
            </a>
            <form action="{{ route('admin.product.destroy', $type === 'medicine' ? $product->id_medicine : $product->id_product) }}" method="POST" onsubmit="return confirm('Archive this asset permanently?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="h-12 px-6 flex items-center gap-2 rounded-2xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 hover:bg-rose-100 dark:hover:bg-rose-900/40 transition-all font-bold text-sm">
                    <span class="material-icons-round text-lg">delete_outline</span>
                    DELETE
                </button>
            </form>
        </div>
    </div>

    <form action="{{ route('admin.product.update', $type === 'medicine' ? $product->id_medicine : $product->id_product) }}" method="POST" enctype="multipart/form-data" class="space-y-8 pb-12">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Visuals & AI -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Visual Identity -->
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 h-32 w-32 bg-primary/5 blur-3xl rounded-full"></div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Visual Identity</label>
                        <span class="text-[9px] font-bold text-primary uppercase tracking-tighter">{{ $product->source ?? 'local' }} source</span>
                    </div>

                    @if(isset($imageData))
                    <div class="space-y-4 mb-6">
                        @if($imageData['source'] === 'placeholder')
                            <div class="p-3 rounded-2xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 border border-amber-100 dark:border-amber-900/30 flex items-center gap-2 text-[10px] font-bold uppercase tracking-tight">
                                <span class="material-icons-round text-base">image_not_supported</span>
                                Using generic placeholder
                            </div>
                        @elseif($imageData['source'] === 'unsplash')
                            <div class="p-3 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 border border-blue-100 dark:border-blue-900/30 flex items-center gap-2 text-[10px] font-bold uppercase tracking-tight">
                                <span class="material-icons-round text-base">image_search</span>
                                Illustrative visual (Unsplash)
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-3">
                            @foreach(array_slice($imageData['images'], 0, 2) as $image)
                            <div class="aspect-square rounded-2xl overflow-hidden border-2 border-slate-50 dark:border-slate-800 shadow-sm relative group/thumb">
                                <img src="{{ $image['url'] }}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='{{ $product->image_fallback_url }}'">
                                @if($image['type'] !== 'fallback' && $image['type'] !== 'existing_image' && $image['type'] !== 'local_match')
                                <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover/thumb:opacity-100 transition-opacity flex flex-col items-center justify-center p-2">
                                    <button type="button" onclick="document.getElementById('currentPreviewImg').src='{{ $image['url'] }}'; document.getElementById('imageUrlInput').value='{{ $image['url'] }}'; document.getElementById('imageUpload').value='';" class="bg-primary hover:bg-primary-dark text-white text-[9px] font-bold py-1.5 px-3 rounded-lg w-full mb-1">
                                        Use Image
                                    </button>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <div class="relative group mt-4">
                        <input type="hidden" name="image_url" id="imageUrlInput" value="">
                        <input type="file" name="image" accept="image/*" class="hidden" id="imageUpload" onchange="previewImage(event); document.getElementById('imageUrlInput').value='';">
                        <label for="imageUpload" class="block cursor-pointer">
                            <div id="imagePreview" class="aspect-square rounded-[2rem] bg-slate-50 dark:bg-slate-800 border-2 border-dashed border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center overflow-hidden group-hover:border-primary transition-all relative">
                                <img src="{{ $product->image ?: '/images/default/general.svg' }}" id="currentPreviewImg" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='{{ $product->image_fallback_url }}'">
                                <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="material-icons-round text-white text-3xl mb-2">cloud_upload</span>
                                    <span class="text-[10px] font-bold text-white uppercase tracking-widest">Update Photo</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    <p class="mt-4 text-[9px] font-medium text-slate-400 text-center leading-relaxed">
                        ID: {{ $type === 'medicine' ? $product->id_medicine : $product->id_product }} • Last modified: {{ $product->updated_at->format('d M Y') }}
                    </p>
                </div>

                <!-- AI Insights -->
                @if($product->halal_analysis)
                <div class="bg-primary/5 rounded-[2.5rem] p-8 border border-primary/10 relative overflow-hidden">
                    <div class="absolute -right-8 -bottom-8 h-32 w-32 bg-primary/10 blur-3xl rounded-full"></div>
                    
                    <div class="flex items-center gap-2 mb-6">
                        <div class="h-8 w-8 rounded-xl bg-primary text-white flex items-center justify-center shadow-lg shadow-primary/20">
                            <span class="material-icons-round text-sm">auto_awesome</span>
                        </div>
                        <h3 class="text-[10px] font-bold text-primary uppercase tracking-widest">AI Intelligence</h3>
                    </div>

                    <div class="space-y-6 relative z-10">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 bg-white/80 dark:bg-slate-900/80 rounded-lg text-[10px] font-extrabold text-primary border border-primary/10 shadow-sm uppercase tracking-tighter">
                                {{ $product->halal_analysis['status'] ?? 'Unknown' }}
                            </span>
                            <span class="px-3 py-1 {{ ($product->halal_analysis['is_potentially_halal'] ?? false) ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }} rounded-lg text-[10px] font-extrabold shadow-sm uppercase tracking-tighter">
                                {{ ($product->halal_analysis['is_potentially_halal'] ?? false) ? 'SAFE' : 'RISK FOUND' }}
                            </span>
                        </div>

                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Recommendation</p>
                            <p class="text-xs text-slate-700 dark:text-slate-300 italic leading-relaxed">
                                "{{ $product->halal_analysis['recommendation'] ?? 'No specific recommendation provided.' }}"
                            </p>
                        </div>

                        @if(!empty($product->halal_analysis['suspicious_ingredients']))
                        <div>
                            <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mb-2">Flagged Ingredients</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($product->halal_analysis['suspicious_ingredients'] as $ingredient)
                                <span class="px-2 py-0.5 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded text-[9px] font-bold border border-rose-100 dark:border-rose-800">
                                    {{ $ingredient }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Halal Status -->
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-6">Assessed Status</label>
                    <div class="space-y-3">
                        @php
                            $currentStatus = $type === 'medicine' ? $product->halal_status : $product->status;
                            $statusName = $type === 'medicine' ? 'halal_status' : 'status';
                        @endphp
                        @foreach(['halal' => ['icon' => 'verified', 'color' => 'text-emerald-500', 'bg' => 'peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20', 'border' => 'peer-checked:border-emerald-500'], 
                                 'syubhat' => ['icon' => 'help', 'color' => 'text-amber-500', 'bg' => 'peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20', 'border' => 'peer-checked:border-amber-500'], 
                                 'tidak halal' => ['icon' => 'cancel', 'color' => 'text-rose-500', 'bg' => 'peer-checked:bg-rose-50 dark:peer-checked:bg-rose-900/20', 'border' => 'peer-checked:border-rose-500'],
                                 'haram' => ['icon' => 'cancel', 'color' => 'text-rose-500', 'bg' => 'peer-checked:bg-rose-50 dark:peer-checked:bg-rose-900/20', 'border' => 'peer-checked:border-rose-500']] as $val => $cfg)
                        @if(($type === 'medicine' && in_array($val, ['halal', 'haram', 'syubhat'])) || ($type === 'general' && in_array($val, ['halal', 'tidak halal', 'syubhat'])))
                        <label class="relative block cursor-pointer group">
                            <input type="radio" name="{{ $statusName }}" value="{{ $val }}" {{ old($statusName, $currentStatus) == $val ? 'checked' : '' }} class="peer sr-only" required>
                            <div class="flex items-center gap-4 p-4 rounded-2xl border-2 border-slate-50 dark:border-slate-800 transition-all {{ $cfg['bg'] }} {{ $cfg['border'] }} group-hover:bg-slate-50 dark:group-hover:bg-slate-800/50">
                                <div class="h-10 w-10 rounded-xl bg-white dark:bg-slate-900 flex items-center justify-center shadow-sm">
                                    <span class="material-icons-round {{ $cfg['color'] }}">{{ $cfg['icon'] }}</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-tight">{{ ($val == 'tidak halal' || $val == 'haram') ? 'HARAM' : strtoupper($val) }}</p>
                                </div>
                                <div class="h-5 w-5 rounded-full border-2 border-slate-200 dark:border-slate-700 flex items-center justify-center peer-checked:border-primary transition-all">
                                    <div class="h-2.5 w-2.5 rounded-full bg-primary scale-0 peer-checked:scale-100 transition-transform"></div>
                                </div>
                            </div>
                        </label>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Column: Form Fields -->
            <div class="lg:col-span-8 space-y-8">
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3 mb-8">
                        <span class="h-8 w-1 bg-primary rounded-full"></span>
                        Asset Characteristics
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Official Name</label>
                            @if($type === 'medicine')
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary transition-all">
                                @error('name') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                            @else
                                <input type="text" name="nama_product" value="{{ old('nama_product', $product->nama_product) }}" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary transition-all">
                                @error('nama_product') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                            @endif
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">EAN/Barcode</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary transition-all">
                            @error('barcode') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Taxonomy Category</label>
                            <div class="relative">
                                <select name="kategori_id" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary appearance-none">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id_kategori }}" {{ old('kategori_id', $product->kategori_id) == $category->id_kategori ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                <span class="material-icons-round absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Registry Value (Rp)</label>
                            <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0" step="0.01" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Workflow Status</label>
                            <div class="relative">
                                <select name="verification_status" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary appearance-none">
                                    <option value="needs_review" {{ old('verification_status', $product->verification_status) == 'needs_review' ? 'selected' : '' }}>Needs Review</option>
                                    <option value="verified" {{ old('verification_status', $product->verification_status) == 'verified' ? 'selected' : '' }}>Verified Asset</option>
                                    <option value="rejected" {{ old('verification_status', $product->verification_status) == 'rejected' ? 'selected' : '' }}>Rejected / Archival</option>
                                </select>
                                <span class="material-icons-round absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">rule</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3 mb-8">
                        <span class="h-8 w-1 bg-emerald-500 rounded-full"></span>
                        Technical Details
                    </h3>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Chemical / Ingredient Composition</label>
                            <textarea name="komposisi" rows="5" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-2 focus:ring-primary transition-all leading-relaxed">{{ old('komposisi', $product->komposisi) }}</textarea>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Nutritional Values Registry</label>
                            <textarea name="info_gizi" rows="4" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-2 focus:ring-primary transition-all leading-relaxed">{{ old('info_gizi', $product->info_gizi) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4">
                    <button type="submit" class="w-full md:w-auto px-12 py-5 bg-primary text-white rounded-[1.5rem] font-extrabold text-sm hover:bg-primary-dark transition-all transform hover:-translate-y-1 shadow-2xl shadow-primary/30 flex items-center justify-center gap-3">
                        <span class="material-icons-round">publish</span>
                        COMMIT REFINEMENTS
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const previewImg = document.getElementById('currentPreviewImg');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('opacity-40');
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush

