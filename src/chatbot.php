<?php
/**
 * PayLekker API - Chatbot Assistant Endpoint
 * POST /chatbot.php - Get financial advice and assistance
 */

require_once 'database.php';
require_once 'jwt.php';
require_once 'response.php';

// Set CORS headers
APIResponse::setCorsHeaders();

// Require authentication
$userData = JWTAuth::requireAuth();
$userId = $userData['user_id'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    APIResponse::error('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    APIResponse::error('Invalid JSON input', 400);
}

// Validate required fields
APIResponse::validateRequired($input, ['message']);

$userMessage = trim($input['message']);

if (strlen($userMessage) < 1 || strlen($userMessage) > 500) {
    APIResponse::error('Message must be between 1 and 500 characters', 400);
}

// Save user message to chat history
saveChatMessage($pdo, $userId, $userMessage, 'user');

// Get AI response based on message content
$botResponse = generateBotResponse($pdo, $userId, $userMessage);

// Save bot response to chat history
saveChatMessage($pdo, $userId, $botResponse['message'], 'assistant');

// Return response with suggestions
APIResponse::success([
    'response' => $botResponse['message'],
    'suggestions' => $botResponse['suggestions'] ?? [],
    'actions' => $botResponse['actions'] ?? []
], 'Chatbot response generated successfully');

/**
 * Save chat message to history
 */
function saveChatMessage($pdo, $userId, $message, $sender) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO chat_history (user_id, message, sender, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $message, $sender]);
    } catch (PDOException $e) {
        error_log("Save chat message error: " . $e->getMessage());
    }
}

/**
 * Generate intelligent bot response based on message content and user data
 */
function generateBotResponse($pdo, $userId, $message) {
    $message = strtolower($message);
    
    // Get user's financial data for personalized responses
    $userData = getUserFinancialData($pdo, $userId);
    
    // Intent detection and response generation
    if (preg_match('/\b(balance|money|account|wallet)\b/i', $message)) {
        return handleBalanceQuery($userData);
        
    } elseif (preg_match('/\b(transfer|send|pay)\b/i', $message)) {
        return handleTransferQuery($userData);
        
    } elseif (preg_match('/\b(budget|spending|save|savings)\b/i', $message)) {
        return handleBudgetQuery($pdo, $userId, $userData);
        
    } elseif (preg_match('/\b(transaction|history|recent)\b/i', $message)) {
        return handleTransactionQuery($userData);
        
    } elseif (preg_match('/\b(help|support|how|what)\b/i', $message)) {
        return handleHelpQuery();
        
    } elseif (preg_match('/\b(hello|hi|hey|good morning|good afternoon)\b/i', $message)) {
        return handleGreeting($userData);
        
    } elseif (preg_match('/\b(thank|thanks|bye|goodbye)\b/i', $message)) {
        return handleFarewell();
        
    } else {
        return handleGeneralQuery($message);
    }
}

/**
 * Get user's financial data for personalized responses
 */
function getUserFinancialData($pdo, $userId) {
    try {
        // Get user balance
        $stmt = $pdo->prepare("SELECT account_balance, first_name FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        // Get recent transaction count
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as transaction_count 
            FROM transactions 
            WHERE (sender_id = ? OR recipient_id = ?) 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAYS)
        ");
        $stmt->execute([$userId, $userId]);
        $recentTransactions = $stmt->fetch()['transaction_count'];
        
        // Get budget summary
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as budget_count,
                SUM(budget_amount) as total_budget,
                SUM(spent_amount) as total_spent
            FROM budget_categories 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $budgetData = $stmt->fetch();
        
        return [
            'balance' => $user['account_balance'] ?? 0,
            'first_name' => $user['first_name'] ?? 'Friend',
            'recent_transactions' => $recentTransactions,
            'budget_count' => $budgetData['budget_count'] ?? 0,
            'total_budget' => $budgetData['total_budget'] ?? 0,
            'total_spent' => $budgetData['total_spent'] ?? 0
        ];
        
    } catch (PDOException $e) {
        error_log("Get user financial data error: " . $e->getMessage());
        return ['balance' => 0, 'first_name' => 'Friend'];
    }
}

// Response handlers for different intents

function handleBalanceQuery($userData) {
    $balance = number_format($userData['balance'], 2);
    $name = $userData['first_name'];
    
    $responses = [
        "Hi {$name}! Your current account balance is R{$balance}. 💰",
        "Your wallet shows R{$balance} available, {$name}! 👛",
        "You have R{$balance} in your PayLekker account right now! 📱"
    ];
    
    $suggestions = ['View transactions', 'Transfer money', 'Create budget'];
    $actions = [
        ['type' => 'view_transactions', 'label' => 'View Recent Transactions'],
        ['type' => 'transfer', 'label' => 'Send Money']
    ];
    
    if ($userData['balance'] < 100) {
        $responses[] = "Your balance is getting low at R{$balance}. Consider topping up soon! ⚠️";
    }
    
    return [
        'message' => $responses[array_rand($responses)],
        'suggestions' => $suggestions,
        'actions' => $actions
    ];
}

