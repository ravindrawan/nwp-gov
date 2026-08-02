/**
 * Main Application Script - Computer Clinic NWP (Digital Division)
 */

document.addEventListener('DOMContentLoaded', () => {
    initCountdown();
    loadTuesdayDrops();
    initBookingForm();
});

// 1. Calculate Countdown Timer to August 4, 2026 09:30 AM
function initCountdown() {
    // Target Event Date: 2026-08-04 09:30:00 AM
    const targetDate = new Date(2026, 7, 4, 9, 30, 0); // Month is 0-indexed (7 = August)

    function updateTimer() {
        const now = new Date();
        const diff = targetDate - now;

        if (diff <= 0) {
            document.getElementById('cd-days').innerText = '00';
            document.getElementById('cd-hours').innerText = '00';
            document.getElementById('cd-mins').innerText = '00';
            document.getElementById('cd-secs').innerText = '00';
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
        const mins = Math.floor((diff / 1000 / 60) % 60);
        const secs = Math.floor((diff / 1000) % 60);

        document.getElementById('cd-days').innerText = days.toString().padStart(2, '0');
        document.getElementById('cd-hours').innerText = hours.toString().padStart(2, '0');
        document.getElementById('cd-mins').innerText = mins.toString().padStart(2, '0');
        document.getElementById('cd-secs').innerText = secs.toString().padStart(2, '0');
    }

    updateTimer();
    setInterval(updateTimer, 1000);
}

// 2. Fetch and Render Clinic Repair Services Catalogue
async function loadTuesdayDrops() {
    const container = document.getElementById('drops-container');
    if (!container) return;

    try {
        const res = await fetch('api/booking.php?action=get_drops');
        const json = await res.json();

        if (json.success && json.data.length > 0) {
            container.innerHTML = json.data.map(drop => `
                <div class="col-md-6 col-lg-3">
                    <div class="drop-card" onclick="selectDropItem(${drop.id}, '${escapeHtml(drop.title)}', '${escapeHtml(drop.title_si || drop.title)}')">
                        <span class="drop-badge">${escapeHtml(drop.image_badge)}</span>
                        <div class="drop-icon"><i class="${drop.icon}"></i></div>
                        <div>
                            <span class="text-uppercase text-warning fw-bold small">${escapeHtml(drop.category)}</span>
                            <h5 class="cyber-font text-white mt-1 mb-2" style="font-size: 1.1rem;">${escapeHtml(drop.title)}</h5>
                            <p class="text-orange small fw-bold mb-2">${escapeHtml(drop.title_si || '')}</p>
                            <p class="text-muted small">${escapeHtml(drop.description)}</p>
                        </div>
                        <div class="mt-3 pt-3 border-top border-secondary d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block text-muted extra-small">SERVICE FEE</span>
                                <strong class="text-green fs-5">FREE (නොමිලේ)</strong>
                            </div>
                            <button class="btn btn-sm btn-cyber-outline">BOOK SLOT</button>
                        </div>
                    </div>
                </div>
            `).join('');

            // Populate select dropdown in form
            const selectEl = document.getElementById('drop_id');
            if (selectEl) {
                selectEl.innerHTML = '<option value="">-- සේවාව තෝරන්න (Select Repair Service) --</option>' +
                    json.data.map(drop => `<option value="${drop.id}">${escapeHtml(drop.title_si || drop.title)} [${escapeHtml(drop.category)}]</option>`).join('');
            }
        }
    } catch (err) {
        console.error('Failed to load drops:', err);
    }
}

function selectDropItem(id, title, titleSi) {
    cyberAudio.playClick();
    const selectEl = document.getElementById('drop_id');
    if (selectEl) {
        selectEl.value = id;
    }
    // Scroll smoothly to form
    const bookingSec = document.getElementById('booking-section');
    if (bookingSec) {
        bookingSec.scrollIntoView({ behavior: 'smooth' });
    }
}

// 3. Handle Booking Form Submit
function initBookingForm() {
    const form = document.getElementById('booking-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        cyberAudio.playClick();

        const btnSubmit = document.getElementById('btn-submit-booking');
        const originalText = btnSubmit.innerHTML;
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> GENERATING CLINIC TOKEN...';

        const formData = new FormData(form);
        formData.append('action', 'create_booking');

        try {
            const res = await fetch('api/booking.php', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;

            if (data.success) {
                cyberAudio.playSuccess();
                renderDigitalTicketModal(data.booking);
                form.reset();
            } else {
                cyberAudio.playError();
                alert(data.message || 'දෝෂයක් සිදු විය! කරුණාකර නැවත උත්සාහ කරන්න.');
            }
        } catch (err) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalText;
            cyberAudio.playError();
            alert('සර්වර් සේවා සම්බන්ධතා දෝෂයකි. කරුණාකර ජාල සම්බන්ධතාවය පරීක්ෂා කරන්න.');
        }
    });
}

// 4. Render Dynamic Ticket Pass Modal with QRCode.js
function renderDigitalTicketModal(booking) {
    document.getElementById('modal-booking-code').innerText = booking.booking_code;
    document.getElementById('modal-customer-name').innerText = booking.customer_name;
    document.getElementById('modal-phone').innerText = booking.phone;
    document.getElementById('modal-item-title').innerText = booking.item_title_si || booking.item_title;
    document.getElementById('modal-item-si').innerText = booking.nic || '-';
    document.getElementById('modal-drop-time').innerText = booking.drop_time;

    // Generate QR Code into container
    const qrContainer = document.getElementById('modal-qr-container');
    qrContainer.innerHTML = '';

    new QRCode(qrContainer, {
        text: booking.booking_code,
        width: 180,
        height: 180,
        colorDark: "#08070a",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    // Show Bootstrap Modal
    const modalEl = document.getElementById('ticketModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// Helper to escape HTML string
function escapeHtml(str) {
    if (!str) return '';
    return str.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
