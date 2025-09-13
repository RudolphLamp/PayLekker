<?php
/**
 * PayLekker API - Money Transfer Endpoints
 * Handles P2P transfers and transaction history
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/jwt.php';

class TransferController {
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
            case 'transfer':
                if ($method !== 'POST') {
                    $this->respondError(405, 'Method not allowed');
                }
                $this->transfer();
                break;
                
            case 'transactions':
                if ($method !== 'GET') {
                    $this->respondError(405, 'Method not allowed');
                }
                $this->getTransactions();
                break;
                
            default:
                $this->respondError(404, 'Endpoint not found');
        }
    }
    
    /**
     * Process money transfer
     */
    private function transfer() {
        // Verify authentication
        $user = $this->auth->verifyToken();
        if (!$user) return; // Auth middleware handles response
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate input
        if (!$this->validateTransferInput($input)) {
            return;
        }
        
        $recipientEmail = trim($input['recipient_email']);
        $amount = floatval($input['amount']);
        $description = isset($input['description']) ? trim($input['description']) : '';
        
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Find recipient
            $recipientQuery = "SELECT id, email, first_name, last_name FROM users WHERE email = ?";
            $stmt = $this->db->prepare($recipientQuery);
            $stmt->execute([$recipientEmail]);
            $recipient = $stmt->fetch();
            
            if (!$recipient) {
                $this->db->rollback();
                $this->respondError(404, 'Recipient not found');
                return;
            }
            
            if ($recipient['id'] == $user['id']) {
                $this->db->rollback();
                $this->respondError(400, 'Cannot transfer to yourself');
                return;
            }
            
            // Check sender balance
            $balanceQuery = "SELECT balance FROM users WHERE id = ?";
            $stmt = $this->db->prepare($balanceQuery);
            $stmt->execute([$user['id']]);
            $senderBalance = $stmt->fetchColumn();
            
            if ($senderBalance < $amount) {
                $this->db->rollback();
                $this->respondError(400, 'Insufficient funds');
                return;
            }
            
            // Update balances
            $updateSenderQuery = "UPDATE users SET balance = balance - ? WHERE id = ?";
            $stmt = $this->db->prepare($updateSenderQuery);
            $stmt->execute([$amount, $user['id']]);
            
            $updateRecipientQuery = "UPDATE users SET balance = balance + ? WHERE id = ?";
            $stmt = $this->db->prepare($updateRecipientQuery);
            $stmt->execute([$amount, $recipient['id']]);
            
            // Record transaction
            $transactionQuery = "INSERT INTO transactions (sender_id, recipient_id, amount, description, status, transaction_type) 
                                VALUES (?, ?, ?, ?, 'completed', 'transfer')";
            $stmt = $this->db->prepare($transactionQuery);
            $stmt->execute([$user['id'], $recipient['id'], $amount, $description]);
            
            $transactionId = $this->db->lastInsertId();
            
            // Commit transaction
            $this->db->commit();
            
            $this->respondSuccess([
                'message' => 'Transfer completed successfully',
                'transaction' => [
                    'id' => $transactionId,
                    'amount' => $amount,
                    'recipient' => [
                        'email' => $recipient['email'],
                        'name' => $recipient['first_name'] . ' ' . $recipient['last_name']
                    ],
                    'description' => $description,
                    'status' => 'completed',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Transfer error: " . $e->getMessage());
            $this->respondError(500, 'Transfer failed');
        }
    }
    
    /**
     * Get user's transaction history
     */
    private function getTransactions() {
        // Verify authentication
        $user = $this->auth->verifyToken();
        if (!$user) return; // Auth middleware handles response
        
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = isset($_GET['limit']) ? min(50, max(1, intval($_GET['limit']))) : 20;
        $offset = ($page - 1) * $limit;
        
        try {
            // Get transactions where user is sender or recipient
            $query = "SELECT 
                        t.*,
                        sender.email as sender_email,
                        sender.first_name as sender_first_name,
                        sender.last_name as sender_last_name,
                        recipient.email as recipient_email,
                        recipient.first_name as recipient_first_name,
                        recipient.last_name as recipient_last_name
                      FROM transactions t
                      JOIN users sender ON t.sender_id = sender.id
                      JOIN users recipient ON t.recipient_id = recipient.id
                      WHERE t.sender_id = ? OR t.recipient_id = ?
                      ORDER BY t.created_at DESC
                      LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$user['id'], $user['id'], $limit, $offset]);
            $transactions = $stmt->fetchAll();
            
            // Format transactions
            $formattedTransactions = [];
            foreach ($transactions as $transaction) {
                $isReceiver = $transaction['recipient_id'] == $user['id'];
                
                $formattedTransactions[] = [
                    'id' => $transaction['id'],
                    'amount' => floatval($transaction['amount']),
                    'description' => $transaction['description'],
                    'status' => $transaction['status'],
                    'type' => $isReceiver ? 'received' : 'sent',
                    'other_party' => [
                        'email' => $isReceiver ? $transaction['sender_email'] : $transaction['recipient_email'],
                        'name' => $isReceiver ? 
                            $transaction['sender_first_name'] . ' ' . $transaction['sender_last_name'] :
                            $transaction['recipient_first_name'] . ' ' . $transaction['recipient_last_name']
                    ],
                    'created_at' => $transaction['created_at'],
                    'updated_at' => $transaction['updated_at']
                ];
            }
            
            // Get total count for pagination
            $countQuery = "SELECT COUNT(*) FROM transactions WHERE sender_id = ? OR recipient_id = ?";
            $stmt = $this->db->prepare($countQuery);
            $stmt->execute([$user['id'], $user['id']]);
            $totalCount = $stmt->fetchColumn();
            
            $this->respondSuccess([
                'transactions' => $formattedTransactions,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => intval($totalCount),
                    'total_pages' => ceil($totalCount / $limit)
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Get transactions error: " . $e->getMessage());
            $this->respondError(500, 'Failed to retrieve transactions');
        }
    }
    
    /**
     * Validate transfer input
     */
    private function validateTransferInput($input) {
        if (!isset($input['recipient_email']) || empty(trim($input['recipient_email']))) {
            $this->respondError(400, 'Recipient email is required');
            return false;
        }
        
        if (!isset($input['amount']) || !is_numeric($input['amount'])) {
            $this->respondError(400, 'Valid amount is required');
            return false;
        }
        
        $amount = floatval($input['amount']);
        if ($amount <= 0) {
            $this->respondError(400, 'Amount must be greater than 0');
            return false;
        }
        
        if ($amount > 10000) { // Max transfer limit
            $this->respondError(400, 'Amount exceeds maximum transfer limit (R10,000)');
            return false;
        }
        
        if (!filter_var($input['recipient_email'], FILTER_VALIDATE_EMAIL)) {
            $this->respondError(400, 'Invalid recipient email format');
            return false;
        }
        
        return true;
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
$controller = new TransferController();
$controller->handleRequest();
?>