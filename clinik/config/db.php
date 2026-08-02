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
        $host     = getenv('DB_HOST') ?: 'meetmedb';
        $port     = getenv('DB_PORT') ?: '3306';
        $dbName   = getenv('DB_NAME') ?: 'clinik_db';
        
        // OpenShift Configured Credentials
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: 'RaviRootPassword';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // 1. Root User ලෙස Connect වීමට උත්සාහ කිරීම
            $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4";
            $this->pdo = new PDO($dsn, $username, $password, $options);
            $this->driver = 'mysql';
        } catch (PDOException $e) {
            try {
                // 2. Fallback: admin User ලෙස Connect වීමට උත්සාහ කිරීම
                $this->pdo = new PDO($dsn, 'admin', 'RaviDb@2026', $options);
                $this->driver = 'mysql';
            } catch (PDOException $e2) {
                // 3. Fallback to SQLite if MySQL fail
                $dataDir = __DIR__ . '/../data';
                if (!is_dir($dataDir)) {
                    mkdir($dataDir, 0777, true);
                }
                $sqlitePath = $dataDir . '/tuesday_booking.db';
                $this->pdo = new PDO("sqlite:" . $sqlitePath, null, null, $options);
                $this->driver = 'sqlite';
            }
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
