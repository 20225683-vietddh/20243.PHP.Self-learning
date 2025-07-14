<?php
/**
 * Cấu hình kết nối Database
 * Sử dụng Singleton pattern để đảm bảo chỉ có một kết nối duy nhất
 */

class Database {
    private static $instance = null;
    private $connection;
    
    // Thông tin kết nối database
    private $host = 'localhost';
    private $dbname = 'employee_management';
    private $username = 'root';
    private $password = '';
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            die("Lỗi kết nối database: " . $e->getMessage());
        }
    }
    
    // Singleton pattern - đảm bảo chỉ có một instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    // Lấy kết nối database
    public function getConnection() {
        return $this->connection;
    }
    
    // Ngăn chặn clone object
    private function __clone() {}
    
    // Ngăn chặn unserialize
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?> 