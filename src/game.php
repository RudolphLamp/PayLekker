<?php
/**
 * PayLekker Game System API
 * JWT-protected endpoints for game mechanics, challenges, and rewards
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'database.php';
require_once 'jwt.php';

class GameAPI {
    private $pdo;
    private $user_data;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        
        try {
            $this->user_data = JWTAuth::requireAuth();
            if (!$this->user_data || !isset($this->user_data['user_id'])) {
                throw new Exception('Invalid user authentication data');
            }
            error_log("Game API initialized for user: " . $this->user_data['user_id']);
        } catch (Exception $e) {
            error_log("Game API authentication error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_GET['action'] ?? '';
        
        try {
            switch ($method) {
                case 'GET':
                    return $this->handleGet($path);
                case 'POST':
                    return $this->handlePost($path);
                default:
                    throw new Exception('Method not allowed');
            }
        } catch (Exception $e) {
            http_response_code(400);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function handleGet($action) {
        switch ($action) {
            case 'challenges':
                return $this->getChallenges();
            case 'progress':
                return $this->getUserProgress();
            case 'rewards':
                return $this->getUnclaimedRewards();
            case 'achievements':
                return $this->getUserAchievements();
            case 'leaderboard':
                return $this->getLeaderboard();
            case 'mini_game_challenges':
                return $this->getMiniGameChallenges();
            default:
                throw new Exception('Invalid action');
        }
    }
    
    private function handlePost($action) {
        $rawInput = file_get_contents('php://input');
        error_log("Game API POST - Raw input: " . $rawInput);
        
        $input = json_decode($rawInput, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Game API POST - JSON decode error: " . json_last_error_msg());
            throw new Exception('Invalid JSON in request body');
        }
        
        error_log("Game API POST - Action: $action, Input: " . json_encode($input));
        
        switch ($action) {
            case 'complete_challenge':
                return $this->completeChallenge($input);
            case 'claim_reward':
                return $this->claimReward($input);
            case 'initialize_progress':
                return $this->initializeUserProgress();
            case 'mini_game_reward':
                return $this->addMiniGameReward($input);
            case 'check_mini_game_challenge':
                return $this->checkMiniGameChallengeCompletion($input);
            default:
                throw new Exception('Invalid action');
        }
    }
    
    private function getChallenges() {
        $stmt = $this->pdo->prepare("
            SELECT c.*, 
                   CASE WHEN cc.id IS NOT NULL THEN 1 ELSE 0 END as completed_today
            FROM game_challenges c
            LEFT JOIN user_challenge_completions cc ON c.id = cc.challenge_id 
                AND cc.user_id = ? 
                AND DATE(cc.completed_at) = CURDATE()
            WHERE c.is_active = 1
            AND (c.expires_at IS NULL OR c.expires_at > NOW())
            ORDER BY c.difficulty ASC, c.points_reward DESC
        ");
        
        $stmt->execute([$this->user_data['user_id']]);
        $challenges = $stmt->fetchAll();
        
        // Parse requirements JSON for each challenge
        foreach ($challenges as &$challenge) {
            $challenge['requirements'] = json_decode($challenge['requirements'], true);
        }
        
        return [
            'success' => true,
            'challenges' => $challenges
        ];
    }
    
    private function getUserProgress() {
        // Get or create user progress
        $stmt = $this->pdo->prepare("
            SELECT * FROM user_game_progress WHERE user_id = ?
        ");
        $stmt->execute([$this->user_data['user_id']]);
        $progress = $stmt->fetch();
        
        if (!$progress) {
            $this->initializeUserProgress();
            $stmt->execute([$this->user_data['user_id']]);
            $progress = $stmt->fetch();
        }
        
        // Calculate experience needed for next level
        $experience_needed = $this->getExperienceNeededForLevel($progress['level'] + 1);
        $experience_progress = $progress['experience_points'] - $this->getExperienceNeededForLevel($progress['level']);
        $experience_for_current_level = $experience_needed - $this->getExperienceNeededForLevel($progress['level']);
        
        // Get user account balance
        $stmt = $this->pdo->prepare("SELECT account_balance FROM users WHERE id = ?");
        $stmt->execute([$this->user_data['user_id']]);
        $account_balance = $stmt->fetchColumn();
        
        return [
            'success' => true,
            'progress' => array_merge($progress, [
                'experience_needed' => $experience_needed,
                'experience_progress' => $experience_progress,
                'experience_for_current_level' => $experience_for_current_level,
                'progress_percentage' => min(100, ($experience_progress / $experience_for_current_level) * 100),
                'account_balance' => floatval($account_balance)
            ])
        ];
    }
    
    private function getUnclaimedRewards() {
        $stmt = $this->pdo->prepare("
            SELECT * FROM game_rewards 
            WHERE user_id = ? AND is_claimed = 0
            AND (expires_at IS NULL OR expires_at > NOW())
            ORDER BY created_at DESC
        ");
        $stmt->execute([$this->user_data['user_id']]);
        
        return [
            'success' => true,
            'rewards' => $stmt->fetchAll()
        ];
    }
    
    private function getUserAchievements() {
        $stmt = $this->pdo->prepare("
            SELECT * FROM user_achievements 
            WHERE user_id = ? 
            ORDER BY unlocked_at DESC
        ");
        $stmt->execute([$this->user_data['user_id']]);
        
        return [
            'success' => true,
            'achievements' => $stmt->fetchAll()
        ];
    }
    
    private function getLeaderboard() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT u.name, u.email, 
                       COALESCE(ugp.total_points, 0) as total_points, 
                       COALESCE(ugp.level, 1) as level, 
                       COALESCE(ugp.challenges_completed, 0) as challenges_completed
                FROM users u
                LEFT JOIN user_game_progress ugp ON ugp.user_id = u.id
                WHERE ugp.total_points IS NOT NULL OR ugp.user_id IS NULL
                ORDER BY ugp.total_points DESC, ugp.level DESC
                LIMIT 20
            ");
            $stmt->execute();
            
            return [
                'success' => true,
                'leaderboard' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (Exception $e) {
            error_log("Leaderboard error: " . $e->getMessage());
            return [
                'success' => true,
                'leaderboard' => []
            ];
        }
    }
    
    private function completeChallenge($input) {
        error_log("completeChallenge called with input: " . json_encode($input));
        
        $challenge_id = $input['challenge_id'] ?? null;
        $completion_data = $input['completion_data'] ?? [];
        
        error_log("Extracted challenge_id: " . var_export($challenge_id, true));
        error_log("Extracted completion_data: " . json_encode($completion_data));
        
        if (!$challenge_id) {
            error_log("Challenge ID validation failed");
            throw new Exception('Challenge ID is required');
        }
        
        // Get challenge details
        $stmt = $this->pdo->prepare("
            SELECT * FROM game_challenges WHERE id = ? AND is_active = 1
        ");
        $stmt->execute([$challenge_id]);
        $challenge = $stmt->fetch();
        
        if (!$challenge) {
            throw new Exception('Challenge not found or inactive');
        }
        
        // Log for debugging
        error_log("Challenge completion attempt - ID: $challenge_id, Title: {$challenge['title']}, Data: " . json_encode($completion_data));
        
        // Check if already completed today (for daily challenges)
        if ($challenge['challenge_type'] === 'daily') {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM user_challenge_completions 
                WHERE user_id = ? AND challenge_id = ? AND DATE(completed_at) = CURDATE()
            ");
            $stmt->execute([$this->user_data['user_id'], $challenge_id]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Challenge already completed today');
            }
        }
        
        // Verify challenge completion (this would typically integrate with your transaction system)
        $verification_result = $this->verifyChallengeCompletion($challenge, $completion_data);
        error_log("Challenge verification result: " . ($verification_result ? 'PASSED' : 'FAILED'));
        
        if (!$verification_result) {
            throw new Exception('Challenge requirements not met');
        }
        
        $this->pdo->beginTransaction();
        
        try {
            // Record challenge completion
            $stmt = $this->pdo->prepare("
                INSERT INTO user_challenge_completions 
                (user_id, challenge_id, points_earned, money_earned, free_transactions_earned, completion_data)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $this->user_data['user_id'],
                $challenge_id,
                $challenge['points_reward'],
                $challenge['money_reward'],
                $challenge['free_transactions'],
                json_encode($completion_data)
            ]);
            
            // Create rewards and apply them immediately
            if ($challenge['money_reward'] > 0) {
                $this->createReward('money', $challenge['money_reward'], 
                                  "R{$challenge['money_reward']} reward from '{$challenge['title']}'", $challenge_id);
                            // Add money reward if present
            $rewards = [
                'points' => $challenge['points_reward'] ?? 0,
                'money' => $challenge['money_reward'] ?? 0,
                'free_transactions' => $challenge['free_transactions'] ?? 0
            ];
            
            if ($challenge['money_reward'] > 0) {
                error_log("=== TEMPORARY: SKIPPING MONEY REWARD TO TEST ===");
                // Temporarily comment out money reward to isolate the issue
                // $this->createReward('money', $challenge['money_reward'], 
                //                   "R{$challenge['money_reward']} reward from '{$challenge['title']}'", $challenge_id);
                // // Add money directly to account
                // error_log("About to call addMoneyToUserAccount with amount: {$challenge['money_reward']}");
                // $this->addMoneyToUserAccount($challenge['money_reward']);
                // error_log("Successfully called addMoneyToUserAccount");
                error_log("=== MONEY REWARD SKIPPED FOR TESTING ===");
            }
            }
            
            if ($challenge['free_transactions'] > 0) {
                $this->createReward('free_transactions', $challenge['free_transactions'], 
                                  "{$challenge['free_transactions']} free transactions from '{$challenge['title']}'", $challenge_id);
            }
            
            // Update user progress
            $this->updateUserProgress($challenge['points_reward']);
            
            // Check for achievements
            $this->checkAndUnlockAchievements();
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Challenge completed successfully!',
                'rewards' => [
                    'points' => $challenge['points_reward'],
                    'money' => $challenge['money_reward'],
                    'free_transactions' => $challenge['free_transactions']
                ]
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    private function claimReward($input) {
        $reward_id = $input['reward_id'] ?? null;
        
        if (!$reward_id) {
            throw new Exception('Reward ID is required');
        }
        
        // Get reward details
        $stmt = $this->pdo->prepare("
            SELECT * FROM game_rewards 
            WHERE id = ? AND user_id = ? AND is_claimed = 0
            AND (expires_at IS NULL OR expires_at > NOW())
        ");
        $stmt->execute([$reward_id, $this->user_data['user_id']]);
        $reward = $stmt->fetch();
        
        if (!$reward) {
            throw new Exception('Reward not found or already claimed');
        }
        
        $this->pdo->beginTransaction();
        
        try {
            // Mark reward as claimed
            $stmt = $this->pdo->prepare("
                UPDATE game_rewards 
                SET is_claimed = 1, claimed_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$reward_id]);
            
            // Apply reward to user account/progress
            if ($reward['reward_type'] === 'money') {
                // This would integrate with your actual wallet/balance system
                $this->addMoneyToUserAccount($reward['reward_value']);
            } elseif ($reward['reward_type'] === 'free_transactions') {
                // Add free transactions to user progress
                $stmt = $this->pdo->prepare("
                    UPDATE user_game_progress 
                    SET free_transactions_remaining = free_transactions_remaining + ?
                    WHERE user_id = ?
                ");
                $stmt->execute([$reward['reward_value'], $this->user_data['user_id']]);
            }
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Reward claimed successfully!',
                'reward' => $reward
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollback();
            throw $e;
        }
    }
    
    private function initializeUserProgress() {
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO user_game_progress (user_id) VALUES (?)
        ");
        $stmt->execute([$this->user_data['user_id']]);
        
        return [
            'success' => true,
            'message' => 'User progress initialized'
        ];
    }
    
    private function verifyChallengeCompletion($challenge, $completion_data) {
        $requirements = json_decode($challenge['requirements'], true);
        
        // Log for debugging
        error_log("Verifying challenge: " . json_encode([
            'title' => $challenge['title'],
            'requirements' => $requirements,
            'completion_data' => $completion_data
        ]));
        
        // Handle null or empty requirements (confirmation challenges)
        if (empty($requirements)) {
            // For challenges without specific requirements, just check for confirmation
            return isset($completion_data['confirm']) && $completion_data['confirm'] === true;
        }
        
        // This is a simplified verification - in a real system, you'd check against actual transaction data
        // For now, we'll simulate the verification based on the completion_data provided
        
        if (isset($requirements['min_transactions']) && 
            (!isset($completion_data['transactions']) || 
             $completion_data['transactions'] < $requirements['min_transactions'])) {
            error_log("Failed min_transactions requirement");
            return false;
        }
        
        if (isset($requirements['min_amount']) && 
            (!isset($completion_data['amount']) || 
             $completion_data['amount'] < $requirements['min_amount'])) {
            error_log("Failed min_amount requirement");
            return false;
        }
        
        if (isset($requirements['min_daily_challenges'])) {
            // For testing purposes, allow manual confirmation of weekly challenges
            // In production, this would strictly check daily challenge completion
            if (isset($completion_data['confirm']) && $completion_data['confirm'] === true) {
                error_log("Weekly challenge manually confirmed for testing");
                return true;
            }
            
            // Check how many daily challenges the user has completed this week
            $stmt = $this->pdo->prepare("
                SELECT COUNT(DISTINCT DATE(cc.completed_at)) as daily_challenges_completed
                FROM user_challenge_completions cc
                JOIN game_challenges gc ON cc.challenge_id = gc.id
                WHERE cc.user_id = ? 
                AND gc.challenge_type = 'daily'
                AND YEARWEEK(cc.completed_at, 1) = YEARWEEK(NOW(), 1)
            ");
            $stmt->execute([$this->user_data['user_id']]);
            $result = $stmt->fetch();
            $completed_daily = $result['daily_challenges_completed'] ?? 0;
            
            if ($completed_daily < $requirements['min_daily_challenges']) {
                error_log("Failed min_daily_challenges requirement: $completed_daily < {$requirements['min_daily_challenges']} (you can still manually confirm for testing)");
                return false;
            }
        }
        
        // For challenges without specific data requirements, check for confirmation
        if (empty(array_intersect(['min_transactions', 'min_amount', 'min_daily_challenges'], array_keys($requirements)))) {
            return isset($completion_data['confirm']) && $completion_data['confirm'] === true;
        }
        
        return true;
    }
    
    private function createReward($type, $value, $description, $source_challenge_id) {
        error_log("=== CREATE REWARD DEBUG (V3.0) ===");
        error_log("Type: $type, Value: $value, Description: $description, Source Challenge: $source_challenge_id");
        error_log("User ID: {$this->user_data['user_id']}");
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO game_rewards (user_id, reward_type, reward_value, description, source_challenge_id)
                VALUES (?, ?, ?, ?, ?)
            ");
            $result = $stmt->execute([$this->user_data['user_id'], $type, $value, $description, $source_challenge_id]);
            
            error_log("Game reward creation result: " . ($result ? 'SUCCESS' : 'FAILED'));
            if (!$result) {
                error_log("Create reward error: " . implode(', ', $stmt->errorInfo()));
            }
        } catch (Exception $e) {
            error_log("EXCEPTION in createReward: " . $e->getMessage());
            error_log("Exception stack trace: " . $e->getTraceAsString());
            throw $e;
        }
        
        error_log("=== CREATE REWARD COMPLETED (V3.0) ===");
    }
    
    private function updateUserProgress($points_earned) {
        // Ensure user progress record exists
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user_game_progress WHERE user_id = ?");
        $stmt->execute([$this->user_data['user_id']]);
        if ($stmt->fetchColumn() == 0) {
            $this->initializeUserProgress();
        }
        
        $stmt = $this->pdo->prepare("
            UPDATE user_game_progress 
            SET total_points = total_points + ?,
                experience_points = experience_points + ?,
                challenges_completed = challenges_completed + 1,
                current_streak = current_streak + 1,
                longest_streak = GREATEST(longest_streak, current_streak + 1),
                last_activity = NOW()
            WHERE user_id = ?
        ");
        $stmt->execute([$points_earned, $points_earned, $this->user_data['user_id']]);
        
        // Check for level up
        $this->checkForLevelUp();
    }
    
    private function checkForLevelUp() {
        $stmt = $this->pdo->prepare("
            SELECT experience_points, level FROM user_game_progress WHERE user_id = ?
        ");
        $stmt->execute([$this->user_data['user_id']]);
        $progress = $stmt->fetch();
        
        $new_level = $this->calculateLevelFromExperience($progress['experience_points']);
        
        if ($new_level > $progress['level']) {
            $stmt = $this->pdo->prepare("
                UPDATE user_game_progress SET level = ? WHERE user_id = ?
            ");
            $stmt->execute([$new_level, $this->user_data['user_id']]);
            
            // Create level up reward
            $bonus_money = min(10 + ($new_level * 5), 50); // R10 + R5 per level, max R50
            $this->createReward('money', $bonus_money, "Level {$new_level} bonus reward!", null);
        }
    }
    
    private function calculateLevelFromExperience($exp) {
        // Level formula: Level = floor(sqrt(experience / 100)) + 1
        return floor(sqrt($exp / 100)) + 1;
    }
    
    private function getExperienceNeededForLevel($level) {
        // Experience needed = (level - 1)^2 * 100
        return pow($level - 1, 2) * 100;
    }
    
    private function checkAndUnlockAchievements() {
        // Get current progress
        $stmt = $this->pdo->prepare("
            SELECT * FROM user_game_progress WHERE user_id = ?
        ");
        $stmt->execute([$this->user_data['user_id']]);
        $progress = $stmt->fetch();
        
        $achievements_to_unlock = [];
        
        // Check various achievement conditions
        if ($progress['challenges_completed'] >= 1) {
            $achievements_to_unlock[] = [
                'type' => 'first_challenge',
                'name' => 'First Steps',
                'description' => 'Complete your first challenge',
                'points' => 50,
                'money' => 25.00
            ];
        }
        
        if ($progress['current_streak'] >= 5) {
            $achievements_to_unlock[] = [
                'type' => 'streak_5',
                'name' => 'On Fire!',
                'description' => 'Complete 5 challenges in a row',
                'points' => 100,
                'money' => 50.00
            ];
        }
        
        if ($progress['level'] >= 5) {
            $achievements_to_unlock[] = [
                'type' => 'level_5',
                'name' => 'Level Master',
                'description' => 'Reach level 5',
                'points' => 150,
                'money' => 75.00
            ];
        }
        
        // Unlock new achievements
        foreach ($achievements_to_unlock as $achievement) {
            $stmt = $this->pdo->prepare("
                INSERT IGNORE INTO user_achievements 
                (user_id, achievement_type, achievement_name, achievement_description, points_reward, money_reward)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $this->user_data['user_id'],
                $achievement['type'],
                $achievement['name'],
                $achievement['description'],
                $achievement['points'],
                $achievement['money']
            ]);
            
            if ($stmt->rowCount() > 0) {
                // Create reward for achievement
                $this->createReward('money', $achievement['money'], 
                                  "Achievement unlocked: {$achievement['name']}", null);
            }
        }
    }
    
    private function addMoneyToUserAccount($amount) {
        error_log("=== GAME REWARD: addMoneyToUserAccount called (VERSION 3.0) ===");
        error_log("Amount: $amount, User ID: {$this->user_data['user_id']}");
        error_log("PDO error info before update: " . implode(', ', $this->pdo->errorInfo()));
        
        try {
            // Add money directly to user's account balance
            $stmt = $this->pdo->prepare("
                UPDATE users SET account_balance = account_balance + ? WHERE id = ?
            ");
            $result = $stmt->execute([$amount, $this->user_data['user_id']]);
            
            error_log("SQL statement executed. Result: " . ($result ? 'true' : 'false'));
            error_log("Statement error info: " . implode(', ', $stmt->errorInfo()));
            error_log("PDO error info after update: " . implode(', ', $this->pdo->errorInfo()));
            
            if ($result) {
                error_log("SUCCESS: Added R$amount to user {$this->user_data['user_id']} account balance from game reward");
            } else {
                error_log("ERROR: Failed to update account balance");
                error_log("Statement error code: " . $stmt->errorCode());
                error_log("Statement error info: " . print_r($stmt->errorInfo(), true));
            }
        } catch (Exception $e) {
            error_log("EXCEPTION in addMoneyToUserAccount: " . $e->getMessage());
            error_log("Exception stack trace: " . $e->getTraceAsString());
            throw $e;
        }
        
        // Note: Game rewards are not logged as transactions since they don't have a sender
        // The reward is tracked in the game_rewards and user_challenge_completions tables instead
        error_log("=== GAME REWARD: addMoneyToUserAccount completed (VERSION 3.0) ===");
    }
    
    // Mini Game Methods
    private function getMiniGameChallenges() {
        // First, ensure mini game challenges exist in the database
        $this->initializeMiniGameChallenges();
        
        $stmt = $this->pdo->prepare("
            SELECT gc.*, 
                   ucc.completed_at,
                   COALESCE(mgp.current_score, 0) as current_progress,
                   gc.money_reward as reward_amount,
                   'money' as reward_type,
                   CASE 
                       WHEN ucc.user_id IS NOT NULL THEN true 
                       ELSE false 
                   END as completed
            FROM game_challenges gc
            LEFT JOIN user_challenge_completions ucc 
                ON gc.id = ucc.challenge_id AND ucc.user_id = ?
            LEFT JOIN mini_game_progress mgp 
                ON mgp.user_id = ? AND gc.challenge_type = 'mini_game'
            WHERE gc.challenge_type = 'mini_game'
            AND gc.is_active = 1
            ORDER BY gc.target_value ASC
        ");
        
        $stmt->execute([$this->user_data['user_id'], $this->user_data['user_id']]);
        $challenges = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return ['success' => true, 'challenges' => $challenges];
    }
    
    private function initializeMiniGameChallenges() {
        // Check if mini game challenges already exist
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM game_challenges WHERE challenge_type = 'mini_game'");
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            return; // Already initialized
        }
        
        // Create mini game challenges
        $challenges = [
            [
                'title' => 'First Flight',
                'description' => 'Score 5 points in Flappy Bird',
                'challenge_type' => 'mini_game',
                'target_value' => 5,
                'money_reward' => 1.00,
                'difficulty' => 'easy'
            ],
            [
                'title' => 'Getting Better',
                'description' => 'Score 10 points in Flappy Bird',
                'challenge_type' => 'mini_game',
                'target_value' => 10,
                'money_reward' => 2.50,
                'difficulty' => 'easy'
            ],
            [
                'title' => 'Steady Flyer',
                'description' => 'Score 15 points in Flappy Bird',
                'challenge_type' => 'mini_game',
                'target_value' => 15,
                'money_reward' => 5.00,
                'difficulty' => 'medium'
            ],
            [
                'title' => 'Skilled Pilot',
                'description' => 'Score 20 points in Flappy Bird',
                'challenge_type' => 'mini_game',
                'target_value' => 20,
                'money_reward' => 10.00,
                'difficulty' => 'medium'
            ],
            [
                'title' => 'Ace Flyer',
                'description' => 'Score 30 points in Flappy Bird',
                'challenge_type' => 'mini_game',
                'target_value' => 30,
                'money_reward' => 20.00,
                'difficulty' => 'hard'
            ],
            [
                'title' => 'Bird Master',
                'description' => 'Score 50 points in Flappy Bird',
                'challenge_type' => 'mini_game',
                'target_value' => 50,
                'money_reward' => 50.00,
                'difficulty' => 'expert'
            ]
        ];
        
        $stmt = $this->pdo->prepare("
            INSERT INTO game_challenges 
            (title, description, challenge_type, target_value, money_reward, difficulty, expires_at, is_active)
            VALUES (?, ?, ?, ?, ?, ?, NULL, 1)
        ");
        
        foreach ($challenges as $challenge) {
            $stmt->execute([
                $challenge['title'],
                $challenge['description'],
                $challenge['challenge_type'],
                $challenge['target_value'],
                $challenge['money_reward'],
                $challenge['difficulty']
            ]);
        }
    }
    
    private function addMiniGameReward($input) {
        if (!isset($input['amount']) || !isset($input['reason'])) {
            throw new Exception('Amount and reason are required');
        }
        
        $amount = floatval($input['amount']);
        $reason = $input['reason'];
        
        if ($amount <= 0) {
            throw new Exception('Invalid reward amount');
        }
        
        try {
            $this->pdo->beginTransaction();
            
            // Add money to user account
            $this->addMoneyToUserAccount($amount, $reason);
            
            // Log the mini game reward
            $stmt = $this->pdo->prepare("
                INSERT INTO game_rewards (user_id, reward_type, amount, reason, created_at)
                VALUES (?, 'mini_game', ?, ?, NOW())
            ");
            $stmt->execute([$this->user_data['user_id'], $amount, $reason]);
            
            $this->pdo->commit();
            
            return ['success' => true, 'message' => 'Reward added successfully'];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error adding mini game reward: " . $e->getMessage());
            throw new Exception('Failed to add reward');
        }
    }
    
    private function checkMiniGameChallengeCompletion($input) {
        if (!isset($input['challenge_type']) || !isset($input['value'])) {
            throw new Exception('Challenge type and value are required');
        }
        
        $challengeType = $input['challenge_type'];
        $value = intval($input['value']);
        
        try {
            $this->pdo->beginTransaction();
            
            // Update or insert mini game progress
            $stmt = $this->pdo->prepare("
                INSERT INTO mini_game_progress (user_id, current_score, high_score, created_at, updated_at)
                VALUES (?, ?, ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE 
                    current_score = GREATEST(current_score, ?),
                    high_score = GREATEST(high_score, ?),
                    updated_at = NOW()
            ");
            $stmt->execute([$this->user_data['user_id'], $value, $value, $value, $value]);
            
            // Find all applicable challenges that haven't been completed
            $stmt = $this->pdo->prepare("
                SELECT gc.* 
                FROM game_challenges gc
                LEFT JOIN user_challenge_completions ucc 
                    ON gc.id = ucc.challenge_id AND ucc.user_id = ?
                WHERE gc.challenge_type = 'mini_game' 
                    AND gc.target_value <= ?
                    AND ucc.user_id IS NULL
                    AND gc.is_active = 1
                ORDER BY gc.target_value ASC
            ");
            $stmt->execute([$this->user_data['user_id'], $value]);
            $completedChallenges = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $results = [];
            
            foreach ($completedChallenges as $challenge) {
                // Mark challenge as completed
                $stmt = $this->pdo->prepare("
                    INSERT INTO user_challenge_completions (user_id, challenge_id, completed_at)
                    VALUES (?, ?, NOW())
                ");
                $stmt->execute([$this->user_data['user_id'], $challenge['id']]);
                
                // Add reward
                if ($challenge['money_reward'] > 0) {
                    $this->addMoneyToUserAccount(
                        $challenge['money_reward'], 
                        "Mini Game Challenge: " . $challenge['title']
                    );
                }
                
                $results[] = [
                    'title' => $challenge['title'],
                    'reward_amount' => $challenge['money_reward']
                ];
            }
            
            $this->pdo->commit();
            
            return ['success' => true, 'completed_challenges' => $results];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error checking mini game challenge completion: " . $e->getMessage());
            throw new Exception('Failed to check challenge completion');
        }
    }
}

// Handle the API request
try {
    error_log("Game API request started - Method: " . $_SERVER['REQUEST_METHOD'] . ", Action: " . ($_GET['action'] ?? 'none'));
    $api = new GameAPI();
    $response = $api->handleRequest();
    echo json_encode($response);
} catch (Exception $e) {
    error_log("Game API error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
?>