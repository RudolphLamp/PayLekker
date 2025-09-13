<?php
/**
 * PayLekker Hosting-Friendly API Router
 * Simple API router that works without .htaccess
 * Use: api_router.php?endpoint=auth&action=register
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Set error reporting for debugging
ini_set('display_errors', 0); // Disable for production
error_reporting(E_ALL);

// Get endpoint and action from URL parameters
$endpoint = $_GET['endpoint'] ?? '';
$action = $_GET['action'] ?? '';

// Simple routing without .htaccess
switch ($endpoint) {
    case 'auth':
        handleAuth($action);
        break;
    
    case 'transfers':
        handleTransfers($action);
        break;
    
    case 'budget':
        handleBudget($action);
        break;
    
    case 'transactions':
        handleTransactions($action);
        break;
    
    case 'chatbot':
        handleChatbot($action);
        break;
    
    case 'test':
        handleTest();
        break;
    
    default:
        respondWithError(404, 'Unknown endpoint: ' . $endpoint);
}

/**
 * Handle authentication endpoints
 */
function handleAuth($action) {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/auth/jwt.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        respondWithError(500, 'Database connection failed');
        return;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($action) {
        case 'register':
            handleRegistration($db, $input);
            break;
        
        case 'login':
            handleLogin($db, $input);
            break;
        
        case 'logout':
            handleLogout($input);
            break;
        
        case 'check_user':
            handleCheckUser($db, $input);
            break;
        
        default:
            respondWithError(404, 'Unknown auth action: ' . $action);
    }
}

/**
 * Handle user registration
 */
function handleRegistration($db, $input) {
    // Validate input
    if (!$input || !isset($input['email']) || !isset($input['password']) || 
        !isset($input['first_name']) || !isset($input['last_name'])) {
        respondWithError(400, 'Missing required fields: email, password, first_name, last_name');
        return;
    }
    
    $email = trim($input['email']);
    $password = $input['password'];
    $firstName = trim($input['first_name']);
    $lastName = trim($input['last_name']);
    $phone = isset($input['phone']) ? trim($input['phone']) : null;
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respondWithError(400, 'Invalid email format');
        return;
    }
    
    // Validate password
    if (strlen($password) < 6) {
        respondWithError(400, 'Password must be at least 6 characters long');
        return;
    }
    
    // Check if user already exists
    try {
        $checkQuery = "SELECT id FROM users WHERE email = ?";
        $stmt = $db->prepare($checkQuery);
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            respondWithError(409, 'Email already registered');
            return;
        }
        
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $insertQuery = "INSERT INTO users (email, password_hash, first_name, last_name, phone, balance, created_at) 
                        VALUES (?, ?, ?, ?, ?, 0.00, NOW())";
        $stmt = $db->prepare($insertQuery);
        $stmt->execute([$email, $passwordHash, $firstName, $lastName, $phone]);
        
        $userId = $db->lastInsertId();
        
        // Generate JWT token
        $payload = [
            'user_id' => $userId,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName
        ];
        
        $token = JWT::encode($payload, 86400); // 24 hours
        
        // Store session
        $sessionQuery = "INSERT INTO sessions (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))";
        $sessionStmt = $db->prepare($sessionQuery);
        $sessionStmt->execute([$userId, $token]);
        
        respondWithSuccess([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $userId,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'balance' => '0.00'
            ],
            'token' => $token
        ]);
        
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        respondWithError(500, 'Registration failed: ' . $e->getMessage());
    }
}

/**
 * Handle user login
 */
function handleLogin($db, $input) {
    if (!$input || !isset($input['email']) || !isset($input['password'])) {
        respondWithError(400, 'Email and password are required');
        return;
    }
    
    $email = trim($input['email']);
    $password = $input['password'];
    
    try {
        // Find user
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            respondWithError(401, 'Invalid email or password');
            return;
        }
        
        // Generate JWT token
        $payload = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name']
        ];
        
        $token = JWT::encode($payload, 86400); // 24 hours
        
        // Store session
        $sessionQuery = "INSERT INTO sessions (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))";
        $sessionStmt = $db->prepare($sessionQuery);
        $sessionStmt->execute([$user['id'], $token]);
        
        // Remove sensitive data
        unset($user['password_hash']);
        
        respondWithSuccess([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
        
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        respondWithError(500, 'Login failed');
    }
}

/**
 * Handle logout
 */
function handleLogout($input) {
    $headers = getallheaders();
    
    if (!isset($headers['Authorization'])) {
        respondWithError(400, 'Authorization header required');
        return;
    }
    
    respondWithSuccess(['message' => 'Logout successful']);
}

/**
 * Handle check user
 */
function handleCheckUser($db, $input) {
    if (!$input || !isset($input['email'])) {
        respondWithError(400, 'Email is required');
        return;
    }
    
    $email = trim($input['email']);
    
    try {
        $query = "SELECT id, first_name, last_name, email FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            respondWithSuccess([
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['first_name'] . ' ' . $user['last_name'],
                    'email' => $user['email']
                ]
            ]);
        } else {
            respondWithError(404, 'User not found');
        }
        
    } catch (PDOException $e) {
        error_log("Check user error: " . $e->getMessage());
        respondWithError(500, 'Database error');
    }
}

/**
 * Handle transfers (placeholder)
 */
function handleTransfers($action) {
    respondWithError(501, 'Transfers endpoint not yet implemented in this router');
}

/**
 * Handle budget (placeholder)
 */
function handleBudget($action) {
    respondWithError(501, 'Budget endpoint not yet implemented in this router');
}

/**
 * Handle transactions (placeholder)
 */
function handleTransactions($action) {
    respondWithError(501, 'Transactions endpoint not yet implemented in this router');
}

/**
 * Handle chatbot (placeholder)
 */
function handleChatbot($action) {
    respondWithError(501, 'Chatbot endpoint not yet implemented in this router');
}

/**
 * Handle test endpoint
 */
function handleTest() {
    // Database connection test
    $dbStatus = 'disconnected';
    try {
        $database = new Database();
        $db = $database->getConnection();
        if ($db) {
            $dbStatus = 'connected';
        }
    } catch (Exception $e) {
        $dbStatus = 'error: ' . $e->getMessage();
    }
    
    respondWithSuccess([
        'message' => 'API Router is working',
        'timestamp' => date('Y-m-d H:i:s'),
        'method' => $_SERVER['REQUEST_METHOD'],
        'database' => $dbStatus,
        'version' => '1.0'
    ]);
}

/**
 * Send success response
 */
function respondWithSuccess($data) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    exit;
}

/**
 * Send error response
 */
function respondWithError($code, $message) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error' => $message,
        'code' => $code
    ]);
    exit;
}
?>