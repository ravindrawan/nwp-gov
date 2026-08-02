<?php
/**
 * Database Connection Manager - Tuesday Drop Cyber Application
 * Dual Mode: MySQL (OpenShift/Local) with automatic SQLite fallback
 */

class Database {
    private static $instance = null;
    private $pdo;
    private $driver = 'mysql';

    private function __construct() {
        // OpenShift Environment variables හෝ Default OpenShift DB values
        $host     = getenv('DB_HOST') ?: 'meetmedb';
        $port     = getenv('DB_PORT') ?: '3306';
        $dbName   = getenv('DB_NAME') ?: 'clinik_db';
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // OpenShift/MySQL Database එකට Direct Connect වීම
            $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4";
            $this->pdo = new PDO($dsn, $username, $password, $options);
            $this->driver = 'mysql';
        } catch (Exception $e) {
            // MySQL සම්බන්ධතාවය අසාර්ථක වුවහොත් SQLite භාවිතයට මාරු වීම
            $dataDir = __DIR__ . '/../data';
            if (!is_dir($dataDir)) {
                mkdir($dataDir, 0777, true);
            }
            
            $sqlitePath = $dataDir . '/tuesday_booking.db';
            $this->pdo = new PDO("sqlite:" . $sqlitePath, null, null, $options);
            $this->driver = 'sqlite';
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function getDriver() {
        return $this->driver;
    }
}
