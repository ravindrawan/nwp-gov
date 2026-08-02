/**
 * QR Code & Manual Verification Scanner Script - Computer Clinic NWP
 */

let html5QrcodeScanner = null;

document.addEventListener('DOMContentLoaded', () => {
    initCameraScanner();
    initManualLookup();
});

// 1. Initialize HTML5 QR Code Camera Scanner
function initCameraScanner() {
    const readerDiv = document.getElementById('reader');
    if (!readerDiv) return;

    html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: { width: 250, height: 250 } },
        /* verbose= */ false
    );

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
}

function onScanSuccess(decodedText, decodedResult) {
    cyberAudio.playClick();
    console.log(`Scan result: ${decodedText}`);

    let searchCode = decodedText;
    try {
        const parsed = JSON.parse(decodedText);
        if (parsed.code) searchCode = parsed.code;
    } catch(e) {
        // Direct string code e.g. CLINIC-2026-8912
    }

    document.getElementById('search_query').value = searchCode;
    performLookup(searchCode);
}

function onScanFailure(error) {
    // Silent fail on frame scan without QR
}

// 2. Manual Token / Phone Lookup
function initManualLookup() {
    const form = document.getElementById('lookup-form');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        cyberAudio.playClick();
        const query = document.getElementById('search_query').value.trim();
        if (query) {
            performLookup(query);
        }
    });
}

// 3. Perform Lookup via API
async function performLookup(query) {
    const resultContainer = document.getElementById('scan-result-container');
    resultContainer.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-warning" role="status"></div>
            <p class="cyber-font text-orange mt-3">SEARCHING COMPUTER CLINIC DATABASE FOR '${query}'...</p>
        </div>
    `;

    try {
        const res = await fetch(`api/booking.php?action=lookup&query=${encodeURIComponent(query)}`);
        const data = await res.json();

        if (data.success && data.data && data.data.length > 0) {
            cyberAudio.playSuccess();
            renderScanResults(data.data);
        } else {
            cyberAudio.playError();
            resultContainer.innerHTML = `
                <div class="alert alert-danger bg-dark border-danger text-danger p-4 text-center">
                    <i class="fas fa-exclamation-triangle fs-2 mb-2"></i>
                    <h5 class="cyber-font">VERIFICATION FAILED</h5>
                    <p class="mb-0">${escapeHtml(data.message || 'අදාළ සායන ලියාපදිංචි විස්තර හමු නොවීය.')}</p>
                </div>
            `;
        }
    } catch (err) {
        cyberAudio.playError();
        resultContainer.innerHTML = `
            <div class="alert alert-danger bg-dark border-danger text-danger p-4 text-center">
                <i class="fas fa-wifi fs-2 mb-2"></i>
                <h5 class="cyber-font">NETWORK CONNECTION ERROR</h5>
            </div>
        `;
    }
}

// 4. Render Booking Ticket Results with Confirm Action Button
function renderScanResults(bookings) {
    const container = document.getElementById('scan-result-container');
    
    container.innerHTML = bookings.map(b => {
        const isConfirmed = b.status === 'CONFIRMED';
        
        return `
            <div class="ticket-pass mb-4">
                <div class="ticket-header">
                    <div>
                        <span class="badge-status ${isConfirmed ? 'status-confirmed' : 'status-pending'}">
                            <i class="fas ${isConfirmed ? 'fa-check-circle' : 'fa-clock'} me-1"></i> ${isConfirmed ? 'COMPUTER RECEIVED' : 'PENDING PICKUP'}
                        </span>
                        <h4 class="cyber-font text-white mt-2 mb-0">${escapeHtml(b.customer_name)}</h4>
                        <small class="text-orange fw-bold"><i class="fas fa-building me-1"></i>${escapeHtml(b.nic || 'දෙපාර්තමේන්තුව සඳහන් කර නොමැත')}</small>
                    </div>
                    <div class="text-end">
                        <span class="booking-code-badge">${escapeHtml(b.booking_code)}</span>
                        <p class="text-muted extra-small mb-0">CLINIC TOKEN ID</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="cyber-label">MOBILE PHONE NUMBER</span>
                        <h5 class="text-warning"><i class="fas fa-phone-alt me-2"></i>${escapeHtml(b.phone)}</h5>
                    </div>
                    <div class="col-md-6">
                        <span class="cyber-label">CLINIC REPAIR SERVICE</span>
                        <h5 class="text-white"><i class="${b.icon} text-orange me-2"></i>${escapeHtml(b.drop_title_si || b.drop_title)}</h5>
                    </div>
                    <div class="col-12">
                        <span class="cyber-label">COMPUTER MODEL & FAULT SYMPTOMS</span>
                        <div class="p-3 bg-dark rounded border border-warning">
                            <p class="small text-white mb-0 fw-bold"><i class="fas fa-laptop-medical text-danger me-2"></i>${escapeHtml(b.special_notes || 'දෝෂ විස්තරයක් නොමැත')}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top border-secondary d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        ${isConfirmed ? `
                            <small class="text-success"><i class="fas fa-check-double me-1"></i> RECEIVED AT: ${b.verified_at} by ${b.verified_by || 'Tech Staff'}</small>
                        ` : `
                            <small class="text-warning"><i class="fas fa-exclamation-circle me-1"></i> Awaiting officer arrival at canteen desk</small>
                        `}
                    </div>

                    ${isConfirmed ? `
                        <button class="btn btn-success btn-lg disabled">
                            <i class="fas fa-check-circle me-2"></i> COMPUTER RECEIVED FOR CLINIC
                        </button>
                    ` : `
                        <button class="btn btn-cyber-primary btn-lg" onclick="confirmTicketPass('${b.booking_code}')">
                            <i class="fas fa-laptop-medical me-2"></i> CONFIRM & RECEIVE COMPUTER
                        </button>
                    `}
                </div>
            </div>
        `;
    }).join('');
}

// 5. Confirm PC Reception Action
async function confirmTicketPass(bookingCode) {
    cyberAudio.playClick();

    if (!confirm(`ඔබට ${bookingCode} පරිගණක සායන ටෝකනය තහවුරු කර පරිගණකය අලුත්වැඩියාව සඳහා භාරගැනීමට අවශ්‍යද?`)) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('action', 'confirm');
        formData.append('booking_code', bookingCode);
        formData.append('staff_name', 'Digital Division Tech Desk');

        const res = await fetch('api/booking.php', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();

        if (data.success) {
            cyberAudio.playScanConfirm();
            alert('🎉 ' + data.message);
            performLookup(bookingCode); // Refresh status
        } else {
            cyberAudio.playError();
            alert(data.message || 'තහවුරු කිරීම අසාර්ථක විය.');
        }
    } catch (err) {
        cyberAudio.playError();
        alert('සම්බන්ධතා දෝෂයකි.');
    }
}
