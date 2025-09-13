<?php
/**
 * PayLekker API - User Registration Endpoint
 * POST /register - Create new user account
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
APIResponse::validateRequired($input, ['first_name', 'last_name', 'email', 'password']);

$firstName = trim($input['first_name']);
$lastName = trim($input['last_name']);
$email = trim(strtolower($input['email']));
$password = $input['password'];
$phone = trim($input['phone'] ?? '');

// Validate email format
APIResponse::validateEmail($email);

// Validate password strength
APIResponse::validatePassword($password);

// Validate name lengths
if (strlen($firstName) < 2 || strlen($firstName) > 100) {
    APIResponse::error('First name must be between 2 and 100 characters', 400);
}

if (strlen($lastName) < 2 || strlen($lastName) > 100) {
    APIResponse::error('Last name must be between 2 and 100 characters', 400);
}

// Validate phone if provided
if ($phone && !preg_match('/^[0-9+\-\s()]{10,20}$/', $phone)) {
    APIResponse::error('Invalid phone number format', 400);
}

try {
    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        APIResponse::error('User already exists with this email address', 409);
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Create user
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, password_hash, phone, account_balance, created_at) 
        VALUES (?, ?, ?, ?, ?, 1000.00, NOW())
    ");
    
    $result = $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $phone]);
    
    if (!$result) {
        APIResponse::error('Failed to create user account', 500);
    }
    
    // Get the new user ID
    $userId = $pdo->lastInsertId();
    
    // Generate JWT token
    $token = JWTAuth::generateToken($userId, $email);
    
    // Return success response with user data and token
    APIResponse::success([
        'user' => [
            'id' => $userId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'account_balance' => '1000.00'
        ],
        'token' => $token,
        'user_id' => $userId
    ], 'User registered successfully', 201);
    
} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    
    // Check for duplicate email constraint
    if ($e->getCode() === '23000') {
        APIResponse::error('Email address is already registered', 409);
    }
    
    APIResponse::error('Database error occurred', 500);
}
?>