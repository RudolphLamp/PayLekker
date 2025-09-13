<?php
/**
 * PayLekker Database Configuration
 * Local database connection for hosting environment
 */

// Database configuration - UPDATE THESE VALUES FOR YOUR HOSTING
$db_config = [
    'host' => 'localhost',          // Usually 'localhost' for shared hosting
    'dbname' => 'paylekker_db',     // Your database name
    'username' => 'your_db_user',    // Your database username
    'password' => 'your_db_password', // Your database password
    'charset' => 'utf8mb4'
];

try {
    // Create PDO connection
    $dsn = "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset={$db_config['charset']}";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    // Test the connection
    $pdo->exec("SELECT 1");
    
} catch (PDOException $e) {
    // For debugging - remove in production
    error_log("Database connection failed: " . $e->getMessage());
    
    // You can uncomment this line for debugging, but remove it in production
    // die("Database connection failed: " . $e->getMessage());
    
    // For production, just set pdo to null
    $pdo = null;
}

/**
 * Create tables if they don't exist
 */
function createTables($pdo) {
    if (!$pdo) return false;
    
    try {
        // Users table
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
        
        // Transactions table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                type ENUM('transfer', 'deposit', 'withdrawal') NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                recipient_email VARCHAR(100),
                description TEXT,
                status ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
        
        // Budget categories table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS budget_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                name VARCHAR(100) NOT NULL,
                budget_amount DECIMAL(10,2) NOT NULL,
                spent_amount DECIMAL(10,2) DEFAULT 0.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Table creation failed: " . $e->getMessage());
        return false;
    }
}

// Auto-create tables if database is connected
if ($pdo) {
    createTables($pdo);
}
?>