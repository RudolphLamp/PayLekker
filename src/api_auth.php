<?php
/**
 * PayLekker API - Authentication
 * Handles user registration, login, and authentication checks
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

// Include database connection
require_once __DIR__ . '/includes/database.php';

/**
 * Response helper functions
 */
function respondSuccess($data = [], $message = 'Success') {
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function respondError($code, $message) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error' => $message
    ]);
    exit;
}

/**
 * Simple JWT functions
 */
function generateToken($userId, $email) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode([
        'user_id' => $userId,
        'email' => $email,
        'exp' => time() + (24 * 60 * 60) // 24 hours
    ]);
    
    $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    
    $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, 'paylekker_secret_key_2024', true);
    $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    return $base64Header . "." . $base64Payload . "." . $base64Signature;
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

// Check if database is available
if (!isset($pdo) || !$pdo) {
    respondError(500, 'Database connection failed');
}

// Get the action from query parameter or POST data
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Handle different actions
switch ($action) {
    case 'register':
        if ($method !== 'POST') respondError(405, 'Method not allowed');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $phone = $input['phone'] ?? '';
        
        // Validation
        if (!$name || !$email || !$password) {
            respondError(400, 'Name, email, and password are required');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            respondError(400, 'Invalid email format');
        }
        
        if (strlen($password) < 6) {
            respondError(400, 'Password must be at least 6 characters');
        }
        
        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                respondError(409, 'User already exists with this email');
            }
            
            // Hash password and create user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, created_at) VALUES (?, ?, ?, ?, NOW())");
            $result = $stmt->execute([$name, $email, $hashedPassword, $phone]);
            
            if ($result) {
                $userId = $pdo->lastInsertId();
                $token = generateToken($userId, $email);
                
                respondSuccess([
                    'user' => [
                        'id' => $userId,
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'balance' => '0.00'
                    ],
                    'token' => $token
                ], 'Registration successful');
            } else {
                respondError(500, 'Registration failed');
            }
            
        } catch (PDOException $e) {
            respondError(500, 'Database error: ' . $e->getMessage());
        }
        break;
        
    case 'login':
        if ($method !== 'POST') respondError(405, 'Method not allowed');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        
        if (!$email || !$password) {
            respondError(400, 'Email and password are required');
        }
        
        try {
            $stmt = $pdo->prepare("SELECT id, name, email, password, phone, balance FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($password, $user['password'])) {
                respondError(401, 'Invalid email or password');
            }
            
            $token = generateToken($user['id'], $user['email']);
            
            respondSuccess([
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'balance' => number_format($user['balance'], 2)
                ],
                'token' => $token
            ], 'Login successful');
            
        } catch (PDOException $e) {
            respondError(500, 'Database error: ' . $e->getMessage());
        }
        break;
        
    case 'check':
        // Get token from Authorization header
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            respondError(401, 'No token provided');
        }
        
        $token = $matches[1];
        $payload = validateToken($token);
        
        if (!$payload) {
            respondError(401, 'Invalid or expired token');
        }
        
        try {
            $stmt = $pdo->prepare("SELECT id, name, email, phone, balance FROM users WHERE id = ?");
            $stmt->execute([$payload['user_id']]);
            $user = $stmt->fetch();
            
            if (!$user) {
                respondError(401, 'User not found');
            }
            
            respondSuccess([
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'balance' => number_format($user['balance'], 2)
                ]
            ], 'Token valid');
            
        } catch (PDOException $e) {
            respondError(500, 'Database error: ' . $e->getMessage());
        }
        break;
        
    default:
        respondError(404, 'Endpoint not found. Use ?action=register, ?action=login, or ?action=check');
}
?>