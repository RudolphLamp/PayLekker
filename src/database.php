<?php
/**
 * PayLekker API - Database Configuration
 * Connects to remote pay.sewdani.co.za database
 */

// Database configuration for pay.sewdani.co.za
$db_config = [
    'host' => 'pay.sewdani.co.za',
    'dbname' => 'pnjdogwh_pay',
    'username' => 'pnjdogwh_pay', 
    'password' => 'Boris44$$$',
    'charset' => 'utf8mb4',
    'port' => 3306
];

try {
    // Create PDO connection to remote database
    $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['dbname']};charset={$db_config['charset']}";
    
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 30, // 30 second timeout for remote connection
    ]);
    
    // Test the connection
    $pdo->exec("SELECT 1");
    
} catch (PDOException $e) {
    // Log the error for debugging
    error_log("Database connection failed: " . $e->getMessage());
    
    // Return error response for API
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed',
        'details' => $e->getMessage()
    ]);
    exit;
}

// Create users table if it doesn't exist
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            balance DECIMAL(10,2) DEFAULT 0.00,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Create transactions table for P2P transfers
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS transactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_id INT NOT NULL,
            recipient_id INT NULL,
            recipient_email VARCHAR(100) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            description TEXT,
            transaction_type ENUM('transfer', 'deposit', 'withdrawal') DEFAULT 'transfer',
            status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'completed',
            reference_number VARCHAR(50) UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE SET NULL
        )
    ");
    
    // Create budget categories table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS budget_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            category_name VARCHAR(100) NOT NULL,
            budget_amount DECIMAL(10,2) NOT NULL,
            spent_amount DECIMAL(10,2) DEFAULT 0.00,
            period ENUM('weekly', 'monthly', 'yearly') DEFAULT 'monthly',
            start_date DATE,
            end_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
    // Create chat history table (optional - for storing chat conversations)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS chat_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            message TEXT NOT NULL,
            response TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
} catch (PDOException $e) {
    error_log("Table creation failed: " . $e->getMessage());
}
?>