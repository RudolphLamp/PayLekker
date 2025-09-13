<?php
/**
 * PayLekker API - Budget Management Endpoint
 * GET /budget - Get user budgets
 * POST /budget - Create new budget
 * PUT /budget - Update budget
 * DELETE /budget - Delete budget
 */

require_once 'database.php';
require_once 'jwt.php';
require_once 'response.php';

// Set CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Validate authentication
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Authorization header required']);
        exit();
    }

    $token = substr($authHeader, 7);
    $userId = extractUserIdFromToken($token);
    
    if (!$userId) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
        exit();
    }

    // Create budgets table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS budgets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            category VARCHAR(50) NOT NULL,
            name VARCHAR(100) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            spent DECIMAL(10,2) DEFAULT 0.00,
            period ENUM('daily', 'weekly', 'monthly') DEFAULT 'monthly',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_category (user_id, category),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            handleGetBudgets($pdo, $userId);
            break;
        case 'POST':
            handleCreateBudget($pdo, $userId);
            break;
        case 'PUT':
            handleUpdateBudget($pdo, $userId);
            break;
        case 'DELETE':
            handleDeleteBudget($pdo, $userId);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }

} catch (Exception $e) {
    error_log("Budget API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error occurred']);
}

function handleGetBudgets($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT id, category, name, amount, spent, period, created_at 
        FROM budgets 
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    $budgets = $stmt->fetchAll();

    // Calculate spending from transactions
    foreach ($budgets as &$budget) {
        $spent = calculateSpentAmount($pdo, $userId, $budget['category'], $budget['period']);
        $budget['spent'] = $spent;
        
        // Update spent amount in database
        $updateStmt = $pdo->prepare("UPDATE budgets SET spent = ? WHERE id = ?");
        $updateStmt->execute([$spent, $budget['id']]);
    }

    echo json_encode([
        'success' => true,
        'data' => ['budgets' => $budgets]
    ]);
}

function handleCreateBudget($pdo, $userId) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['category']) || !isset($input['amount'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Category and amount are required']);
        return;
    }

    $category = trim($input['category']);
    $amount = (float)$input['amount'];
    $period = $input['period'] ?? 'monthly';
    
    // Get category display name
    $categoryNames = [
        'groceries' => '🛒 Groceries',
        'transport' => '🚗 Transport',
        'entertainment' => '🎬 Entertainment',
        'utilities' => '💡 Utilities',
        'dining' => '🍽️ Dining',
        'shopping' => '🛍️ Shopping',
        'other' => '📝 Other'
    ];
    $name = $categoryNames[$category] ?? ucfirst($category);

    if ($amount < 1 || $amount > 50000) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Amount must be between R1 and R50,000']);
        return;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO budgets (user_id, category, name, amount, period) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $result = $stmt->execute([$userId, $category, $name, $amount, $period]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $pdo->lastInsertId(),
                    'message' => 'Budget created successfully'
                ]
            ]);
        } else {
            throw new Exception('Failed to create budget');
        }
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            http_response_code(409);
            echo json_encode(['success' => false, 'error' => 'Budget for this category already exists']);
        } else {
            throw $e;
        }
    }
}

function handleUpdateBudget($pdo, $userId) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Budget ID is required']);
        return;
    }

    $budgetId = (int)$input['id'];
    $amount = isset($input['amount']) ? (float)$input['amount'] : null;
    $addSpent = isset($input['add_spent']) ? (float)$input['add_spent'] : null;
    $description = $input['description'] ?? 'Budget expense';

    if ($amount !== null && ($amount <= 0 || $amount > 50000)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Amount must be between R1 and R50,000']);
        return;
    }

    if ($addSpent !== null && $addSpent <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Expense amount must be greater than 0']);
        return;
    }

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Verify budget belongs to user
        $stmt = $pdo->prepare("SELECT id, spent, category FROM budgets WHERE id = ? AND user_id = ?");
        $stmt->execute([$budgetId, $userId]);
        $budget = $stmt->fetch();
        
        if (!$budget) {
            $pdo->rollback();
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Budget not found']);
            return;
        }

        if ($amount !== null) {
            // Update budget amount
            $stmt = $pdo->prepare("UPDATE budgets SET amount = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
            $stmt->execute([$amount, $budgetId, $userId]);
        }

        if ($addSpent !== null) {
            // Add to spent amount
            $newSpent = (float)$budget['spent'] + $addSpent;
            $stmt = $pdo->prepare("UPDATE budgets SET spent = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
            $stmt->execute([$newSpent, $budgetId, $userId]);

            // Create a transaction record for budget expense tracking
            $stmt = $pdo->prepare("
                INSERT INTO transactions (sender_id, recipient_id, amount, description, reference_number, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $referenceNumber = 'BDG' . date('YmdHis') . $budgetId;
            $fullDescription = $budget['category'] . ' - ' . $description;
            $stmt->execute([$userId, $userId, $addSpent, $fullDescription, $referenceNumber]);
        }

        // Commit transaction
        $pdo->commit();

        echo json_encode([
            'success' => true,
            'data' => ['message' => 'Budget updated successfully']
        ]);

    } catch (PDOException $e) {
        $pdo->rollback();
        error_log("Budget update error: " . $e->getMessage());
        throw $e;
    }
}

function handleDeleteBudget($pdo, $userId) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Budget ID is required']);
        return;
    }

    $budgetId = (int)$input['id'];

    $stmt = $pdo->prepare("DELETE FROM budgets WHERE id = ? AND user_id = ?");
    $result = $stmt->execute([$budgetId, $userId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'data' => ['message' => 'Budget deleted successfully']
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Budget not found']);
    }
}

function calculateSpentAmount($pdo, $userId, $category, $period) {
    // Calculate date range based on period
    $startDate = '';
    switch ($period) {
        case 'daily':
            $startDate = date('Y-m-d') . ' 00:00:00';
            break;
        case 'weekly':
            $startDate = date('Y-m-d', strtotime('monday this week')) . ' 00:00:00';
            break;
        case 'monthly':
        default:
            $startDate = date('Y-m-01') . ' 00:00:00';
            break;
    }

    // Get transactions that match the category from budget expenses
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(amount), 0) as total_spent 
        FROM transactions 
        WHERE sender_id = ? 
        AND recipient_id = sender_id
        AND created_at >= ? 
        AND (description LIKE ? OR description LIKE ?)
    ");
    $categoryPattern1 = $category . '%';
    $categoryPattern2 = '%' . $category . '%';
    $stmt->execute([$userId, $startDate, $categoryPattern1, $categoryPattern2]);
    $result = $stmt->fetch();
    
    return (float)($result['total_spent'] ?? 0);
}

function extractUserIdFromToken($token) {
    $parts = explode('.', $token);
    if (count($parts) === 3) {
        try {
            $payload = json_decode(base64_decode($parts[1]), true);
            return $payload['user_id'] ?? null;
        } catch (Exception $e) {
            error_log("Failed to decode token: " . $e->getMessage());
        }
    }
    return null;
}
?>