<?php
/**
 * PayLekker API - Response Helper Functions
 * Standardized JSON response functions
 */

class APIResponse {
    
    /**
     * Send successful response
     */
    public static function success($data = [], $message = 'Success', $code = 200) {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    /**
     * Send error response
     */
    public static function error($message, $code = 400, $details = null) {
        http_response_code($code);
        $response = [
            'success' => false,
            'error' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if ($details) {
            $response['details'] = $details;
        }
        
        echo json_encode($response);
        exit;
    }
    
    /**
     * Validate required fields in input
     */
    public static function validateRequired($input, $requiredFields) {
        $missing = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($input[$field]) || empty(trim($input[$field]))) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            self::error('Missing required fields: ' . implode(', ', $missing), 400);
        }
    }
    
    /**
     * Validate email format
     */
    public static function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::error('Invalid email format', 400);
        }
    }
    
    /**
     * Validate password strength
     */
    public static function validatePassword($password) {
        if (strlen($password) < 6) {
            self::error('Password must be at least 6 characters long', 400);
        }
        
        // Optional: Add more password requirements
        // if (!preg_match('/[A-Z]/', $password)) {
        //     self::error('Password must contain at least one uppercase letter', 400);
        // }
    }
    
    /**
     * Set CORS headers for API
     */
    public static function setCorsHeaders() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}
?>