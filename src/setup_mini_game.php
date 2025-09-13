<?php
/**
 * Setup Mini Game Database
 * Creates necessary tables and data for the Flappy Bird mini game
 */

require_once 'database.php';

echo "Setting up mini game database...\n";

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // 1. First, let's check the current structure of game_challenges table
    echo "Checking game_challenges table structure...\n";
    
    $stmt = $pdo->query("DESCRIBE game_challenges");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Current columns: " . implode(', ', $columns) . "\n";
    
    // 2. Add missing columns to game_challenges table if they don't exist
    if (!in_array('target_value', $columns)) {
        echo "Adding target_value column...\n";
        $pdo->exec("ALTER TABLE game_challenges ADD COLUMN target_value INT DEFAULT 1");
    }
    
    if (!in_array('challenge_type', $columns)) {
        echo "Adding challenge_type column...\n";
        $pdo->exec("ALTER TABLE game_challenges ADD COLUMN challenge_type VARCHAR(50) DEFAULT 'general'");
    }
    
    if (!in_array('difficulty', $columns)) {
        echo "Adding difficulty column...\n";
        $pdo->exec("ALTER TABLE game_challenges ADD COLUMN difficulty VARCHAR(20) DEFAULT 'easy'");
    }
    
    if (!in_array('active', $columns)) {
        echo "Adding active column...\n";
        $pdo->exec("ALTER TABLE game_challenges ADD COLUMN active TINYINT(1) DEFAULT 1");
    }
    
    // 3. Create mini_game_progress table
    echo "Creating mini_game_progress table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS mini_game_progress (
            id INT PRIMARY KEY AUTO_INCREMENT,
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
    echo "✓ Mini game progress table created\n";
    
    // 4. Check if mini game challenges already exist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM game_challenges WHERE challenge_type = 'mini_game'");
    $stmt->execute();
    $existingCount = $stmt->fetchColumn();
    
    if ($existingCount == 0) {
        echo "Adding mini game challenges...\n";
        
        // 5. Insert mini game challenges
        $challenges = [
            [
                'title' => 'First Flight',
                'description' => 'Score 5 points in Flappy Bird',
                'challenge_type' => 'mini_game',
                'target_value' => 5,
                'reward_type' => 'money',
                'reward_amount' => 1.00,
                'difficulty' => 'easy',
                'active' => 1
            ],
            [
                'title' => 'Getting Better',
                'description' => 'Score 10 points in Flappy Bird',
                'challenge_type' => 'mini_game',
                'target_value' => 10,
                'reward_type' => 'money',
                'reward_amount' => 2.50,
                'difficulty' => 'easy',
                'active' => 1
            ],
            [
                'title' => 'Steady Flyer',
                'description' => 'Score 15 points in Flappy Bird',
                'challenge_type' => 'mini_game',
                'target_value' => 15,
                'reward_type' => 'money',
                'reward_amount' => 5.00,
                'difficulty' => 'medium',
                'active' => 1
            ],
            [
                'title' => 'Skilled Pilot',
                'description' => 'Score 20 points in Flappy Bird',
                'challenge_type' => 'mini_game',
                'target_value' => 20,
                'reward_type' => 'money',
                'reward_amount' => 10.00,
                'difficulty' => 'medium',
                'active' => 1
            ],
            [
                'title' => 'Ace Flyer',
                'description' => 'Score 30 points in Flappy Bird',
                'challenge_type' => 'mini_game',
                'target_value' => 30,
                'reward_type' => 'money',
                'reward_amount' => 20.00,
                'difficulty' => 'hard',
                'active' => 1
            ],
            [
                'title' => 'Bird Master',
                'description' => 'Score 50 points in Flappy Bird',
                'challenge_type' => 'mini_game',
                'target_value' => 50,
                'reward_type' => 'money',
                'reward_amount' => 50.00,
                'difficulty' => 'expert',
                'active' => 1
            ]
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO game_challenges 
            (title, description, challenge_type, target_value, reward_type, reward_amount, difficulty, active, expires_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULL)
        ");
        
        foreach ($challenges as $challenge) {
            $stmt->execute([
                $challenge['title'],
                $challenge['description'],
                $challenge['challenge_type'],
                $challenge['target_value'],
                $challenge['reward_type'],
                $challenge['reward_amount'],
                $challenge['difficulty'],
                $challenge['active']
            ]);
        }
        
        echo "✓ Mini game challenges added (" . count($challenges) . " challenges)\n";
    } else {
        echo "✓ Mini game challenges already exist ($existingCount challenges found)\n";
    }
    
    // 6. Create game_rewards table if it doesn't exist
    echo "Creating game_rewards table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS game_rewards (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            reward_type VARCHAR(50) NOT NULL,
            amount DECIMAL(10,2) DEFAULT 0,
            reason TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_rewards (user_id, created_at)
        )
    ");
    echo "✓ Game rewards table created\n";
    
    // Commit transaction
    $pdo->commit();
    
    echo "\n=== MINI GAME DATABASE SETUP COMPLETED SUCCESSFULLY ===\n";
    echo "✓ All tables created/updated\n";
    echo "✓ Mini game challenges installed\n";
    echo "✓ Ready to play Flappy Bird!\n";
    
} catch (Exception $e) {
    // Rollback on error
    $pdo->rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
?>