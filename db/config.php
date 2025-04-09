<?php
class DatabaseConnection {
    private static $instance = null;
    private $connection;
    
    private $host = 'localhost';
    private $dbname = 'your_database';
    private $username = 'your_username';
    private $password = 'your_password';
    private $charset = 'utf8mb4';

    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    // Prevent cloning of the instance
    private function __clone() {}
}

// Usage example:
// try {
//     $db = DatabaseConnection::getInstance();
//     $conn = $db->getConnection();
// } catch (Exception $e) {
//     echo $e->getMessage();
// }
?>