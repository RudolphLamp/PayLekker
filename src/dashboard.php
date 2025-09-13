<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PayLekker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/dashboard-enhancements.css">
    <link rel="stylesheet" href="assets/css/force-fix.css">
    <style>
        /* ABSOLUTE FINAL FORCE - INLINE CSS CANNOT BE OVERRIDDEN */
        /* Force ALL Bootstrap Icons to Black */
        i[class*="bi"]:before,
        .bi:before,
        [class*="bi-"]:before,
        i.bi:before,
        i.bi-wallet2:before,
        i.bi-house-door:before,
        i.bi-arrow-left-right:before,
        i.bi-pie-chart:before,
        i.bi-plus-circle:before,
        i.bi-clock-history:before,
        i.bi-chat-dots:before,
        i.bi-person:before,
        i.bi-box-arrow-right:before,
        i.bi-list:before,
        i.bi-send:before,
        i.bi-arrow-right:before,
        i.bi-graph-up:before,
        i.bi-chat:before {
            color: #000000 !important;
        }

        /* Force icon containers and parents */
        i, .bi, [class*="bi-"], 
        .card-icon, .card-icon *, 
        .sidebar-header i, .sidebar-header .bi,
        .nav-link i, .nav-link .bi,
        .dashboard-card i, .dashboard-card .bi,
        .transaction-icon i, .transaction-icon .bi,
        .top-bar i, .top-bar .bi {
            color: #000000 !important;
            fill: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            background-color: transparent !important;
        }
        
        /* Specifically target the logo */
        .sidebar-header h4,
        .sidebar-header h4 i,
        .sidebar-header h4 .bi,
        .sidebar-header h4 .bi-wallet2 {
            color: #000000 !important;
            display: inline-block !important;
            visibility: visible !important;
        }

        /* Force all text in cards to be black except balance content */
        .dashboard-card, .dashboard-card *:not(.balance-content):not(.balance-content *) {
            color: #000000 !important;
        }
        
        /* Keep balance content white */
        .balance-content * {
            color: white !important;
        }

        /* Transaction icons should be visible */
        .transaction-icon {
            color: white !important;
        }

        .transaction-icon i,
        .transaction-icon .bi {
            color: white !important;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h4><i class="bi bi-wallet2 me-2"></i>PayLekker</h4>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link active">
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
                <a href="budget-page.php" class="nav-link">
                    <i class="bi bi-pie-chart"></i>
                    Budget
                </a>
            </div>
            <div class="nav-item">
                <a href="add-funds-page.php" class="nav-link">
                    <i class="bi bi-plus-circle"></i>
                    Add Funds
                </a>
            </div>
            <div class="nav-item">
                <a href="history-page.php" class="nav-link">
                    <i class="bi bi-clock-history"></i>
                    Transaction History
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
                <div class="user-avatar">
                    <span id="userInitials">PL</span>
                </div>
                <div>
                    <div class="fw-semibold" id="userNameNav">Loading...</div>
                    <div class="text-muted small">Welcome back</div>
                </div>
            </div>
        </div>

        <!-- Balance Card -->
        <div class="balance-card">
            <div class="balance-content">
                <div class="balance-label">Total Balance</div>
                <h1 class="balance-amount" id="accountBalance">R 0.00</h1>
                <div class="balance-subtitle">Available for transfers and payments</div>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="bi bi-send"></i>
                </div>
                <h3 class="card-title">Send Money</h3>
                <p class="card-subtitle">Transfer to friends and family instantly</p>
                <button class="card-action" onclick="window.location.href='transfer-page.php'">
                    <i class="bi bi-arrow-right me-2"></i>Transfer Now
                </button>
            </div>
            
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="bi bi-pie-chart"></i>
                </div>
                <h3 class="card-title">Budget</h3>
                <p class="card-subtitle">Manage your spending and track expenses</p>
                <button class="card-action" onclick="window.location.href='budget-page.php'">
                    <i class="bi bi-graph-up me-2"></i>View Budget
                </button>
            </div>
            
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="bi bi-chat-dots"></i>
                </div>
                <h3 class="card-title">AI Assistant</h3>
                <p class="card-subtitle">Get financial advice and support 24/7</p>
                <button class="card-action" onclick="window.location.href='chat-page.php'">
                    <i class="bi bi-chat me-2"></i>Start Chat
                </button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats">
            <div class="stat-card">
                <div class="stat-value" id="monthlySpent">R 0</div>
                <div class="stat-label">This Month</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="transfersSent">0</div>
                <div class="stat-label">Transfers Sent</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="transfersReceived">0</div>
                <div class="stat-label">Transfers Received</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">R 2.50</div>
                <div class="stat-label">Avg Transfer</div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="transactions-card">
            <div class="transactions-header">
                <h3><i class="bi bi-clock-history me-2"></i>Recent Transactions</h3>
                <a href="history.php" class="btn btn-outline-primary btn-sm">View All</a>
            </div>
            <div class="transactions-list" id="recentTransactions">
                <div class="loading-container">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading recent transactions...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Modal -->
    <div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="transferModalLabel">
                        <i class="bi bi-send me-2"></i>Send Money
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="transferForm">
                        <div class="mb-3">
                            <label for="recipientPhone" class="form-label">Recipient Phone Number</label>
                            <input type="tel" class="form-control" id="recipientPhone" placeholder="0XX XXX XXXX" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="transferAmount" class="form-label">Amount (ZAR)</label>
                            <div class="input-group">
                                <span class="input-group-text">R</span>
                                <input type="number" class="form-control" id="transferAmount" step="0.01" min="1" max="5000" placeholder="0.00" required>
                            </div>
                            <div class="form-text">Maximum transfer: R 5,000.00</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="transferReference" class="form-label">Reference (Optional)</label>
                            <input type="text" class="form-control" id="transferReference" placeholder="Payment for..." maxlength="50">
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Secure Transfer:</strong> All transfers are encrypted and secure.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="processTransfer()">
                        <i class="bi bi-send me-2"></i>Send Money
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/common.js"></script>
    <script>
        // Check authentication status with enhanced dashboard features
        async function checkDashboardAuth() {
            const token = sessionStorage.getItem('auth_token');
            const userData = sessionStorage.getItem('user_data');
            
            console.log('Checking authentication...', {
                hasToken: !!token,
                hasUserData: !!userData,
                tokenPreview: token ? token.substring(0, 20) + '...' : null
            });
            
            if (!token) {
                console.warn('No auth token found, redirecting to login');
                window.location.href = 'auth/login.php';
                return;
            }
            
            // If we have user data cached, use it first and validate in background
            if (userData) {
                try {
                    const user = JSON.parse(userData);
                    console.log('Using cached user data:', user);
                    updateUserInfo(user);
                } catch (e) {
                    console.error('Failed to parse cached user data:', e);
                }
            }
            
            try {
                // Test token validation first with our test endpoint
                console.log('Testing token with test-token.php endpoint...');
                let testResponse = await fetch(API_BASE + 'test-token.php', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });
                
                // If header method fails, try URL parameter
                if (!testResponse.ok) {
                    console.log('Header method failed, trying URL parameter method...');
                    testResponse = await fetch(API_BASE + 'test-token.php?token=' + encodeURIComponent(token), {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });
                }
                
                if (testResponse.ok) {
                    const testResult = await testResponse.json();
                    console.log('Token test result:', testResult);
                    
                    if (!testResult.success) {
                        throw new Error('Token test failed: ' + testResult.error);
                    }
                } else {
                    console.error('Token test failed with status:', testResponse.status);
                    throw new Error('Token validation failed');
                }
                
                // Now try to get profile data
                console.log('Token validated, fetching profile data...');
                let response = await fetch(API_BASE + 'profile.php', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });
                
                // If header method fails, try URL parameter
                if (!response.ok) {
                    console.log('Profile header method failed, trying URL parameter...');
                    response = await fetch(API_BASE + 'profile.php?token=' + encodeURIComponent(token), {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });
                }
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('Profile API response:', result);
                    
                    if (result.success) {
                        // Update session storage with fresh user data
                        sessionStorage.setItem('user_data', JSON.stringify(result.data.user));
                        updateUserInfo(result.data.user);
                        console.log('Authentication successful, user updated');
                        return result.data.user;
                    } else {
                        console.error('Invalid profile API response:', result);
                        throw new Error('Invalid API response: ' + (result.error || 'Unknown error'));
                    }
                } else if (response.status === 401) {
                    console.warn('Token expired or invalid (401 response)');
                    // Token expired or invalid
                    sessionStorage.removeItem('auth_token');
                    sessionStorage.removeItem('user_data');
                    alert('Your session has expired. Please login again.');
                    window.location.href = 'auth/login.php';
                    return;
                } else {
                    console.error('Profile fetch failed with status:', response.status);
                    const errorText = await response.text();
                    console.error('Response body:', errorText);
                    throw new Error('Failed to fetch profile: ' + response.status);
                }
                
            } catch (error) {
                console.error('Authentication error:', error);
                
                // Don't redirect immediately on network errors if we have cached data
                if (userData) {
                    console.warn('Using cached user data due to authentication error');
                    showAlert('Connection issue detected. Some data may not be up to date.', 'warning');
                    return;
                }
                
                // On critical error without cached data, redirect to login
                console.error('No cached data available, redirecting to login');
                alert('Authentication failed: ' + error.message + '. Please login again.');
                sessionStorage.removeItem('auth_token');
                sessionStorage.removeItem('user_data');
                window.location.href = 'auth/login.php';
            }
        }
        
        // Update user information in the UI
        function updateUserInfo(user) {
            try {
                // Update user name in navigation
                const userNameNav = document.getElementById('userNameNav');
                if (userNameNav) {
                    userNameNav.textContent = `${user.first_name} ${user.last_name}`;
                }
                
                // Update account balance
                const accountBalance = document.getElementById('accountBalance');
                if (accountBalance) {
                    const balance = parseFloat(user.account_balance.toString().replace(/[^\d.-]/g, '')) || 0;
                    accountBalance.textContent = `R ${balance.toLocaleString('en-ZA', {
                        minimumFractionDigits: 2, 
                        maximumFractionDigits: 2
                    })}`;
                }
                
                // Update any other user info elements
                const elements = {
                    'userFirstName': user.first_name,
                    'userLastName': user.last_name,
                    'userEmail': user.email,
                    'userPhone': user.phone
                };
                
                for (const [elementId, value] of Object.entries(elements)) {
                    const element = document.getElementById(elementId);
                    if (element) {
                        element.textContent = value;
                    }
                }
                
                console.log('User info updated successfully:', user);
                
            } catch (error) {
                console.error('Error updating user info:', error);
            }
        }
        
        // Load dashboard data
        async function loadDashboard() {
            try {
                // Load sample data instead of API calls
                loadSampleData();
                
                // Load sample transactions
                displaySampleTransactions();
                
            } catch (error) {
                console.error('Error loading dashboard:', error);
                loadSampleData();
            }
        }
        
        // Load user profile data from API
        async function loadUserProfile() {
            const token = sessionStorage.getItem('auth_token');
            
            if (!token) return;
            
            try {
                const response = await fetch(API_BASE + 'profile.php', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        const user = result.data.user;
                        
                        // Update balance display
                        const balance = parseFloat(user.account_balance);
                        document.getElementById('accountBalance').textContent = formatCurrency(balance);
                        
                        // Update user info
                        updateUserInfo(user);
                        
                        return user;
                    }
                } else {
                    throw new Error('Failed to load profile');
                }
                
            } catch (error) {
                console.error('Error loading user profile:', error);
                document.getElementById('accountBalance').textContent = 'Error loading balance';
            }
        }
        // Load recent transactions from API
        async function loadRecentTransactions() {
            const token = sessionStorage.getItem('auth_token');
            
            if (!token) return;
            
            try {
                const response = await fetch(API_BASE + 'transactions.php?limit=5&type=all', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        displayRecentTransactions(result.data.transactions);
                        updateSummaryStats(result.data.transactions);
                    } else {
                        document.getElementById('recentTransactions').innerHTML = '<p class="text-muted text-center py-4">No transactions found</p>';
                    }
                } else if (response.status === 401) {
                    // Token expired
                    window.location.href = 'auth/login.php';
                } else {
                    throw new Error('Failed to load transactions');
                }
                
            } catch (error) {
                console.error('Error loading transactions:', error);
                document.getElementById('recentTransactions').innerHTML = '<p class="text-danger text-center py-4">Failed to load transactions</p>';
            }
        }
        
        // Display recent transactions
        function displayRecentTransactions(transactions) {
            const container = document.getElementById('recentTransactions');
            
            if (!transactions || transactions.length === 0) {
                container.innerHTML = '<div class="loading-container"><p class="text-muted">No recent transactions</p></div>';
                return;
            }
            
            const transactionHTML = transactions.map(transaction => {
                const isReceived = transaction.type === 'received';
                const amountClass = isReceived ? 'positive' : 'negative';
                const amountPrefix = isReceived ? '+' : '-';
                const iconClass = isReceived ? 'received' : 'sent';
                const icon = isReceived ? 'bi-arrow-down-left' : 'bi-arrow-up-right';
                
                return `
                    <div class="transaction-item">
                        <div class="transaction-icon ${iconClass}">
                            <i class="bi ${icon}"></i>
                        </div>
                        <div class="transaction-details">
                            <div class="transaction-name">
                                ${transaction.description || (isReceived ? 
                                    (transaction.sender ? transaction.sender.name : 'Money Received') : 
                                    (transaction.recipient ? transaction.recipient.name : 'Money Sent'))}
                            </div>
                            <div class="transaction-description">${formatDate(transaction.created_at)} • ${transaction.reference_number || 'Transfer'}</div>
                        </div>
                        <div class="transaction-amount ${amountClass}">
                            ${amountPrefix}R${parseFloat(transaction.amount).toFixed(2)}
                        </div>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = transactionHTML;
        }
        
        // Update summary statistics
        function updateSummaryStats(transactions) {
            if (!transactions || transactions.length === 0) return;
            
            let sent = 0, received = 0, sentCount = 0, receivedCount = 0;
            
            transactions.forEach(transaction => {
                const amount = parseFloat(transaction.amount) || 0;
                if (transaction.type === 'sent') {
                    sent += amount;
                    sentCount++;
                } else if (transaction.type === 'received') {
                    received += amount;
                    receivedCount++;
                }
            });
            
            document.getElementById('monthlySpent').textContent = sent.toFixed(0);
            document.getElementById('transfersSent').textContent = sentCount;
            document.getElementById('transfersReceived').textContent = receivedCount;
        }
        
        // Utility functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays === 1) {
                return 'Today';
            } else if (diffDays === 2) {
                return 'Yesterday';
            } else if (diffDays <= 7) {
                return date.toLocaleDateString('en-US', { weekday: 'long' });
            } else {
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }
        }

        // Initialize dashboard when page loads
        document.addEventListener('DOMContentLoaded', async function() {
            // Check authentication and load user data
            const user = await checkDashboardAuth();
            if (user) {
                // Set up sidebar functionality
                setupSidebar();
                
                // Load user profile data
                await loadUserProfile();
                
                // Load recent transactions
                await loadRecentTransactions();
            }
        });

        // Process real transfer using API
        async function processTransfer() {
            const phone = document.getElementById('recipientPhone').value;
            const amount = document.getElementById('transferAmount').value;
            const reference = document.getElementById('transferReference').value;
            
            if (!phone || !amount) {
                alert('Please fill in all required fields.');
                return;
            }
            
            const token = sessionStorage.getItem('auth_token');
            if (!token) {
                alert('Please login again.');
                window.location.href = 'auth/login.php';
                return;
            }
            
            // Show loading state
            const submitBtn = document.querySelector('#transferModal .btn-primary');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Processing...';
            submitBtn.disabled = true;
            
            try {
                const response = await fetch(API_BASE + 'transfer.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        recipient_phone: phone,
                        amount: parseFloat(amount),
                        description: reference || 'Transfer'
                    })
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('transferModal'));
                    modal.hide();
                    
                    // Show success message
                    const successAlert = document.createElement('div');
                    successAlert.className = 'alert alert-success alert-dismissible fade show position-fixed';
                    successAlert.style.cssText = 'top: 20px; right: 20px; z-index: 1060; max-width: 400px;';
                    successAlert.innerHTML = `
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Transfer Successful!</strong><br>
                        R${result.data.transaction.amount} sent to ${result.data.transaction.recipient.name}<br>
                        Reference: ${result.data.transaction.reference_number}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(successAlert);
                    
                    // Update balance display
                    document.getElementById('accountBalance').textContent = formatCurrency(result.data.sender_new_balance);
                    
                    // Clear form
                    document.getElementById('transferForm').reset();
                    
                    // Reload transactions
                    loadRecentTransactions();
                    
                    // Remove alert after 7 seconds
                    setTimeout(() => {
                        if (successAlert.parentNode) {
                            successAlert.remove();
                        }
                    }, 7000);
                    
                } else {
                    // Show error message
                    alert(`Transfer failed: ${result.error || 'Unknown error occurred'}`);
                }
                
            } catch (error) {
                console.error('Transfer error:', error);
                alert('Network error occurred. Please try again.');
            } finally {
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }
    </script>
</body>
</html>
