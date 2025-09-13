<?php
/**
 * Database Migration - Add mini_game to challenge_type ENUM and create mini_game_progress table
 */

require_once 'database.php';

try {
    echo "Updating challenge_type ENUM to include 'mini_game'...\n";
    
    $pdo->exec("ALTER TABLE game_challenges MODIFY COLUMN challenge_type ENUM('daily', 'weekly', 'one_time', 'milestone', 'mini_game') DEFAULT 'daily'");
    
    echo "Successfully updated challenge_type ENUM\n";
    
    // Add target_value column if it doesn't exist
    echo "Adding target_value column if missing...\n";
    try {
        $pdo->exec("ALTER TABLE game_challenges ADD COLUMN target_value INT DEFAULT 0");
        echo "Added target_value column\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "target_value column already exists\n";
        } else {
            throw $e;
        }
    }
    
    // Make requirements column nullable
    echo "Making requirements column nullable...\n";
    try {
        $pdo->exec("ALTER TABLE game_challenges MODIFY COLUMN requirements JSON NULL");
        echo "Updated requirements column\n";
    } catch (Exception $e) {
        echo "Requirements column update failed (may not exist): " . $e->getMessage() . "\n";
    }
    
    // Create mini_game_progress table if it doesn't exist
    echo "Creating mini_game_progress table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS mini_game_progress (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            current_score INT DEFAULT 0,
            high_score INT DEFAULT 0,
            total_games_played INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user (user_id)
        )
    ");
    
    echo "Successfully created mini_game_progress table\n";
    
    // Clean up any existing broken records
    $pdo->exec("DELETE FROM game_challenges WHERE challenge_type = 'mini_game'");
    
    echo "Database migration completed successfully!\n";
    echo "\nYou can now test the game page - the mini game challenges should load without errors.\n";
    
} catch (Exception $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
    exit(1);
}
?>