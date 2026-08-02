<?php
/**
 * Database Connection Manager - Fixed Hardcoded for OpenShift
 */

class Database {
    private static $instance = null;
    private $pdo;
    private $driver = 'mysql';

    private function __construct() {
        $host     = 'meetmedb';
        $port     = '3306';
        $dbName   = 'clinik_db';
        $username = 'root';
        
        // OpenShift Environment password හෝ default empty
        $password = getenv('MYSQL_ROOT_PASSWORD') ?: (getenv('MYSQL_PASSWORD') ?: '');

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4";
            $this->pdo = new PDO($dsn, $username, $password, $options);
            $this->driver = 'mysql';
        } catch (PDOException $e) {
            // Password නැතුව බැරි වුණොත්, password එකක් නැතුව ආයේ try කරන්න
            try {
                $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4";
                $this->pdo = new PDO($dsn, $username, '', $options);
                $this->driver = 'mysql';
            } catch (PDOException $e2) {
                // Connection එක පත්තුවුණේ නැත්නම් Exact error එක Screen එකේ පෙන්වන්න
                die("<div style='padding:20px; background:#f8d7da; color:#721c24; font-family:sans-serif;'>
                        <h3>Database Connection Failed</h3>
                        <p><b>Error Details:</b> " . htmlspecialchars($e2->getMessage()) . "</p>
                     </div>");
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
