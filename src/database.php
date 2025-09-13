<?php
/**
 * PayLekker API - Database Configuration
 * Connects to remote pay.sewdani.co.za database
 */

// Database configuration for pay.sewdani.co.za
define('DB_HOST', 'pay.sewdani.co.za');
define('DB_NAME', 'pnjdogwh_pay');
define('DB_USER', 'pnjdogwh_pay');
define('DB_PASS', 'Boris44$$$');

try {
    // Create PDO connection to remote database
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true, // Enable buffered queries to prevent the error
        PDO::ATTR_TIMEOUT => 30, // 30 second timeout for remote connection
    ]);
    
    // Test the connection with a simple query
    $test = $pdo->query("SELECT 1 as test");
    $test->fetchAll(); // Properly consume the result set
    
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

// Note: Use setup_database.php to create tables
// This file only handles the database connection
?>