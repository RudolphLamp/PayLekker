<?php
/**
 * PayLekker Game System - User Wallet Update
 * Adds wallet_balance column to users table for game rewards
 */

require_once 'database.php';

try {
    echo "Adding wallet_balance column to users table...\n";
    
    // Check if column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'wallet_balance'");
    if ($stmt->rowCount() == 0) {
        // Add wallet_balance column
        $pdo->exec("
            ALTER TABLE users 
            ADD COLUMN wallet_balance DECIMAL(10,2) DEFAULT 0.00 
            COMMENT 'Game rewards and wallet balance'
        ");
        echo "✅ wallet_balance column added successfully\n";
    } else {
        echo "✅ wallet_balance column already exists\n";
    }
    
    echo "User wallet system ready for game rewards! 🎉\n";
    
} catch (PDOException $e) {
    echo "❌ Error updating users table: " . $e->getMessage() . "\n";
}
?>