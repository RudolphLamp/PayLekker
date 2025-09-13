<?php
/**
 * PayLekker UI - Configuration and Utilities
 * Shared configuration and helper functions for the frontend
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuration
define('API_BASE_URL', 'https://pay.sewdani.co.za/api/');
define('SITE_NAME', 'PayLekker');
define('SITE_TAGLINE', 'South African Digital Banking Made Easy');

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user']) && isset($_SESSION['token']);
}

/**
 * Get current user data
 */
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Get current auth token
 */
function getAuthToken() {
    return $_SESSION['token'] ?? null;
}

/**
 * Redirect to login if not authenticated
 */
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Logout user
 */
function logout() {
    // Call logout API
    $token = getAuthToken();
    if ($token) {
        callAPI('POST', 'auth/logout', null, $token);
    }
    
    // Clear session
    session_unset();
    session_destroy();
    
    // Redirect to login
    header('Location: login.php');
    exit;
}

/**
 * Make API call
 */
function callAPI($method, $endpoint, $data = null, $token = null) {
    $url = API_BASE_URL . ltrim($endpoint, '/');
    
    $curl = curl_init();
    
    $headers = [
        'Content-Type: application/json',
    ];
    
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false, // For development - enable in production
    ]);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    
    curl_close($curl);
    
    if ($error) {
        return ['success' => false, 'error' => 'Connection error: ' . $error];
    }
    
    $decodedResponse = json_decode($response, true);
    
    return [
        'success' => $httpCode >= 200 && $httpCode < 300,
        'data' => $decodedResponse,
        'http_code' => $httpCode,
        'raw_response' => $response
    ];
}

/**
 * Format currency amount in Rands
 */
function formatRands($amount) {
    return 'R' . number_format((float)$amount, 2, '.', ',');
}

/**
 * Format date for display
 */
function formatDate($dateString) {
    return date('M j, Y g:i A', strtotime($dateString));
}

/**
 * Format date as relative time (e.g., "2 hours ago")
 */
function formatRelativeDate($dateString) {
    $date = new DateTime($dateString);
    $now = new DateTime();
    $diff = $now->diff($date);
    
    if ($diff->days > 0) {
        return $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
    } elseif ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    } elseif ($diff->i > 0) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    } else {
        return 'Just now';
    }
}

/**
 * Sanitize output for HTML
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get flash message
 */
function getFlashMessage($type = null) {
    if ($type) {
        $message = $_SESSION['flash_' . $type] ?? null;
        unset($_SESSION['flash_' . $type]);
        return $message;
    }
    
    $messages = [];
    foreach (['success', 'error', 'warning', 'info'] as $type) {
        if (isset($_SESSION['flash_' . $type])) {
            $messages[$type] = $_SESSION['flash_' . $type];
            unset($_SESSION['flash_' . $type]);
        }
    }
    
    return $messages;
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_' . $type] = $message;
}

/**
 * Get transaction type badge class
 */
function getTransactionBadgeClass($type, $status = 'completed') {
    if ($status !== 'completed') {
        return 'badge bg-warning';
    }
    
    switch ($type) {
        case 'sent':
            return 'badge bg-danger';
        case 'received':
            return 'badge bg-success';
        default:
            return 'badge bg-secondary';
    }
}

/**
 * Get budget status color
 */
function getBudgetStatusColor($status) {
    switch ($status) {
        case 'active':
            return 'success';
        case 'warning':
            return 'warning';
        case 'exceeded':
            return 'danger';
        case 'expired':
            return 'secondary';
        default:
            return 'primary';
    }
}
?>