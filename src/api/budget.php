<?php
/**
 * PayLekker API - Budget Management Endpoints
 * Handles budgets and spending tracking
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/jwt.php';

class BudgetController {
    private $db;
    private $auth;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->auth = new AuthMiddleware($this->db);
        
        if (!$this->db) {
            $this->respondError(500, 'Database connection failed');
        }
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Extract endpoint from path
        $pathParts = explode('/', trim($path, '/'));
        $endpoint = end($pathParts);
        
        switch ($endpoint) {
            case 'budget':
                if ($method === 'GET') {
                    $this->getBudgets();
                } elseif ($method === 'POST') {
                    $this->createBudget();
                } elseif ($method === 'PUT') {
                    $this->updateBudget();
                } elseif ($method === 'DELETE') {
                    $this->deleteBudget();
                } else {
                    $this->respondError(405, 'Method not allowed');
                }
                break;
                
            case 'spending':
                if ($method === 'GET') {
                    $this->getSpendingAnalysis();
                } else {
                    $this->respondError(405, 'Method not allowed');
                }
                break;
                
            default:
                $this->respondError(404, 'Endpoint not found');
        }
    }
    
    /**
     * Get user's budgets
     */
    private function getBudgets() {
        // Verify authentication
        $user = $this->auth->verifyToken();
        if (!$user) return;
        
        try {
            $query = "SELECT * FROM budgets WHERE user_id = ? ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$user['id']]);
            $budgets = $stmt->fetchAll();
            
            // Calculate progress for each budget
            $formattedBudgets = [];
            foreach ($budgets as $budget) {
                $progress = $budget['budget_amount'] > 0 ? 
                    ($budget['spent_amount'] / $budget['budget_amount']) * 100 : 0;
                
                $formattedBudgets[] = [
                    'id' => $budget['id'],
                    'category' => $budget['category'],
                    'budget_amount' => floatval($budget['budget_amount']),
                    'spent_amount' => floatval($budget['spent_amount']),
                    'remaining_amount' => floatval($budget['budget_amount'] - $budget['spent_amount']),
                    'progress_percentage' => round($progress, 2),
                    'budget_period' => $budget['budget_period'],
                    'start_date' => $budget['start_date'],
                    'end_date' => $budget['end_date'],
                    'status' => $this->getBudgetStatus($budget),
                    'created_at' => $budget['created_at'],
                    'updated_at' => $budget['updated_at']
                ];
            }
            
            $this->respondSuccess([
                'budgets' => $formattedBudgets,
                'total_budgets' => count($formattedBudgets)
            ]);
            
        } catch (Exception $e) {
            error_log("Get budgets error: " . $e->getMessage());
            $this->respondError(500, 'Failed to retrieve budgets');
        }
    }
    
    /**
     * Create new budget
     */
    private function createBudget() {
        // Verify authentication
        $user = $this->auth->verifyToken();
        if (!$user) return;
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate input
        if (!$this->validateBudgetInput($input)) {
            return;
        }
        
        $category = trim($input['category']);
        $budgetAmount = floatval($input['budget_amount']);
        $budgetPeriod = $input['budget_period'];
        
        // Calculate dates based on period
        $dates = $this->calculateBudgetDates($budgetPeriod);
        
        try {
            // Check if budget already exists for this category and period
            $checkQuery = "SELECT id FROM budgets WHERE user_id = ? AND category = ? 
                          AND budget_period = ? AND end_date >= CURDATE()";
            $stmt = $this->db->prepare($checkQuery);
            $stmt->execute([$user['id'], $category, $budgetPeriod]);
            
            if ($stmt->fetch()) {
                $this->respondError(409, 'Budget already exists for this category and period');
                return;
            }
            
            // Insert new budget
            $insertQuery = "INSERT INTO budgets (user_id, category, budget_amount, budget_period, start_date, end_date) 
                           VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($insertQuery);
            $stmt->execute([
                $user['id'],
                $category,
                $budgetAmount,
                $budgetPeriod,
                $dates['start_date'],
                $dates['end_date']
            ]);
            
            $budgetId = $this->db->lastInsertId();
            
            $this->respondSuccess([
                'message' => 'Budget created successfully',
                'budget' => [
                    'id' => $budgetId,
                    'category' => $category,
                    'budget_amount' => $budgetAmount,
                    'spent_amount' => 0.00,
                    'remaining_amount' => $budgetAmount,
                    'budget_period' => $budgetPeriod,
                    'start_date' => $dates['start_date'],
                    'end_date' => $dates['end_date'],
                    'status' => 'active'
                ]
            ], 201);
            
        } catch (Exception $e) {
            error_log("Create budget error: " . $e->getMessage());
            $this->respondError(500, 'Failed to create budget');
        }
    }
    
    /**
     * Update existing budget
     */
    private function updateBudget() {
        // Verify authentication
        $user = $this->auth->verifyToken();
        if (!$user) return;
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id']) || !is_numeric($input['id'])) {
            $this->respondError(400, 'Budget ID is required');
            return;
        }
        
        $budgetId = intval($input['id']);
        
        try {
            // Check if budget exists and belongs to user
            $checkQuery = "SELECT * FROM budgets WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($checkQuery);
            $stmt->execute([$budgetId, $user['id']]);
            $budget = $stmt->fetch();
            
            if (!$budget) {
                $this->respondError(404, 'Budget not found');
                return;
            }
            
            // Update fields
            $updates = [];
            $params = [];
            
            if (isset($input['budget_amount']) && is_numeric($input['budget_amount'])) {
                $updates[] = "budget_amount = ?";
                $params[] = floatval($input['budget_amount']);
            }
            
            if (isset($input['category']) && !empty(trim($input['category']))) {
                $updates[] = "category = ?";
                $params[] = trim($input['category']);
            }
            
            if (empty($updates)) {
                $this->respondError(400, 'No valid fields to update');
                return;
            }
            
            $params[] = $budgetId;
            $params[] = $user['id'];
            
            $updateQuery = "UPDATE budgets SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($updateQuery);
            $stmt->execute($params);
            
            $this->respondSuccess(['message' => 'Budget updated successfully']);
            
        } catch (Exception $e) {
            error_log("Update budget error: " . $e->getMessage());
            $this->respondError(500, 'Failed to update budget');
        }
    }
    
    /**
     * Delete budget
     */
    private function deleteBudget() {
        // Verify authentication
        $user = $this->auth->verifyToken();
        if (!$user) return;
        
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            $this->respondError(400, 'Budget ID is required');
            return;
        }
        
        $budgetId = intval($_GET['id']);
        
        try {
            $deleteQuery = "DELETE FROM budgets WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($deleteQuery);
            $stmt->execute([$budgetId, $user['id']]);
            
            if ($stmt->rowCount() === 0) {
                $this->respondError(404, 'Budget not found');
                return;
            }
            
            $this->respondSuccess(['message' => 'Budget deleted successfully']);
            
        } catch (Exception $e) {
            error_log("Delete budget error: " . $e->getMessage());
            $this->respondError(500, 'Failed to delete budget');
        }
    }
    
    /**
     * Get spending analysis
     */
    private function getSpendingAnalysis() {
        // Verify authentication
        $user = $this->auth->verifyToken();
        if (!$user) return;
        
        try {
            // Get spending by category from transactions (last 30 days)
            $spendingQuery = "SELECT 
                                'Transfer' as category,
                                SUM(amount) as total_spent,
                                COUNT(*) as transaction_count
                              FROM transactions 
                              WHERE sender_id = ? 
                              AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                              AND status = 'completed'";
            
            $stmt = $this->db->prepare($spendingQuery);
            $stmt->execute([$user['id']]);
            $spending = $stmt->fetchAll();
            
            // Get budget vs spending comparison
            $budgetQuery = "SELECT 
                              category,
                              budget_amount,
                              spent_amount,
                              (budget_amount - spent_amount) as remaining
                            FROM budgets 
                            WHERE user_id = ? 
                            AND end_date >= CURDATE()";
            
            $stmt = $this->db->prepare($budgetQuery);
            $stmt->execute([$user['id']]);
            $budgetComparison = $stmt->fetchAll();
            
            $this->respondSuccess([
                'spending_analysis' => [
                    'last_30_days' => $spending,
                    'budget_vs_spending' => $budgetComparison,
                    'analysis_date' => date('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Spending analysis error: " . $e->getMessage());
            $this->respondError(500, 'Failed to generate spending analysis');
        }
    }
    
    /**
     * Validate budget input
     */
    private function validateBudgetInput($input) {
        if (!isset($input['category']) || empty(trim($input['category']))) {
            $this->respondError(400, 'Category is required');
            return false;
        }
        
        if (!isset($input['budget_amount']) || !is_numeric($input['budget_amount'])) {
            $this->respondError(400, 'Valid budget amount is required');
            return false;
        }
        
        $amount = floatval($input['budget_amount']);
        if ($amount <= 0) {
            $this->respondError(400, 'Budget amount must be greater than 0');
            return false;
        }
        
        if (!isset($input['budget_period']) || 
            !in_array($input['budget_period'], ['weekly', 'monthly', 'yearly'])) {
            $this->respondError(400, 'Valid budget period is required (weekly, monthly, yearly)');
            return false;
        }
        
        return true;
    }
    
    /**
     * Calculate budget dates based on period
     */
    private function calculateBudgetDates($period) {
        $startDate = date('Y-m-d');
        
        switch ($period) {
            case 'weekly':
                $endDate = date('Y-m-d', strtotime('+1 week'));
                break;
            case 'monthly':
                $endDate = date('Y-m-d', strtotime('+1 month'));
                break;
            case 'yearly':
                $endDate = date('Y-m-d', strtotime('+1 year'));
                break;
            default:
                $endDate = date('Y-m-d', strtotime('+1 month'));
        }
        
        return [
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }
    
    /**
     * Determine budget status
     */
    private function getBudgetStatus($budget) {
        $today = date('Y-m-d');
        
        if ($today > $budget['end_date']) {
            return 'expired';
        }
        
        $progress = $budget['budget_amount'] > 0 ? 
            ($budget['spent_amount'] / $budget['budget_amount']) : 0;
        
        if ($progress >= 1) {
            return 'exceeded';
        } elseif ($progress >= 0.8) {
            return 'warning';
        } else {
            return 'active';
        }
    }
    
    /**
     * Send success response
     */
    private function respondSuccess($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
    
    /**
     * Send error response
     */
    private function respondError($code, $message) {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit;
    }
}

// Handle the request
$controller = new BudgetController();
$controller->handleRequest();
?>