<?php
/**
 * PayLekker API - Token Test Endpoint
 * Simple endpoint to test JWT token validation
 */

require_once 'jwt.php';
require_once 'response.php';

// Set CORS headers
APIResponse::setCorsHeaders();

// Allow GET and POST
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    APIResponse::error('Method not allowed. Use GET or POST.', 405);
}

try {
    // This will test all our token validation methods
    $userData = JWTAuth::requireAuth();
    
    APIResponse::success([
        'message' => 'Token is valid',
        'user_data' => $userData,
        'server_info' => [
            'method' => $_SERVER['REQUEST_METHOD'],
            'has_auth_header' => isset($_SERVER['HTTP_AUTHORIZATION']),
            'has_alt_auth_header' => isset($_SERVER['Authorization']),
            'has_token_param' => isset($_GET['token']) || isset($_POST['token'])
        ]
    ], 'Token validation successful');
    
} catch (Exception $e) {
    error_log("Token test error: " . $e->getMessage());
    APIResponse::error('Token test failed: ' . $e->getMessage(), 500);
}
?>