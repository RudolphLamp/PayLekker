<?php
/**
 * PayLekker API - User Profile Endpoint
 * GET /profile - Get current user information (requires authentication)
 * POST /profile - Update user profile or add funds (requires authentication)
 */

require_once 'database.php';
require_once 'jwt.php';
require_once 'response.php';

// Set CORS headers
APIResponse::setCorsHeaders();

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

// Require authentication
$userData = JWTAuth::requireAuth();

if ($method === 'GET') {
    // Handle GET request - return user profile
    try {
        // Get fresh user data from database
        $stmt = $pdo->prepare("
            SELECT id, first_name, last_name, email, phone, account_balance, created_at, updated_at 
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$userData['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            APIResponse::error('User not found', 404);
        }
        
        // Return user profile data
        APIResponse::success([
            'user' => [
                'id' => $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'account_balance' => number_format($user['account_balance'], 2),
                'balance' => $user['account_balance'], // For JS access
                'created_at' => $user['created_at'],
                'updated_at' => $user['updated_at']
            ]
        ], 'User profile retrieved successfully');
        
    } catch (PDOException $e) {
        APIResponse::error('Database error: ' . $e->getMessage(), 500);
    }

} elseif ($method === 'POST') {
    // Handle POST request - profile updates or add funds
    
    // Check if it's form data or JSON
    $input = null;
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
        $input = $_POST;
    } else {
        $input = json_decode(file_get_contents('php://input'), true);
    }
    
    if (!$input || !isset($input['action'])) {
        APIResponse::error('Invalid input or missing action', 400);
    }
    
    if ($input['action'] === 'add_funds') {
        // Handle add funds request
        $amount = floatval($input['amount'] ?? 0);
        
        if ($amount <= 0 || $amount > 10000) {
            APIResponse::error('Invalid amount. Must be between 0.01 and 10000.00', 400);
        }
        
        try {
            // Update user balance
            $stmt = $pdo->prepare("
                UPDATE users 
                SET account_balance = account_balance + ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$amount, $userData['user_id']]);
            
            // Get updated balance
            $stmt = $pdo->prepare("SELECT account_balance FROM users WHERE id = ?");
            $stmt->execute([$userData['user_id']]);
            $newBalance = $stmt->fetchColumn();
            
            APIResponse::success([
                'new_balance' => $newBalance,
                'amount_added' => $amount
            ], 'Funds added successfully');
            
        } catch (PDOException $e) {
            APIResponse::error('Database error: ' . $e->getMessage(), 500);
        }
    } else {
        APIResponse::error('Unknown action', 400);
    }
    
} else {
    APIResponse::error('Method not allowed', 405);
}
?>