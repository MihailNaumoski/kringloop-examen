<?php
// Database connectie class - PDO singleton pattern
class Database {
    private static $instance = null;
    private $conn;

    // Database configuratie (gebruikt environment variables voor Docker)
    private $host;
    private $dbname;
    private $username;
    private $password;

    private function loadConfig() {
        $this->host = getenv('DB_HOST') ?: 'duurzaam-mysql';
        $this->dbname = getenv('DB_NAME') ?: 'duurzaam';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: 'root';
    }

    // Private constructor voor singleton
    private function __construct() {
        $this->loadConfig();
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $e) {
            die("Database connectie mislukt: " . $e->getMessage());
        }
    }

    // Singleton getInstance methode
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Haal PDO connectie op
    public function getConnection() {
        return $this->conn;
    }

    // Voorkom klonen
    private function __clone() {}

    // Voorkom unserialiseren
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
