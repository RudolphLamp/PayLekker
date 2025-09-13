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
        SELECT id, name, email, password, phone, balance, created_at 
        FROM users 
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Check if user exists and password is correct
    if (!$user || !password_verify($password, $user['password'])) {
        APIResponse::error('Invalid email or password', 401);
    }
    
    // Generate JWT token
    $token = JWTAuth::generateToken($user['id'], $user['email']);
    
    // Update last login (optional - you can add this field to users table)
    // $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    // $stmt->execute([$user['id']]);
    
    // Return success response with user data and token
    APIResponse::success([
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'balance' => number_format($user['balance'], 2),
            'created_at' => $user['created_at']
        ],
        'token' => $token
    ], 'Login successful');
    
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    APIResponse::error('Database error occurred', 500);
}
?>