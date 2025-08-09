<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title><?= $title ?> - <?= $Pengaturan->judul ?? 'KopMensa' ?></title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('public/assets/theme/admin-lte-3/plugins/fontawesome-free/css/all.min.css') ?>">
    
    <style>
        body {
            background: #1a1a1a;
            color: white;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        .scanner-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .scanner-header {
            background: rgba(0,0,0,0.8);
            padding: 15px;
            text-align: center;
            z-index: 1000;
        }
        
        .scanner-header h4 {
            margin: 0;
            color: #28a745;
        }
        
        .transaction-info {
            background: rgba(0,0,0,0.7);
            padding: 10px 15px;
            border-radius: 10px;
            margin: 10px;
            text-align: center;
        }
        
        .scanner-area {
            flex: 1;
            position: relative;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        
        #reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .scanner-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 250px;
            height: 250px;
            border: 3px solid #28a745;
            border-radius: 15px;
            box-shadow: 
                0 0 0 5px rgba(40, 167, 69, 0.2),
                inset 0 0 0 5px rgba(40, 167, 69, 0.2);
            z-index: 100;
            pointer-events: none;
        }
        
        .scanner-overlay::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            border: 2px solid rgba(40, 167, 69, 0.5);
            border-radius: 15px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.05); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        .corner-marker {
            position: absolute;
            width: 30px;
            height: 30px;
            border: 4px solid #28a745;
        }
        
        .corner-marker.top-left {
            top: -2px;
            left: -2px;
            border-right: none;
            border-bottom: none;
        }
        
        .corner-marker.top-right {
            top: -2px;
            right: -2px;
            border-left: none;
            border-bottom: none;
        }
        
        .corner-marker.bottom-left {
            bottom: -2px;
            left: -2px;
            border-right: none;
            border-top: none;
        }
        
        .corner-marker.bottom-right {
            bottom: -2px;
            right: -2px;
            border-left: none;
            border-top: none;
        }
        
        .scanner-instructions {
            position: absolute;
            bottom: 100px;
            left: 0;
            right: 0;
            text-align: center;
            padding: 20px;
            background: rgba(0,0,0,0.7);
            color: white;
        }
        
        .scanner-controls {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            padding: 20px;
        }
        
        .btn-scanner {
            background: #28a745;
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            margin: 5px;
            font-size: 16px;
            min-width: 120px;
        }
        
        .btn-scanner:hover {
            background: #218838;
            color: white;
        }
        
        .btn-close {
            background: #dc3545;
        }
        
        .btn-close:hover {
            background: #c82333;
            color: white;
        }
        
        .scan-result {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(40, 167, 69, 0.9);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            z-index: 1001;
            display: none;
        }
        
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 999;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(40, 167, 69, 0.3);
            border-top: 5px solid #28a745;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Mobile specific adjustments */
        @media (max-width: 768px) {
            .scanner-overlay {
                width: 80vw;
                height: 80vw;
                max-width: 300px;
                max-height: 300px;
            }
            
            .scanner-instructions {
                bottom: 80px;
                font-size: 14px;
            }
            
            .btn-scanner {
                padding: 10px 20px;
                font-size: 14px;
                min-width: 100px;
            }
        }
    </style>
