<?php
/**
 * PayLekker API - Budget Management
 * Handles budget categories and tracking
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/includes/database.php';

function respondSuccess($data = [], $message = 'Success') {
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
    exit;
}

function respondError($code, $message) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
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

if (!isset($pdo) || !$pdo) {
    respondError(500, 'Database connection failed');
}

// Get token
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    respondError(401, 'No token provided');
}

$token = $matches[1];
$payload = validateToken($token);
if (!$payload) {
    respondError(401, 'Invalid or expired token');
}

$userId = $payload['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

switch ($action) {
    case 'list':
        try {
            $stmt = $pdo->prepare("SELECT id, name, budget_amount, spent_amount, created_at FROM budget_categories WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
            $categories = $stmt->fetchAll();
            
            respondSuccess(['categories' => $categories], 'Budget categories retrieved');
            
        } catch (PDOException $e) {
            respondError(500, 'Database error: ' . $e->getMessage());
        }
        break;
        
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') respondError(405, 'Method not allowed');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $name = $input['name'] ?? '';
        $budgetAmount = floatval($input['budget_amount'] ?? 0);
        
        if (!$name || $budgetAmount <= 0) {
            respondError(400, 'Category name and valid budget amount are required');
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO budget_categories (user_id, name, budget_amount, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$userId, $name, $budgetAmount]);
            
            respondSuccess([
                'category_id' => $pdo->lastInsertId(),
                'name' => $name,
                'budget_amount' => number_format($budgetAmount, 2)
            ], 'Budget category created');
            
        } catch (PDOException $e) {
            respondError(500, 'Database error: ' . $e->getMessage());
        }
        break;
        
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') respondError(405, 'Method not allowed');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $categoryId = intval($_GET['id'] ?? 0);
        $spentAmount = floatval($input['spent_amount'] ?? 0);
        
        if (!$categoryId || $spentAmount < 0) {
            respondError(400, 'Category ID and valid spent amount are required');
        }
        
        try {
            $stmt = $pdo->prepare("UPDATE budget_categories SET spent_amount = ? WHERE id = ? AND user_id = ?");
            $result = $stmt->execute([$spentAmount, $categoryId, $userId]);
            
            if ($stmt->rowCount() === 0) {
                respondError(404, 'Budget category not found');
            }
            
            respondSuccess(['spent_amount' => number_format($spentAmount, 2)], 'Budget category updated');
            
        } catch (PDOException $e) {
            respondError(500, 'Database error: ' . $e->getMessage());
        }
        break;
        
    default:
        respondError(404, 'Action not found. Use ?action=list, ?action=create, or ?action=update');
}
?>