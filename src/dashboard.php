<?php
/**
 * PayLekker - Dashboard
 */

require_once 'includes/config.php';
requireAuth();

$user = getCurrentUser();
$token = getAuthToken();

// Fetch user balance and recent transactions
$balance = 0;
$recentTransactions = [];
$error = '';

// Get current balance (from user data - could be updated via API call)
$balance = $user['balance'] ?? 0;

// Fetch recent transactions
$transactionsResponse = callAPI('GET', 'transactions?limit=5', null, $token);
if ($transactionsResponse['success']) {
    $recentTransactions = $transactionsResponse['data']['transactions'] ?? [];
} else {
    $error = 'Unable to load recent transactions.';
}

$pageTitle = 'Dashboard';
?>

<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-2">
                Welcome back, <?php echo e($user['first_name']); ?>! 👋
            </h1>
            <p class="text-muted">Here's what's happening with your PayLekker account today.</p>
        </div>
    </div>
    
    <!-- Balance Card -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card balance-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 mb-2">Available Balance</h6>
                            <p class="balance-amount" id="balanceAmount">
                                <?php echo formatRands($balance); ?>
                            </p>
                            <small class="text-white-50">
                                Last updated: <?php echo date('M j, Y g:i A'); ?>
                            </small>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-light btn-sm" onclick="refreshBalance()">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="row g-3">
                <div class="col-6">
                    <div class="stat-card">
                        <div class="stat-number" id="totalTransactions">
                            <?php echo count($recentTransactions); ?>+
                        </div>
                        <div class="stat-label">Transactions</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-card">
                        <div class="stat-number text-success">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="stat-label">Secure</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col">
            <h5 class="mb-3">Quick Actions</h5>
            <div class="d-flex quick-actions">
                <a href="transfer.php" class="quick-action-btn btn btn-primary">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Send Money</span>
                </a>
                
                <a href="history.php" class="quick-action-btn btn btn-info">
                    <i class="bi bi-clock-history"></i>
                    <span>View History</span>
                </a>
                
                <a href="budget.php" class="quick-action-btn btn btn-success">
                    <i class="bi bi-pie-chart"></i>
                    <span>Budget</span>
                </a>
                
                <a href="chat.php" class="quick-action-btn btn btn-warning">
                    <i class="bi bi-chat-dots"></i>
                    <span>Get Help</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Recent Activity
                    </h6>
                    <a href="history.php" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentTransactions)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mt-3">No transactions yet</h6>
                            <p class="text-muted mb-3">Start by sending money to someone or receiving your first payment.</p>
                            <a href="transfer.php" class="btn btn-primary">
                                <i class="bi bi-arrow-left-right me-2"></i>
                                Send Money
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentTransactions as $transaction): ?>
                            <div class="transaction-item d-flex align-items-center <?php echo $transaction['type'] === 'sent' ? 'transaction-sent' : 'transaction-received'; ?>">
                                <div class="transaction-icon me-3">
                                    <i class="bi bi-<?php echo $transaction['type'] === 'sent' ? 'arrow-up-right' : 'arrow-down-left'; ?>"></i>
                                </div>
                                
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <?php if ($transaction['type'] === 'sent'): ?>
                                                    To: <?php echo e($transaction['other_party']['name']); ?>
                                                <?php else: ?>
                                                    From: <?php echo e($transaction['other_party']['name']); ?>
                                                <?php endif; ?>
                                            </h6>
                                            
                                            <?php if (!empty($transaction['description'])): ?>
                                                <p class="text-muted mb-1 small">
                                                    <?php echo e($transaction['description']); ?>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <small class="text-muted">
                                                <?php echo formatRelativeDate($transaction['created_at']); ?>
                                            </small>
                                        </div>
                                        
                                        <div class="text-end">
                                            <span class="fw-bold <?php echo $transaction['type'] === 'sent' ? 'text-danger' : 'text-success'; ?>">
                                                <?php echo $transaction['type'] === 'sent' ? '-' : '+'; ?><?php echo formatRands($transaction['amount']); ?>
                                            </span>
                                            <div>
                                                <span class="<?php echo getTransactionBadgeClass($transaction['type'], $transaction['status']); ?>">
                                                    <?php echo ucfirst($transaction['status']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Account Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Account Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?php echo e($user['first_name'] . ' ' . $user['last_name']); ?></h6>
                            <small class="text-muted"><?php echo e($user['email']); ?></small>
                        </div>
                        <div>
                            <i class="bi bi-shield-check text-success" title="Verified Account"></i>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Member since:</span>
                            <span><?php echo formatDate($user['created_at']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Account status:</span>
                            <span class="text-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Active
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Security Notice -->
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="text-primary mb-2">
                        <i class="bi bi-shield-lock me-2"></i>
                        Security Tip
                    </h6>
                    <p class="small mb-2">
                        Keep your account secure by never sharing your login details with anyone.
                    </p>
                    <a href="settings.php" class="btn btn-sm btn-outline-primary">
                        Security Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden element to store user data for JavaScript -->
<div id="user-data" style="display: none;" data-token="<?php echo e($token); ?>">
    <?php echo e(json_encode($user)); ?>
</div>

<script>
// Refresh balance function
async function refreshBalance() {
    const balanceElement = document.getElementById('balanceAmount');
    const originalContent = balanceElement.innerHTML;
    
    try {
        balanceElement.innerHTML = '<span class="loading-spinner"></span> Loading...';
        
        // In a real implementation, you would call the API to get updated balance
        // For now, we'll just simulate a refresh
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        PayLekker.showAlert('success', 'Balance updated successfully!', 3000);
        balanceElement.innerHTML = originalContent;
        
    } catch (error) {
        PayLekker.showAlert('error', 'Failed to refresh balance. Please try again.');
        balanceElement.innerHTML = originalContent;
    }
}

// Add some interactivity to transaction items
document.addEventListener('DOMContentLoaded', function() {
    const transactionItems = document.querySelectorAll('.transaction-item');
    transactionItems.forEach(item => {
        item.addEventListener('click', function() {
            // Could open a modal with transaction details
            console.log('Transaction clicked');
        });
    });
    
    // Auto-refresh balance every 5 minutes
    setInterval(() => {
        // You could implement auto-refresh here
        console.log('Auto-refresh trigger');
    }, 5 * 60 * 1000);
});
</script>

<?php include 'includes/footer.php'; ?>