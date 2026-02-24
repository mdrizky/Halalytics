@extends('admin.layouts.admin_layout')

@section('title', 'OCR Scanner - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="text-slate-400">Products</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">OCR Scanner</span>
@endsection

@section('content')
<!-- Page Title -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Smart OCR Scanner</h2>
        <p class="text-slate-500 text-sm mt-1">Use advanced OCR technology to automatically extract product information from packaging images.</p>
    </div>
    <a href="{{ route('admin.product.create') }}" class="flex items-center space-x-2 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
        <span class="material-icons-round text-lg">edit</span>
        <span class="text-sm font-medium">Manual Input</span>
    </a>
</div>

<!-- OCR Scanner Interface -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 dark:border-slate-800">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Live Scanner</h3>
                <p class="text-sm text-slate-500 mt-1">Position the product packaging within the frame for best results.</p>
            </div>
            <div class="flex items-center space-x-2">
                <span id="cameraStatus" class="flex items-center text-xs text-amber-500">
                    <span class="w-2 h-2 bg-amber-500 rounded-full mr-1 animate-pulse"></span>
                    Camera Off
                </span>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <!-- Scanner View -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Camera View -->
            <div>
                <div class="relative bg-slate-100 dark:bg-slate-800 rounded-xl overflow-hidden aspect-video flex items-center justify-center">
                    <video id="video" class="w-full h-full object-cover" autoplay playsinline></video>
                    <canvas id="canvas" class="hidden"></canvas>
                    <div id="focusBracket" class="absolute inset-0 border-2 border-emerald-500/50 m-8 rounded-lg pointer-events-none"></div>
                    <div id="cameraPlaceholder" class="absolute inset-0 flex items-center justify-center bg-slate-100 dark:bg-slate-800">
                        <div class="text-center">
                            <span class="material-icons-round text-6xl text-slate-400 mb-4">photo_camera</span>
                            <p class="text-slate-500">Click "Start Camera" to begin scanning</p>
                        </div>
                    </div>
                </div>
                
                <!-- Camera Controls -->
                <div class="mt-4 flex items-center justify-center space-x-4">
                    <button type="button" id="startCameraBtn" onclick="startCamera()" class="flex items-center space-x-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all">
                        <span class="material-icons-round">videocam</span>
                        <span>Start Camera</span>
                    </button>
                    <button type="button" id="stopCameraBtn" onclick="stopCamera()" class="hidden flex items-center space-x-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all">
                        <span class="material-icons-round">videocam_off</span>
                        <span>Stop Camera</span>
                    </button>
                    <button type="button" id="captureBtn" onclick="capturePhoto()" disabled class="flex items-center space-x-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="material-icons-round">camera_alt</span>
                        <span>Capture</span>
                    </button>
                </div>
            </div>
            
            <!-- OCR Results -->
            <div>
                <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-6">
                    <h4 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Extracted Information</h4>
                    
                    <div id="ocrResults" class="space-y-4">
                        <div class="text-center py-8">
                            <span class="material-icons-round text-4xl text-slate-300 mb-2">text_snippet</span>
                            <p class="text-slate-400">No text extracted yet</p>
                            <p class="text-xs text-slate-400 mt-1">Capture an image to extract product information</p>
                        </div>
                    </div>
                    
                    <div id="ocrActions" class="hidden mt-6 space-y-3">
                        <button type="button" onclick="useExtractedData()" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all">
                            <span class="material-icons-round text-sm mr-2">check</span>
                            Use This Data
                        </button>
                        <button type="button" onclick="capturePhoto()" class="w-full px-4 py-2 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                            <span class="material-icons-round text-sm mr-2">refresh</span>
                            Try Again
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Processing Status -->
        <div id="processingStatus" class="hidden mt-6 p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-amber-500 border-t-transparent"></div>
                <p class="text-sm text-amber-700 dark:text-amber-300">Processing image and extracting text...</p>
            </div>
        </div>
    </div>
</div>

<!-- Upload Alternative -->
<div class="mt-6 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
    <h4 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Or Upload Image</h4>
    <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-6 text-center hover:border-primary transition-colors">
        <input type="file" id="fileUpload" accept="image/*" class="hidden" onchange="handleFileUpload(event)">
        <label for="fileUpload" class="cursor-pointer">
            <span class="material-icons-round text-4xl text-slate-400 mb-4">upload_file</span>
            <p class="text-slate-600 dark:text-slate-400">Click to upload product image</p>
            <p class="text-xs text-slate-400 mt-1">JPEG, PNG, JPG up to 10MB</p>
        </label>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let stream = null;
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const startBtn = document.getElementById('startCameraBtn');
    const stopBtn = document.getElementById('stopCameraBtn');
    const captureBtn = document.getElementById('captureBtn');
    const statusEl = document.getElementById('cameraStatus');
    const placeholder = document.getElementById('cameraPlaceholder');
    const results = document.getElementById('ocrResults');
    const actions = document.getElementById('ocrActions');
    const processing = document.getElementById('processingStatus');
    let extractedData = {};

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'environment',
                    width: { ideal: 1920 },
                    height: { ideal: 1080 }
                } 
            });
            video.srcObject = stream;
            
            placeholder.classList.add('hidden');
            startBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
            captureBtn.disabled = false;
            
            statusEl.innerHTML = '<span class="w-2 h-2 bg-emerald-500 rounded-full mr-1 animate-pulse"></span>Camera Active';
            statusEl.className = 'flex items-center text-xs text-emerald-500';
            
        } catch (err) {
            console.error("Error accessing camera:", err);
            alert("Could not access camera. Please ensure you have given permission and try again.");
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        
        video.srcObject = null;
        placeholder.classList.remove('hidden');
        startBtn.classList.remove('hidden');
        stopBtn.classList.add('hidden');
        captureBtn.disabled = true;
        
        statusEl.innerHTML = '<span class="w-2 h-2 bg-amber-500 rounded-full mr-1"></span>Camera Off';
        statusEl.className = 'flex items-center text-xs text-amber-500';
    }

    async function capturePhoto() {
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = canvas.toDataURL('image/jpeg', 0.9);
        await processImage(imageData);
    }

    async function handleFileUpload(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = async function(e) {
                await processImage(e.target.result);
            }
            reader.readAsDataURL(file);
        }
    }

    async function processImage(imageData) {
        processing.classList.remove('hidden');
        actions.classList.add('hidden');
        
        try {
            const formData = new FormData();
            formData.append('image', dataURItoBlob(imageData), 'scan.jpg');
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch('{{ route("admin.ocr.upload_web") }}', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                extractedData = result.data || {};
                displayResults(result);
            } else {
                showError(result.message || 'OCR processing failed');
            }
        } catch (err) {
            console.error("OCR Error:", err);
            showError('Failed to process image. Please try again.');
        } finally {
            processing.classList.add('hidden');
        }
    }

    function displayResults(result) {
        let html = '<div class="space-y-4">';
        
        if (result.data && result.data.extracted_text) {
            html += `
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Extracted Text</label>
                    <div class="p-3 bg-white dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600">
                        <p class="text-sm text-slate-600 dark:text-slate-300 whitespace-pre-wrap">${result.data.extracted_text}</p>
                    </div>
                </div>
            `;
        }
        
        if (result.data && result.data.ingredients && result.data.ingredients.length > 0) {
            html += `
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Detected Ingredients</label>
                    <div class="p-3 bg-white dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600">
                        <p class="text-sm text-slate-600 dark:text-slate-300">${result.data.ingredients.join(', ')}</p>
                    </div>
                </div>
            `;
        }
        
        html += '</div>';
        results.innerHTML = html;
        actions.classList.remove('hidden');
    }

    function showError(message) {
        results.innerHTML = `
            <div class="text-center py-8">
                <span class="material-icons-round text-4xl text-red-400 mb-2">error</span>
                <p class="text-red-500">${message}</p>
            </div>
        `;
    }

    function useExtractedData() {
        // Store data in sessionStorage for use in manual form
        sessionStorage.setItem('ocrData', JSON.stringify(extractedData));
        
        // Redirect to manual input form
        window.location.href = '{{ route("admin.product.create") }}';
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

    // Check for OCR data on page load
    window.addEventListener('load', function() {
        const ocrData = sessionStorage.getItem('ocrData');
        if (ocrData) {
            const data = JSON.parse(ocrData);
            // Auto-fill form fields if we have data
            if (data.extracted_text) {
                const productNameInput = document.querySelector('input[name="nama_product"]');
                if (productNameInput && !productNameInput.value) {
                    const lines = data.extracted_text.split('\n');
                    if (lines.length > 0) {
                        productNameInput.value = lines[0].trim();
                    }
                }
            }
            
            if (data.ingredients && data.ingredients.length > 0) {
                const komposisiTextarea = document.querySelector('textarea[name="komposisi"]');
                if (komposisiTextarea) {
                    komposisiTextarea.value = data.ingredients.join(', ');
                }
            }
            
            // Clear the stored data
            sessionStorage.removeItem('ocrData');
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        stopCamera();
    });
</script>
@endpush
