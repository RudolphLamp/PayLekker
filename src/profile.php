<?php
/**
 * PayLekker API - User Profile Endpoint
 * GET /profile - Get current user information (requires authentication)
 */

require_once 'database.php';
require_once 'jwt.php';
require_once 'response.php';

// Set CORS headers
APIResponse::setCorsHeaders();

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    APIResponse::error('Method not allowed. Use GET.', 405);
}

// Require authentication
$userData = JWTAuth::requireAuth();

try {
    // Get fresh user data from database
    $stmt = $pdo->prepare("
        SELECT id, name, email, phone, balance, created_at, updated_at 
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
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'balance' => number_format($user['balance'], 2),
            'created_at' => $user['created_at'],
            'updated_at' => $user['updated_at']
        ]
    ], 'User profile retrieved successfully');
    
} catch (PDOException $e) {
    error_log("Profile error: " . $e->getMessage());
    APIResponse::error('Database error occurred', 500);
}
?>