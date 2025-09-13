<?php
/**
 * PayLekker API - Transfers
 * Handles money transfers between users
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
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
$action = $_GET['action'] ?? $_POST['action'] ?? 'send';

switch ($action) {
    case 'send':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') respondError(405, 'Method not allowed');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $recipientEmail = $input['recipient_email'] ?? '';
        $amount = floatval($input['amount'] ?? 0);
        $description = $input['description'] ?? '';
        
        if (!$recipientEmail || $amount <= 0) {
            respondError(400, 'Recipient email and valid amount are required');
        }
        
        try {
            $pdo->beginTransaction();
            
            // Get sender info
            $stmt = $pdo->prepare("SELECT name, email, balance FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $sender = $stmt->fetch();
            
            if (!$sender) {
                $pdo->rollBack();
                respondError(404, 'Sender not found');
            }
            
            if ($sender['balance'] < $amount) {
                $pdo->rollBack();
                respondError(400, 'Insufficient balance');
            }
            
            // Get recipient
            $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
            $stmt->execute([$recipientEmail]);
            $recipient = $stmt->fetch();
            
            if (!$recipient) {
                $pdo->rollBack();
                respondError(404, 'Recipient not found');
            }
            
            if ($sender['email'] === $recipientEmail) {
                $pdo->rollBack();
                respondError(400, 'Cannot transfer to yourself');
            }
            
            // Update balances
            $stmt = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$amount, $userId]);
            
            $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$amount, $recipient['id']]);
            
            // Record transaction
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, recipient_email, description, created_at) VALUES (?, 'transfer', ?, ?, ?, NOW())");
            $stmt->execute([$userId, $amount, $recipientEmail, $description]);
            
            $pdo->commit();
            
            respondSuccess([
                'transaction_id' => $pdo->lastInsertId(),
                'amount' => number_format($amount, 2),
                'recipient' => $recipient['name'],
                'new_balance' => number_format($sender['balance'] - $amount, 2)
            ], 'Transfer completed successfully');
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            respondError(500, 'Transfer failed: ' . $e->getMessage());
        }
        break;
        
    case 'history':
        try {
            $stmt = $pdo->prepare("
                SELECT id, type, amount, recipient_email, description, status, created_at 
                FROM transactions 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 50
            ");
            $stmt->execute([$userId]);
            $transactions = $stmt->fetchAll();
            
            respondSuccess(['transactions' => $transactions], 'Transaction history retrieved');
            
        } catch (PDOException $e) {
            respondError(500, 'Database error: ' . $e->getMessage());
        }
        break;
        
    default:
        respondError(404, 'Action not found. Use ?action=send or ?action=history');
}
?>