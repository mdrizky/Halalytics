@extends('admin.layouts.admin_layout')

@section('title', 'QR Scanner - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<a href="{{ route('admin.blood-appointments.index') }}" class="text-slate-400 hover:text-primary">Participants</a>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">QR Scanner</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Scanner & Verification</h2>
        <p class="text-slate-500 text-sm mt-1">Scan donor QR Code and input health verification details.</p>
    </div>
    <a href="{{ route('admin.blood-appointments.index') }}" class="flex items-center space-x-2 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
        <span class="material-icons-round text-lg">arrow_back</span>
        <span class="text-sm font-medium">Back</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Scanner Side -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col h-[600px]">
        <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 dark:text-white">Camera Scanner</h3>
            <button id="cameraToggle" class="text-sm px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-full font-medium text-slate-600 dark:text-slate-300">
                Stop Camera
            </button>
        </div>
        <div class="flex-1 bg-black relative flex items-center justify-center overflow-hidden">
            <video id="video" class="w-full h-full object-cover"></video>
            <canvas id="canvas" class="hidden"></canvas>
            
            <!-- Scanning overlay -->
            <div id="scanOverlay" class="absolute inset-0 border-[6px] border-primary/50 m-12 rounded-2xl flex flex-col items-center justify-center pointer-events-none">
                <div class="w-full h-1 bg-primary animate-pulse absolute top-1/2 -translate-y-1/2"></div>
                <span class="mt-auto mb-4 bg-black/50 text-white px-3 py-1 rounded text-xs">Point QR Code Here</span>
            </div>
        </div>
        <div class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-800 text-center">
            <p id="scanStatus" class="text-sm font-medium text-slate-500">Waiting for QR Code...</p>
        </div>
    </div>

    <!-- Verification Side -->
    <div id="verificationPanel" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm hidden flex flex-col h-[600px]">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white" id="vName">-</h3>
                    <p class="text-sm text-slate-500" id="vEmail">-</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-xl font-bold text-slate-700 dark:text-white" id="vQueue">-</span>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-wider">Queue</p>
                </div>
            </div>
            
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded text-xs font-medium">Blood: <span class="font-bold text-primary" id="vBlood">-</span></span>
                <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded text-xs font-medium">Passed Pre-Screening: <span class="font-bold" id="vPassed">-</span></span>
            </div>
        </div>
        
        <div class="p-6 flex-1 overflow-y-auto">
            <form id="verifyForm">
                <input type="hidden" id="appointmentId">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Weight (kg)</label>
                        <input type="number" id="vWeight" step="0.1" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Hemoglobin (Hb)</label>
                        <input type="number" id="vHb" step="0.1" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Blood Pressure (mmHg)</label>
                        <input type="text" id="vBp" placeholder="e.g. 120/80" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Collected Volume (ml)</label>
                        <input type="number" id="vVolume" value="350" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Final Status</label>
                        <select id="vStatus" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium" required>
                            <option value="approved">Approved & Collected</option>
                            <option value="rejected">Rejected (Medical Reason)</option>
                            <option value="no_show">No Show</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Notes</label>
                        <textarea id="vNotes" rows="2" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm"></textarea>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
            <button type="button" id="btnSubmit" class="w-full py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all text-sm font-bold flex items-center justify-center space-x-2">
                <span class="material-icons-round text-lg">check_circle</span>
                <span>Submit Verification</span>
            </button>
        </div>
    </div>
    
    <!-- Placeholder when nothing scanned -->
    <div id="placeholderPanel" class="bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center h-[600px] text-center p-8">
        <span class="material-icons-round text-6xl text-slate-300 dark:text-slate-600 mb-4">document_scanner</span>
        <h3 class="text-xl font-bold text-slate-600 dark:text-slate-400">Ready to Scan</h3>
        <p class="text-slate-500 text-sm mt-2 max-w-sm">Position the participant's QR code within the camera frame to load their details.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    const video = document.getElementById("video");
    const canvasElement = document.getElementById("canvas");
    const canvas = canvasElement.getContext("2d");
    const scanStatus = document.getElementById("scanStatus");
    const cameraToggle = document.getElementById("cameraToggle");
    
    const verificationPanel = document.getElementById("verificationPanel");
    const placeholderPanel = document.getElementById("placeholderPanel");
    
    let isScanning = true;
    let stream = null;

    // Start camera
    function startCamera() {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(s) {
            stream = s;
            video.srcObject = stream;
            video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
            video.play();
            requestAnimationFrame(tick);
            cameraToggle.innerText = "Stop Camera";
            isScanning = true;
        });
    }

    // Stop camera
    function stopCamera() {
        if(stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        cameraToggle.innerText = "Start Camera";
        isScanning = false;
    }

    cameraToggle.addEventListener("click", () => {
        if(isScanning) stopCamera();
        else startCamera();
    });

    let frameCount = 0;
    function tick() {
        if (!isScanning) return;
        
        frameCount++;
        if (frameCount % 4 !== 0) {
            requestAnimationFrame(tick);
            return;
        }

        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvasElement.height = video.videoHeight;
            canvasElement.width = video.videoWidth;
            canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
            var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
            var code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "dontInvert",
            });
            
            if (code) {
                // Found QR Code
                scanStatus.innerText = "QR Code detected! Loading...";
                scanStatus.className = "text-sm font-bold text-primary";
                fetchAppointment(code.data);
                // Pause scanning briefly
                isScanning = false;
                setTimeout(() => { isScanning = true; requestAnimationFrame(tick); }, 3000);
                return;
            } else {
                scanStatus.innerText = "Waiting for QR Code...";
                scanStatus.className = "text-sm font-medium text-slate-500";
            }
        }
        requestAnimationFrame(tick);
    }

    function fetchAppointment(qrCode) {
        fetch("{{ route('admin.blood-appointments.scan-qr') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ qr_code: qrCode })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                populateForm(data.data);
            } else {
                alert(data.message);
                scanStatus.innerText = "Invalid QR Code";
                scanStatus.className = "text-sm font-bold text-red-500";
            }
        })
        .catch(err => console.error(err));
    }

    function populateForm(data) {
        placeholderPanel.classList.add('hidden');
        verificationPanel.classList.remove('hidden');
        
        document.getElementById('appointmentId').value = data.id;
        document.getElementById('vName').innerText = data.user.full_name;
        document.getElementById('vEmail').innerText = data.user.email;
        document.getElementById('vQueue').innerText = data.queue_number;
        document.getElementById('vBlood').innerText = data.user.blood_type || 'Unspecified';
        
        const passedSpan = document.getElementById('vPassed');
        if(data.screening_passed) {
            passedSpan.innerText = 'YES';
            passedSpan.className = 'font-bold text-emerald-500';
        } else {
            passedSpan.innerText = 'NO';
            passedSpan.className = 'font-bold text-red-500';
        }

        // Reset form
        document.getElementById('verifyForm').reset();
        document.getElementById('vVolume').value = 350;
    }

    // Submit form
    document.getElementById('btnSubmit').addEventListener('click', () => {
        const id = document.getElementById('appointmentId').value;
        if(!id) return;

        const payload = {
            status: document.getElementById('vStatus').value,
            weight_kg: document.getElementById('vWeight').value,
            hemoglobin: document.getElementById('vHb').value,
            blood_pressure: document.getElementById('vBp').value,
            blood_volume_ml: document.getElementById('vVolume').value,
            admin_notes: document.getElementById('vNotes').value,
        };

        fetch(`/admin/blood-appointments/${id}/verify`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert("Verification saved successfully!");
                verificationPanel.classList.add('hidden');
                placeholderPanel.classList.remove('hidden');
            } else {
                alert("Error saving data");
            }
        })
        .catch(err => console.error(err));
    });

    // Init
    startCamera();
</script>
@endsection
