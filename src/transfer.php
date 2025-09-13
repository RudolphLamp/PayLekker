<?php
/**
 * PayLekker API - P2P Money Transfer Endpoint
 * POST /transfer.php - Send money from one user to another
 */

require_once 'database.php';
require_once 'jwt.php';
require_once 'response.php';

// Set CORS headers
APIResponse::setCorsHeaders();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    APIResponse::error('Method not allowed. Use POST.', 405);
}

// Require authentication
$userData = JWTAuth::requireAuth();
$senderId = $userData['user_id'];

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    APIResponse::error('Invalid JSON input', 400);
}

// Validate required fields
APIResponse::validateRequired($input, ['recipient_phone', 'amount']);

$recipientPhone = trim($input['recipient_phone']);
$amount = floatval($input['amount']);
$description = trim($input['description'] ?? '');

// Validate phone format
if (!preg_match('/^[0-9+\-\s()]{10,20}$/', $recipientPhone)) {
    APIResponse::error('Invalid phone number format', 400);
}

// Validate amount
if ($amount <= 0) {
    APIResponse::error('Amount must be greater than 0', 400);
}

if ($amount > 50000) { // Set a reasonable limit
    APIResponse::error('Amount exceeds maximum transfer limit (R50,000)', 400);
}

// Validate description length
if (strlen($description) > 255) {
    APIResponse::error('Description too long (max 255 characters)', 400);
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Get sender information and check balance
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, phone, account_balance 
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$senderId]);
    $sender = $stmt->fetch();
    
    if (!$sender) {
        $pdo->rollBack();
        APIResponse::error('Sender account not found', 404);
    }
    
    // Check if sender has sufficient balance
    if ($sender['account_balance'] < $amount) {
        $pdo->rollBack();
        APIResponse::error('Insufficient balance. Current balance: R' . number_format($sender['account_balance'], 2), 400);
    }
    
    // Check if trying to send to self
    if ($sender['phone'] === $recipientPhone) {
        $pdo->rollBack();
        APIResponse::error('Cannot transfer money to yourself', 400);
    }
    
    // Get recipient information
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, phone, account_balance 
        FROM users 
        WHERE phone = ?
    ");
    $stmt->execute([$recipientPhone]);
    $recipient = $stmt->fetch();
    
    if (!$recipient) {
        $pdo->rollBack();
        APIResponse::error('Recipient not found. Please check the phone number.', 404);
    }
    
    // Generate unique reference number
    $referenceNumber = 'PL' . date('YmdHis') . rand(1000, 9999);
    
    // Update sender balance (deduct amount)
    $stmt = $pdo->prepare("
        UPDATE users 
        SET account_balance = account_balance - ?, updated_at = NOW() 
        WHERE id = ?
    ");
    $result1 = $stmt->execute([$amount, $senderId]);
    
    // Update recipient balance (add amount)
    $stmt = $pdo->prepare("
        UPDATE users 
        SET account_balance = account_balance + ?, updated_at = NOW() 
        WHERE id = ?
    ");
    $result2 = $stmt->execute([$amount, $recipient['id']]);
    
    if (!$result1 || !$result2) {
        $pdo->rollBack();
        APIResponse::error('Failed to update account balances', 500);
    }
    
    // Record the transaction
    $stmt = $pdo->prepare("
        INSERT INTO transactions (
            sender_id, recipient_id, amount, description, 
            transaction_type, status, reference_number, created_at
        ) VALUES (?, ?, ?, ?, 'transfer', 'completed', ?, NOW())
    ");
    
    $result3 = $stmt->execute([
        $senderId, 
        $recipient['id'], 
        $amount, 
        $description, 
        $referenceNumber
    ]);
    
    if (!$result3) {
        $pdo->rollBack();
        APIResponse::error('Failed to record transaction', 500);
    }
    
    // Get transaction ID
    $transactionId = $pdo->lastInsertId();
    
    // Commit the transaction
    $pdo->commit();
    
    // Get updated sender balance
    $stmt = $pdo->prepare("SELECT account_balance FROM users WHERE id = ?");
    $stmt->execute([$senderId]);
    $newBalance = $stmt->fetchColumn();
    
    // Return success response
    APIResponse::success([
        'transaction' => [
            'id' => $transactionId,
            'reference_number' => $referenceNumber,
            'amount' => $amount, // Raw amount for calculations
            'amount_formatted' => number_format($amount, 2, '.', ','), // Formatted with commas
            'recipient' => [
                'name' => $recipient['first_name'] . ' ' . $recipient['last_name'],
                'phone' => $recipient['phone']
            ],
            'description' => $description,
            'status' => 'completed'
        ],
        'sender_new_balance' => $newBalance, // Raw balance for calculations
        'sender_new_balance_formatted' => number_format($newBalance, 2, '.', ','),
        'transfer_fee' => '0.00' // No fees for now
    ], 'Transfer completed successfully', 201);
    
} catch (PDOException $e) {
    // Rollback on any database error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Transfer error: " . $e->getMessage());
    APIResponse::error('Transfer failed due to database error', 500);
} catch (Exception $e) {
    // Rollback on any other error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Transfer error: " . $e->getMessage());
    APIResponse::error('Transfer failed', 500);
}
?>