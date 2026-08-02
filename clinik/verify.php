<?php
/**
 * Verification & QR Scanner Console - Computer Clinic (Digital Division)
 */
require_once __DIR__ . '/config/db.php';
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Scanner & Verification | Digital Division Computer Clinic</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/cyber-style.css">

    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body>

    <!-- CRT Overlay -->
    <div class="scanline-overlay"></div>

    <!-- Cyber Navbar -->
    <nav class="cyber-navbar">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php" class="text-decoration-none d-flex align-items-center gap-2">
                <i class="fas fa-microchip text-orange fs-2"></i>
                <div>
                    <span class="brand-badge fs-4">TECHNICIAN SCANNER HUB</span>
                    <span class="d-block text-orange fw-bold extra-small" style="font-size:0.75rem; letter-spacing:1px;">DIGITAL DIVISION | NWP COMPUTER CLINIC</span>
                </div>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a href="index.php" class="btn-cyber-outline btn-sm">
                    <i class="fas fa-home me-1"></i> PUBLIC PORTAL
                </a>
                <a href="admin.php" class="btn btn-sm btn-outline-secondary text-white">
                    <i class="fas fa-chart-line me-1"></i> DASHBOARD
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row g-4">
            
            <!-- Left Side: Live Camera Scanner & Manual Input -->
            <div class="col-lg-5">
                <div class="cyber-card mb-4">
                    <div class="d-flex align-items-center justify-content-between border-bottom border-secondary pb-3 mb-3">
                        <h4 class="cyber-font text-white mb-0"><i class="fas fa-qrcode text-orange me-2"></i> SCAN TOKEN QR CODE</h4>
                        <span class="badge bg-success text-dark font-monospace"><i class="fas fa-video me-1"></i> LIVE CAM</span>
                    </div>
                    
                    <p class="text-muted small">පරිගණකය ගෙන ආ නිලධාරියාගේ QR සංකේතය Scan කරන්න:</p>
                    <div class="scanner-frame">
                        <div id="reader"></div>
                    </div>
                </div>

                <!-- Manual Search Form -->
                <div class="cyber-card">
                    <h5 class="cyber-font text-white border-bottom border-secondary pb-2 mb-3">
                        <i class="fas fa-search text-warning me-2"></i> SEARCH BY TOKEN / PHONE
                    </h5>
                    <form id="lookup-form">
                        <div class="mb-3">
                            <label class="cyber-label" for="search_query">Token කේතය හෝ ෆෝන් අංකය ඇතුළත් කරන්න:</label>
                            <input type="text" class="cyber-input" id="search_query" placeholder="උදා: CLINIC-2026-8912 හෝ 0771234567" required>
                        </div>
                        <button type="submit" class="btn-cyber-primary w-100">
                            <i class="fas fa-search me-2"></i> SEARCH & VERIFY CLINIC TOKEN
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Side: Ticket Verification Result -->
            <div class="col-lg-7">
                <div class="cyber-card h-100">
                    <div class="border-bottom border-secondary pb-3 mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="cyber-label"><i class="fas fa-shield-alt me-1"></i> DIGITAL DIVISION TECHNICIAN DESK</span>
                            <h4 class="cyber-font text-white mb-0">පරිගණක සායන ලියාපදිංචි විස්තර</h4>
                        </div>
                        <i class="fas fa-laptop-medical fs-1 text-orange opacity-50"></i>
                    </div>

                    <div id="scan-result-container">
                        <div class="text-center py-5">
                            <i class="fas fa-qrcode fs-1 text-orange mb-3 opacity-50"></i>
                            <h5 class="cyber-font text-white">READY TO SCAN & RECEIVE</h5>
                            <p class="text-muted">Scan a Token QR Code using the camera or enter a Token Code / Phone Number on the left.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- JS Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/cyber-audio.js"></script>
    <script src="assets/js/scanner.js"></script>
</body>
</html>
