@extends('admin.layouts.admin_layout')

@section('title', 'Add Product - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="text-slate-400">Products</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Add New</span>
@endsection

@section('content')
<!-- Page Title -->
<div class="flex items-center justify-between mb-8">
    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.product.index') }}" class="flex items-center space-x-2 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
            <span class="material-icons-round text-lg">arrow_back</span>
            <span class="text-sm font-medium">Back to Products</span>
        </a>
        <a href="{{ route('admin.product.ocr') }}" class="flex items-center space-x-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all shadow-sm">
            <span class="material-icons-round text-lg">photo_camera</span>
            <span class="text-sm font-bold">Smart Fill (OCR)</span>
        </a>
    </div>
</div>

<!-- OCR Scanner Modal -->
<div id="scannerModal" class="fixed inset-0 z-[60] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/75 transition-opacity" aria-hidden="true" onclick="closeScanner()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
            <div class="bg-white dark:bg-slate-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="w-full">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="modal-title">Scan Product Packaging</h3>
                            <button onclick="closeScanner()" class="text-slate-400 hover:text-slate-500">
                                <span class="material-icons-round">close</span>
                            </button>
                        </div>
                        
                        <div id="scannerView" class="relative bg-slate-100 dark:bg-slate-800 rounded-xl overflow-hidden aspect-video flex items-center justify-center">
                            <video id="video" class="w-full h-full object-cover" autoplay playsinline></video>
                            <canvas id="canvas" class="hidden"></canvas>
                            <div id="focusBracket" class="absolute inset-0 border-2 border-emerald-500/50 m-12 rounded-lg pointer-events-none"></div>
                        </div>

                        <div class="mt-4 flex items-center justify-center space-x-4">
                            <button type="button" onclick="capturePhoto('front')" class="flex flex-col items-center p-3 rounded-xl border-2 border-slate-100 dark:border-slate-800 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all group">
                                <span class="material-icons-round text-emerald-500 mb-1">front_loader</span>
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Front View</span>
                            </button>
                            <button type="button" onclick="capturePhoto('back')" class="flex flex-col items-center p-3 rounded-xl border-2 border-slate-100 dark:border-slate-800 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all group">
                                <span class="material-icons-round text-emerald-500 mb-1">back_loader</span>
                                <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Back View</span>
                            </button>
                        </div>

                        <div id="ocrStatus" class="mt-4 p-4 rounded-lg bg-slate-50 dark:bg-slate-800 hidden">
                            <div class="flex items-center space-x-3">
                                <div class="animate-spin rounded-full h-4 w-4 border-2 border-emerald-500 border-t-transparent"></div>
                                <p class="text-sm text-slate-600 dark:text-slate-300">Analyzing packaging text...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Card -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Product Information</h3>
            <p class="text-sm text-slate-500 mt-1">Enter the basic details of the product.</p>
        </div>
        
        <div class="p-6 space-y-6">
            <!-- Product Image -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Product Image</label>
                <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-6 text-center hover:border-primary transition-colors">
                    <input type="file" name="image" accept="image/*" class="hidden" id="imageUpload" onchange="previewImage(event)">
                    <label for="imageUpload" class="cursor-pointer">
                        <div id="imagePreview" class="mb-4">
                            <span class="material-icons-round text-4xl text-slate-400">image</span>
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Click to upload product image</p>
                        <p class="text-xs text-slate-400 mt-1">JPEG, PNG, JPG, GIF up to 5MB</p>
                    </label>
                </div>
            </div>
            
            <!-- Product Name -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Product Name <span class="text-red-500">*</span></label>
                <input type="text" name="nama_product" value="{{ old('nama_product') }}" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent @error('nama_product') border-red-500 @enderror" placeholder="Enter product name">
                @error('nama_product')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Barcode -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Barcode <span class="text-red-500">*</span></label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent @error('barcode') border-red-500 @enderror" placeholder="e.g., 8992388116014">
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
                        <option value="{{ $category->id_kategori }}" {{ old('kategori_id') == $category->id_kategori ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Halal Status -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Halal Status <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-4">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="status" value="halal" {{ old('status') == 'halal' ? 'checked' : '' }} class="peer sr-only" required>
                        <div class="p-4 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 transition-all">
                            <span class="material-icons-round text-emerald-500 text-2xl mb-2">verified</span>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Halal</p>
                            <p class="text-xs text-slate-400">Certified halal</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="status" value="syubhat" {{ old('status') == 'syubhat' ? 'checked' : '' }} class="peer sr-only">
                        <div class="p-4 border-2 border-slate-200 dark:border-slate-700 rounded-xl text-center peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 transition-all">
                            <span class="material-icons-round text-amber-500 text-2xl mb-2">help</span>
                            <p class="text-sm font-bold text-slate-800 dark:text-white">Syubhat</p>
                            <p class="text-xs text-slate-400">Needs verification</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="status" value="tidak halal" {{ old('status') == 'tidak halal' ? 'checked' : '' }} class="peer sr-only">
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
            
            <!-- Composition -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Composition / Ingredients</label>
                <textarea name="komposisi" rows="4" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="List the ingredients or composition of the product">{{ old('komposisi') }}</textarea>
            </div>
            
            <!-- Nutrition Info -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nutrition Information</label>
                <textarea name="info_gizi" rows="4" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Enter nutrition facts (optional)">{{ old('info_gizi') }}</textarea>
            </div>
        </div>
        
        <div class="p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.product.index') }}" class="px-6 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-all text-sm font-medium">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all text-sm font-bold flex items-center space-x-2">
                <span class="material-icons-round text-lg">add</span>
                <span>Add Product</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let stream = null;
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const scannerModal = document.getElementById('scannerModal');
    const ocrStatus = document.getElementById('ocrStatus');

    async function openScanner() {
        scannerModal.classList.remove('hidden');
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment' } 
            });
            video.srcObject = stream;
        } catch (err) {
            console.error("Error accessing camera:", err);
            alert("Could not access camera. Please ensure you have given permission.");
            closeScanner();
        }
    }

    function closeScanner() {
        scannerModal.classList.add('hidden');
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }

    async function capturePhoto(step) {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = canvas.toDataURL('image/jpeg');
        
        // Show status
        ocrStatus.classList.remove('hidden');
        
        try {
            const formData = new FormData();
            formData.append('image', dataURItoBlob(imageData), 'capture.jpg');
            formData.append('step', step);
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch('{{ route("admin.ocr.upload_web") }}', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                // Auto-fill fields if data found
                if (result.data && result.data.extracted_text) {
                    const text = result.data.extracted_text;
                    
                    // Basic heuristic for product name if not already filled
                    if (!document.querySelector('input[name="nama_product"]').value) {
                        const lines = text.split('\n');
                        if (lines.length > 0) {
                            document.querySelector('input[name="nama_product"]').value = lines[0].trim();
                        }
                    }

                    // Fill ingredients
                    if (result.data.ingredients && result.data.ingredients.length > 0) {
                        const ingredientsText = result.data.ingredients.join(', ');
                        const currentKomposisi = document.querySelector('textarea[name="komposisi"]').value;
                        document.querySelector('textarea[name="komposisi"]').value = currentKomposisi ? currentKomposisi + '\n' + ingredientsText : ingredientsText;
                    }

                    // Fill nutrition if found
                    if (text.toLowerCase().includes('nutrition') || text.toLowerCase().includes('gizi')) {
                        const currentGizi = document.querySelector('textarea[name="info_gizi"]').value;
                        document.querySelector('textarea[name="info_gizi"]').value = currentGizi ? currentGizi + '\n' + text : text;
                    }

                    alert('Data successfully extracted!');
                } else {
                    alert('Text detected but could not extract specific product details.');
                }
            } else {
                alert('OCR failed: ' + result.message);
            }
        } catch (err) {
            console.error("OCR Error:", err);
            alert('Failed to process image');
        } finally {
            ocrStatus.classList.add('hidden');
            if (step === 'back') {
                setTimeout(closeScanner, 1000);
            }
        }
    }

    function dataURItoBlob(dataURI) {
        const byteString = atob(dataURI.split(',')[1]);
        const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
        const ab = new ArrayBuffer(byteString.length);
        const ia = new Uint8Array(ab);
        for (let i = 0; i < byteString.length; i++) {
            ia[i] = byteString.charCodeAt(i);
        }
        return new Blob([ab], {type: mimeString});
    }

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
