<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - PayLekker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/dashboard-enhancements.css">
    <link rel="stylesheet" href="assets/css/force-fix.css">
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
                <a href="history-page.php" class="nav-link active">
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
                <a href="game-page.php" class="nav-link">
                    <i class="bi bi-controller"></i>
                    Games & Rewards
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
                <h1><i class="bi bi-clock-history me-3"></i>Transaction History</h1>
                <p class="text-muted">View and manage all your payment transactions</p>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label for="typeFilter" class="form-label">Transaction Type</label>
                            <select class="form-select" id="typeFilter">
                                <option value="all">All Transactions</option>
                                <option value="sent">Sent Money</option>
                                <option value="received">Received Money</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="limitFilter" class="form-label">Show</label>
                            <select class="form-select" id="limitFilter">
                                <option value="10">10 transactions</option>
                                <option value="25" selected>25 transactions</option>
                                <option value="50">50 transactions</option>
                                <option value="100">100 transactions</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="searchFilter" class="form-label">Search</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchFilter" placeholder="Search by amount, description...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-outline-primary d-block w-100" onclick="loadTransactions()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-arrow-up-circle fs-1 text-danger"></i>
                            <h4 id="totalSent" class="card-title mt-2">R 0</h4>
                            <p class="card-text text-muted">Total Sent</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-arrow-down-circle fs-1 text-success"></i>
                            <h4 id="totalReceived" class="card-title mt-2">R 0</h4>
                            <p class="card-text text-muted">Total Received</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-list-ol fs-1 text-info"></i>
                            <h4 id="totalTransactions" class="card-title mt-2">0</h4>
                            <p class="card-text text-muted">Total Transactions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-calendar-check fs-1 text-warning"></i>
                            <h4 id="thisMonthCount" class="card-title mt-2">0</h4>
                            <p class="card-text text-muted">This Month</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="bi bi-list-ul me-2"></i>Recent Transactions</h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportTransactions('csv')">
                            <i class="bi bi-download me-1"></i>CSV
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportTransactions('pdf')">
                            <i class="bi bi-file-pdf me-1"></i>PDF
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="transactionsList">
                        <div class="text-center py-5">
                            <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Loading transactions...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Transaction pagination" class="mt-4">
                <ul class="pagination justify-content-center" id="pagination">
                    <!-- Pagination will be inserted here -->
                </ul>
            </nav>
        </div>
    </div>

    <!-- Transaction Detail Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionModalLabel">
                        <i class="bi bi-receipt me-2"></i>Transaction Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="transactionDetails">
                    <!-- Transaction details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="downloadReceipt()">
                        <i class="bi bi-download me-2"></i>Download Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/common.js"></script>
    <script>
        let currentTransactions = [];
        let currentOffset = 0;
        let totalTransactions = 0;
        let selectedTransaction = null;
        
        // Update user info in UI
        function updateUserInfo(user) {
            const userNameElement = document.getElementById('userName');
            if (userNameElement) {
                userNameElement.textContent = user.first_name + ' ' + user.last_name;
            }
            
            const userEmailElement = document.getElementById('userEmail');
            if (userEmailElement) {
                userEmailElement.textContent = user.email;
            }
            
            console.log('User info updated on history page:', user);
        }
        
        // Load transactions
        async function loadTransactions(offset = 0) {
            const token = sessionStorage.getItem('auth_token');
            const type = document.getElementById('typeFilter').value;
            const limit = document.getElementById('limitFilter').value;
            
            currentOffset = offset;
            
            try {
                const response = await fetch(API_BASE + `transactions.php?limit=${limit}&offset=${offset}&type=${type}`, {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        currentTransactions = result.data.transactions;
                        totalTransactions = result.data.pagination.total;
                        displayTransactions(result.data.transactions);
                        updatePagination(result.data.pagination);
                        updateStatistics(result.data.transactions);
                    } else {
                        throw new Error(result.error);
                    }
                } else {
                    throw new Error('Failed to load transactions');
                }
                
            } catch (error) {
                console.error('Error loading transactions:', error);
                document.getElementById('transactionsList').innerHTML = '<p class="text-danger text-center py-4">Failed to load transactions</p>';
            }
        }
        
        // Display transactions
        function displayTransactions(transactions) {
            const container = document.getElementById('transactionsList');
            
            if (!transactions || transactions.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <h4 class="text-muted">No Transactions Found</h4>
                        <p class="text-muted">You haven't made any transactions yet</p>
                    </div>
                `;
                return;
            }
            
            const searchTerm = document.getElementById('searchFilter').value.toLowerCase();
            let filteredTransactions = transactions;
            
            if (searchTerm) {
                filteredTransactions = transactions.filter(transaction => 
                    transaction.description.toLowerCase().includes(searchTerm) ||
                    transaction.amount.toString().includes(searchTerm) ||
                    (transaction.recipient && transaction.recipient.name.toLowerCase().includes(searchTerm)) ||
                    (transaction.sender && transaction.sender.name.toLowerCase().includes(searchTerm)) ||
                    transaction.reference_number.toLowerCase().includes(searchTerm)
                );
            }
            
            const transactionsHtml = filteredTransactions.map(transaction => {
                // Fix for Fund Addition transactions - they should be positive
                const isFundAddition = transaction.description && 
                    (transaction.description.includes('Fund Addition') || 
                     transaction.description.includes('FUND ADDITION') ||
                     transaction.reference_number.includes('FUND'));
                
                const isReceived = transaction.type === 'received' || isFundAddition;
                const iconClass = isReceived ? 'bi-arrow-down-circle text-success' : 'bi-arrow-up-circle text-danger';
                const amountClass = isReceived ? 'text-success' : 'text-danger';
                const sign = isReceived ? '+' : '-';
                const otherParty = isReceived ? transaction.sender : transaction.recipient;
                const statusBadge = getStatusBadge(transaction.status);
                
                return `
                    <div class="transaction-row d-flex align-items-center p-3 border-bottom" 
                         style="cursor: pointer;" onclick="showTransactionDetails(${transaction.id})">
                        <div class="transaction-icon me-3">
                            <i class="bi ${iconClass} fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">${transaction.description}</h6>
                                    <div class="d-flex align-items-center">
                                        <small class="text-muted me-2">
                                            ${otherParty ? otherParty.name : 'Unknown'} • 
                                            ${new Date(transaction.created_at).toLocaleDateString('en-ZA')}
                                        </small>
                                        ${statusBadge}
                                    </div>
                                    <small class="text-muted">${transaction.reference_number}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold ${amountClass} fs-5">
                                        ${sign}${formatCurrency(parseFloat(transaction.amount))}
                                    </div>
                                    <small class="text-muted">
                                        ${new Date(transaction.created_at).toLocaleTimeString('en-ZA', {hour: '2-digit', minute: '2-digit'})}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = transactionsHtml || '<p class="text-muted text-center py-4">No transactions match your search</p>';
        }
        
        // Get status badge
        function getStatusBadge(status) {
            const badges = {
                'completed': '<span class="badge bg-success">Completed</span>',
                'pending': '<span class="badge bg-warning">Pending</span>',
                'failed': '<span class="badge bg-danger">Failed</span>',
                'cancelled': '<span class="badge bg-secondary">Cancelled</span>'
            };
            return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
        }
        
        // Update statistics
        function updateStatistics(transactions) {
            let totalSent = 0, totalReceived = 0, thisMonthCount = 0;
            const thisMonth = new Date().getMonth();
            const thisYear = new Date().getFullYear();
            
            transactions.forEach(transaction => {
                const amount = parseFloat(transaction.amount);
                const transactionDate = new Date(transaction.created_at);
                
                if (transaction.type === 'sent') {
                    totalSent += amount;
                } else {
                    totalReceived += amount;
                }
                
                if (transactionDate.getMonth() === thisMonth && transactionDate.getFullYear() === thisYear) {
                    thisMonthCount++;
                }
            });
            
            document.getElementById('totalSent').textContent = `R ${totalSent.toLocaleString('en-ZA', {minimumFractionDigits: 2})}`;
            document.getElementById('totalReceived').textContent = `R ${totalReceived.toLocaleString('en-ZA', {minimumFractionDigits: 2})}`;
            document.getElementById('totalTransactions').textContent = totalTransactions.toString();
            document.getElementById('thisMonthCount').textContent = thisMonthCount.toString();
        }
        
        // Update pagination
        function updatePagination(pagination) {
            const paginationContainer = document.getElementById('pagination');
            const limit = parseInt(pagination.limit);
            const totalPages = Math.ceil(pagination.total / limit);
            const currentPage = Math.floor(pagination.offset / limit) + 1;
            
            if (totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }
            
            let paginationHtml = '';
            
            // Previous button
            if (currentPage > 1) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="loadTransactions(${(currentPage - 2) * limit})">Previous</a>
                    </li>
                `;
            }
            
            // Page numbers
            for (let i = 1; i <= Math.min(totalPages, 10); i++) {
                const isActive = i === currentPage ? 'active' : '';
                paginationHtml += `
                    <li class="page-item ${isActive}">
                        <a class="page-link" href="#" onclick="loadTransactions(${(i - 1) * limit})">${i}</a>
                    </li>
                `;
            }
            
            // Next button
            if (currentPage < totalPages) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="loadTransactions(${currentPage * limit})">Next</a>
                    </li>
                `;
            }
            
            paginationContainer.innerHTML = paginationHtml;
        }
        
        // Show transaction details
        function showTransactionDetails(transactionId) {
            const transaction = currentTransactions.find(t => t.id == transactionId);
            if (!transaction) return;
            
            selectedTransaction = transaction;
            
            // Fix for Fund Addition transactions in modal
            const isFundAddition = transaction.description && 
                (transaction.description.includes('Fund Addition') || 
                 transaction.description.includes('FUND ADDITION') ||
                 transaction.reference_number.includes('FUND'));
                 
            const isReceived = transaction.type === 'received' || isFundAddition;
            const otherParty = isReceived ? transaction.sender : transaction.recipient;
            
            const detailsHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Transaction ID:</strong><br>
                        <span class="text-muted">${transaction.reference_number}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong><br>
                        ${getStatusBadge(transaction.status)}
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Amount:</strong><br>
                        <span class="fs-4 ${isReceived ? 'text-success' : 'text-danger'}">
                            ${isReceived ? '+' : '-'}${formatCurrency(parseFloat(transaction.amount))}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Date & Time:</strong><br>
                        <span class="text-muted">
                            ${new Date(transaction.created_at).toLocaleString('en-ZA')}
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <strong>${isReceived ? 'From' : 'To'}:</strong><br>
                        <span class="text-muted">${otherParty ? otherParty.name : 'Unknown'}</span><br>
                        <small class="text-muted">${otherParty ? otherParty.phone : 'N/A'}</small>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <strong>Description:</strong><br>
                        <span class="text-muted">${transaction.description}</span>
                    </div>
                </div>
            `;
            
            document.getElementById('transactionDetails').innerHTML = detailsHtml;
            new bootstrap.Modal(document.getElementById('transactionModal')).show();
        }
        
        // Export transactions
        function exportTransactions(format) {
            const type = document.getElementById('typeFilter').value;
            const limit = document.getElementById('limitFilter').value;
            
            showAlert(`Exporting transactions as ${format.toUpperCase()}... (Feature coming soon)`, 'info');
        }
        
        // Download receipt
        function downloadReceipt() {
            if (selectedTransaction) {
                showAlert(`Downloading receipt for transaction ${selectedTransaction.reference_number}... (Feature coming soon)`, 'info');
            }
        }
        
        // Event listeners for filters
        document.getElementById('typeFilter').addEventListener('change', () => loadTransactions(0));
        document.getElementById('limitFilter').addEventListener('change', () => loadTransactions(0));
        document.getElementById('searchFilter').addEventListener('input', () => {
            displayTransactions(currentTransactions);
        });
        
        // Show alert function
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1060; max-width: 400px;';
            alertDiv.innerHTML = `
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'info' ? 'info-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
        
        // Setup sidebar
        function setupSidebar() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
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
                loadTransactions();
            }
        });
    </script>
</body>
</html>