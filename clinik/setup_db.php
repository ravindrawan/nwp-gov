<?php
/**
 * Setup & Initialize Computer Clinic Database (MySQL or SQLite)
 */

require_once __DIR__ . '/config/db.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $db = Database::getInstance()->getConnection();
    $driver = Database::getInstance()->getDriver();

    echo "<div style='font-family: monospace; background: #08070a; color: #ff6600; padding: 20px; border-radius: 8px; max-width: 800px; margin: 40px auto; border: 1px solid #ff6600; box-shadow: 0 0 20px rgba(255,102,0,0.3);'>";
    echo "<h2 style='color: #ffe600; margin-top:0;'>⚡ COMPUTER CLINIC NWP - DATABASE INITIALIZER</h2>";
    echo "<p>Connected Driver: <strong>" . strtoupper($driver) . "</strong></p>";

    if ($driver === 'mysql') {
        $db->exec("
            CREATE TABLE IF NOT EXISTS `tuesday_drops` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `drop_code` VARCHAR(50) NOT NULL UNIQUE,
              `title` VARCHAR(255) NOT NULL,
              `title_si` VARCHAR(255) DEFAULT NULL,
              `category` VARCHAR(100) NOT NULL DEFAULT 'Hardware Repair',
              `description` TEXT NOT NULL,
              `price_lkr` DECIMAL(10,2) DEFAULT 0.00,
              `stock_qty` INT DEFAULT 50,
              `booked_qty` INT DEFAULT 0,
              `drop_time` VARCHAR(100) DEFAULT '2026 අගෝස්තු 04 (පෙ.ව. 9.30 - 10.30)',
              `status` VARCHAR(50) DEFAULT 'ACTIVE',
              `icon` VARCHAR(100) DEFAULT 'fas fa-desktop',
              `image_badge` VARCHAR(50) DEFAULT 'CLINIC SERVICE',
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `tuesday_bookings` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `booking_code` VARCHAR(50) NOT NULL UNIQUE,
              `drop_id` INT NOT NULL,
              `customer_name` VARCHAR(150) NOT NULL,
              `phone` VARCHAR(30) NOT NULL,
              `nic` VARCHAR(100) DEFAULT NULL,
              `quantity` INT DEFAULT 1,
              `special_notes` TEXT DEFAULT NULL,
              `status` VARCHAR(50) DEFAULT 'PENDING',
              `verified_at` DATETIME DEFAULT NULL,
              `verified_by` VARCHAR(100) DEFAULT NULL,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS `admin_logs` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `action` VARCHAR(100) NOT NULL,
              `booking_code` VARCHAR(50) DEFAULT NULL,
              `staff_name` VARCHAR(100) DEFAULT 'Digital Division Tech',
              `notes` TEXT DEFAULT NULL,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    } else {
        $db->exec("
            CREATE TABLE IF NOT EXISTS tuesday_drops (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              drop_code TEXT NOT NULL UNIQUE,
              title TEXT NOT NULL,
              title_si TEXT DEFAULT NULL,
              category TEXT NOT NULL DEFAULT 'Hardware Repair',
              description TEXT NOT NULL,
              price_lkr REAL DEFAULT 0.00,
              stock_qty INTEGER DEFAULT 50,
              booked_qty INTEGER DEFAULT 0,
              drop_time TEXT DEFAULT '2026 අගෝස්තු 04 (පෙ.ව. 9.30 - 10.30)',
              status TEXT DEFAULT 'ACTIVE',
              icon TEXT DEFAULT 'fas fa-desktop',
              image_badge TEXT DEFAULT 'CLINIC SERVICE',
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS tuesday_bookings (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              booking_code TEXT NOT NULL UNIQUE,
              drop_id INTEGER NOT NULL,
              customer_name TEXT NOT NULL,
              phone TEXT NOT NULL,
              nic TEXT DEFAULT NULL,
              quantity INTEGER DEFAULT 1,
              special_notes TEXT DEFAULT NULL,
              status TEXT DEFAULT 'PENDING',
              verified_at DATETIME DEFAULT NULL,
              verified_by TEXT DEFAULT NULL,
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS admin_logs (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              action TEXT NOT NULL,
              booking_code TEXT DEFAULT NULL,
              staff_name TEXT DEFAULT 'Digital Division Tech',
              notes TEXT DEFAULT NULL,
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    // Re-seed drops if empty
    $db->exec("DELETE FROM tuesday_drops");
    $stmt = $db->prepare("INSERT INTO tuesday_drops (drop_code, title, title_si, category, description, price_lkr, stock_qty, booked_qty, drop_time, status, icon, image_badge) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $seeds = [
        ['CLINIC-SERVICE-01', 'Desktop PC Diagnostic & Repair', 'ඩෙස්ක්ටොප් පරිගණක අලුත්වැඩියාව & පරීක්ෂාව', 'Desktop', 'Power supply faults, RAM/HDD diagnostics, motherboard issues, and casing maintenance.', 0.00, 40, 12, '2026 අගෝස්තු 04 (පෙ.ව. 9.30 - 10.30)', 'ACTIVE', 'fas fa-desktop', 'FREE SERVICE'],
        ['CLINIC-SERVICE-02', 'Laptop Computer Maintenance', 'ලැප්ටොප් පරිගණක නඩත්තුව & දෝෂ නිරාකරණය', 'Laptop', 'Screen replacement, battery diagnostics, fan cleaning, thermal paste, and keyboard replacement.', 0.00, 30, 8, '2026 අගෝස්තු 04 (පෙ.ව. 9.30 - 10.30)', 'ACTIVE', 'fas fa-laptop', 'FREE SERVICE'],
        ['CLINIC-SERVICE-03', 'Software, OS & Virus Remediation', 'මෘදුකාංග, වයිරස් ඉවත් කිරීම & OS සුසර කිරීම', 'Software', 'Windows OS reinstall, virus & malware cleanup, official government software & driver setup.', 0.00, 50, 15, '2026 අගෝස්තු 04 (පෙ.ව. 9.30 - 10.30)', 'ACTIVE', 'fas fa-bug', 'FREE SERVICE'],
        ['CLINIC-SERVICE-04', 'Monitor, UPS & Printer Diagnostics', 'මොනිටර්, UPS & ප්‍රින්ටර් සායනය', 'Peripheral', 'Display flickering, power board repairs, UPS battery replacement, printer network configuration.', 0.00, 25, 5, '2026 අගෝස්තු 04 (පෙ.ව. 9.30 - 10.30)', 'ACTIVE', 'fas fa-print', 'FREE SERVICE']
    ];

    foreach ($seeds as $seed) {
        $stmt->execute($seed);
    }

    // Re-seed sample bookings
    $db->exec("DELETE FROM tuesday_bookings");
    $stmtBook = $db->prepare("INSERT INTO tuesday_bookings (booking_code, drop_id, customer_name, phone, nic, quantity, special_notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtBook->execute(['CLINIC-2026-8912', 1, 'කසුන් අමරසිංහ', '0771234567', 'ප්‍රධාන ලේකම් කාර්යාලය', 1, 'Dell Desktop PC - Power turns on but no display signal', 'PENDING']);
    $stmtBook->execute(['CLINIC-2026-4431', 2, 'නිලූකා ජයවර්ධන', '0719876543', 'පළාත් ආදායම් දෙපාර්තමේන්තුව', 1, 'HP Laptop - Very slow performance & battery draining fast', 'CONFIRMED']);

    echo "<p style='color:#00ff88;'>✔️ Computer Clinic tables & services initialized successfully.</p>";
    echo "<h3 style='color:#ff6600; margin-bottom: 5px;'>🚀 SETUP COMPLETE!</h3>";
    echo "<p><a href='index.php' style='color:#ffe600; text-decoration: underline; font-weight:bold;'>Go to Computer Clinic Main Portal &rarr;</a></p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div style='color:red; background:#1e0000; padding:20px; border:1px solid red; font-family:monospace;'>";
    echo "<h3>SETUP ERROR</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
