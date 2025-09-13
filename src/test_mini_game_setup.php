<?php
/**
 * Test Mini Game Database Setup
 */

require_once 'database.php';
require_once 'jwt.php';

// Create a test token for user ID 1 (adjust as needed)
$test_user_id = 1;

try {
    echo "Testing mini game database setup...\n\n";
    
    // 1. Test database connection
    echo "1. Testing database connection... ";
    $stmt = $pdo->query("SELECT 1");
    echo "✓ Connected\n";
    
    // 2. Check if challenge_type enum supports mini_game
    echo "2. Checking challenge_type enum... ";
    $stmt = $pdo->query("SHOW COLUMNS FROM game_challenges LIKE 'challenge_type'");
    $result = $stmt->fetch();
    if (strpos($result['Type'], 'mini_game') !== false) {
        echo "✓ Contains 'mini_game'\n";
    } else {
        echo "✗ Missing 'mini_game' - running migration...\n";
        $pdo->exec("ALTER TABLE game_challenges MODIFY COLUMN challenge_type ENUM('daily', 'weekly', 'one_time', 'milestone', 'mini_game') DEFAULT 'daily'");
        echo "✓ Migration applied\n";
    }
    
    // 3. Check mini_game_progress table
    echo "3. Checking mini_game_progress table... ";
    try {
        $stmt = $pdo->query("DESCRIBE mini_game_progress");
        echo "✓ Table exists\n";
    } catch (Exception $e) {
        echo "✗ Missing - creating table...\n";
        $pdo->exec("
            CREATE TABLE mini_game_progress (
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
        echo "✓ Table created\n";
    }
    
    // 4. Test creating mini game challenges
    echo "4. Testing mini game challenges creation... ";
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM game_challenges WHERE challenge_type = 'mini_game'");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        echo "Creating challenges... ";
        $stmt = $pdo->prepare("
            INSERT INTO game_challenges 
            (title, description, challenge_type, target_value, money_reward, difficulty, expires_at, is_active)
            VALUES (?, ?, ?, ?, ?, ?, NULL, 1)
        ");
        
        $challenges = [
            ['First Flight', 'Score 5 points in Flappy Bird', 'mini_game', 5, 1.00, 'easy'],
            ['Getting Better', 'Score 10 points in Flappy Bird', 'mini_game', 10, 2.50, 'easy']
        ];
        
        foreach ($challenges as $challenge) {
            $stmt->execute($challenge);
        }
        echo "✓ Created " . count($challenges) . " challenges\n";
    } else {
        echo "✓ Found $count existing challenges\n";
    }
    
    // 5. Test API endpoint
    echo "5. Testing game API... ";
    include 'game.php';
    echo "✓ Game API loaded successfully\n";
    
    echo "\n✅ All tests passed! Mini game system should work now.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
?>