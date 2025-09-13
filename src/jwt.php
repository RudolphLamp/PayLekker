<?php
/**
 * PayLekker API - JWT Authentication Helper
 * Simple JWT token functions for user authentication
 */

class JWTAuth {
    private static $secret_key = 'paylekker_secret_key_2024_south_africa';
    
    /**
     * Generate JWT token for user
     */
    public static function generateToken($userId, $email) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'user_id' => $userId,
            'email' => $email,
            'exp' => time() + (24 * 60 * 60), // 24 hours
            'iat' => time() // issued at
        ]);
        
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, self::$secret_key, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }
    
    /**
     * Validate JWT token
     */
    public static function validateToken($token) {
        if (!$token) return false;
        
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;
        
        try {
            // Decode header and payload
            $header = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[0])), true);
            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
            
            // Check if token is expired
            if ($payload['exp'] < time()) return false;
            
            // Verify signature
            $signature = str_replace(['-', '_'], ['+', '/'], $parts[2]);
            $expectedSignature = hash_hmac('sha256', $parts[0] . "." . $parts[1], self::$secret_key, true);
            $expectedSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));
            
            if ($parts[2] !== $expectedSignature) return false;
            
            return $payload;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get token from Authorization header
     */
    public static function getTokenFromHeader() {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (!$authHeader) {
            // Try alternative header names
            $authHeader = $_SERVER['Authorization'] ?? '';
        }
        
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Require authentication middleware
     */
    public static function requireAuth() {
        $token = self::getTokenFromHeader();
        $payload = self::validateToken($token);
        
        if (!$payload) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid or expired token. Please login again.'
            ]);
            exit;
        }
        
        return $payload; // Return user data from token
    }
}
?>