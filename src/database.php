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
} catch (PDOException $e) {
    error_log("Table creation failed: " . $e->getMessage());
}
?>