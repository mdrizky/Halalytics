@extends('master')
@section('isi')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code - Halalytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        :root {
            --primary-color: #2E8B57;
            --primary-light: #3A9D66;
            --primary-dark: #1A5632;
            --accent-color: #4CAF50;
            --bg-dark: #121212;
            --bg-card: #1E1E1E;
            --bg-hover: #2A2A2A;
            --text-light: #E0E0E0;
            --text-muted: #A0A0A0;
            --border-color: #333333;
            --card-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            --glow-effect: 0 0 10px rgba(46, 139, 87, 0.3);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            min-height: 100vh;
        }
        
        .page-container {
            padding: 20px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            background-color: var(--bg-card);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow), var(--glow-effect);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 1.25rem 1.5rem;
            border: none;
            border-radius: 15px 15px 0 0 !important;
        }
        
        .card-header h5 {
            font-weight: 700;
            margin: 0;
        }
        
        /* Scanner Styles */
        .scanner-container {
            position: relative;
            background: #000;
            border-radius: 15px;
            overflow: hidden;
            margin: 20px 0;
        }
        
        #reader {
            border-radius: 15px;
        }
        
        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 10;
        }
        
        .scanner-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 250px;
            height: 250px;
            border: 3px solid var(--primary-color);
            border-radius: 15px;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);
        }
        
        .scanner-frame::before,
        .scanner-frame::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 4px solid var(--primary-light);
        }
        
        .scanner-frame::before {
            top: -2px;
            left: -2px;
            border-right: none;
            border-bottom: none;
        }
        
        .scanner-frame::after {
            bottom: -2px;
            right: -2px;
            border-left: none;
            border-top: none;
        }
        
        .scanner-line {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary-light), transparent);
            animation: scan 2s linear infinite;
        }
        
        @keyframes scan {
            0% { top: 0; }
            50% { top: calc(100% - 2px); }
            100% { top: 0; }
        }
        
        /* Button Styles */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            position: relative;
            overflow: hidden;
            margin: 0.25rem;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
            box-shadow: 0 0 10px rgba(46, 139, 87, 0.5);
        }
        
        .btn-outline-primary {
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 0 10px rgba(46, 139, 87, 0.5);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #e63946, #c1121f);
            color: white;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #c1121f, #e63946);
            box-shadow: 0 0 10px rgba(230, 57, 70, 0.5);
        }
        
        /* Result Section */
        .result-section {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .result-section.show {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .product-card {
            background: var(--bg-hover);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        
        .product-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .info-item {
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            border-left: 3px solid var(--primary-color);
        }
        
        .info-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-weight: 600;
            color: var(--text-light);
        }
        
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.75rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .badge-halal {
            background: linear-gradient(135deg, #38b000, #2a8500);
            color: white;
        }
        
        .badge-tidak-halal {
            background: linear-gradient(135deg, #e63946, #c1121f);
            color: white;
        }
        
        .badge-sehat {
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            color: white;
        }
        
        .badge-tidak-sehat {
            background: linear-gradient(135deg, #f8961e, #e07c0c);
            color: white;
        }
        
        /* Status Indicators */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .status-scanning {
            background: rgba(46, 139, 87, 0.2);
            color: var(--primary-light);
            border: 1px solid var(--primary-color);
        }
        
        .status-success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }
        
        .status-error {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }
        
        .loading-spinner.show {
            display: block;
        }
        
        .spinner {
            border: 3px solid rgba(46, 139, 87, 0.3);
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .page-container {
                padding: 15px;
            }
            
            .scanner-frame {
                width: 200px;
                height: 200px;
            }
            
            .product-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title">
                        <i class="fas fa-qrcode me-2"></i>
                        Scan QR Code Produk
                    </h5>
                    <div class="status-indicator status-scanning" id="scanStatus">
                        <i class="fas fa-circle"></i>
                        Siap Scan
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Scanner Section -->
                <div class="scanner-section" id="scannerSection">
                    <div class="scanner-container">
                        <div id="reader"></div>
                        <div class="scanner-overlay">
                            <div class="scanner-frame">
                                <div class="scanner-line"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <button class="btn btn-primary" id="startScanBtn">
                            <i class="fas fa-camera me-2"></i>
                            Mulai Scan
                        </button>
                        <button class="btn btn-danger d-none" id="stopScanBtn">
                            <i class="fas fa-stop me-2"></i>
                            Stop Scan
                        </button>
                        <button class="btn btn-outline-primary" id="switchCameraBtn">
                            <i class="fas fa-sync-alt me-2"></i>
                            Ganti Kamera
                        </button>
                    </div>
                </div>
                
                <!-- Loading Section -->
                <div class="loading-spinner" id="loadingSection">
                    <div class="spinner"></div>
                    <p>Sedang mencari produk...</p>
                </div>
                
                <!-- Result Section -->
                <div class="result-section" id="resultSection">
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <div>
                            <strong>Scan Berhasil!</strong> Barcode ditemukan: <code id="scannedBarcode"></code>
                        </div>
                    </div>
                    
                    <div class="product-card" id="productCard">
                        <h6 class="mb-3">
                            <i class="fas fa-box me-2"></i>
                            Informasi Produk
                        </h6>
                        
                        <div class="product-info" id="productInfo">
                            <!-- Product info will be loaded here -->
                        </div>
                        
                        <div class="mt-3">
                            <button class="btn btn-primary" id="saveScanBtn">
                                <i class="fas fa-save me-2"></i>
                                Simpan Scan
                            </button>
                            <button class="btn btn-outline-primary" id="scanAgainBtn">
                                <i class="fas fa-redo me-2"></i>
                                Scan Lagi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let html5QrCode = null;
        let currentCameraId = null;
        let cameras = [];
        let scannedData = null;
        
        // Initialize scanner
        function initScanner() {
            html5QrCode = new Html5Qrcode("reader");
            
            // Get available cameras
            Html5Qrcode.getCameras().then(devices => {
                cameras = devices;
                if (devices && devices.length) {
                    currentCameraId = devices[0].id;
                }
            }).catch(err => {
                console.error('Error getting cameras:', err);
                showError('Tidak dapat mengakses kamera. Pastikan izin kamera diaktifkan.');
            });
        }
        
        // Start scanning
        function startScanning() {
            if (!currentCameraId) {
                showError('Tidak ada kamera yang tersedia');
                return;
            }
            
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };
            
            html5QrCode.start(
                currentCameraId,
                config,
                (decodedText, decodedResult) => {
                    // Success callback
                    onScanSuccess(decodedText);
                },
                (errorMessage) => {
                    // Error callback (ignore continuous errors)
                }
            ).then(() => {
                updateScanStatus('scanning', 'Sedang Scan...');
                document.getElementById('startScanBtn').classList.add('d-none');
                document.getElementById('stopScanBtn').classList.remove('d-none');
            }).catch((err) => {
                console.error('Error starting scanner:', err);
                showError('Gagal memulai scanner: ' + err.message);
            });
        }
        
        // Stop scanning
        function stopScanning() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    updateScanStatus('ready', 'Siap Scan');
                    document.getElementById('startScanBtn').classList.remove('d-none');
                    document.getElementById('stopScanBtn').classList.add('d-none');
                }).catch((err) => {
                    console.error('Error stopping scanner:', err);
                });
            }
        }
        
        // Switch camera
        function switchCamera() {
            if (cameras.length < 2) {
                showError('Hanya ada satu kamera yang tersedia');
                return;
            }
            
            const currentIndex = cameras.findIndex(cam => cam.id === currentCameraId);
            const nextIndex = (currentIndex + 1) % cameras.length;
            currentCameraId = cameras[nextIndex].id;
            
            if (html5QrCode && html5QrCode.isScanning) {
                stopScanning();
                setTimeout(() => startScanning(), 500);
            }
        }
        
        // Handle successful scan
        function onScanSuccess(decodedText) {
            stopScanning();
            scannedData = decodedText;
            
            // Update UI
            document.getElementById('scannedBarcode').textContent = decodedText;
            document.getElementById('scannerSection').style.display = 'none';
            document.getElementById('loadingSection').classList.add('show');
            updateScanStatus('success', 'Scan Berhasil!');
            
            // Search product by barcode
            searchProductByBarcode(decodedText);
        }
        
        // Search product by barcode
        async function searchProductByBarcode(barcode) {
            try {
                const response = await fetch(`/admin/product/search/${barcode}`);
                const data = await response.json();
                
                document.getElementById('loadingSection').classList.remove('show');
                
                if (data.success) {
                    displayProductInfo(data.product);
                    document.getElementById('resultSection').classList.add('show');
                } else {
                    showProductNotFound(barcode);
                    document.getElementById('resultSection').classList.add('show');
                }
            } catch (error) {
                console.error('Error searching product:', error);
                document.getElementById('loadingSection').classList.remove('show');
                showError('Terjadi kesalahan saat mencari produk');
                document.getElementById('resultSection').classList.add('show');
            }
        }
        
        // Display product information
        function displayProductInfo(product) {
            const productInfo = document.getElementById('productInfo');
            
            const statusHalalBadge = product.status_halal === 'halal' 
                ? '<span class="badge badge-halal"><i class="fas fa-check-circle"></i> Halal</span>'
                : '<span class="badge badge-tidak-halal"><i class="fas fa-times-circle"></i> Tidak Halal</span>';
            
            const statusKesehatanBadge = product.status_kesehatan === 'sehat'
                ? '<span class="badge badge-sehat"><i class="fas fa-heart"></i> Sehat</span>'
                : '<span class="badge badge-tidak-sehat"><i class="fas fa-exclamation-triangle"></i> Tidak Sehat</span>';
            
            productInfo.innerHTML = `
                <div class="info-item">
                    <div class="info-label">Nama Produk</div>
                    <div class="info-value">${product.nama_produk || '-'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Barcode</div>
                    <div class="info-value">${product.barcode || '-'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kategori</div>
                    <div class="info-value">${product.kategori || '-'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status Halal</div>
                    <div class="info-value">${statusHalalBadge}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status Kesehatan</div>
                    <div class="info-value">${statusKesehatanBadge}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tanggal Expired</div>
                    <div class="info-value">${product.tanggal_expired ? new Date(product.tanggal_expired).toLocaleDateString('id-ID') : '-'}</div>
                </div>
            `;
        }
        
        // Show product not found
        function showProductNotFound(barcode) {
            const productInfo = document.getElementById('productInfo');
            productInfo.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Produk dengan barcode <strong>${barcode}</strong> tidak ditemukan dalam database.
                </div>
            `;
        }
        
        // Save scan
        async function saveScan() {
            try {
                const response = await fetch('/admin/scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        barcode: scannedData,
                        user_id: {{ auth()->user()->id }},
                        tanggal_scan: new Date().toISOString()
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showSuccess('Data scan berhasil disimpan!');
                    setTimeout(() => {
                        window.location.href = '/admin/scan';
                    }, 2000);
                } else {
                    showError('Gagal menyimpan scan: ' + data.message);
                }
            } catch (error) {
                console.error('Error saving scan:', error);
                showError('Terjadi kesalahan saat menyimpan scan');
            }
        }
        
        // Scan again
        function scanAgain() {
            document.getElementById('resultSection').classList.remove('show');
            document.getElementById('scannerSection').style.display = 'block';
            scannedData = null;
            updateScanStatus('ready', 'Siap Scan');
        }
        
        // Update scan status
        function updateScanStatus(status, text) {
            const statusElement = document.getElementById('scanStatus');
            statusElement.className = `status-indicator status-${status}`;
            statusElement.innerHTML = `<i class="fas fa-circle"></i> ${text}`;
        }
        
        // Show error message
        function showError(message) {
            // Create or update error alert
            let errorAlert = document.getElementById('errorAlert');
            if (!errorAlert) {
                errorAlert = document.createElement('div');
                errorAlert.id = 'errorAlert';
                errorAlert.className = 'alert alert-danger alert-dismissible fade show mt-3';
                errorAlert.innerHTML = `
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="errorMessage"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.card-body').insertBefore(errorAlert, document.querySelector('.scanner-section'));
            }
            
            document.getElementById('errorMessage').textContent = message;
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                if (errorAlert) {
                    errorAlert.remove();
                }
            }, 5000);
        }
        
        // Show success message
        function showSuccess(message) {
            const successAlert = document.createElement('div');
            successAlert.className = 'alert alert-success alert-dismissible fade show mt-3';
            successAlert.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.card-body').insertBefore(successAlert, document.querySelector('.scanner-section'));
            
            // Auto hide after 3 seconds
            setTimeout(() => {
                successAlert.remove();
            }, 3000);
        }
        
        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            initScanner();
            
            document.getElementById('startScanBtn').addEventListener('click', startScanning);
            document.getElementById('stopScanBtn').addEventListener('click', stopScanning);
            document.getElementById('switchCameraBtn').addEventListener('click', switchCamera);
            document.getElementById('saveScanBtn').addEventListener('click', saveScan);
            document.getElementById('scanAgainBtn').addEventListener('click', scanAgain);
        });
    </script>
</body>
</html>
@endsection
