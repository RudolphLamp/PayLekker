<?php
/**
 * PayLekker Database Setup Script
 * This file creates all necessary tables and initial data for the PayLekker API
 * Run this file once to set up the complete database schema
 */

// Include database connection
require_once 'database.php';

// Set content type for web viewing
header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker Database Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .result-success { color: #28a745; }
        .result-error { color: #dc3545; }
        .sql-query { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title mb-0">🏗️ PayLekker Database Setup</h1>
                        <p class="mb-0 text-muted">Setting up complete database schema for PayLekker API</p>
                    </div>
                    <div class="card-body">
                        
<?php

/**
 * Execute SQL query and display result
 */
function executeQuery($pdo, $sql, $description) {
    echo "<div class='mb-3'>";
    echo "<h5>$description</h5>";
    echo "<div class='sql-query'>" . htmlspecialchars($sql) . "</div>";
    
    try {
        $result = $pdo->exec($sql);
        if ($result !== false) {
            echo "<p class='result-success mt-2'>✅ Success: $description completed</p>";
        } else {
            echo "<p class='result-error mt-2'>⚠️ Warning: Query executed but returned false</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='result-error mt-2'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    echo "</div>";
}

echo "<h3>🔗 Database Connection Test</h3>";
try {
    // Test database connection
    $testQuery = $pdo->query("SELECT 1");
    if ($testQuery) {
        echo "<div class='alert alert-success'>✅ Successfully connected to PayLekker database!</div>";
        echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
        echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit();
}

echo "<hr><h3>🏗️ Creating Database Tables</h3>";

// 1. Create users table
$createUsersTable = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    account_balance DECIMAL(15, 2) DEFAULT 1000.00,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    phone_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

executeQuery($pdo, $createUsersTable, "Creating users table");

// 2. Create transactions table
$createTransactionsTable = "
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    recipient_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    description TEXT,
    reference_number VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'completed',
    transaction_type ENUM('transfer', 'deposit', 'withdrawal', 'refund') DEFAULT 'transfer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_sender_id (sender_id),
    INDEX idx_recipient_id (recipient_id),
    INDEX idx_reference_number (reference_number),
    INDEX idx_created_at (created_at),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

executeQuery($pdo, $createTransactionsTable, "Creating transactions table");

// 3. Create budget_categories table
$createBudgetCategoriesTable = "
CREATE TABLE IF NOT EXISTS budget_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_name VARCHAR(100) NOT NULL,
    budget_amount DECIMAL(15, 2) NOT NULL,
    spent_amount DECIMAL(15, 2) DEFAULT 0.00,
    period ENUM('weekly', 'monthly', 'yearly') DEFAULT 'monthly',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_user_category (user_id, category_name),
    INDEX idx_user_id (user_id),
    INDEX idx_period (period),
    INDEX idx_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

executeQuery($pdo, $createBudgetCategoriesTable, "Creating budget_categories table");

// 4. Create chat_history table
$createChatHistoryTable = "
CREATE TABLE IF NOT EXISTS chat_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    sender ENUM('user', 'assistant') NOT NULL,
    intent VARCHAR(100),
    response_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_user_id (user_id),
    INDEX idx_sender (sender),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

executeQuery($pdo, $createChatHistoryTable, "Creating chat_history table");

// 5. Create user_sessions table for token management (optional but recommended)
$createUserSessionsTable = "
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_agent TEXT,
    ip_address VARCHAR(45),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_user_id (user_id),
    INDEX idx_token_hash (token_hash),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

executeQuery($pdo, $createUserSessionsTable, "Creating user_sessions table");

echo "<hr><h3>📊 Creating Database Views</h3>";

// Create a view for transaction summaries
$createTransactionSummaryView = "
CREATE OR REPLACE VIEW transaction_summary AS
SELECT 
    u.id as user_id,
    u.first_name,
    u.last_name,
    u.account_balance,
    COUNT(CASE WHEN t.sender_id = u.id THEN 1 END) as sent_count,
    COUNT(CASE WHEN t.recipient_id = u.id THEN 1 END) as received_count,
    COALESCE(SUM(CASE WHEN t.sender_id = u.id THEN t.amount END), 0) as total_sent,
    COALESCE(SUM(CASE WHEN t.recipient_id = u.id THEN t.amount END), 0) as total_received
FROM users u
LEFT JOIN transactions t ON (u.id = t.sender_id OR u.id = t.recipient_id)
WHERE t.status = 'completed' OR t.id IS NULL
GROUP BY u.id;
";

executeQuery($pdo, $createTransactionSummaryView, "Creating transaction_summary view");

echo "<hr><h3>🎯 Inserting Sample Data</h3>";

// Insert sample users for testing (only if table is empty)
$checkUsers = $pdo->query("SELECT COUNT(*) as count FROM users")->fetchColumn();
if ($checkUsers == 0) {
    $insertSampleUsers = "
    INSERT INTO users (first_name, last_name, email, phone, password_hash, account_balance) VALUES
    ('Admin', 'User', 'admin@paylekker.com', '0821111111', '$2y$10\$example.hash.for.admin', 10000.00),
    ('John', 'Doe', 'john@example.com', '0821234567', '$2y$10\$example.hash.for.john', 2500.00),
    ('Jane', 'Smith', 'jane@example.com', '0829876543', '$2y$10\$example.hash.for.jane', 3200.00),
    ('Bob', 'Wilson', 'bob@example.com', '0823456789', '$2y$10\$example.hash.for.bob', 1800.00);
    ";
    
    executeQuery($pdo, $insertSampleUsers, "Inserting sample users");
    
    // Insert sample budget categories
    $insertSampleBudgets = "
    INSERT INTO budget_categories (user_id, category_name, budget_amount, spent_amount, period, start_date, end_date) VALUES
    (2, 'Food & Groceries', 2000.00, 450.00, 'monthly', '2025-09-01', '2025-09-30'),
    (2, 'Transport', 800.00, 320.00, 'monthly', '2025-09-01', '2025-09-30'),
    (2, 'Entertainment', 500.00, 150.00, 'monthly', '2025-09-01', '2025-09-30'),
    (3, 'Shopping', 1500.00, 680.00, 'monthly', '2025-09-01', '2025-09-30'),
    (3, 'Utilities', 1200.00, 900.00, 'monthly', '2025-09-01', '2025-09-30');
    ";
    
    executeQuery($pdo, $insertSampleBudgets, "Inserting sample budget categories");
    
    // Insert sample transactions
    $insertSampleTransactions = "
    INSERT INTO transactions (sender_id, recipient_id, amount, description, reference_number, status) VALUES
    (2, 3, 500.00, 'Lunch payment', 'PL' || LPAD(FLOOR(RAND() * 999999), 6, '0'), 'completed'),
    (3, 2, 250.00, 'Movie tickets', 'PL' || LPAD(FLOOR(RAND() * 999999), 6, '0'), 'completed'),
    (2, 4, 100.00, 'Coffee money', 'PL' || LPAD(FLOOR(RAND() * 999999), 6, '0'), 'completed'),
    (4, 3, 75.00, 'Taxi fare', 'PL' || LPAD(FLOOR(RAND() * 999999), 6, '0'), 'completed');
    ";
    
    executeQuery($pdo, $insertSampleTransactions, "Inserting sample transactions");
    
} else {
    echo "<div class='alert alert-info'>ℹ️ Sample data already exists, skipping insertion.</div>";
}

echo "<hr><h3>✅ Database Setup Complete!</h3>";

// Display summary
try {
    $userCount = $pdo->query("SELECT COUNT(*) as count FROM users")->fetchColumn();
    $transactionCount = $pdo->query("SELECT COUNT(*) as count FROM transactions")->fetchColumn();
    $budgetCount = $pdo->query("SELECT COUNT(*) as count FROM budget_categories")->fetchColumn();
    $chatCount = $pdo->query("SELECT COUNT(*) as count FROM chat_history")->fetchColumn();
    
    echo "<div class='alert alert-success'>";
    echo "<h5>🎉 PayLekker Database Successfully Set Up!</h5>";
    echo "<div class='row'>";
    echo "<div class='col-md-3'><strong>Users:</strong> $userCount</div>";
    echo "<div class='col-md-3'><strong>Transactions:</strong> $transactionCount</div>";
    echo "<div class='col-md-3'><strong>Budget Categories:</strong> $budgetCount</div>";
    echo "<div class='col-md-3'><strong>Chat Messages:</strong> $chatCount</div>";
    echo "</div>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='alert alert-warning'>⚠️ Could not retrieve table counts: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<div class='mt-4'>";
echo "<h5>🔗 Next Steps</h5>";
echo "<div class='alert alert-info'>";
echo "<p>Your PayLekker database is now ready! You can:</p>";
echo "<ul class='mb-0'>";
echo "<li>✅ <a href='test.php' class='btn btn-sm btn-primary'>Run API Test Suite</a></li>";
echo "<li>✅ <a href='index.php' class='btn btn-sm btn-info'>View API Documentation</a></li>";
echo "<li>✅ Start building your frontend application</li>";
echo "<li>✅ Deploy your API to production</li>";
echo "</ul>";
echo "</div>";
echo "</div>";

?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>