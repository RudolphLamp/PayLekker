<?php
/**
 * PayLekker API - Database Setup
 * Creates all required tables for the PayLekker application
 */

require_once __DIR__ . '/database.php';

class DatabaseSetup {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function createTables() {
        if (!$this->db) {
            echo "Database connection failed!\n";
            return false;
        }
        
        try {
            // Users table
            $this->createUsersTable();
            
            // Sessions table for JWT token management
            $this->createSessionsTable();
            
            // Transactions table
            $this->createTransactionsTable();
            
            // Budgets table
            $this->createBudgetsTable();
            
            echo "All tables created successfully!\n";
            return true;
            
        } catch (PDOException $e) {
            echo "Error creating tables: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    private function createUsersTable() {
        $query = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            balance DECIMAL(10,2) DEFAULT 0.00,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($query);
        echo "Users table created.\n";
    }
    
    private function createSessionsTable() {
        $query = "CREATE TABLE IF NOT EXISTS sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            token_hash VARCHAR(255) NOT NULL,
            expires_at TIMESTAMP NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_token_hash (token_hash),
            INDEX idx_expires (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($query);
        echo "Sessions table created.\n";
    }
    
    private function createTransactionsTable() {
        $query = "CREATE TABLE IF NOT EXISTS transactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_id INT NOT NULL,
            recipient_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            description TEXT,
            status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
            transaction_type ENUM('transfer', 'deposit', 'withdrawal') DEFAULT 'transfer',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_sender (sender_id),
            INDEX idx_recipient (recipient_id),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($query);
        echo "Transactions table created.\n";
    }
    
    private function createBudgetsTable() {
        $query = "CREATE TABLE IF NOT EXISTS budgets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            category VARCHAR(100) NOT NULL,
            budget_amount DECIMAL(10,2) NOT NULL,
            spent_amount DECIMAL(10,2) DEFAULT 0.00,
            budget_period ENUM('weekly', 'monthly', 'yearly') DEFAULT 'monthly',
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_category (user_id, category),
            INDEX idx_period (budget_period)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($query);
        echo "Budgets table created.\n";
    }
}

// Run the setup if this file is executed directly
if (php_sapi_name() === 'cli') {
    $setup = new DatabaseSetup();
    $setup->createTables();
}
?>