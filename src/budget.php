<?php
/**
 * PayLekker API - Budget Management Endpoint
 * GET /budget.php - Get user's budget categories
 * POST /budget.php - Create new budget category
 * PUT /budget.php - Update budget category
 * DELETE /budget.php - Delete budget category
 */

require_once 'database.php';
require_once 'jwt.php';
require_once 'response.php';

// Set CORS headers
APIResponse::setCorsHeaders();

// Require authentication
$userData = JWTAuth::requireAuth();
$userId = $userData['user_id'];

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
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
        APIResponse::error('Method not allowed', 405);
}

/**
 * Get user's budget categories
 */
function handleGetBudgets($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                id,
                category_name,
                budget_amount,
                spent_amount,
                period,
                start_date,
                end_date,
                created_at,
                updated_at
            FROM budget_categories 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        $budgets = $stmt->fetchAll();
        
        // Calculate remaining amounts and percentages
        $formattedBudgets = [];
        foreach ($budgets as $budget) {
            $remaining = $budget['budget_amount'] - $budget['spent_amount'];
            $percentage = ($budget['budget_amount'] > 0) 
                ? round(($budget['spent_amount'] / $budget['budget_amount']) * 100, 1) 
                : 0;
            
            $formattedBudgets[] = [
                'id' => $budget['id'],
                'category_name' => $budget['category_name'],
                'budget_amount' => number_format($budget['budget_amount'], 2),
                'spent_amount' => number_format($budget['spent_amount'], 2),
                'remaining_amount' => number_format($remaining, 2),
                'percentage_used' => $percentage,
                'period' => $budget['period'],
                'start_date' => $budget['start_date'],
                'end_date' => $budget['end_date'],
                'status' => ($remaining >= 0) ? 'on_track' : 'over_budget',
                'created_at' => $budget['created_at'],
                'updated_at' => $budget['updated_at']
            ];
        }
        
        // Calculate total budget summary
        $totalBudget = array_sum(array_column($budgets, 'budget_amount'));
        $totalSpent = array_sum(array_column($budgets, 'spent_amount'));
        $totalRemaining = $totalBudget - $totalSpent;
        
        APIResponse::success([
            'budgets' => $formattedBudgets,
            'summary' => [
                'total_budget' => number_format($totalBudget, 2),
                'total_spent' => number_format($totalSpent, 2),
                'total_remaining' => number_format($totalRemaining, 2),
                'overall_percentage' => ($totalBudget > 0) ? round(($totalSpent / $totalBudget) * 100, 1) : 0
            ]
        ], 'Budget categories retrieved successfully');
        
    } catch (PDOException $e) {
        error_log("Get budgets error: " . $e->getMessage());
        APIResponse::error('Failed to retrieve budget categories', 500);
    }
}

/**
 * Create new budget category
 */
