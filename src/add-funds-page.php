<?php
require_once 'jwt.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Funds - PayLekker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/dashboard-enhancements.css">
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h4><i class="bi bi-wallet2 me-2"></i>PayLekker</h4>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link">
                    <i class="bi bi-house-door"></i>
                    Dashboard
                </a>
            </div>
            <div class="nav-item">
                <a href="transfer-page.php" class="nav-link">
                    <i class="bi bi-arrow-left-right"></i>
                    Transfer Money
                </a>
            </div>
            <div class="nav-item">
                <a href="history-page.php" class="nav-link">
                    <i class="bi bi-clock-history"></i>
                    Transaction History
                </a>
            </div>
            <div class="nav-item">
                <a href="budget-page.php" class="nav-link">
                    <i class="bi bi-pie-chart"></i>
                    Budget
                </a>
            </div>
            <div class="nav-item">
                <a href="add-funds-page.php" class="nav-link active">
                    <i class="bi bi-plus-circle"></i>
                    Add Funds
                </a>
            </div>
            <div class="nav-item">
                <a href="chat-page.php" class="nav-link">
                    <i class="bi bi-chat-dots"></i>
                    AI Assistant
                </a>
            </div>
            <div class="nav-item">
                <a href="profile-page.php" class="nav-link">
                    <i class="bi bi-person"></i>
                    Profile
                </a>
            </div>
            <div class="nav-item mt-auto">
                <a href="#" class="nav-link" onclick="logout()">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </div>
        </nav>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- Main Content -->
    <div id="mainContent" class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <button id="sidebarToggle" class="sidebar-toggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="user-info">
                <div class="user-avatar" id="userAvatar">
                    <i class="bi bi-person"></i>
                </div>
                <div>
                    <div class="user-name" id="userName">Loading...</div>
                    <div class="user-email" id="userEmail"></div>
                </div>
            </div>
        </div>

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="page-title mb-1">Add Funds</h2>
                <p class="text-muted">Top up your PayLekker account instantly</p>
            </div>
        </div>

        <!-- Add Funds Form -->
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="dashboard-card">
                    <div class="card-icon mb-4">
                        <i class="bi bi-plus-circle-fill"></i>
                    </div>
                    <h4 class="card-title mb-4">Add Funds to Your Account</h4>
                    
                    <form id="addFundsForm" onsubmit="addFunds(event)">
                        <!-- Current Balance Display -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Current Balance:</span>
                                <span class="h5 mb-0" id="currentBalance">R 0.00</span>
                            </div>
                        </div>

                        <!-- Amount Input -->
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount to Add (ZAR)</label>
                            <div class="input-group">
                                <span class="input-group-text">R</span>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       min="10" max="10000" step="0.01" required 
                                       placeholder="0.00">
                            </div>
                            <div class="form-text">Minimum: R10.00 | Maximum: R10,000.00</div>
                        </div>

                        <!-- Quick Amount Buttons -->
                        <div class="mb-4">
                            <label class="form-label">Quick Select:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAmount(100)">R100</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAmount(250)">R250</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAmount(500)">R500</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setAmount(1000)">R1,000</button>
                            </div>
                        </div>

                        <!-- Payment Method (Demo) -->
                        <div class="mb-4">
                            <label class="form-label">Payment Method</label>
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-credit-card fs-4 me-3"></i>
                                        <div>
                                            <div class="fw-medium">Demo Payment</div>
                                            <small class="text-muted">Instant deposit for demo purposes</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="card-action mb-3" id="addFundsBtn">
                            <i class="bi bi-plus-circle me-2"></i>
                            Add Funds
                        </button>
                        
                        <!-- Demo Notice -->
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Demo Mode:</strong> This is a demonstration. Funds will be added instantly to your account for testing purposes.
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="dashboard-card">
                    <h5 class="mb-4">Recent Fund Additions</h5>
                    <div id="recentFunds">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-clock-history fs-1"></i>
                            <p class="mt-2">No recent fund additions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="text-success mb-3">
                        <i class="bi bi-check-circle-fill fs-1"></i>
                    </div>
                    <h4 class="mb-3">Funds Added Successfully!</h4>
                    <p class="text-muted mb-4" id="successMessage">Your account has been topped up.</p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Continue</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/common.js"></script>
    <script>
        // Set amount function
        function setAmount(amount) {
            document.getElementById('amount').value = amount;
        }

        // Load current balance from user profile data
        async function loadCurrentBalance() {
            try {
                const response = await makeAuthenticatedRequest('profile.php', {
                    method: 'GET'
                });

                if (response && response.data && response.data.user) {
                    const balance = parseFloat(response.data.user.balance || 0);
                    document.getElementById('currentBalance').textContent = formatCurrency(balance);
                } else {
                    document.getElementById('currentBalance').textContent = 'R 0.00';
                }
            } catch (error) {
                console.error('Error loading balance:', error);
                document.getElementById('currentBalance').textContent = 'R 0.00';
            }
        }

        // Add funds function
        async function addFunds(event) {
            event.preventDefault();
            
            const amount = parseFloat(document.getElementById('amount').value);
            const btn = document.getElementById('addFundsBtn');
            
            if (amount < 10 || amount > 10000) {
                showAlert('Please enter an amount between R10.00 and R10,000.00', 'warning');
                return;
            }

            // Disable button and show loading
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding Funds...';

            try {
                const response = await makeAuthenticatedRequest('profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=add_funds&amount=${amount}`
                });

                if (response && response.success !== false) {
                    // Parse response if it's text
                    let result = response;
                    if (typeof response === 'string') {
                        try {
                            result = JSON.parse(response);
                        } catch (e) {
                            // Response might be success text
                            result = { success: true };
                        }
                    }

                    // Show success modal with actual new balance
                    const newBalance = result.data ? result.data.new_balance : null;
                    if (newBalance) {
                        document.getElementById('successMessage').textContent = 
                            `${formatCurrency(amount)} has been added to your account. New balance: ${formatCurrency(newBalance)}`;
                    } else {
                        document.getElementById('successMessage').textContent = 
                            `${formatCurrency(amount)} has been added to your account.`;
                    }
                    
                    const modal = new bootstrap.Modal(document.getElementById('successModal'));
                    modal.show();

                    // Reset form
                    document.getElementById('addFundsForm').reset();
                    
                    // Reload balance
                    loadCurrentBalance();
                    
                    // Add to recent funds display
                    addToRecentFunds(amount);
                    
                } else {
                    showAlert(result.error || result.message || 'Failed to add funds', 'danger');
                }
            } catch (error) {
                console.error('Error adding funds:', error);
                showAlert('An error occurred. Please try again.', 'danger');
            } finally {
                // Re-enable button
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Funds';
            }
        }

        // Add to recent funds display
        function addToRecentFunds(amount) {
            const recentFunds = document.getElementById('recentFunds');
            const now = new Date();
            
            // Create new transaction item
            const fundItem = document.createElement('div');
            fundItem.className = 'transaction-item';
            fundItem.innerHTML = `
                <div class="transaction-icon received">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <div class="transaction-details">
                    <div class="transaction-name">Fund Addition</div>
                    <div class="transaction-description">Demo payment - ${now.toLocaleString()}</div>
                </div>
                <div class="transaction-amount positive">+${formatCurrency(amount)}</div>
            `;
            
            // Replace empty state or prepend to existing
            if (recentFunds.querySelector('.text-muted')) {
                recentFunds.innerHTML = '';
            }
            recentFunds.insertBefore(fundItem, recentFunds.firstChild);
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            updateUserInfo();
            loadCurrentBalance();
            setupSidebar();
        });
    </script>
</body>
</html>