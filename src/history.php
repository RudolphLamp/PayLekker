<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-gradient">Transaction History</h2>
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Filters Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Transactions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="dateFrom" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="dateFrom">
                        </div>
                        <div class="col-md-3">
                            <label for="dateTo" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="dateTo">
                        </div>
                        <div class="col-md-3">
                            <label for="typeFilter" class="form-label">Type</label>
                            <select class="form-select" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="sent">Money Sent</option>
                                <option value="received">Money Received</option>
                                <option value="deposit">Deposits</option>
                                <option value="withdrawal">Withdrawals</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="amountFilter" class="form-label">Amount Range</label>
                            <select class="form-select" id="amountFilter">
                                <option value="">All Amounts</option>
                                <option value="0-100">R0 - R100</option>
                                <option value="100-500">R100 - R500</option>
                                <option value="500-1000">R500 - R1,000</option>
                                <option value="1000+">R1,000+</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" 
                                       placeholder="Search by description or recipient...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="applyFilters">
                                    <i class="fas fa-search me-2"></i>Apply Filters
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="clearFilters">
                                    <i class="fas fa-times me-2"></i>Clear
                                </button>
                                <button type="button" class="btn btn-success" id="exportTransactions">
                                    <i class="fas fa-download me-2"></i>Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-down fa-2x mb-2"></i>
                            <h5 class="card-title" id="totalReceived">R0.00</h5>
                            <p class="card-text">Total Received</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-up fa-2x mb-2"></i>
                            <h5 class="card-title" id="totalSent">R0.00</h5>
                            <p class="card-text">Total Sent</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-exchange-alt fa-2x mb-2"></i>
                            <h5 class="card-title" id="totalTransactions">0</h5>
                            <p class="card-text">Total Transactions</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar fa-2x mb-2"></i>
                            <h5 class="card-title" id="thisMonth">R0.00</h5>
                            <p class="card-text">This Month</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Your Transactions</h5>
                    <div class="d-flex align-items-center gap-3">
                        <span id="transactionCount" class="badge bg-light text-dark">Loading...</span>
                        <div class="d-flex align-items-center gap-2">
                            <label for="sortBy" class="form-label mb-0 text-white">Sort by:</label>
                            <select class="form-select form-select-sm" id="sortBy" style="width: auto;">
                                <option value="date_desc">Latest First</option>
                                <option value="date_asc">Oldest First</option>
                                <option value="amount_desc">Highest Amount</option>
                                <option value="amount_asc">Lowest Amount</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="transactionsContainer">
                        <div class="text-center p-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading your transactions...</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <nav id="paginationContainer">
                        <!-- Pagination will be loaded here -->
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Detail Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="transactionDetails">
                    <!-- Transaction details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printReceipt">
                    <i class="fas fa-print me-2"></i>Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let currentFilters = {};
    let currentSort = 'date_desc';
    const itemsPerPage = 10;
    const transactionModal = new bootstrap.Modal(document.getElementById('transactionModal'));

    // Initialize
    loadTransactions();
    loadSummaryStats();
    
    // Set default date range (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    document.getElementById('dateTo').value = today.toISOString().split('T')[0];
    document.getElementById('dateFrom').value = thirtyDaysAgo.toISOString().split('T')[0];

    // Event listeners
    document.getElementById('applyFilters').addEventListener('click', applyFilters);
    document.getElementById('clearFilters').addEventListener('click', clearFilters);
    document.getElementById('exportTransactions').addEventListener('click', exportTransactions);
    document.getElementById('sortBy').addEventListener('change', function() {
        currentSort = this.value;
        currentPage = 1;
        loadTransactions();
    });

    function applyFilters() {
        currentFilters = {
            dateFrom: document.getElementById('dateFrom').value,
            dateTo: document.getElementById('dateTo').value,
            type: document.getElementById('typeFilter').value,
            amount: document.getElementById('amountFilter').value,
            search: document.getElementById('searchInput').value.trim()
        };
        currentPage = 1;
        loadTransactions();
        loadSummaryStats();
    }

    function clearFilters() {
        document.getElementById('dateFrom').value = '';
        document.getElementById('dateTo').value = '';
        document.getElementById('typeFilter').value = '';
        document.getElementById('amountFilter').value = '';
        document.getElementById('searchInput').value = '';
        currentFilters = {};
        currentPage = 1;
        loadTransactions();
        loadSummaryStats();
    }

    function loadTransactions() {
        const params = new URLSearchParams({
            page: currentPage,
            limit: itemsPerPage,
            sort: currentSort,
            ...currentFilters
        });

        fetch(`api/transactions.php?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayTransactions(data.transactions);
                displayPagination(data.pagination);
                document.getElementById('transactionCount').textContent = 
                    `${data.pagination.total} transactions`;
            } else {
                document.getElementById('transactionsContainer').innerHTML = 
                    '<div class="text-center p-5"><i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i><p class="text-muted">Error loading transactions</p></div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('transactionsContainer').innerHTML = 
                '<div class="text-center p-5"><i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i><p class="text-muted">Failed to load transactions</p></div>';
        });
    }

    function displayTransactions(transactions) {
        const container = document.getElementById('transactionsContainer');
        
        if (!transactions || transactions.length === 0) {
            container.innerHTML = `
                <div class="text-center p-5">
                    <i class="fas fa-inbox text-muted fa-3x mb-3"></i>
                    <h5 class="text-muted">No transactions found</h5>
                    <p class="text-muted">Try adjusting your filters or make your first transaction!</p>
                </div>
            `;
            return;
        }

        const transactionRows = transactions.map(transaction => {
            const isIncoming = transaction.type === 'received' || transaction.type === 'deposit';
            const amountClass = isIncoming ? 'text-success' : 'text-danger';
            const amountPrefix = isIncoming ? '+' : '-';
            const icon = getTransactionIcon(transaction.type);
            const statusBadge = getStatusBadge(transaction.status);
            
            return `
                <tr class="transaction-row" data-transaction-id="${transaction.id}" style="cursor: pointer;">
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center">
                            <div class="transaction-icon me-3">
                                <i class="${icon} fa-lg text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">${escapeHtml(transaction.description)}</div>
                                <small class="text-muted">
                                    ${transaction.type === 'sent' ? 'To: ' + escapeHtml(transaction.recipient_name || transaction.recipient_email) : 
                                      transaction.type === 'received' ? 'From: ' + escapeHtml(transaction.sender_name || transaction.sender_email) : 
                                      transaction.type}
                                </small>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="fw-bold ${amountClass}">
                            ${amountPrefix}R${parseFloat(transaction.amount).toFixed(2)}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        ${statusBadge}
                    </td>
                    <td class="px-4 py-3">
                        <div class="fw-semibold">${formatDate(transaction.created_at)}</div>
                        <small class="text-muted">${formatTime(transaction.created_at)}</small>
                    </td>
                    <td class="px-4 py-3">
                        <button class="btn btn-outline-primary btn-sm" onclick="viewTransaction(${transaction.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        container.innerHTML = `
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Transaction</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${transactionRows}
                    </tbody>
                </table>
            </div>
        `;

        // Add click handlers for transaction rows
        document.querySelectorAll('.transaction-row').forEach(row => {
            row.addEventListener('click', function() {
                const transactionId = this.dataset.transactionId;
                viewTransaction(transactionId);
            });
        });
    }

    function displayPagination(pagination) {
        const container = document.getElementById('paginationContainer');
        
        if (pagination.totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHtml = '<ul class="pagination justify-content-center mb-0">';
        
        // Previous button
        paginationHtml += `
            <li class="page-item ${pagination.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${pagination.currentPage - 1})">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;

        // Page numbers
        for (let i = 1; i <= pagination.totalPages; i++) {
            if (i === 1 || i === pagination.totalPages || (i >= pagination.currentPage - 2 && i <= pagination.currentPage + 2)) {
                paginationHtml += `
                    <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                    </li>
                `;
            } else if (i === pagination.currentPage - 3 || i === pagination.currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Next button
        paginationHtml += `
            <li class="page-item ${pagination.currentPage === pagination.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${pagination.currentPage + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
        
        paginationHtml += '</ul>';
        container.innerHTML = paginationHtml;
    }

    function loadSummaryStats() {
        const params = new URLSearchParams(currentFilters);
        
        fetch(`api/transactions.php?action=summary&${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalReceived').textContent = 'R' + parseFloat(data.stats.total_received || 0).toFixed(2);
                document.getElementById('totalSent').textContent = 'R' + parseFloat(data.stats.total_sent || 0).toFixed(2);
                document.getElementById('totalTransactions').textContent = data.stats.total_count || 0;
                document.getElementById('thisMonth').textContent = 'R' + parseFloat(data.stats.this_month || 0).toFixed(2);
            }
        })
        .catch(error => console.error('Error loading summary stats:', error));
    }

    // Global functions
    window.changePage = function(page) {
        currentPage = page;
        loadTransactions();
    };

    window.viewTransaction = function(transactionId) {
        fetch(`api/transactions.php?id=${transactionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.transaction) {
                displayTransactionDetails(data.transaction);
                transactionModal.show();
            }
        })
        .catch(error => console.error('Error loading transaction details:', error));
    };

    function displayTransactionDetails(transaction) {
        const isIncoming = transaction.type === 'received' || transaction.type === 'deposit';
        const amountClass = isIncoming ? 'text-success' : 'text-danger';
        const amountPrefix = isIncoming ? '+' : '-';
        
        document.getElementById('transactionDetails').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-3">Transaction Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <td class="text-muted">Transaction ID:</td>
                            <td class="fw-semibold">#${transaction.id}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Type:</td>
                            <td class="fw-semibold text-capitalize">${transaction.type}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Amount:</td>
                            <td class="fw-bold ${amountClass}">${amountPrefix}R${parseFloat(transaction.amount).toFixed(2)}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status:</td>
                            <td>${getStatusBadge(transaction.status)}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date:</td>
                            <td class="fw-semibold">${formatDateTime(transaction.created_at)}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold mb-3">Details</h6>
                    <table class="table table-sm">
                        <tr>
                            <td class="text-muted">Description:</td>
                            <td class="fw-semibold">${escapeHtml(transaction.description)}</td>
                        </tr>
                        ${transaction.recipient_name ? `
                        <tr>
                            <td class="text-muted">Recipient:</td>
                            <td class="fw-semibold">${escapeHtml(transaction.recipient_name)}<br>
                                <small class="text-muted">${escapeHtml(transaction.recipient_email)}</small>
                            </td>
                        </tr>
                        ` : ''}
                        ${transaction.sender_name ? `
                        <tr>
                            <td class="text-muted">Sender:</td>
                            <td class="fw-semibold">${escapeHtml(transaction.sender_name)}<br>
                                <small class="text-muted">${escapeHtml(transaction.sender_email)}</small>
                            </td>
                        </tr>
                        ` : ''}
                    </table>
                </div>
            </div>
        `;
    }

    function exportTransactions() {
        const params = new URLSearchParams({
            action: 'export',
            format: 'csv',
            ...currentFilters
        });
        
        window.open(`api/transactions.php?${params}`, '_blank');
    }

    // Utility functions
    function getTransactionIcon(type) {
        const icons = {
            'sent': 'fas fa-arrow-up',
            'received': 'fas fa-arrow-down',
            'deposit': 'fas fa-plus-circle',
            'withdrawal': 'fas fa-minus-circle'
        };
        return icons[type] || 'fas fa-exchange-alt';
    }

    function getStatusBadge(status) {
        const badges = {
            'completed': '<span class="badge bg-success">Completed</span>',
            'pending': '<span class="badge bg-warning">Pending</span>',
            'failed': '<span class="badge bg-danger">Failed</span>',
            'cancelled': '<span class="badge bg-secondary">Cancelled</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-ZA');
    }

    function formatTime(dateString) {
        return new Date(dateString).toLocaleTimeString('en-ZA', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    function formatDateTime(dateString) {
        return new Date(dateString).toLocaleString('en-ZA');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>