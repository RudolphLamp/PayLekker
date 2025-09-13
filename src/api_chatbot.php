<?php
/**
 * PayLekker API - Chatbot
 * Simple financial assistant chatbot
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/includes/database.php';

function respondSuccess($data = [], $message = 'Success') {
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
    exit;
}

function respondError($code, $message) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

function validateToken($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;
    
    try {
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
        if ($payload['exp'] < time()) return false;
        return $payload;
    } catch (Exception $e) {
        return false;
    }
}

function getBotResponse($message, $pdo, $userId) {
    $message = strtolower(trim($message));
    
    // Balance inquiry
    if (strpos($message, 'balance') !== false || strpos($message, 'money') !== false) {
        try {
            $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if ($user) {
                return "Your current balance is R" . number_format($user['balance'], 2) . ". Is there anything else I can help you with?";
            }
        } catch (Exception $e) {
            return "I'm having trouble accessing your balance right now. Please try again later.";
        }
    }
    
    // Transaction history
    if (strpos($message, 'transaction') !== false || strpos($message, 'history') !== false) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM transactions WHERE user_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            
            return "You have " . $result['count'] . " transactions in your history. You can view them in the Transfer section.";
        } catch (Exception $e) {
            return "I'm having trouble accessing your transaction history right now.";
        }
    }
    
    // Help with transfers
    if (strpos($message, 'transfer') !== false || strpos($message, 'send') !== false) {
        return "To send money, go to the Transfer section and enter the recipient's email address and amount. Make sure you have sufficient balance!";
    }
    
    // Budget help
    if (strpos($message, 'budget') !== false || strpos($message, 'save') !== false) {
        return "I can help you manage your budget! Go to the Budget section to create spending categories and track your expenses. Good budgeting is key to financial success!";
    }
    
    // Greetings
    if (strpos($message, 'hello') !== false || strpos($message, 'hi') !== false || strpos($message, 'hey') !== false) {
        return "Hello! I'm your PayLekker financial assistant. I can help you with balance inquiries, transaction history, transfers, and budgeting advice. What would you like to know?";
    }
    
    // Thanks
    if (strpos($message, 'thank') !== false || strpos($message, 'thanks') !== false) {
        return "You're welcome! I'm here to help with all your PayLekker banking needs. Feel free to ask me anything about your account or finances.";
    }
    
    // Default response
    $responses = [
        "I'm here to help with your PayLekker account! You can ask me about your balance, transaction history, how to send money, or budgeting tips.",
        "I can assist you with balance inquiries, transaction information, transfer guidance, and budget management. What would you like to know?",
        "Feel free to ask me about your account balance, recent transactions, sending money, or creating budgets. I'm here to help!",
    ];
    
    return $responses[array_rand($responses)];
}

if (!isset($pdo) || !$pdo) {
    respondError(500, 'Database connection failed');
}

// Get token
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    respondError(401, 'No token provided');
}

$token = $matches[1];
$payload = validateToken($token);
if (!$payload) {
    respondError(401, 'Invalid or expired token');
}

$userId = $payload['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError(405, 'Method not allowed');
}

$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? '';

if (!$message) {
    respondError(400, 'Message is required');
}

$botResponse = getBotResponse($message, $pdo, $userId);

respondSuccess([
    'user_message' => $message,
    'bot_response' => $botResponse,
    'timestamp' => date('Y-m-d H:i:s')
], 'Chat response generated');
?>