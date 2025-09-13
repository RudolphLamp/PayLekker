<?php
/**
 * PayLekker API - Add Funds Endpoint
 * POST /add-funds - Add funds to user account
 */

require_once 'database.php';
require_once 'jwt.php';
require_once 'response.php';

// Set CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed. Use POST.']);
    exit();
}

try {
    // Validate authentication
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Authorization header required']);
        exit();
    }

    $token = substr($authHeader, 7);
    
    if (empty($token)) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid token']);
        exit();
    }

    // Extract user ID from token
    $userId = extractUserIdFromToken($token);
    if (!$userId) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
        exit();
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['amount'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Amount is required']);
        exit();
    }

    $amount = (float)$input['amount'];

    if ($amount < 10 || $amount > 10000) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Amount must be between R10 and R10,000']);
        exit();
    }

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Get current user data with row lock
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, account_balance FROM users WHERE id = ? FOR UPDATE");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            throw new Exception('User not found');
        }

        $currentBalance = (float)$user['account_balance'];
        $newBalance = $currentBalance + $amount;

        // Update user balance in database
        $stmt = $pdo->prepare("UPDATE users SET account_balance = ? WHERE id = ?");
        $result = $stmt->execute([$newBalance, $userId]);
        
        if (!$result) {
            throw new Exception('Failed to update balance');
        }

        // Verify the update worked
        $stmt = $pdo->prepare("SELECT account_balance FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $updatedUser = $stmt->fetch();
        $verifiedBalance = (float)$updatedUser['account_balance'];

        if (abs($verifiedBalance - $newBalance) > 0.01) {
            throw new Exception('Balance update verification failed');
        }

        // Create a transaction record for the fund addition
        $stmt = $pdo->prepare("
            INSERT INTO transactions (sender_id, recipient_id, amount, description, reference_number, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $referenceNumber = 'ADD' . date('YmdHis') . $userId;
        $stmt->execute([$userId, $userId, $amount, 'Fund Addition - Demo Payment', $referenceNumber]);

        // Commit transaction
        $pdo->commit();

        // Return success response with updated user data
        echo json_encode([
            'success' => true,
            'data' => [
                'amount_added' => $amount,
                'previous_balance' => $currentBalance,
                'new_balance' => $verifiedBalance,
                'user' => [
                    'id' => (int)$user['id'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'account_balance' => $verifiedBalance
                ]
            ],
            'message' => 'Funds added successfully'
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollback();
        throw $e;
    }

} catch (Exception $e) {
    error_log("Add funds error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

/**
 * Extract user ID from token (simple method for demo)
 */
function extractUserIdFromToken($token) {
    $parts = explode('.', $token);
    if (count($parts) === 3) {
        try {
            $payload = json_decode(base64_decode($parts[1]), true);
            return $payload['user_id'] ?? null;
        } catch (Exception $e) {
            error_log("Failed to decode token: " . $e->getMessage());
        }
    }
    return null;
}
?>
