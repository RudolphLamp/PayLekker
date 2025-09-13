<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Money - PayLekker</title>
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
                <a href="transfer-page.php" class="nav-link active">
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
                <a href="add-funds-page.php" class="nav-link">
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
                <span class="user-name" id="userName">Loading...</span>
                <div class="user-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            <div class="page-header">
                <h1><i class="bi bi-send me-3"></i>Send Money</h1>
                <p class="text-muted">Transfer funds instantly to friends and family</p>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Transfer Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="bi bi-credit-card me-2"></i>Transfer Details</h5>
                        </div>
                        <div class="card-body">
                            <form id="transferForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="recipientPhone" class="form-label">Recipient Phone Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                                <input type="tel" class="form-control" id="recipientPhone" placeholder="0XX XXX XXXX" required>
                                            </div>
                                            <div class="form-text">Enter the recipient's registered phone number</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="transferAmount" class="form-label">Amount (ZAR)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R</span>
                                                <input type="number" class="form-control" id="transferAmount" step="0.01" min="1" max="50000" placeholder="0.00" required>
                                            </div>
                                            <div class="form-text">Maximum: R 50,000 per transaction</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="transferReference" class="form-label">Reference (Optional)</label>
                                    <input type="text" class="form-control" id="transferReference" placeholder="Payment for..." maxlength="255">
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-outline-secondary me-md-2" onclick="clearForm()">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Clear
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-2"></i>Send Money
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Recent Recipients -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5><i class="bi bi-people me-2"></i>Recent Recipients</h5>
                        </div>
                        <div class="card-body" id="recentRecipients">
                            <div class="text-center py-4">
                                <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Loading recent recipients...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Balance Card -->
                    <div class="card">
                        <div class="card-body text-center">
                            <h6 class="card-subtitle mb-2 text-muted">Available Balance</h6>
                            <h2 class="card-title text-primary" id="accountBalance">R 0.00</h2>
                            <small class="text-muted">Updated just now</small>
                        </div>
                    </div>

                    <!-- Transfer Limits -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6><i class="bi bi-shield-check me-2"></i>Transfer Limits</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Daily Limit:</span>
                                <strong>R 100,000</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Per Transaction:</span>
                                <strong>R 50,000</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Transfer Fee:</span>
                                <strong class="text-success">FREE</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Security Info -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6><i class="bi bi-lock me-2"></i>Security</h6>
                            <ul class="list-unstyled mb-0">
                                <li><i class="bi bi-check-circle text-success me-2"></i>256-bit encryption</li>
                                <li><i class="bi bi-check-circle text-success me-2"></i>Instant transfers</li>
                                <li><i class="bi bi-check-circle text-success me-2"></i>24/7 monitoring</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/common.js"></script>
    <script>
        // Check authentication on page load
        async function checkAuth() {
            const token = sessionStorage.getItem('auth_token');
            const userData = sessionStorage.getItem('user_data');
            
            console.log('Checking authentication on transfer page...', {
                hasToken: !!token,
                hasUserData: !!userData
            });
            
            if (!token) {
                console.warn('No auth token found, redirecting to login');
                window.location.href = 'auth/login.php';
                return;
            }
            
            // If we have user data cached, use it first
            if (userData) {
                try {
                    const user = JSON.parse(userData);
                    console.log('Using cached user data on transfer page');
                    updateUserInfo(user);
                } catch (e) {
                    console.error('Failed to parse cached user data:', e);
                }
            }
            
            try {
                // Test token validation first
                let testResponse = await fetch(API_BASE + 'test-token.php', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });
                
                // If header method fails, try URL parameter
                if (!testResponse.ok) {
                    testResponse = await fetch(API_BASE + 'test-token.php?token=' + encodeURIComponent(token), {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });
                }
                
                if (testResponse.ok) {
                    const testResult = await testResponse.json();
                    if (!testResult.success) {
                        throw new Error('Token test failed: ' + testResult.error);
                    }
                } else {
                    throw new Error('Token validation failed');
                }
                
                // Now try to get profile data
                let response = await fetch(API_BASE + 'profile.php', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });
                
                // If header method fails, try URL parameter
                if (!response.ok) {
                    response = await fetch(API_BASE + 'profile.php?token=' + encodeURIComponent(token), {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });
                }
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        sessionStorage.setItem('user_data', JSON.stringify(result.data.user));
                        updateUserInfo(result.data.user);
                        return result.data.user;
                    } else {
                        throw new Error('Invalid API response: ' + (result.error || 'Unknown error'));
                    }
                } else if (response.status === 401) {
                    console.warn('Token expired or invalid (401 response)');
                    sessionStorage.removeItem('auth_token');
                    sessionStorage.removeItem('user_data');
                    alert('Your session has expired. Please login again.');
                    window.location.href = 'auth/login.php';
                    return;
                } else {
                    throw new Error('Failed to fetch profile: ' + response.status);
                }
                
            } catch (error) {
                console.error('Authentication error on transfer page:', error);
                
                // Don't redirect immediately on network errors if we have cached data
                if (userData) {
                    console.warn('Using cached user data due to authentication error on transfer page');
                    showAlert('Connection issue detected. Some data may not be up to date.', 'warning');
                    return;
                }
                
                // On critical error without cached data, redirect to login
                alert('Authentication failed: ' + error.message + '. Please login again.');
                sessionStorage.removeItem('auth_token');
                sessionStorage.removeItem('user_data');
                window.location.href = 'auth/login.php';
            }
        }
        
        // Update user info in UI
        function updateUserInfo(user) {
            document.getElementById('userName').textContent = user.first_name + ' ' + user.last_name;
            document.getElementById('accountBalance').textContent = formatCurrency(user.account_balance);
        }
        
        // Load recent recipients
        async function loadRecentRecipients() {
            const token = sessionStorage.getItem('auth_token');
            
            try {
                const response = await fetch(API_BASE + 'transactions.php?limit=10&type=sent', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        displayRecentRecipients(result.data.transactions);
                    }
                } else {
                    throw new Error('Failed to load recipients');
                }
                
            } catch (error) {
                console.error('Error loading recipients:', error);
                document.getElementById('recentRecipients').innerHTML = '<p class="text-danger text-center py-4">Failed to load recent recipients</p>';
            }
        }
        
        // Display recent recipients
        function displayRecentRecipients(transactions) {
            const container = document.getElementById('recentRecipients');
            
            if (!transactions || transactions.length === 0) {
                container.innerHTML = '<p class="text-muted text-center py-4">No recent transfers found</p>';
                return;
            }
            
            // Get unique recipients
            const recipients = [];
            const seenPhones = new Set();
            
            transactions.forEach(transaction => {
                if (transaction.recipient && !seenPhones.has(transaction.recipient.phone)) {
                    recipients.push(transaction.recipient);
                    seenPhones.add(transaction.recipient.phone);
                }
            });
            
            const recipientsHtml = recipients.slice(0, 5).map(recipient => {
                return `
                    <div class="recipient-item d-flex align-items-center justify-content-between p-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <i class="bi bi-person-circle fs-4 text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-bold">${recipient.name}</div>
                                <small class="text-muted">${recipient.phone}</small>
                            </div>
                        </div>
                        <button class="btn btn-outline-primary btn-sm" onclick="quickTransfer('${recipient.phone}', '${recipient.name}')">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = recipientsHtml || '<p class="text-muted text-center py-4">No recent recipients found</p>';
        }
        
        // Quick transfer to recent recipient
        function quickTransfer(phone, name) {
            document.getElementById('recipientPhone').value = phone;
            document.getElementById('transferReference').value = `Transfer to ${name}`;
            document.getElementById('recipientPhone').focus();
        }
        
        // Clear form
        function clearForm() {
            document.getElementById('transferForm').reset();
        }
        
        // Handle form submission
        document.getElementById('transferForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const phone = document.getElementById('recipientPhone').value;
            const amount = document.getElementById('transferAmount').value;
            const reference = document.getElementById('transferReference').value;
            const token = sessionStorage.getItem('auth_token');
            
            if (!token) {
                alert('Please login again.');
                window.location.href = 'auth/login.php';
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
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
                    // Show success message with proper formatting
                    const transferAmount = parseFloat(amount); // Use original amount from form
                    showAlert(`
                        <strong>Transfer Successful!</strong><br>
                        ${formatCurrency(transferAmount)} sent to ${result.data.transaction.recipient.name}<br>
                        Reference: ${result.data.transaction.reference_number}
                    `, 'success');
                    
                    // Update balance
                    document.getElementById('accountBalance').textContent = formatCurrency(result.data.sender_new_balance);
                    
                    // Clear form
                    this.reset();
                    
                    // Reload recipients
                    loadRecentRecipients();
                    
                } else {
                    showAlert(`Transfer failed: ${result.error || 'Unknown error occurred'}`, 'danger');
                }
                
            } catch (error) {
                console.error('Transfer error:', error);
                showAlert('Network error occurred. Please try again.', 'danger');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
        
        // Show alert function
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1060; max-width: 400px;';
            alertDiv.innerHTML = `
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 7000);
        }
        
        // Setup sidebar
        function setupSidebar() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('show');
            });
            
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                overlay.classList.remove('show');
            });
        }
        
        // Logout function
        async function logout() {
            if (confirm('Are you sure you want to logout?')) {
                const token = sessionStorage.getItem('auth_token');
                
                try {
                    await fetch(API_BASE + 'logout.php', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json'
                        }
                    });
                } catch (error) {
                    console.error('Logout error:', error);
                }
                
                sessionStorage.removeItem('auth_token');
                sessionStorage.removeItem('user_data');
                window.location.href = 'auth/login.php';
            }
        }
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', async function() {
            const user = await checkAuth();
            if (user) {
                setupSidebar();
                loadRecentRecipients();
            }
        });
        
        // Show alert function
        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alertContainer') || createAlertContainer();
            const alertId = 'alert-' + Date.now();
            
            const alertHtml = `
                <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : type === 'danger' ? 'x-circle' : 'info-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            alertContainer.innerHTML = alertHtml;
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                const alertElement = document.getElementById(alertId);
                if (alertElement) {
                    const alert = new bootstrap.Alert(alertElement);
                    alert.close();
                }
            }, 5000);
        }
        
        // Create alert container if it doesn't exist
        function createAlertContainer() {
            let container = document.getElementById('alertContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'alertContainer';
                container.className = 'position-fixed top-0 end-0 p-3';
                container.style.zIndex = '1055';
                document.body.appendChild(container);
            }
            return container;
        }
    </script>
</body>
</html>