function handleCreateBudget($pdo, $userId) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        APIResponse::error('Invalid JSON input', 400);
    }
    
    // Validate required fields
    APIResponse::validateRequired($input, ['category_name', 'budget_amount']);
    
    $categoryName = trim($input['category_name']);
    $budgetAmount = floatval($input['budget_amount']);
    $period = $input['period'] ?? 'monthly';
    $startDate = $input['start_date'] ?? date('Y-m-01'); // First day of current month
    $endDate = $input['end_date'] ?? date('Y-m-t'); // Last day of current month
    
    // Validate inputs
    if (strlen($categoryName) < 2 || strlen($categoryName) > 100) {
        APIResponse::error('Category name must be between 2 and 100 characters', 400);
    }
    
    if ($budgetAmount <= 0 || $budgetAmount > 1000000) {
        APIResponse::error('Budget amount must be between R0.01 and R1,000,000', 400);
    }
    
    if (!in_array($period, ['weekly', 'monthly', 'yearly'])) {
        APIResponse::error('Period must be: weekly, monthly, or yearly', 400);
    }
    
    // Validate dates
    if (!strtotime($startDate) || !strtotime($endDate)) {
        APIResponse::error('Invalid date format. Use YYYY-MM-DD', 400);
    }
    
    if (strtotime($startDate) >= strtotime($endDate)) {
        APIResponse::error('Start date must be before end date', 400);
    }
    
    try {
        // Check if category already exists for this user
        $stmt = $pdo->prepare("
            SELECT id FROM budget_categories 
            WHERE user_id = ? AND category_name = ?
        ");
        $stmt->execute([$userId, $categoryName]);
        
        if ($stmt->fetch()) {
            APIResponse::error('Budget category already exists with this name', 409);
        }
        
        // Create new budget category
        $stmt = $pdo->prepare("
            INSERT INTO budget_categories (
                user_id, category_name, budget_amount, period, 
                start_date, end_date, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $userId, $categoryName, $budgetAmount, 
            $period, $startDate, $endDate
        ]);
        
        if (!$result) {
            APIResponse::error('Failed to create budget category', 500);
        }
        
        $budgetId = $pdo->lastInsertId();
        
        APIResponse::success([
            'budget' => [
                'id' => $budgetId,
                'category_name' => $categoryName,
                'budget_amount' => number_format($budgetAmount, 2),
                'spent_amount' => '0.00',
                'remaining_amount' => number_format($budgetAmount, 2),
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ], 'Budget category created successfully', 201);
        
    } catch (PDOException $e) {
        error_log("Create budget error: " . $e->getMessage());
        APIResponse::error('Failed to create budget category', 500);
    }
}

/**
 * Update budget category
 */
function handleUpdateBudget($pdo, $userId) {
    $budgetId = intval($_GET['id'] ?? 0);
    
    if (!$budgetId) {
        APIResponse::error('Budget ID is required in query parameter', 400);
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        APIResponse::error('Invalid JSON input', 400);
    }
    
    try {
        // Check if budget belongs to user
        $stmt = $pdo->prepare("
            SELECT id, category_name, budget_amount, spent_amount, period, start_date, end_date 
            FROM budget_categories 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$budgetId, $userId]);
        $budget = $stmt->fetch();
        
        if (!$budget) {
            APIResponse::error('Budget category not found', 404);
        }
        
        // Update fields if provided
        $spentAmount = isset($input['spent_amount']) ? floatval($input['spent_amount']) : $budget['spent_amount'];
        $budgetAmount = isset($input['budget_amount']) ? floatval($input['budget_amount']) : $budget['budget_amount'];
        
        // Validate amounts
        if ($spentAmount < 0) {
            APIResponse::error('Spent amount cannot be negative', 400);
        }
        
        if ($budgetAmount <= 0) {
            APIResponse::error('Budget amount must be greater than 0', 400);
        }
        
        // Update the budget
        $stmt = $pdo->prepare("
            UPDATE budget_categories 
            SET spent_amount = ?, budget_amount = ?, updated_at = NOW() 
            WHERE id = ? AND user_id = ?
        ");
        
        $result = $stmt->execute([$spentAmount, $budgetAmount, $budgetId, $userId]);
        
        if (!$result) {
            APIResponse::error('Failed to update budget category', 500);
        }
        
        $remaining = $budgetAmount - $spentAmount;
        $percentage = ($budgetAmount > 0) ? round(($spentAmount / $budgetAmount) * 100, 1) : 0;
        
        APIResponse::success([
            'budget' => [
                'id' => $budgetId,
                'spent_amount' => number_format($spentAmount, 2),
                'budget_amount' => number_format($budgetAmount, 2),
                'remaining_amount' => number_format($remaining, 2),
                'percentage_used' => $percentage,
                'status' => ($remaining >= 0) ? 'on_track' : 'over_budget'
            ]
        ], 'Budget category updated successfully');
        
    } catch (PDOException $e) {
        error_log("Update budget error: " . $e->getMessage());
        APIResponse::error('Failed to update budget category', 500);
    }
}

/**
 * Delete budget category
 */
function handleDeleteBudget($pdo, $userId) {
    $budgetId = intval($_GET['id'] ?? 0);
    
    if (!$budgetId) {
        APIResponse::error('Budget ID is required in query parameter', 400);
    }
    
    try {
        // Check if budget belongs to user and delete
        $stmt = $pdo->prepare("
            DELETE FROM budget_categories 
            WHERE id = ? AND user_id = ?
        ");
        $result = $stmt->execute([$budgetId, $userId]);
        
        if ($stmt->rowCount() === 0) {
            APIResponse::error('Budget category not found', 404);
        }
        
        APIResponse::success([], 'Budget category deleted successfully');
        
    } catch (PDOException $e) {
        error_log("Delete budget error: " . $e->getMessage());
        APIResponse::error('Failed to delete budget category', 500);
    }
}
?>