</head>
<body>
    <div class="scanner-container">
        <!-- Header -->
        <div class="scanner-header">
            <h4><i class="fas fa-qrcode"></i> QR Scanner - Piutang</h4>
            <div class="transaction-info">
                <strong>No. Nota:</strong> <?= $transaction->no_nota ?><br>
                <strong>Total:</strong> Rp <?= number_format($transaction->jml_gtotal, 0, ',', '.') ?><br>
                <strong>Customer:</strong> <?= $transaction->customer_name ?? 'Umum' ?>
            </div>
        </div>
        
        <!-- Scanner Area -->
        <div class="scanner-area">
            <div class="loading">
                <div class="spinner"></div>
                <p style="margin-top: 10px; color: #28a745;">Memuat Scanner...</p>
            </div>
            
            <div id="reader"></div>
            
            <!-- Scanner Overlay -->
            <div class="scanner-overlay">
                <div class="corner-marker top-left"></div>
                <div class="corner-marker top-right"></div>
                <div class="corner-marker bottom-left"></div>
                <div class="corner-marker bottom-right"></div>
            </div>
            
            <!-- Instructions -->
            <div class="scanner-instructions">
                <h5><i class="fas fa-mobile-alt"></i> Arahkan kamera ke QR Code</h5>
                <p>Posisikan QR Code di dalam area hijau untuk scan otomatis</p>
            </div>
        </div>
        
        <!-- Controls -->
        <div class="scanner-controls">
            <button class="btn btn-scanner" id="toggleFlash">
                <i class="fas fa-flashlight"></i> Flash
            </button>
            <button class="btn btn-scanner" id="switchCamera">
                <i class="fas fa-camera-rotate"></i> Flip
            </button>
            <button class="btn btn-scanner btn-close" onclick="window.close()">
                <i class="fas fa-times"></i> Tutup
            </button>
        </div>
        
        <!-- Scan Result -->
        <div class="scan-result" id="scanResult">
            <h5><i class="fas fa-check-circle"></i> Scan Berhasil!</h5>
            <p id="scanData"></p>
            <button class="btn btn-scanner" id="continueScan">Scan Lagi</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('public/assets/theme/admin-lte-3/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    
    <!-- Html5-QRCode Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <script>
        let html5QrcodeScanner;
        let currentCamera = 0;
        let cameras = [];
        let flashOn = false;
        
        // Note: CSRF disabled for this route to allow mobile scanning
        
        $(document).ready(function() {
            initializeScanner();
            
            // Flash toggle
            $('#toggleFlash').click(function() {
                toggleFlash();
            });
            
            // Camera switch
            $('#switchCamera').click(function() {
                switchCamera();
            });
            
            // Continue scanning
            $('#continueScan').click(function() {
                $('#scanResult').hide();
                startScanning();
            });
        });
        
        function initializeScanner() {
            // Get camera devices
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length > 0) {
                    cameras = devices;
                    $('.loading').hide();
                    startScanning();
                } else {
                    showError("Tidak ada kamera yang tersedia");
                }
            }).catch(err => {
                console.error("Error getting cameras:", err);
                showError("Gagal mengakses kamera");
            });
        }
        
        function startScanning() {
            const cameraId = cameras[currentCamera]?.id || 'environment';
            
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                }
            };
            
            html5QrcodeScanner = new Html5Qrcode("reader");
            
            html5QrcodeScanner.start(
                cameraId,
                config,
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error("Scanner start failed:", err);
                showError("Gagal memulai scanner");
            });
        }
        
        function onScanSuccess(decodedText, decodedResult) {
            console.log("Scan result:", decodedText);
            
            // Stop scanner
            html5QrcodeScanner.stop().then(() => {
                // Show result
                $('#scanData').text(decodedText);
                $('#scanResult').show();
                
                // Process the scanned data
                processScannedData(decodedText);
            });
        }
        
        function onScanFailure(error) {
            // Ignore scan failures (continuous scanning)
        }
        
        function processScannedData(data) {
            // Send scanned data to server for processing
            $.ajax({
                url: '<?= base_url('api/qr-scan') ?>',
                type: 'POST',
                data: {
                    transaction_id: <?= $transactionId ?>,
                    scan_data: data
                },
                success: function(response) {
                    if (response.success) {
                        showSuccess("QR Code berhasil diproses!");
                        setTimeout(function() {
                            window.close();
                        }, 2000);
                    } else {
                        showError(response.message || "Gagal memproses QR Code");
                    }
                },
                error: function(xhr, status, error) {
                    console.error('QR Scan Error:', xhr);
                    let errorMsg = "Terjadi kesalahan saat memproses QR Code";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMsg = response.message || errorMsg;
                        } catch (e) {
                            errorMsg += " (Status: " + xhr.status + ")";
                        }
                    }
                    showError(errorMsg);
                }
            });
        }
        
        function toggleFlash() {
            // Note: Flash control depends on browser support
            if (html5QrcodeScanner) {
                try {
                    const track = html5QrcodeScanner.getRunningTrackCapabilities();
                    if (track.torch) {
                        flashOn = !flashOn;
                        track.torch = flashOn;
                        $('#toggleFlash').html(flashOn ? 
                            '<i class="fas fa-flashlight"></i> Flash ON' : 
                            '<i class="fas fa-flashlight"></i> Flash'
                        );
                    }
                } catch (e) {
                    console.log("Flash not supported:", e);
                }
            }
        }
        
        function switchCamera() {
            if (cameras.length > 1) {
                html5QrcodeScanner.stop().then(() => {
                    currentCamera = (currentCamera + 1) % cameras.length;
                    startScanning();
                });
            }
        }
        
        function showError(message) {
            $('.loading').hide();
            alert("Error: " + message);
        }
        
        function showSuccess(message) {
            alert("Success: " + message);
        }
        
        // Prevent page zoom on mobile
        document.addEventListener('touchstart', function(event) {
            if (event.touches.length > 1) {
                event.preventDefault();
            }
        }, { passive: false });
        
        // Prevent context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    </script>
</body>
</html>
