<?php
/**
 * PayLekker API - User Login Endpoint
 * POST /login - Authenticate user and return token
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

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    APIResponse::error('Invalid JSON input', 400);
}

// Validate required fields
APIResponse::validateRequired($input, ['email', 'password']);

$email = trim(strtolower($input['email']));
$password = $input['password'];

// Validate email format
APIResponse::validateEmail($email);

try {
    // Get user from database
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, password_hash, phone, account_balance, created_at 
        FROM users 
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Check if user exists and password is correct
    if (!$user || !password_verify($password, $user['password_hash'])) {
        APIResponse::error('Invalid email or password', 401);
    }
    
    // Generate JWT token
    $token = JWTAuth::generateToken($user['id'], $user['email']);
    
    // Return success response with user data and token
    APIResponse::success([
        'user' => [
            'id' => (int)$user['id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'account_balance' => (float)$user['account_balance'],
            'created_at' => $user['created_at']
        ],
        'token' => $token,
        'user_id' => (int)$user['id']
    ], 'Login successful');
    
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    APIResponse::error('Database error occurred', 500);
}
?>