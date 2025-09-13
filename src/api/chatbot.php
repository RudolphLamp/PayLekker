<?php
/**
 * PayLekker API - Chatbot Support Endpoint
 * Simple FAQ-driven chatbot for customer support
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/jwt.php';

class ChatbotController {
    private $db;
    private $auth;
    private $faqData;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->auth = new AuthMiddleware($this->db);
        
        if (!$this->db) {
            $this->respondError(500, 'Database connection failed');
        }
        
        $this->initializeFAQ();
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method !== 'POST') {
            $this->respondError(405, 'Method not allowed');
        }
        
        $this->chatbot();
    }
    
    /**
     * Process chatbot query
     */
    private function chatbot() {
        // Verify authentication
        $user = $this->auth->verifyToken();
        if (!$user) return;
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['message']) || empty(trim($input['message']))) {
            $this->respondError(400, 'Message is required');
            return;
        }
        
        $userMessage = trim($input['message']);
        $response = $this->generateResponse($userMessage, $user);
        
        // Log the conversation (optional)
        $this->logConversation($user['id'], $userMessage, $response['message']);
        
        $this->respondSuccess($response);
    }
    
    /**
     * Generate chatbot response based on user message
     */
    private function generateResponse($message, $user) {
        $message = strtolower($message);
        
        // Check for specific intents
        foreach ($this->faqData as $intent => $data) {
            foreach ($data['keywords'] as $keyword) {
                if (strpos($message, strtolower($keyword)) !== false) {
                    $response = $data['responses'][array_rand($data['responses'])];
                    
                    // Personalize response if needed
                    $response = $this->personalizeResponse($response, $user, $intent);
                    
                    return [
                        'message' => $response,
                        'intent' => $intent,
                        'confidence' => 0.8,
                        'suggestions' => $data['suggestions'] ?? []
                    ];
                }
            }
        }
        
        // Default response for unrecognized queries
        return [
            'message' => "I'm sorry, I didn't understand your question. Here are some things I can help you with:\n\n" .
                        "• Account balance and transactions\n" .
                        "• Money transfers\n" .
                        "• Budget management\n" .
                        "• Account security\n" .
                        "• General app usage\n\n" .
                        "Please rephrase your question or ask about one of these topics.",
            'intent' => 'fallback',
            'confidence' => 0.1,
            'suggestions' => [
                'What is my balance?',
                'How do I send money?',
                'How do I set up a budget?',
                'Is my account secure?'
            ]
        ];
    }
    
    /**
     * Personalize response with user data
     */
    private function personalizeResponse($response, $user, $intent) {
        $personalizedResponse = str_replace('{name}', $user['first_name'], $response);
        
        // Add dynamic data based on intent
        if ($intent === 'balance_inquiry') {
            $personalizedResponse .= "\n\nYour current balance is R" . number_format($user['balance'], 2);
        } elseif ($intent === 'recent_transactions') {
            $recentTransactions = $this->getRecentTransactions($user['id'], 3);
            if (!empty($recentTransactions)) {
                $personalizedResponse .= "\n\nYour recent transactions:\n";
                foreach ($recentTransactions as $transaction) {
                    $type = $transaction['sender_id'] == $user['id'] ? 'Sent' : 'Received';
                    $amount = number_format($transaction['amount'], 2);
                    $date = date('M j', strtotime($transaction['created_at']));
                    $personalizedResponse .= "• $type R$amount on $date\n";
                }
            }
        }
        
        return $personalizedResponse;
    }
    
    /**
     * Get recent transactions for personalization
     */
    private function getRecentTransactions($userId, $limit = 3) {
        try {
            $query = "SELECT * FROM transactions 
                     WHERE sender_id = ? OR recipient_id = ? 
                     ORDER BY created_at DESC LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId, $userId, $limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Log conversation for analysis (optional)
     */
    private function logConversation($userId, $userMessage, $botResponse) {
        try {
            // You could create a chat_logs table for this
            // For now, we'll just log to error log
            error_log("Chatbot conversation - User $userId: $userMessage -> $botResponse");
        } catch (Exception $e) {
            // Silent fail for logging
        }
    }
    
    /**
     * Initialize FAQ data
     */
    private function initializeFAQ() {
        $this->faqData = [
            'greeting' => [
                'keywords' => ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening'],
                'responses' => [
                    'Hello {name}! Welcome to PayLekker. How can I help you today?',
                    'Hi {name}! I\'m here to help you with your PayLekker account. What do you need assistance with?',
                    'Hey there {name}! What can I help you with today?'
                ],
                'suggestions' => ['Check my balance', 'Send money', 'View transactions']
            ],
            
            'balance_inquiry' => [
                'keywords' => ['balance', 'money', 'how much', 'account balance', 'funds'],
                'responses' => [
                    'Let me check your account balance for you, {name}.',
                    'Here\'s your current balance information:',
                    'Your account balance details:'
                ]
            ],
            
            'transfer_help' => [
                'keywords' => ['send money', 'transfer', 'pay someone', 'payment', 'send cash'],
                'responses' => [
                    'To send money on PayLekker:\n\n1. Go to the Transfer section\n2. Enter the recipient\'s email address\n3. Enter the amount you want to send\n4. Add a description (optional)\n5. Confirm and send!\n\nMake sure you have sufficient balance in your account.',
                    'Sending money is easy! You\'ll need the recipient\'s email address and sufficient balance. The transfer happens instantly once confirmed.',
                    'To make a payment, use the Transfer feature. Enter the recipient\'s email, amount, and description, then confirm the transaction.'
                ],
                'suggestions' => ['What\'s the transfer limit?', 'How long do transfers take?', 'Can I cancel a transfer?']
            ],
            
            'recent_transactions' => [
                'keywords' => ['transactions', 'history', 'recent', 'past payments', 'transaction history'],
                'responses' => [
                    'Here are your recent transactions, {name}:',
                    'Let me show you your recent activity:',
                    'Your transaction history:'
                ]
            ],
            
            'budget_help' => [
                'keywords' => ['budget', 'budgeting', 'spending', 'save money', 'financial planning'],
                'responses' => [
                    'PayLekker\'s budgeting features help you manage your finances:\n\n• Create budgets by category\n• Track your spending automatically\n• Set weekly, monthly, or yearly budgets\n• Get alerts when you\'re close to your limit\n\nWould you like help setting up a budget?',
                    'Our budgeting tools help you stay on track with your financial goals. You can create budgets for different spending categories and monitor your progress.',
                    'To create a budget:\n1. Go to Budget section\n2. Choose a category\n3. Set your spending limit\n4. Select the time period\n5. Save your budget'
                ],
                'suggestions' => ['Create a budget', 'View my budgets', 'Spending analysis']
            ],
            
            'security_help' => [
                'keywords' => ['security', 'safe', 'secure', 'password', 'account safety', 'fraud'],
                'responses' => [
                    'Your security is our priority! PayLekker uses:\n\n• Bank-level encryption\n• Secure authentication tokens\n• Password hashing\n• Transaction monitoring\n\nAlways keep your login details private and use a strong password.',
                    'PayLekker is designed with security in mind. We use industry-standard encryption and security measures to protect your account and transactions.',
                    'Security tips:\n• Never share your password\n• Log out when finished\n• Check transactions regularly\n• Contact us if you notice suspicious activity'
                ],
                'suggestions' => ['Change my password', 'Report suspicious activity', 'Security settings']
            ],
            
            'app_features' => [
                'keywords' => ['features', 'what can I do', 'how to use', 'app functions', 'capabilities'],
                'responses' => [
                    'PayLekker offers:\n\n💰 Instant money transfers\n📊 Budget tracking\n📱 Transaction history\n💬 24/7 chatbot support\n🔒 Secure authentication\n\nWhat feature would you like to learn more about?',
                    'You can use PayLekker to send money, track spending, manage budgets, and view your transaction history. Everything is designed to be simple and secure.',
                    'Main features:\n• Send & receive money instantly\n• Create and manage budgets\n• View detailed transaction history\n• Get spending insights\n• Chat support'
                ],
                'suggestions' => ['Send money', 'Create budget', 'View transactions']
            ],
            
            'transfer_limits' => [
                'keywords' => ['limit', 'maximum', 'how much can I send', 'transfer limit'],
                'responses' => [
                    'Transfer limits on PayLekker:\n\n• Maximum per transaction: R10,000\n• Daily limit: R50,000\n• Monthly limit: R200,000\n\nThese limits help keep your account secure.',
                    'You can send up to R10,000 per transaction. Higher limits may be available based on your account verification level.',
                    'Current transfer limits are set to R10,000 per transaction for security purposes.'
                ]
            ],
            
            'goodbye' => [
                'keywords' => ['bye', 'goodbye', 'thanks', 'thank you', 'that\'s all'],
                'responses' => [
                    'You\'re welcome, {name}! Have a great day and happy banking with PayLekker! 👋',
                    'Glad I could help! If you need anything else, just ask. Enjoy using PayLekker! 😊',
                    'Thank you for using PayLekker, {name}! Feel free to reach out anytime you need help.'
                ]
            ]
        ];
    }
    
    /**
     * Send success response
     */
    private function respondSuccess($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
    
    /**
     * Send error response
     */
    private function respondError($code, $message) {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit;
    }
}

// Handle the request
$controller = new ChatbotController();
$controller->handleRequest();
?>