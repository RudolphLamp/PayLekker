<?php
/**
 * PayLekker Database Configuration - Remote Database Connection
 * Connects to the database hosted on pay.sewdani.co.za
 */

// Remote database configuration for pay.sewdani.co.za
$db_config = [
    'host' => 'pay.sewdani.co.za',      // Remote database host
    'dbname' => 'pnjdogwh_pay',         // Database name on remote server
    'username' => 'pnjdogwh_pay',       // Database username
    'password' => 'Boris44$$$',         // Database password
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
    
    // Test the remote connection
    $pdo->exec("SELECT 1");
    
} catch (PDOException $e) {
    // Log the error for debugging
    error_log("Remote database connection failed: " . $e->getMessage());
    
    // For debugging - uncomment this line to see the error
    // die("Database connection failed: " . $e->getMessage());
    
    // Set pdo to null if connection fails
    $pdo = null;
}

/**
 * Note: Tables should already exist on the remote database.
 * If you need to create tables, do it directly on pay.sewdani.co.za
 */
?>