<?php
/**
 * PayLekker Game System - Database Setup
 * Creates all necessary tables for the reward-based game system
 */

require_once 'database.php';

function setupGameDatabase() {
    global $pdo;
    
    try {
        echo "Setting up PayLekker Game System Database...\n\n";
        
        // 1. Create game_challenges table
        echo "Creating game_challenges table...\n";
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS game_challenges (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                challenge_type ENUM('daily', 'weekly', 'one_time', 'milestone', 'mini_game') DEFAULT 'daily',
                difficulty ENUM('easy', 'medium', 'hard', 'expert') DEFAULT 'easy',
                points_reward INT DEFAULT 10,
                money_reward DECIMAL(10,2) DEFAULT 0.00,
                free_transactions INT DEFAULT 0,
                target_value INT DEFAULT 0,
                requirements JSON NULL COMMENT 'Challenge requirements and criteria',
                is_active BOOLEAN DEFAULT TRUE,
                expires_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        // 2. Create user_game_progress table
        echo "Creating user_game_progress table...\n";
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS user_game_progress (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                total_points INT DEFAULT 0,
                level INT DEFAULT 1,
                experience_points INT DEFAULT 0,
                free_transactions_remaining INT DEFAULT 0,
                total_money_earned DECIMAL(10,2) DEFAULT 0.00,
                challenges_completed INT DEFAULT 0,
                current_streak INT DEFAULT 0,
                longest_streak INT DEFAULT 0,
                last_activity DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
        
        // 3. Create user_challenge_completions table
        echo "Creating user_challenge_completions table...\n";
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS user_challenge_completions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                challenge_id INT NOT NULL,
                completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                completion_date DATE GENERATED ALWAYS AS (DATE(completed_at)) STORED,
                points_earned INT DEFAULT 0,
                money_earned DECIMAL(10,2) DEFAULT 0.00,
                free_transactions_earned INT DEFAULT 0,
                completion_data JSON NULL COMMENT 'Additional data about how challenge was completed',
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (challenge_id) REFERENCES game_challenges(id) ON DELETE CASCADE,
                UNIQUE KEY unique_completion (user_id, challenge_id, completion_date)
            )
        ");
        
        // 4. Create game_rewards table  
        echo "Creating game_rewards table...\n";
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS game_rewards (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                reward_type ENUM('money', 'free_transactions', 'points', 'badge') NOT NULL,
                reward_value DECIMAL(10,2) NOT NULL,
                description VARCHAR(255) NOT NULL,
                is_claimed BOOLEAN DEFAULT FALSE,
                claimed_at DATETIME NULL,
                expires_at DATETIME NULL,
                source_challenge_id INT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (source_challenge_id) REFERENCES game_challenges(id) ON DELETE SET NULL
            )
        ");
        
        // 5. Create user_achievements table
        echo "Creating user_achievements table...\n";
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS user_achievements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                achievement_type ENUM('first_challenge', 'streak_5', 'streak_10', 'big_earner', 'level_5', 'level_10', 'challenge_master') NOT NULL,
                achievement_name VARCHAR(255) NOT NULL,
                achievement_description TEXT NOT NULL,
                icon_class VARCHAR(100) DEFAULT 'fas fa-trophy',
                points_reward INT DEFAULT 50,
                money_reward DECIMAL(10,2) DEFAULT 0.00,
                unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_achievement (user_id, achievement_type)
            )
        ");
        
        // 6. Create mini_game_progress table
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
        
        echo "\nInserting sample challenges...\n";
        
        // Insert sample challenges
        $challenges = [
            [
                'title' => 'First Transaction',
                'description' => 'Complete your first transaction of the day',
                'challenge_type' => 'daily',
                'difficulty' => 'easy',
                'points_reward' => 10,
                'money_reward' => 5.00,
                'free_transactions' => 0,
                'requirements' => json_encode(['min_transactions' => 1, 'min_amount' => 10])
            ],
            [
                'title' => 'Big Spender',
                'description' => 'Make a transaction of R100 or more',
                'challenge_type' => 'daily',
                'difficulty' => 'medium',
                'points_reward' => 25,
                'money_reward' => 15.00,
                'free_transactions' => 1,
                'requirements' => json_encode(['min_transactions' => 1, 'min_amount' => 100])
            ],
            [
                'title' => 'Transaction Master',
                'description' => 'Complete 5 transactions in one day',
                'challenge_type' => 'daily',
                'difficulty' => 'hard',
                'points_reward' => 50,
                'money_reward' => 25.00,
                'free_transactions' => 2,
                'requirements' => json_encode(['min_transactions' => 5, 'min_amount' => 10])
            ],
            [
                'title' => 'Weekly Warrior',
                'description' => 'Complete at least 3 daily challenges this week',
                'challenge_type' => 'weekly',
                'difficulty' => 'medium',
                'points_reward' => 100,
                'money_reward' => 50.00,
                'free_transactions' => 5,
                'requirements' => json_encode(['min_daily_challenges' => 3])
            ],
            [
                'title' => 'High Roller',
                'description' => 'Make a transaction of R500 or more',
                'challenge_type' => 'one_time',
                'difficulty' => 'expert',
                'points_reward' => 200,
                'money_reward' => 100.00,
                'free_transactions' => 10,
                'requirements' => json_encode(['min_transactions' => 1, 'min_amount' => 500])
            ],
            [
                'title' => 'Budget Tracker',
                'description' => 'Use the budget feature and stay within your limits',
                'challenge_type' => 'daily',
                'difficulty' => 'easy',
                'points_reward' => 15,
                'money_reward' => 10.00,
                'free_transactions' => 1,
                'requirements' => json_encode(['use_budget' => true, 'stay_within_limit' => true])
            ],
            [
                'title' => 'Social Butterfly',
                'description' => 'Send money to 3 different people today',
                'challenge_type' => 'daily',
                'difficulty' => 'medium',
                'points_reward' => 30,
                'money_reward' => 20.00,
                'free_transactions' => 2,
                'requirements' => json_encode(['unique_recipients' => 3, 'min_amount' => 5])
            ],
            [
                'title' => 'Milestone Maker',
                'description' => 'Reach your first R1000 in total transactions',
                'challenge_type' => 'milestone',
                'difficulty' => 'medium',
                'points_reward' => 150,
                'money_reward' => 75.00,
                'free_transactions' => 15,
                'requirements' => json_encode(['total_transaction_amount' => 1000])
            ]
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO game_challenges 
            (title, description, challenge_type, difficulty, points_reward, money_reward, free_transactions, requirements) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($challenges as $challenge) {
            $stmt->execute([
                $challenge['title'],
                $challenge['description'],
                $challenge['challenge_type'],
                $challenge['difficulty'],
                $challenge['points_reward'],
                $challenge['money_reward'],
                $challenge['free_transactions'],
                $challenge['requirements']
            ]);
        }
        
        echo "Game database setup completed successfully!\n\n";
        
        // Show summary
        echo "=== GAME SYSTEM SUMMARY ===\n";
        echo "Tables created:\n";
        echo "- game_challenges: Store all available challenges\n";
        echo "- user_game_progress: Track each user's overall progress\n";
        echo "- user_challenge_completions: Record completed challenges\n";
        echo "- game_rewards: Store unclaimed rewards\n";
        echo "- user_achievements: Track special achievements\n\n";
        
        echo "Sample challenges inserted: " . count($challenges) . "\n";
        echo "Reward range: R10 - R100 (plus free transactions)\n";
        echo "Challenge types: daily, weekly, one_time, milestone\n";
        echo "Difficulty levels: easy, medium, hard, expert\n\n";
        
        return true;
        
    } catch (PDOException $e) {
        echo "Error setting up game database: " . $e->getMessage() . "\n";
        return false;
    }
}

// Run the setup if this file is executed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    setupGameDatabase();
}
?>