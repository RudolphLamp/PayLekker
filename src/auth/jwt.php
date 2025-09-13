<?php
/**
 * PayLekker API - JWT Authentication
 * Simple JWT token generation and validation
 */

class JWT {
    private static $secret = 'PayLekker2025SecretKey!@#$%^&*()_+';
    private static $algorithm = 'sha256';
    
    /**
     * Generate JWT token
     */
    public static function encode($payload, $expiry = 3600) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        // Add expiry time to payload
        $payload['exp'] = time() + $expiry;
        $payload['iat'] = time();
        
        $payload = json_encode($payload);
        
        $headerEncoded = self::base64UrlEncode($header);
        $payloadEncoded = self::base64UrlEncode($payload);
        
        $signature = hash_hmac(self::$algorithm, $headerEncoded . "." . $payloadEncoded, self::$secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;
    }
    
    /**
     * Validate and decode JWT token
     */
    public static function decode($jwt) {
        $parts = explode('.', $jwt);
        
        if (count($parts) !== 3) {
            return false;
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        $header = json_decode(self::base64UrlDecode($headerEncoded), true);
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        $signature = self::base64UrlDecode($signatureEncoded);
        
        // Verify signature
        $expectedSignature = hash_hmac(self::$algorithm, $headerEncoded . "." . $payloadEncoded, self::$secret, true);
        
        if (!hash_equals($signature, $expectedSignature)) {
            return false;
        }
        
        // Check if token is expired
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }
    
    /**
     * Base64 URL-safe encode
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL-safe decode
     */
    private static function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}

/**
 * Authentication middleware
 */
class AuthMiddleware {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Verify JWT token from Authorization header
     */
    public function verifyToken() {
        $headers = getallheaders();
        
        if (!isset($headers['Authorization'])) {
            return $this->respondUnauthorized('Authorization header missing');
        }
        
        $authHeader = $headers['Authorization'];
        
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->respondUnauthorized('Invalid authorization format');
        }
        
        $token = $matches[1];
        $decoded = JWT::decode($token);
        
        if (!$decoded) {
            return $this->respondUnauthorized('Invalid or expired token');
        }
        
        // Verify token exists in sessions table
        $query = "SELECT u.* FROM users u 
                  JOIN sessions s ON u.id = s.user_id 
                  WHERE s.token_hash = ? AND s.expires_at > NOW()";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([hash('sha256', $token)]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return $this->respondUnauthorized('Session not found or expired');
        }
        
        return $user;
    }
    
    /**
     * Store token in sessions table
     */
    public function storeSession($userId, $token, $expiryHours = 24) {
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + ($expiryHours * 3600));
        
        $query = "INSERT INTO sessions (user_id, token_hash, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$userId, $tokenHash, $expiresAt]);
    }
    
    /**
     * Remove token from sessions table
     */
    public function destroySession($token) {
        $tokenHash = hash('sha256', $token);
        
        $query = "DELETE FROM sessions WHERE token_hash = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$tokenHash]);
    }
    
    private function respondUnauthorized($message) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized', 'message' => $message]);
        exit;
    }
}
?>