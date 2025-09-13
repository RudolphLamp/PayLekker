<?php
/**
 * PayLekker API - Main Router
 * Routes requests to appropriate controllers
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in production
ini_set('log_errors', 1);

// Basic security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Rate limiting (basic implementation)
session_start();
$clientIP = $_SERVER['REMOTE_ADDR'];
$currentTime = time();
$rateLimit = 100; // requests per hour
$timeWindow = 3600; // 1 hour

if (!isset($_SESSION['api_requests'])) {
    $_SESSION['api_requests'] = [];
}

// Clean old requests
$_SESSION['api_requests'] = array_filter($_SESSION['api_requests'], function($timestamp) use ($currentTime, $timeWindow) {
    return ($currentTime - $timestamp) < $timeWindow;
});

// Check rate limit
if (count($_SESSION['api_requests']) >= $rateLimit) {
    http_response_code(429);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Too Many Requests', 
        'message' => 'Rate limit exceeded. Please try again later.'
    ]);
    exit;
}

// Record this request
$_SESSION['api_requests'][] = $currentTime;

// Parse the request URI
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove base path if needed (adjust for your hosting setup)
$basePath = '/src/api';
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

// Clean the URI
$requestUri = trim($requestUri, '/');
$uriParts = explode('/', $requestUri);

// Route the request
switch ($uriParts[0]) {
    case 'auth':
        if (isset($uriParts[1])) {
            // Handle auth endpoints (register, login, logout)
            require_once __DIR__ . '/auth.php';
        } else {
            respondError(404, 'Auth endpoint not specified');
        }
        break;
        
    case 'transfer':
        require_once __DIR__ . '/transfer.php';
        break;
        
    case 'transactions':
        // Redirect to transfer controller for transaction history
        require_once __DIR__ . '/transfer.php';
        break;
        
    case 'budget':
        require_once __DIR__ . '/budget.php';
        break;
        
    case 'spending':
        // Redirect to budget controller for spending analysis
        require_once __DIR__ . '/budget.php';
        break;
        
    case 'chatbot':
        require_once __DIR__ . '/chatbot.php';
        break;
        
    case 'health':
        // Health check endpoint
        respondSuccess([
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0.0',
            'service' => 'PayLekker API'
        ]);
        break;
        
    case '':
        // API root - show available endpoints
        respondSuccess([
            'message' => 'PayLekker API v1.0.0',
            'endpoints' => [
                'POST /auth/register' => 'User registration',
                'POST /auth/login' => 'User login',
                'POST /auth/logout' => 'User logout',
                'POST /transfer' => 'Send money',
                'GET /transactions' => 'Get transaction history',
                'GET /budget' => 'Get budgets',
                'POST /budget' => 'Create budget',
                'PUT /budget' => 'Update budget',
                'DELETE /budget' => 'Delete budget',
                'GET /spending' => 'Get spending analysis',
                'POST /chatbot' => 'Chat with support bot',
                'GET /health' => 'Health check'
            ],
            'documentation' => 'https://pay.sewdani.co.za/docs'
        ]);
        break;
        
    default:
        respondError(404, 'Endpoint not found');
}

/**
 * Send success response
 */
function respondSuccess($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Send error response
 */
function respondError($code, $message) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

/**
 * Validate JSON input
 */
function validateJsonInput() {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        respondError(400, 'Invalid JSON input');
    }
    
    return $data;
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    } else {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
}
?>