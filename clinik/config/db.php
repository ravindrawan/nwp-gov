<?php
/**
 * Database Connection Manager - Tuesday Drop Cyber Application
 * Dual Mode: MySQL with automatic SQLite fallback
 */

class Database {
    private static $instance = null;
    private $pdo;
    private $driver = 'mysql';

    private function __construct() {
        $host = '127.0.0.1';
        $port = '3306';
        $dbName = 'tuesday_booking_db';
        $username = 'root';
        $password = '';

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            // Try connecting to MySQL
            $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
            $tempPdo = new PDO($dsn, $username, $password, $options);
            
            // Create database if not exists
            $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $tempPdo->exec("USE `$dbName` ");
            
            $this->pdo = $tempPdo;
            $this->driver = 'mysql';
        } catch (Exception $e) {
            // Fallback to SQLite if MySQL connection fails
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
