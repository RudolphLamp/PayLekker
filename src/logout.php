<?php
/**
 * PayLekker API - User Logout Endpoint
 * POST /logout - Invalidate user token (client-side mainly)
 */

require_once 'jwt.php';
require_once 'response.php';

// Set CORS headers
APIResponse::setCorsHeaders();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    APIResponse::error('Method not allowed. Use POST.', 405);
}

// Require authentication
$userData = JWTAuth::requireAuth();

// Since JWT tokens are stateless, we can't truly "invalidate" them server-side
// without maintaining a blacklist. For simplicity, we'll just return success
// and let the client handle token removal.

// In a production app, you might:
// 1. Add token to a blacklist table in database
// 2. Use shorter token expiry times
// 3. Use refresh tokens

APIResponse::success([
    'message' => 'Please remove the token from your client storage'
], 'Logged out successfully');
?>