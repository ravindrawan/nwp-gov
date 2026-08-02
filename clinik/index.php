<?php
/**
 * Computer Clinic - North Western Province (Powered by Digital Division)
 * Official Booking & Dynamic QR Token System
 */
require_once __DIR__ . '/config/db.php';
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Clinic | වයඹ පළාත් සභා ඩිජිටල් අංශය (Digital Division)</title>
    <meta name="description" content="Official Computer Clinic Booking System for North Western Province Office Complex staff. Organised by Digital Division.">
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/cyber-style.css">

    <!-- QRCode.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>

    <!-- CRT Scanlines Overlay -->
    <div class="scanline-overlay"></div>

    <!-- Cyber Navbar -->
    <nav class="cyber-navbar">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php" class="text-decoration-none d-flex align-items-center gap-2">
                <i class="fas fa-laptop-medical text-orange fs-2"></i>
                <div>
                    <span class="brand-badge fs-4">COMPUTER CLINIC</span>
                    <span class="d-block text-orange fw-bold extra-small" style="font-size:0.8rem; letter-spacing:1px;"><i class="fas fa-microchip me-1"></i> DIGITAL DIVISION | වයඹ පළාත් සභාව</span>
                </div>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a href="#booking-section" class="btn-cyber-primary btn-sm d-none d-sm-inline-flex">
                    <i class="fas fa-plus-circle me-1"></i> BOOK CLINIC PASS
                </a>
                <a href="verify.php" class="btn-cyber-outline btn-sm">
                    <i class="fas fa-qrcode me-1"></i> SCAN / VERIFY PASS
                </a>
                <a href="admin.php" class="btn btn-sm btn-outline-secondary text-white" title="Admin Dashboard">
                    <i class="fas fa-user-shield"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section Banner -->
    <header class="hero-header container">
        <div class="d-inline-flex align-items-center gap-2 bg-dark border border-warning px-3 py-2 rounded-pill mb-3 shadow">
            <i class="fas fa-microchip text-orange fs-5"></i>
            <span class="text-warning font-monospace fw-bold text-uppercase" style="letter-spacing:1px;">ORGANISED BY DIGITAL DIVISION - NWP</span>
        </div>
        
        <h1 class="hero-title text-uppercase">Computer Clinic - North Western Province</h1>
        <p class="hero-subtitle mb-3">වයඹ පළාත් සභා ඩිජිටල් අංශය මගින් සංවිධානය කරන පරිගණක සායනය (Computer Clinic)</p>

        <!-- Clinic Notice Card -->
        <div class="row justify-content-center my-4">
            <div class="col-lg-10">
                <div class="cyber-card text-start border-warning" style="background: rgba(26, 16, 10, 0.95);">
                    <div class="row align-items-center g-3">
                        <div class="col-md-7">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-danger text-white font-monospace">DIGITAL DIVISION</span>
                                <h4 class="text-amber mb-0"><i class="fas fa-bullhorn me-1"></i> විශේෂ නිවේදනයයි</h4>
                            </div>
                            <p class="text-white small mb-2">
                                වයඹ පළාත් සභා කාර්යාල සංකීර්ණයේ රාජකාරි කරන ඔබට මහඟු අවස්ථාවක්! ඔබගේ කාර්යාලයේ ක්‍රියා විරහිත පරිගණකය අලුත්වැඩියා කර ගැනීමට <strong>ඩිජිටල් අංශය (Digital Division)</strong> වෙතින් සහාය ලබාදෙන්නෙමු.
                            </p>
                            <p class="text-warning fw-bold small mb-0">
                                <i class="fas fa-calendar-alt me-1"></i> දෝෂ සහිත පරිගණකය රැගෙන <strong>2026 අගෝස්තු මස 4 දින</strong> පැමිණෙන්න!
                            </p>
                        </div>
                        <div class="col-md-5">
                            <div class="p-3 rounded border border-secondary bg-dark text-white shadow">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fas fa-clock text-orange fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">වේලාව (Time Slot):</small>
                                        <strong class="text-gold">පෙ.ව. 9.30 සිට 10.30 දක්වා</strong>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-danger fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">ස්ථානය (Location):</small>
                                        <strong class="text-white">පළාත් සභා පරිශ්‍රයේ ආපනශාලාව ඉදිරිපිට</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Countdown Timer -->
        <div class="timer-container">
            <div class="timer-box">
                <span class="timer-number" id="cd-days">00</span>
                <span class="timer-label">DAYS</span>
            </div>
            <div class="timer-box">
                <span class="timer-number" id="cd-hours">00</span>
                <span class="timer-label">HOURS</span>
            </div>
            <div class="timer-box">
                <span class="timer-number" id="cd-mins">00</span>
                <span class="timer-label">MINS</span>
            </div>
            <div class="timer-box">
                <span class="timer-number" id="cd-secs">00</span>
                <span class="timer-label">SECS</span>
            </div>
        </div>
    </header>

    <!-- Clinic Repair Services Showcase -->
    <section class="container my-5">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="cyber-label"><i class="fas fa-tools me-1"></i> CLINIC SERVICES BY DIGITAL DIVISION</span>
                <h3 class="cyber-font text-white mb-0">පරිගණක සායනයේ සේවා කාණ්ඩ</h3>
            </div>
            <span class="text-muted small d-none d-md-inline"><i class="fas fa-info-circle text-warning me-1"></i> Click any service to book your slot</span>
        </div>

        <div class="row g-4" id="drops-container">
            <!-- Loaded dynamically via JavaScript app.js -->
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-warning" role="status"></div>
                <p class="cyber-font text-orange mt-3">LOADING CLINIC SERVICES...</p>
            </div>
        </div>
    </section>

    <!-- Booking Form Section -->
    <section class="container my-5" id="booking-section">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="cyber-card">
                    <div class="d-flex align-items-center justify-content-between border-bottom border-secondary pb-3 mb-4">
                        <div>
                            <span class="cyber-label"><i class="fas fa-edit me-1"></i> APPOINTMENT REGISTRATION</span>
                            <h3 class="cyber-font text-white mb-0">පරිගණක සායනය සඳහා ලියාපදිංචි වන්න</h3>
                            <small class="text-orange"><i class="fas fa-shield-alt me-1"></i> Digital Division - North Western Province</small>
                        </div>
                        <i class="fas fa-id-badge fs-1 text-orange opacity-50"></i>
                    </div>

                    <form id="booking-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="cyber-label" for="customer_name"><i class="fas fa-user me-1"></i> ඔබේ නම (Officer Full Name) *</label>
                                <input type="text" class="cyber-input" id="customer_name" name="customer_name" placeholder="උදා: කසුන් අමරසිංහ" required>
                            </div>
                            <div class="col-md-6">
                                <label class="cyber-label" for="phone"><i class="fas fa-phone-alt me-1"></i> ෆෝන් අංකය (Mobile Phone Number) *</label>
                                <input type="tel" class="cyber-input" id="phone" name="phone" placeholder="උදා: 0771234567" required>
                            </div>
                            <div class="col-md-6">
                                <label class="cyber-label" for="nic"><i class="fas fa-building me-1"></i> දෙපාර්තමේන්තුව / අංශය (Office / Department) *</label>
                                <input type="text" class="cyber-input" id="nic" name="nic" placeholder="උදා: ප්‍රධාන ලේකම් කාර්යාලය / ආදායම් දෙපාර්තමේන්තුව" required>
                            </div>
                            <div class="col-md-6">
                                <label class="cyber-label" for="drop_id"><i class="fas fa-tools me-1"></i> අලුත්වැඩියා සේවා කාණ්ඩය (Clinic Service) *</label>
                                <select class="cyber-input" id="drop_id" name="drop_id" required>
                                    <option value="">-- සේවාව තෝරන්න (Select Repair Service) --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="cyber-label" for="quantity"><i class="fas fa-desktop me-1"></i> පරිගණක ගණන (PC Quantity)</label>
                                <input type="number" class="cyber-input" id="quantity" name="quantity" min="1" max="5" value="1" required>
                            </div>
                            <div class="col-md-8">
                                <label class="cyber-label" for="special_notes"><i class="fas fa-comment-medical me-1"></i> පරිගණකයේ මාදිලිය සහ ලෙඩේ/දෝෂයේ විස්තරය *</label>
                                <input type="text" class="cyber-input" id="special_notes" name="special_notes" placeholder="උදා: Dell Desktop PC - Power අන් වෙනවා ඩිස්ප්ලේ එන්නේ නෑ" required>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top border-secondary text-end">
                            <button type="submit" class="btn-cyber-primary btn-lg w-100" id="btn-submit-booking">
                                <i class="fas fa-qrcode fs-4 me-2"></i> SUBMIT & ISSUE COMPUTER CLINIC TOKEN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Digital Pass Modal -->
    <div class="modal fade" id="ticketModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-0 shadow-lg">
                <div class="modal-body p-0">
                    <div class="ticket-pass" id="printable-ticket">
                        <div class="ticket-header">
                            <div>
                                <span class="badge bg-warning text-dark font-monospace px-2 py-1 rounded small"><i class="fas fa-check-circle me-1"></i> OFFICIAL TOKEN PASS</span>
                                <h4 class="cyber-font text-white mt-2 mb-0">COMPUTER CLINIC PASS</h4>
                                <small class="text-orange fw-bold d-block"><i class="fas fa-microchip me-1"></i> DIGITAL DIVISION - NWP</small>
                            </div>
                            <div class="text-end">
                                <span class="booking-code-badge" id="modal-booking-code">CLINIC-2026-0000</span>
                                <p class="text-muted extra-small mb-0">TOKEN PASS ID</p>
                            </div>
                        </div>

                        <div class="text-center my-3">
                            <div class="qr-box" id="modal-qr-container"></div>
                            <p class="text-muted extra-small mt-2"><i class="fas fa-camera me-1"></i> Scan QR at Digital Division desk when bringing computer</p>
                        </div>

                        <div class="row g-2 text-start p-3 bg-dark rounded border border-secondary">
                            <div class="col-6">
                                <small class="text-muted">Officer Name:</small>
                                <strong class="d-block text-orange" id="modal-customer-name">-</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Phone Number:</small>
                                <strong class="d-block text-warning" id="modal-phone">-</strong>
                            </div>
                            <div class="col-12 mt-2">
                                <small class="text-muted">Office / Department:</small>
                                <strong class="d-block text-white" id="modal-item-si">-</strong>
                            </div>
                            <div class="col-12 mt-2">
                                <small class="text-muted">Clinic Repair Service:</small>
                                <strong class="d-block text-gold" id="modal-item-title">-</strong>
                            </div>
                            <div class="col-6 mt-2">
                                <small class="text-muted">Clinic Date & Time:</small>
                                <span class="d-block text-warning fw-bold" id="modal-drop-time">2026-08-04 (09.30 - 10.30 AM)</span>
                            </div>
                            <div class="col-6 mt-2">
                                <small class="text-muted">Clinic Location:</small>
                                <span class="d-block text-white fw-bold">පළාත් සභා ආපනශාලාව ඉදිරිපිට</span>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary text-white btn-sm" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> CLOSE
                            </button>
                            <button type="button" class="btn-cyber-primary btn-sm" onclick="window.print()">
                                <i class="fas fa-print me-1"></i> PRINT CLINIC TOKEN
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 border-top border-secondary mt-5">
        <div class="container">
            <p class="text-white mb-1 font-monospace fw-bold">COMPUTER CLINIC 2026 | POWERED BY DIGITAL DIVISION</p>
            <p class="extra-small text-orange mb-0">Chief Secretariat Complex, North Western Provincial Council, Sri Lanka</p>
        </div>
    </footer>

    <!-- JS Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/cyber-audio.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
