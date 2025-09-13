<?php
/**
 * PayLekker API - Transaction History Endpoint
 * GET /transactions.php - View user's transaction history
 */

require_once 'database.php';
require_once 'jwt.php';
require_once 'response.php';

// Set CORS headers
APIResponse::setCorsHeaders();

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    APIResponse::error('Method not allowed. Use GET.', 405);
}

// Require authentication
$userData = JWTAuth::requireAuth();
$userId = $userData['user_id'];

// Get query parameters for filtering
$limit = intval($_GET['limit'] ?? 50); // Default 50 transactions
$offset = intval($_GET['offset'] ?? 0); // For pagination
$type = $_GET['type'] ?? 'all'; // 'sent', 'received', or 'all'

// Validate limit (max 100)
if ($limit > 100) {
    $limit = 100;
}

// Validate type
$validTypes = ['all', 'sent', 'received'];
if (!in_array($type, $validTypes)) {
    APIResponse::error('Invalid type. Use: all, sent, or received', 400);
}

try {
    // Build query based on type
    $whereClause = '';
    $params = [$userId];
    
    if ($type === 'sent') {
        $whereClause = 'WHERE t.sender_id = ?';
    } elseif ($type === 'received') {
        $whereClause = 'WHERE t.recipient_id = ?';
    } else {
        // All transactions (sent or received)
        $whereClause = 'WHERE (t.sender_id = ? OR t.recipient_id = ?)';
        $params = [$userId, $userId];
    }
    
    // Get transactions with user details
    $sql = "
        SELECT 
            t.id,
            t.sender_id,
            t.recipient_id,
            t.amount,
            t.description,
            t.transaction_type,
            t.status,
            t.reference_number,
            t.created_at,
            CONCAT(sender.first_name, ' ', sender.last_name) as sender_name,
            sender.email as sender_email,
            sender.phone as sender_phone,
            CONCAT(recipient.first_name, ' ', recipient.last_name) as recipient_name,
            recipient.email as recipient_email,
            recipient.phone as recipient_phone
        FROM transactions t
        LEFT JOIN users sender ON t.sender_id = sender.id
        LEFT JOIN users recipient ON t.recipient_id = recipient.id
        $whereClause
        ORDER BY t.created_at DESC
        LIMIT ? OFFSET ?
    ";
    
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll();
    
    // Get total count for pagination
    $countSql = "
        SELECT COUNT(*) as total
        FROM transactions t
        $whereClause
    ";
    
    $countParams = ($type === 'all') ? [$userId, $userId] : [$userId];
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $totalCount = $countStmt->fetchColumn();
    
    // Format transactions for response
    $formattedTransactions = [];
    
    foreach ($transactions as $transaction) {
        $isSender = ($transaction['sender_id'] == $userId);
        
        $formattedTransaction = [
            'id' => $transaction['id'],
            'reference_number' => $transaction['reference_number'],
            'amount' => $transaction['amount'], // Keep raw amount for calculations
            'amount_formatted' => number_format($transaction['amount'], 2, '.', ','), // Formatted with commas
            'description' => $transaction['description'],
            'type' => $isSender ? 'sent' : 'received',
            'status' => $transaction['status'],
            'created_at' => $transaction['created_at']
        ];
        
        if ($isSender) {
            // User sent this transaction
            $formattedTransaction['recipient'] = [
                'name' => $transaction['recipient_name'],
                'phone' => $transaction['recipient_phone']
            ];
        } else {
            // User received this transaction
            $formattedTransaction['sender'] = [
                'name' => $transaction['sender_name'],
                'phone' => $transaction['sender_phone']
            ];
        }
        
        $formattedTransactions[] = $formattedTransaction;
    }
    
    // Return success response with pagination info
    APIResponse::success([
        'transactions' => $formattedTransactions,
        'pagination' => [
            'total' => intval($totalCount),
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + $limit) < $totalCount
        ],
        'filter' => $type
    ], 'Transaction history retrieved successfully');
    
} catch (PDOException $e) {
    error_log("Transaction history error: " . $e->getMessage());
    APIResponse::error('Failed to retrieve transaction history', 500);
}
?>