function handleTransferQuery($userData) {
    $name = $userData['first_name'];
    $balance = number_format($userData['balance'], 2);
    
    return [
        'message' => "Ready to send money, {$name}? You have R{$balance} available to transfer. I can help you send money to friends or family instantly! 🚀",
        'suggestions' => ['Send R50', 'Send R100', 'Send R200', 'Check balance'],
        'actions' => [
            ['type' => 'transfer', 'label' => 'Send Money Now'],
            ['type' => 'contacts', 'label' => 'View Recent Recipients']
        ]
    ];
}

function handleBudgetQuery($pdo, $userId, $userData) {
    $name = $userData['first_name'];
    $budgetCount = $userData['budget_count'];
    
    if ($budgetCount > 0) {
        $totalBudget = number_format($userData['total_budget'], 2);
        $totalSpent = number_format($userData['total_spent'], 2);
        $remaining = $userData['total_budget'] - $userData['total_spent'];
        
        $message = "Hi {$name}! You have {$budgetCount} budget categories with R{$totalBudget} total budget. You've spent R{$totalSpent} so far";
        
        if ($remaining > 0) {
            $message .= ", leaving R" . number_format($remaining, 2) . " remaining. Great job staying on track! 🎯";
        } else {
            $message .= ". You're over budget by R" . number_format(abs($remaining), 2) . ". Time to review your spending! 📊";
        }
    } else {
        $message = "Hi {$name}! You don't have any budgets set up yet. Creating budgets helps you manage your money better and reach your financial goals! 💡";
    }
    
    return [
        'message' => $message,
        'suggestions' => ['Create budget', 'View spending', 'Financial tips', 'Set savings goal'],
        'actions' => [
            ['type' => 'create_budget', 'label' => 'Create New Budget'],
            ['type' => 'view_budgets', 'label' => 'View My Budgets']
        ]
    ];
}

function handleTransactionQuery($userData) {
    $name = $userData['first_name'];
    $recentCount = $userData['recent_transactions'];
    
    $message = "Hi {$name}! You've made {$recentCount} transactions in the past week. ";
    
    if ($recentCount > 10) {
        $message .= "You're quite active! 📈";
    } elseif ($recentCount > 5) {
        $message .= "Nice activity level! 👍";
    } else {
        $message .= "Keeping it steady! 😊";
    }
    
    return [
        'message' => $message,
        'suggestions' => ['View all transactions', 'This month\'s spending', 'Export history'],
        'actions' => [
            ['type' => 'view_transactions', 'label' => 'View Transaction History'],
            ['type' => 'export', 'label' => 'Export Statements']
        ]
    ];
}

function handleGreeting($userData) {
    $name = $userData['first_name'];
    $hour = (int)date('H');
    
    if ($hour < 12) {
        $greeting = "Good morning";
    } elseif ($hour < 17) {
        $greeting = "Good afternoon";
    } else {
        $greeting = "Good evening";
    }
    
    $messages = [
        "{$greeting}, {$name}! Welcome to PayLekker! How can I assist you with your finances today? 😊",
        "Hey there, {$name}! Ready to manage your money like a pro? What can I help you with? 💪",
        "Hi {$name}! I'm here to help you with transfers, budgets, and financial advice. What would you like to do? 🤖"
    ];
    
    return [
        'message' => $messages[array_rand($messages)],
        'suggestions' => ['Check balance', 'Send money', 'View budgets', 'Recent transactions'],
        'actions' => [
            ['type' => 'quick_balance', 'label' => 'Quick Balance Check'],
            ['type' => 'quick_transfer', 'label' => 'Send Money']
        ]
    ];
}

function handleHelpQuery() {
    return [
        'message' => "I'm your PayLekker financial assistant! 🤝 I can help you with:\n\n💰 Check your balance\n📤 Send money to friends\n📊 Manage budgets\n📱 View transaction history\n💡 Get financial tips\n\nJust tell me what you need!",
        'suggestions' => ['Check balance', 'Send money', 'Create budget', 'View transactions'],
        'actions' => [
            ['type' => 'tutorial', 'label' => 'App Tutorial'],
            ['type' => 'support', 'label' => 'Contact Support']
        ]
    ];
}

function handleFarewell() {
    $messages = [
        "You're welcome! Have a great day and smart spending! 🌟",
        "Thanks for using PayLekker! Take care and see you soon! 👋",
        "Glad I could help! Remember, I'm here 24/7 for your financial needs! 💙"
    ];
    
    return [
        'message' => $messages[array_rand($messages)],
        'suggestions' => [],
        'actions' => []
    ];
}

function handleGeneralQuery($message) {
    // Financial tips and general advice
    $tips = [
        "💡 Tip: Try the 50/30/20 rule - 50% needs, 30% wants, 20% savings!",
        "💡 Tip: Set up automatic transfers to build your emergency fund gradually!",
        "💡 Tip: Track small expenses - they add up quickly over time!",
        "💡 Tip: Review your spending weekly to stay on top of your finances!",
        "💡 Tip: Use budgets to allocate money for different categories like food, transport, and entertainment!"
    ];
    
    return [
        'message' => "I understand you're asking about: \"$message\". Here's a helpful tip: " . $tips[array_rand($tips)],
        'suggestions' => ['Financial tips', 'Create budget', 'Check spending', 'Send money'],
        'actions' => [
            ['type' => 'tips', 'label' => 'More Financial Tips'],
            ['type' => 'help', 'label' => 'Get Help']
        ]
    ];
}
?>