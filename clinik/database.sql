-- Computer Clinic NWP - Royal Orange Cyber Edition Database Schema
-- Database Name: tuesday_booking_db

CREATE DATABASE IF NOT EXISTS `tuesday_booking_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tuesday_booking_db`;

DROP TABLE IF EXISTS `tuesday_bookings`;
DROP TABLE IF EXISTS `tuesday_drops`;
DROP TABLE IF EXISTS `admin_logs`;

-- 1. Computer Clinic Repair Categories Table
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

-- 2. Officer Computer Clinic Bookings Table
CREATE TABLE IF NOT EXISTS `tuesday_bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_code` VARCHAR(50) NOT NULL UNIQUE,
  `drop_id` INT NOT NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `nic` VARCHAR(50) DEFAULT NULL, -- Department / Office Branch
  `quantity` INT DEFAULT 1,
  `special_notes` TEXT DEFAULT NULL, -- Computer model & Fault description
  `status` VARCHAR(50) DEFAULT 'PENDING', -- PENDING, CONFIRMED, REPAIRED, CANCELLED
  `verified_at` DATETIME DEFAULT NULL,
  `verified_by` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`drop_id`) REFERENCES `tuesday_drops`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Audit Logs
CREATE TABLE IF NOT EXISTS `admin_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `action` VARCHAR(100) NOT NULL,
  `booking_code` VARCHAR(50) DEFAULT NULL,
  `staff_name` VARCHAR(100) DEFAULT 'Digital Division Tech',
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data matching the Poster details
INSERT INTO `tuesday_drops` (`drop_code`, `title`, `title_si`, `category`, `description`, `price_lkr`, `stock_qty`, `booked_qty`, `drop_time`, `status`, `icon`, `image_badge`) VALUES
('CLINIC-SERVICE-01', 'Desktop PC Diagnostic & Repair', 'ඩෙස්ක්ටොප් පරිගණක අලුත්වැඩියාව & පරීක්ෂාව', 'Desktop', 'Power supply faults, RAM/HDD diagnostics, motherboard issues, and casing maintenance.', 0.00, 40, 12, '2026 අගෝස්තු 04 (පෙ.ව. 9.30 - 10.30)', 'ACTIVE', 'fas fa-desktop', 'FREE SERVICE'),
('CLINIC-SERVICE-02', 'Laptop Computer Maintenance', 'ලැප්ටොප් පරිගණක නඩත්තුව & දෝෂ නිරාකරණය', 'Laptop', 'Screen replacement, battery diagnostics, fan cleaning, thermal paste, and keyboard replacement.', 0.00, 30, 8, '2026 අගෝස්තු 04 (පෙ.ව. 9.30 - 10.30)', 'ACTIVE', 'fas fa-laptop', 'FREE SERVICE'),
('CLINIC-SERVICE-03', 'Software, OS & Virus Remediation', 'මෘදුකාංග, වයිරස් ඉවත් කිරීම & OS සුසර කිරීම', 'Software', 'Windows OS reinstall, virus & malware cleanup, official government software & driver setup.', 0.00, 50, 15, '2026 අගෝස්තු 04 (පෙ.ව. 9.30 - 10.30)', 'ACTIVE', 'fas fa-bug', 'FREE SERVICE'),
('CLINIC-SERVICE-04', 'Monitor, UPS & Printer Diagnostics', 'මොනිටර්, UPS & ප්‍රින්ටර් සායනය', 'Peripheral', 'Display flickering, power board repairs, UPS battery replacement, printer network configuration.', 0.00, 25, 5, '2026 අගෝස්තු 04 (පෙ.ව. 9.30 - 10.30)', 'ACTIVE', 'fas fa-print', 'FREE SERVICE');

INSERT INTO `tuesday_bookings` (`booking_code`, `drop_id`, `customer_name`, `phone`, `nic`, `quantity`, `special_notes`, `status`, `created_at`) VALUES
('CLINIC-2026-8912', 1, 'කසුන් අමරසිංහ', '0771234567', 'ප්‍රධාන ලේකම් කාර්යාලය', 1, 'Dell Desktop PC - Power turns on but no display signal', 'PENDING', NOW()),
('CLINIC-2026-4431', 2, 'නිලූකා ජයවර්ධන', '0719876543', 'පළාත් ආදායම් දෙපාර්තමේන්තුව', 1, 'HP Laptop - Very slow performance & battery draining fast', 'CONFIRMED', NOW());
