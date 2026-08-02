<?php
/**
 * Admin Management Console - Tuesday Drop HQ
 */
require_once __DIR__ . '/config/db.php';
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Console | Tuesday Drop HQ</title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/cyber-style.css">
</head>
<body>

    <!-- CRT Overlay -->
    <div class="scanline-overlay"></div>

    <!-- Cyber Navbar -->
    <nav class="cyber-navbar">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php" class="text-decoration-none d-flex align-items-center gap-2">
                <i class="fas fa-bolt text-warning fs-3"></i>
                <div>
                    <span class="brand-badge fs-4">ADMIN DASHBOARD</span>
                    <span class="d-block text-muted extra-small" style="font-size:0.7rem; letter-spacing:1px;">SYSTEM ANALYTICS & PASS MANAGEMENT</span>
                </div>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a href="index.php" class="btn-cyber-outline btn-sm">
                    <i class="fas fa-home me-1"></i> PUBLIC PORTAL
                </a>
                <a href="verify.php" class="btn-cyber-primary btn-sm">
                    <i class="fas fa-qrcode me-1"></i> SCANNER CONSOLE
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-5">

        <!-- Stats Overview Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="cyber-card text-center">
                    <span class="cyber-label">TOTAL BOOKINGS</span>
                    <h2 class="cyber-font text-cyan my-2" id="stat-total">0</h2>
                    <span class="extra-small text-muted"><i class="fas fa-ticket-alt me-1"></i> Received All-time</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="cyber-card text-center">
                    <span class="cyber-label">CONFIRMED PASSES</span>
                    <h2 class="cyber-font text-green my-2" id="stat-confirmed" style="color:var(--cyber-green);">0</h2>
                    <span class="extra-small text-muted"><i class="fas fa-check-circle me-1"></i> Verified & Claimed</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="cyber-card text-center">
                    <span class="cyber-label">PENDING PASSES</span>
                    <h2 class="cyber-font text-gold my-2" id="stat-pending" style="color:var(--cyber-gold);">0</h2>
                    <span class="extra-small text-muted"><i class="fas fa-clock me-1"></i> Awaiting Tuesday Pickup</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="cyber-card text-center">
                    <span class="cyber-label">TUESDAY DROPS</span>
                    <h2 class="cyber-font text-pink my-2" id="stat-drops" style="color:var(--cyber-pink);">0</h2>
                    <span class="extra-small text-muted"><i class="fas fa-boxes me-1"></i> Active Inventory Catalog</span>
                </div>
            </div>
        </div>

        <!-- Bookings Management Table -->
        <div class="cyber-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-3 border-bottom border-secondary">
                <div>
                    <span class="cyber-label"><i class="fas fa-list me-1"></i> REAL-TIME DATABASE</span>
                    <h4 class="cyber-font text-white mb-0">අඟහරුවාදා බුකින් ලැයිස්තුව</h4>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select class="cyber-input py-1 px-3" id="status-filter" style="width: auto;" onchange="loadAdminBookings()">
                        <option value="">සියලුම බුකින් (All Status)</option>
                        <option value="PENDING">PENDING පමණක්</option>
                        <option value="CONFIRMED">CONFIRMED පමණක්</option>
                    </select>
                    <button class="btn btn-outline-info btn-sm" onclick="loadAdminBookings()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle border-secondary" id="bookings-table">
                    <thead>
                        <tr class="text-cyan cyber-font small">
                            <th>CODE</th>
                            <th>CUSTOMER NAME</th>
                            <th>PHONE NUMBER</th>
                            <th>TUESDAY ITEM</th>
                            <th>QTY</th>
                            <th>STATUS</th>
                            <th>BOOKED AT</th>
                            <th class="text-end">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="admin-table-body">
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="spinner-border text-info" role="status"></div>
                                <p class="cyber-font text-cyan mt-2">FETCHING BOOKINGS...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- JS Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/cyber-audio.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', loadAdminBookings);

        async function loadAdminBookings() {
            const filter = document.getElementById('status-filter').value;
            const tbody = document.getElementById('admin-table-body');
            
            try {
                const res = await fetch(`api/booking.php?action=list&status=${encodeURIComponent(filter)}`);
                const json = await res.json();

                if (json.success) {
                    // Update stats
                    document.getElementById('stat-total').innerText = json.stats.total;
                    document.getElementById('stat-confirmed').innerText = json.stats.confirmed;
                    document.getElementById('stat-pending').innerText = json.stats.pending;
                    document.getElementById('stat-drops').innerText = json.stats.drops;

                    if (json.data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-4">බුකින් සටහන් හමු නොවීය.</td></tr>`;
                        return;
                    }

                    tbody.innerHTML = json.data.map(b => {
                        const isConfirmed = b.status === 'CONFIRMED';
                        return `
                            <tr>
                                <td><strong class="text-warning font-monospace">${escapeHtml(b.booking_code)}</strong></td>
                                <td><strong class="text-white">${escapeHtml(b.customer_name)}</strong></td>
                                <td><span class="text-cyan"><i class="fas fa-phone-alt me-1"></i>${escapeHtml(b.phone)}</span></td>
                                <td>${escapeHtml(b.drop_title)}</td>
                                <td><span class="badge bg-secondary">${b.quantity}</span></td>
                                <td>
                                    <span class="badge-status ${isConfirmed ? 'status-confirmed' : 'status-pending'}">
                                        ${b.status}
                                    </span>
                                </td>
                                <td><small class="text-muted">${b.created_at}</small></td>
                                <td class="text-end">
                                    ${isConfirmed ? `
                                        <span class="badge bg-success"><i class="fas fa-check-double me-1"></i> Verified</span>
                                    ` : `
                                        <button class="btn btn-sm btn-cyber-primary py-1 px-2" onclick="confirmFromAdmin('${b.booking_code}')">
                                            <i class="fas fa-check me-1"></i> Verify Pass
                                        </button>
                                    `}
                                </td>
                            </tr>
                        `;
                    }).join('');
                }
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">Error loading data.</td></tr>`;
            }
        }

        async function confirmFromAdmin(code) {
            cyberAudio.playClick();
            if (!confirm(`Confirm ticket ${code}?`)) return;

            const formData = new FormData();
            formData.append('action', 'confirm');
            formData.append('booking_code', code);
            formData.append('staff_name', 'Admin Console');

            try {
                const res = await fetch('api/booking.php', { method: 'POST', body: formData });
                const json = await res.json();
                if (json.success) {
                    cyberAudio.playScanConfirm();
                    loadAdminBookings();
                } else {
                    cyberAudio.playError();
                    alert(json.message);
                }
            } catch (e) {
                alert('Connection error');
            }
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
        }
    </script>
</body>
</html>
