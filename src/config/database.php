<?php
/**
 * PayLekker API - Database Configuration
 * Database connection and configuration for MySQL
 */

class Database {
    private $host = 'localhost';  // Will be the hosting server when deployed
    private $db_name = 'pnjdogwh_pay';
    private $username = 'pnjdogwh_pay';
    private $password = 'Boris44$$$';
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            
            // Set PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            return null;
        }
        
        return $this->conn;
    }
}
?>