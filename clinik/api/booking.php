<?php
/**
 * RESTful API Endpoint for Computer Clinic NWP Bookings & QR Verification
 */

require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $db = Database::getInstance()->getConnection();
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    switch ($action) {
        
        // 1. Fetch Computer Clinic Repair Services
        case 'get_drops':
            $stmt = $db->query("SELECT * FROM tuesday_drops ORDER BY id ASC");
            $drops = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $drops]);
            break;

        // 2. Submit New Computer Clinic Registration / Booking
        case 'create_booking':
            $dropId = intval($_POST['drop_id'] ?? 0);
            $customerName = trim($_POST['customer_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $nic = trim($_POST['nic'] ?? ''); // Department / Office Branch
            $quantity = max(1, intval($_POST['quantity'] ?? 1));
            $specialNotes = trim($_POST['special_notes'] ?? ''); // Computer model & fault description

            if (empty($customerName) || empty($phone) || $dropId <= 0) {
                echo json_encode(['success' => false, 'message' => 'කරුණාකර ඔබගේ නම, ෆෝන් අංකය සහ පරිගණක සේවාව තෝරන්න!']);
                exit;
            }

            // Verify service exists
            $stmtDrop = $db->prepare("SELECT * FROM tuesday_drops WHERE id = ?");
            $stmtDrop->execute([$dropId]);
            $drop = $stmtDrop->fetch();

            if (!$drop) {
                echo json_encode(['success' => false, 'message' => 'තෝරාගත් සේවාව පද්ධතියේ හමු නොවීය!']);
                exit;
            }

            // Generate unique Booking Code CLINIC-2026-XXXX
            $randomNum = rand(1000, 9999);
            $bookingCode = 'CLINIC-2026-' . $randomNum;

            // Check uniqueness
            $checkStmt = $db->prepare("SELECT COUNT(*) FROM tuesday_bookings WHERE booking_code = ?");
            $checkStmt->execute([$bookingCode]);
            if ($checkStmt->fetchColumn() > 0) {
                $bookingCode = 'CLINIC-2026-' . rand(10000, 99999);
            }

            // Insert Booking
            $stmtInsert = $db->prepare("
                INSERT INTO tuesday_bookings (booking_code, drop_id, customer_name, phone, nic, quantity, special_notes, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'PENDING', CURRENT_TIMESTAMP)
            ");
            $stmtInsert->execute([$bookingCode, $dropId, $customerName, $phone, $nic, $quantity, $specialNotes]);

            // Update booked quantity
            $stmtUpdateDrop = $db->prepare("UPDATE tuesday_drops SET booked_qty = booked_qty + ? WHERE id = ?");
            $stmtUpdateDrop->execute([$quantity, $dropId]);

            echo json_encode([
                'success' => true,
                'message' => 'ඔබගේ පරිගණක සායන (Computer Clinic) ලියාපදිංචිය සාර්ථකයි!',
                'booking' => [
                    'booking_code' => $bookingCode,
                    'customer_name' => $customerName,
                    'phone' => $phone,
                    'nic' => $nic,
                    'quantity' => $quantity,
                    'item_title' => $drop['title'],
                    'item_title_si' => $drop['title_si'],
                    'price_lkr' => $drop['price_lkr'],
                    'drop_time' => $drop['drop_time'],
                    'special_notes' => $specialNotes,
                    'status' => 'PENDING',
                    'created_at' => date('Y-m-d H:i:s'),
                    'qr_data' => $bookingCode
                ]
            ]);
            break;

        // 3. Lookup Booking by Token Code or Phone Number
        case 'lookup':
            $query = trim($_REQUEST['query'] ?? '');
            if (empty($query)) {
                echo json_encode(['success' => false, 'message' => 'සෙවීමට QR කේතය, බුකින් Token අංකය හෝ ෆෝන් අංකය ඇතුළත් කරන්න.']);
                exit;
            }

            $stmt = $db->prepare("
                SELECT b.*, d.title as drop_title, d.title_si as drop_title_si, d.price_lkr, d.drop_time, d.icon
                FROM tuesday_bookings b
                JOIN tuesday_drops d ON b.drop_id = d.id
                WHERE b.booking_code = ? OR b.phone = ? OR b.phone LIKE ?
                ORDER BY b.id DESC LIMIT 5
            ");
            $phoneLike = '%' . $query;
            $stmt->execute([$query, $query, $phoneLike]);
            $results = $stmt->fetchAll();

            if (empty($results)) {
                echo json_encode(['success' => false, 'message' => "'$query' අංකයට අදාළ සායන ලියාපදිංචි විස්තර හමු නොවීය."]);
            } else {
                echo json_encode(['success' => true, 'count' => count($results), 'data' => $results]);
            }
            break;

        // 4. Confirm / Verify Pass Action at Clinic Desk
        case 'confirm':
            $bookingCode = trim($_POST['booking_code'] ?? '');
            $staffName = trim($_POST['staff_name'] ?? 'Digital Division Tech');

            if (empty($bookingCode)) {
                echo json_encode(['success' => false, 'message' => 'බුකින් කේතය හිස් විය නොහැක!']);
                exit;
            }

            // Fetch current booking
            $stmtFetch = $db->prepare("SELECT b.*, d.title as drop_title FROM tuesday_bookings b JOIN tuesday_drops d ON b.drop_id = d.id WHERE b.booking_code = ?");
            $stmtFetch->execute([$bookingCode]);
            $booking = $stmtFetch->fetch();

            if (!$booking) {
                echo json_encode(['success' => false, 'message' => 'වලංගු නොවන බුකින් කේතයකි!']);
                exit;
            }

            if ($booking['status'] === 'CONFIRMED') {
                echo json_encode([
                    'success' => true,
                    'already_confirmed' => true,
                    'message' => 'මෙම පරිගණකය මීට පෙර භාරගෙන ඇත (Already Received & Confirmed)!',
                    'booking' => $booking
                ]);
                exit;
            }

            // Update to CONFIRMED
            $nowStr = date('Y-m-d H:i:s');
            $stmtConfirm = $db->prepare("
                UPDATE tuesday_bookings
                SET status = 'CONFIRMED', verified_at = ?, verified_by = ?
                WHERE booking_code = ?
            ");
            $stmtConfirm->execute([$nowStr, $staffName, $bookingCode]);

            // Add Audit Log
            $stmtLog = $db->prepare("INSERT INTO admin_logs (action, booking_code, staff_name, notes) VALUES ('RECEIVE_PC', ?, ?, ?)");
            $stmtLog->execute([$bookingCode, $staffName, "Computer received from {$booking['customer_name']} ({$booking['nic']})"]);

            $booking['status'] = 'CONFIRMED';
            $booking['verified_at'] = $nowStr;
            $booking['verified_by'] = $staffName;

            echo json_encode([
                'success' => true,
                'already_confirmed' => false,
                'message' => 'පරිගණකය සාර්ථකව පරීක්ෂාවට භාරගන්නා ලදී (PC RECEIVED FOR CLINIC)!',
                'booking' => $booking
            ]);
            break;

        // 5. Admin List & Stats
        case 'list':
            $statusFilter = trim($_GET['status'] ?? '');
            $sql = "SELECT b.*, d.title as drop_title, d.category FROM tuesday_bookings b JOIN tuesday_drops d ON b.drop_id = d.id";
            $params = [];

            if (!empty($statusFilter)) {
                $sql .= " WHERE b.status = ?";
                $params[] = $statusFilter;
            }

            $sql .= " ORDER BY b.id DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $bookings = $stmt->fetchAll();

            // Stats
            $totalBookings = $db->query("SELECT COUNT(*) FROM tuesday_bookings")->fetchColumn();
            $confirmedBookings = $db->query("SELECT COUNT(*) FROM tuesday_bookings WHERE status = 'CONFIRMED'")->fetchColumn();
            $pendingBookings = $db->query("SELECT COUNT(*) FROM tuesday_bookings WHERE status = 'PENDING'")->fetchColumn();
            $totalDrops = $db->query("SELECT COUNT(*) FROM tuesday_drops")->fetchColumn();

            echo json_encode([
                'success' => true,
                'stats' => [
                    'total' => $totalBookings,
                    'confirmed' => $confirmedBookings,
                    'pending' => $pendingBookings,
                    'drops' => $totalDrops
                ],
                'data' => $bookings
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid API Action requested']);
            break;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
}
