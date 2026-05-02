@extends('admin.master')

@section('title', 'Register Asset - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Catalog</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<a href="{{ route('admin.product.index') }}" class="text-slate-400 hover:text-primary transition-colors">Product Hub</a>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Register Asset</span>
@endsection

@section('content')
<div class="max-w-5xl">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Register New Asset</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Add a new verified product to the internal catalog registry.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.product.index') }}" class="h-12 px-6 flex items-center gap-2 rounded-2xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all font-bold text-sm">
                <span class="material-icons-round text-lg">arrow_back</span>
                BACK
            </a>
            <button type="button" onclick="openScanner()" class="h-12 px-6 flex items-center gap-2 rounded-2xl bg-emerald-600 text-white hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/20 font-bold text-sm">
                <span class="material-icons-round text-lg">document_scanner</span>
                SMART FILL
            </button>
        </div>
    </div>

    <!-- Scanner Modal (Glassmorphic) -->
    <div id="scannerModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
        <div class="w-full max-w-2xl rounded-[2.5rem] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden relative">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Vision Scanner</h3>
                    <button onclick="closeScanner()" class="h-10 w-10 rounded-full flex items-center justify-center bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-slate-600">
                        <span class="material-icons-round">close</span>
                    </button>
                </div>
                
                <div class="relative bg-slate-900 rounded-[2rem] overflow-hidden aspect-video shadow-inner">
                    <video id="video" class="w-full h-full object-cover opacity-80" autoplay playsinline></video>
                    <canvas id="canvas" class="hidden"></canvas>
                    <div class="absolute inset-0 border-2 border-emerald-500/30 m-8 rounded-3xl pointer-events-none"></div>
                    <!-- Scanning line animation -->
                    <div class="absolute inset-x-8 top-8 h-0.5 bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.8)] animate-scan-line"></div>
                </div>

                <div class="mt-8 flex items-center justify-center gap-4">
                    <button type="button" onclick="capturePhoto('front')" class="flex-1 flex flex-col items-center gap-3 p-5 rounded-3xl border-2 border-slate-50 dark:border-slate-800 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all group">
                        <div class="h-12 w-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-icons-round">flip_to_front</span>
                        </div>
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Front Packaging</span>
                    </button>
                    <button type="button" onclick="capturePhoto('back')" class="flex-1 flex flex-col items-center gap-3 p-5 rounded-3xl border-2 border-slate-50 dark:border-slate-800 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all group">
                        <div class="h-12 w-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <span class="material-icons-round">flip_to_back</span>
                        </div>
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Ingredients List</span>
                    </button>
                </div>

                <div id="ocrStatus" class="mt-6 p-5 rounded-3xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 hidden">
                    <div class="flex items-center gap-4">
                        <div class="relative h-6 w-6">
                            <div class="absolute inset-0 rounded-full border-2 border-emerald-500/20"></div>
                            <div class="absolute inset-0 rounded-full border-2 border-emerald-500 border-t-transparent animate-spin"></div>
                        </div>
                        <p class="text-sm font-bold text-slate-600 dark:text-slate-300 tracking-tight">AI Vision is analyzing the packaging text...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Registration Form -->
    <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 pb-12">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Media & Primary Info -->
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 h-32 w-32 bg-primary/5 blur-3xl rounded-full"></div>
                    
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-6">Visual Identity</label>
                    <div class="relative group">
                        <input type="file" name="image" accept="image/*" class="hidden" id="imageUpload" onchange="previewImage(event)">
                        <label for="imageUpload" class="block cursor-pointer">
                            <div id="imagePreview" class="aspect-square rounded-[2rem] bg-slate-50 dark:bg-slate-800 border-2 border-dashed border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center overflow-hidden group-hover:border-primary transition-all relative">
                                <img src="/images/default/general.svg" id="currentPreviewImg" class="w-full h-full object-cover opacity-40 group-hover:opacity-100 transition-opacity" alt="Placeholder">
                                <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/0 group-hover:bg-slate-900/40 transition-all opacity-0 group-hover:opacity-100">
                                    <span class="material-icons-round text-white text-3xl mb-2">add_a_photo</span>
                                    <span class="text-[10px] font-bold text-white uppercase tracking-widest">Change Visual</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    <p class="mt-4 text-[9px] font-medium text-slate-400 text-center leading-relaxed">
                        JPG, PNG or WEBP. Max 5MB. Visuals will be synced automatically if left blank.
                    </p>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-6">Halal Certification Status</label>
                    <div class="space-y-3">
                        @foreach(['halal' => ['icon' => 'verified', 'color' => 'text-emerald-500', 'bg' => 'peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20', 'border' => 'peer-checked:border-emerald-500'], 
                                 'syubhat' => ['icon' => 'help', 'color' => 'text-amber-500', 'bg' => 'peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20', 'border' => 'peer-checked:border-amber-500'], 
                                 'tidak halal' => ['icon' => 'cancel', 'color' => 'text-rose-500', 'bg' => 'peer-checked:bg-rose-50 dark:peer-checked:bg-rose-900/20', 'border' => 'peer-checked:border-rose-500']] as $val => $cfg)
                        <label class="relative block cursor-pointer group">
                            <input type="radio" name="status" value="{{ $val }}" {{ old('status') == $val ? 'checked' : '' }} class="peer sr-only" required>
                            <div class="flex items-center gap-4 p-4 rounded-2xl border-2 border-slate-50 dark:border-slate-800 transition-all {{ $cfg['bg'] }} {{ $cfg['border'] }} group-hover:bg-slate-50 dark:group-hover:bg-slate-800/50">
                                <div class="h-10 w-10 rounded-xl bg-white dark:bg-slate-900 flex items-center justify-center shadow-sm">
                                    <span class="material-icons-round {{ $cfg['color'] }}">{{ $cfg['icon'] }}</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-tight">{{ $val == 'tidak halal' ? 'HARAM' : strtoupper($val) }}</p>
                                    <p class="text-[10px] font-medium text-slate-400">{{ $val == 'halal' ? 'Certified by authorities' : ($val == 'syubhat' ? 'Inconclusive / Mixed' : 'Contains non-halal elements') }}</p>
                                </div>
                                <div class="h-5 w-5 rounded-full border-2 border-slate-200 dark:border-slate-700 flex items-center justify-center peer-checked:border-primary transition-all">
                                    <div class="h-2.5 w-2.5 rounded-full bg-primary scale-0 peer-checked:scale-100 transition-transform"></div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Column: Form Fields -->
            <div class="lg:col-span-8 space-y-8">
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3 mb-8">
                        <span class="h-8 w-1 bg-primary rounded-full"></span>
                        General Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2 space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Product Designation</label>
                            <input type="text" name="nama_product" value="{{ old('nama_product') }}" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary transition-all" placeholder="e.g. Ultra Milk Full Cream 250ml">
                            @error('nama_product') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Universal Barcode (EAN/UPC)</label>
                            <input type="text" name="barcode" value="{{ old('barcode') }}" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary transition-all" placeholder="8992388116014">
                            @error('barcode') <p class="text-[10px] text-rose-500 font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Market Classification</label>
                            <div class="relative">
                                <select name="kategori_id" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary appearance-none">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id_kategori }}" {{ old('kategori_id') == $category->id_kategori ? 'selected' : '' }}>{{ $category->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                <span class="material-icons-round absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Base Price (Demo)</label>
                            <div class="relative">
                                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">Rp</span>
                                <input type="number" name="price" value="{{ old('price') }}" min="0" step="0.01" class="w-full pl-12 pr-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm font-bold focus:ring-2 focus:ring-primary transition-all" placeholder="12.000">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3 mb-8">
                        <span class="h-8 w-1 bg-emerald-500 rounded-full"></span>
                        Ingredient & Nutritional Analysis
                    </h3>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Full Composition</label>
                            <textarea name="komposisi" rows="4" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-2 focus:ring-primary transition-all leading-relaxed" placeholder="List all ingredients found on the packaging...">{{ old('komposisi') }}</textarea>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Nutritional Values</label>
                            <textarea name="info_gizi" rows="4" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-2 focus:ring-primary transition-all leading-relaxed" placeholder="Energy, Fat, Protein, Carbohydrates, etc...">{{ old('info_gizi') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4">
                    <button type="submit" class="w-full md:w-auto px-12 py-5 bg-primary text-white rounded-[1.5rem] font-extrabold text-sm hover:bg-primary-dark transition-all transform hover:-translate-y-1 shadow-2xl shadow-primary/30 flex items-center justify-center gap-3">
                        <span class="material-icons-round">save</span>
                        SAVE PRODUCT TO REGISTRY
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<style>
@keyframes scan-line {
    0% { top: 32px; }
    100% { top: calc(100% - 32px); }
}
.animate-scan-line {
    animation: scan-line 2.5s linear infinite;
}
</style>
<script>
    let stream = null;
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const scannerModal = document.getElementById('scannerModal');
    const ocrStatus = document.getElementById('ocrStatus');

    async function openScanner() {
        scannerModal.classList.remove('hidden');
        scannerModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
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
        scannerModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
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
                if (result.data && result.data.extracted_text) {
                    const text = result.data.extracted_text;
                    
                    if (!document.querySelector('input[name="nama_product"]').value) {
                        const lines = text.split('\n');
                        if (lines.length > 0) {
                            document.querySelector('input[name="nama_product"]').value = lines[0].trim();
                        }
                    }

                    if (result.data.ingredients && result.data.ingredients.length > 0) {
                        const ingredientsText = result.data.ingredients.join(', ');
                        const currentKomposisi = document.querySelector('textarea[name="komposisi"]').value;
                        document.querySelector('textarea[name="komposisi"]').value = currentKomposisi ? currentKomposisi + '\n' + ingredientsText : ingredientsText;
                    }

                    if (text.toLowerCase().includes('nutrition') || text.toLowerCase().includes('gizi')) {
                        const currentGizi = document.querySelector('textarea[name="info_gizi"]').value;
                        document.querySelector('textarea[name="info_gizi"]').value = currentGizi ? currentGizi + '\n' + text : text;
                    }

                    // Toast/Alert
                    alert('Data successfully extracted!');
                }
            }
        } catch (err) {
            console.error("OCR Error:", err);
        } finally {
            ocrStatus.classList.add('hidden');
            if (step === 'back') {
                setTimeout(closeScanner, 500);
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
