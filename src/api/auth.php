<?php
/**
 * PayLekker API - Authentication Endpoints
 * Handles user registration, login, and logout
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

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/jwt.php';

class AuthController {
    private $db;
    private $auth;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->auth = new AuthMiddleware($this->db);
        
        if (!$this->db) {
            $this->respondError(500, 'Database connection failed');
        }
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Extract endpoint from path
        $pathParts = explode('/', trim($path, '/'));
        $endpoint = end($pathParts);
        
        switch ($endpoint) {
            case 'register':
                if ($method !== 'POST') {
                    $this->respondError(405, 'Method not allowed');
                }
                $this->register();
                break;
                
            case 'login':
                if ($method !== 'POST') {
                    $this->respondError(405, 'Method not allowed');
                }
                $this->login();
                break;
                
            case 'logout':
                if ($method !== 'POST') {
                    $this->respondError(405, 'Method not allowed');
                }
                $this->logout();
                break;
                
            default:
                $this->respondError(404, 'Endpoint not found');
        }
    }
    
    /**
     * Register new user
     */
    private function register() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate input
        if (!$this->validateRegistrationInput($input)) {
            return;
        }
        
        $email = trim($input['email']);
        $password = $input['password'];
        $firstName = trim($input['first_name']);
        $lastName = trim($input['last_name']);
        $phone = isset($input['phone']) ? trim($input['phone']) : null;
        
        // Check if user already exists
        $checkQuery = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->db->prepare($checkQuery);
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $this->respondError(409, 'Email already registered');
            return;
        }
        
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $insertQuery = "INSERT INTO users (email, password_hash, first_name, last_name, phone) 
                        VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($insertQuery);
        
        try {
            $stmt->execute([$email, $passwordHash, $firstName, $lastName, $phone]);
            $userId = $this->db->lastInsertId();
            
            // Generate JWT token
            $payload = [
                'user_id' => $userId,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName
            ];
            
            $token = JWT::encode($payload, 86400); // 24 hours
            $this->auth->storeSession($userId, $token);
            
            $this->respondSuccess([
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $userId,
                    'email' => $email,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone,
                    'balance' => 0.00
                ],
                'token' => $token
            ]);
            
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $this->respondError(500, 'Registration failed');
        }
    }
    
    /**
     * User login
     */
    private function login() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['email']) || !isset($input['password'])) {
            $this->respondError(400, 'Email and password are required');
            return;
        }
        
        $email = trim($input['email']);
        $password = $input['password'];
        
        // Find user
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->respondError(401, 'Invalid email or password');
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
        $this->auth->storeSession($user['id'], $token);
        
        // Remove sensitive data
        unset($user['password_hash']);
        
        $this->respondSuccess([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }
    
    /**
     * User logout
     */
    private function logout() {
        $headers = getallheaders();
        
        if (!isset($headers['Authorization'])) {
            $this->respondError(400, 'Authorization header required');
            return;
        }
        
        $authHeader = $headers['Authorization'];
        
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $this->auth->destroySession($token);
        }
        
        $this->respondSuccess(['message' => 'Logout successful']);
    }
    
    /**
     * Validate registration input
     */
    private function validateRegistrationInput($input) {
        $required = ['email', 'password', 'first_name', 'last_name'];
        
        foreach ($required as $field) {
            if (!isset($input[$field]) || empty(trim($input[$field]))) {
                $this->respondError(400, "Field '$field' is required");
                return false;
            }
        }
        
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $this->respondError(400, 'Invalid email format');
            return false;
        }
        
        if (strlen($input['password']) < 6) {
            $this->respondError(400, 'Password must be at least 6 characters');
            return false;
        }
        
        return true;
    }
    
    /**
     * Send success response
     */
    private function respondSuccess($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
    
    /**
     * Send error response
     */
    private function respondError($code, $message) {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit;
    }
}

// Handle the request
$controller = new AuthController();
$controller->handleRequest();
?>