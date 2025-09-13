<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget - PayLekker</title>
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
                <a href="budget-page.php" class="nav-link active">
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1><i class="bi bi-pie-chart me-3"></i>Budget Management</h1>
                        <p class="text-muted">Track your spending and manage your budget categories</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBudgetModal">
                        <i class="bi bi-plus-circle me-2"></i>Add Budget
                    </button>
                </div>
            </div>

            <!-- Budget Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-wallet2 fs-1 text-primary"></i>
                            <h4 id="totalBudget" class="card-title mt-2">R 0</h4>
                            <p class="card-text text-muted">Total Budget</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-graph-down fs-1 text-warning"></i>
                            <h4 id="totalSpent" class="card-title mt-2">R 0</h4>
                            <p class="card-text text-muted">Total Spent</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-piggy-bank fs-1 text-success"></i>
                            <h4 id="totalRemaining" class="card-title mt-2">R 0</h4>
                            <p class="card-text text-muted">Remaining</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-list-check fs-1 text-info"></i>
                            <h4 id="totalCategories" class="card-title mt-2">0</h4>
                            <p class="card-text text-muted">Categories</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Categories -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-list-ul me-2"></i>Budget Categories</h5>
                </div>
                <div class="card-body" id="budgetCategories">
                    <div class="text-center py-5">
                        <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Loading budget categories...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Budget Modal -->
    <div class="modal fade" id="addBudgetModal" tabindex="-1" aria-labelledby="addBudgetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addBudgetModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Add Budget Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="budgetForm">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" placeholder="e.g., Groceries, Transport" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="budgetAmount" class="form-label">Budget Amount (ZAR)</label>
                            <div class="input-group">
                                <span class="input-group-text">R</span>
                                <input type="number" class="form-control" id="budgetAmount" step="0.01" min="1" placeholder="0.00" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="budgetPeriod" class="form-label">Period</label>
                            <select class="form-select" id="budgetPeriod">
                                <option value="monthly" selected>Monthly</option>
                                <option value="weekly">Weekly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="startDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="startDate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="endDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="endDate" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createBudget()">
                        <i class="bi bi-plus-circle me-2"></i>Create Budget
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Budget Modal -->
    <div class="modal fade" id="editBudgetModal" tabindex="-1" aria-labelledby="editBudgetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editBudgetModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Update Budget Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editBudgetForm">
                        <input type="hidden" id="editBudgetId">
                        
                        <div class="mb-3">
                            <label for="editSpentAmount" class="form-label">Amount Spent (ZAR)</label>
                            <div class="input-group">
                                <span class="input-group-text">R</span>
                                <input type="number" class="form-control" id="editSpentAmount" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editBudgetAmount" class="form-label">Budget Amount (ZAR)</label>
                            <div class="input-group">
                                <span class="input-group-text">R</span>
                                <input type="number" class="form-control" id="editBudgetAmount" step="0.01" min="1" placeholder="0.00" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="updateBudget()">
                        <i class="bi bi-check-circle me-2"></i>Update Budget
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/common.js"></script>
    <script>
        let currentBudgets = [];
        
        // Update user info in UI
        function updateUserInfo(user) {
            const userNameElement = document.getElementById('userName');
            if (userNameElement) {
                userNameElement.textContent = user.first_name + ' ' + user.last_name;
            }
            
            console.log('User info updated on budget page:', user);
        }
        
        // Load budget categories
        async function loadBudgets() {
            const token = sessionStorage.getItem('auth_token');
            
            try {
                const response = await fetch(API_BASE + 'budget.php', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        currentBudgets = result.data.budgets || [];
                        displayBudgets(result.data);
                    } else {
                        throw new Error(result.error);
                    }
                } else {
                    throw new Error('Failed to load budgets');
                }
                
            } catch (error) {
                console.error('Error loading budgets:', error);
                document.getElementById('budgetCategories').innerHTML = '<p class="text-danger text-center py-4">Failed to load budget categories</p>';
            }
        }
        
        // Display budget categories
        function displayBudgets(data) {
            const container = document.getElementById('budgetCategories');
            const budgets = data.budgets || [];
            const summary = data.summary || {};
            
            // Update summary cards
            document.getElementById('totalBudget').textContent = `R ${parseFloat(summary.total_budget || 0).toLocaleString('en-ZA', {minimumFractionDigits: 2})}`;
            document.getElementById('totalSpent').textContent = `R ${parseFloat(summary.total_spent || 0).toLocaleString('en-ZA', {minimumFractionDigits: 2})}`;
            document.getElementById('totalRemaining').textContent = `R ${parseFloat(summary.total_remaining || 0).toLocaleString('en-ZA', {minimumFractionDigits: 2})}`;
            document.getElementById('totalCategories').textContent = budgets.length;
            
            if (budgets.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-pie-chart fs-1 text-muted"></i>
                        <h4 class="text-muted">No Budget Categories</h4>
                        <p class="text-muted">Create your first budget category to start tracking your spending</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBudgetModal">
                            <i class="bi bi-plus-circle me-2"></i>Add Budget Category
                        </button>
                    </div>
                `;
                return;
            }
            
            const budgetsHtml = budgets.map(budget => {
                const percentage = budget.percentage || 0;
                const isOverBudget = percentage > 100;
                const progressColor = isOverBudget ? 'danger' : (percentage > 80 ? 'warning' : 'success');
                
                return `
                    <div class="budget-item border rounded-3 p-3 mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">${budget.category_name}</h6>
                                    <span class="badge bg-light text-dark">${budget.period}</span>
                                </div>
                                
                                <div class="progress mb-2" style="height: 10px;">
                                    <div class="progress-bar bg-${progressColor}" role="progressbar" 
                                         style="width: ${Math.min(percentage, 100)}%" 
                                         aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between text-sm">
                                    <span class="text-muted">
                                        R${parseFloat(budget.spent_amount).toFixed(2)} of R${parseFloat(budget.budget_amount).toFixed(2)}
                                    </span>
                                    <span class="fw-bold ${isOverBudget ? 'text-danger' : 'text-success'}">
                                        ${percentage.toFixed(1)}%
                                    </span>
                                </div>
                                
                                <small class="text-muted">
                                    ${new Date(budget.start_date).toLocaleDateString()} - ${new Date(budget.end_date).toLocaleDateString()}
                                </small>
                            </div>
                            
                            <div class="col-md-4 text-end">
                                <div class="mb-2">
                                    <strong class="${budget.remaining_amount >= 0 ? 'text-success' : 'text-danger'}">
                                        R${Math.abs(parseFloat(budget.remaining_amount)).toFixed(2)} 
                                        ${budget.remaining_amount >= 0 ? 'left' : 'over'}
                                    </strong>
                                </div>
                                
                                <div class="btn-group">
                                    <button class="btn btn-outline-warning btn-sm" onclick="editBudget(${budget.id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteBudget(${budget.id}, '${budget.category_name}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = budgetsHtml;
        }
        
        // Set default dates
        function setDefaultDates() {
            const now = new Date();
            const startDate = new Date(now.getFullYear(), now.getMonth(), 1); // First day of month
            const endDate = new Date(now.getFullYear(), now.getMonth() + 1, 0); // Last day of month
            
            document.getElementById('startDate').value = startDate.toISOString().split('T')[0];
            document.getElementById('endDate').value = endDate.toISOString().split('T')[0];
        }
        
        // Create budget
        async function createBudget() {
            const token = sessionStorage.getItem('auth_token');
            const formData = {
                category_name: document.getElementById('categoryName').value,
                budget_amount: parseFloat(document.getElementById('budgetAmount').value),
                period: document.getElementById('budgetPeriod').value,
                start_date: document.getElementById('startDate').value,
                end_date: document.getElementById('endDate').value
            };
            
            try {
                const response = await fetch(API_BASE + 'budget.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    showAlert('Budget category created successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('addBudgetModal')).hide();
                    document.getElementById('budgetForm').reset();
                    setDefaultDates();
                    loadBudgets();
                } else {
                    showAlert(`Failed to create budget: ${result.error || 'Unknown error'}`, 'danger');
                }
                
            } catch (error) {
                console.error('Error creating budget:', error);
                showAlert('Network error occurred. Please try again.', 'danger');
            }
        }
        
        // Edit budget
        function editBudget(budgetId) {
            const budget = currentBudgets.find(b => b.id == budgetId);
            if (budget) {
                document.getElementById('editBudgetId').value = budgetId;
                document.getElementById('editSpentAmount').value = budget.spent_amount;
                document.getElementById('editBudgetAmount').value = budget.budget_amount;
                
                new bootstrap.Modal(document.getElementById('editBudgetModal')).show();
            }
        }
        
        // Update budget
        async function updateBudget() {
            const token = sessionStorage.getItem('auth_token');
            const budgetId = document.getElementById('editBudgetId').value;
            const formData = {
                spent_amount: parseFloat(document.getElementById('editSpentAmount').value),
                budget_amount: parseFloat(document.getElementById('editBudgetAmount').value)
            };
            
            try {
                const response = await fetch(API_BASE + `budget.php?id=${budgetId}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    showAlert('Budget updated successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('editBudgetModal')).hide();
                    loadBudgets();
                } else {
                    showAlert(`Failed to update budget: ${result.error || 'Unknown error'}`, 'danger');
                }
                
            } catch (error) {
                console.error('Error updating budget:', error);
                showAlert('Network error occurred. Please try again.', 'danger');
            }
        }
        
        // Delete budget
        async function deleteBudget(budgetId, categoryName) {
            if (!confirm(`Are you sure you want to delete the "${categoryName}" budget category?`)) {
                return;
            }
            
            const token = sessionStorage.getItem('auth_token');
            
            try {
                const response = await fetch(API_BASE + `budget.php?id=${budgetId}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    showAlert('Budget category deleted successfully!', 'success');
                    loadBudgets();
                } else {
                    showAlert(`Failed to delete budget: ${result.error || 'Unknown error'}`, 'danger');
                }
                
            } catch (error) {
                console.error('Error deleting budget:', error);
                showAlert('Network error occurred. Please try again.', 'danger');
            }
        }
        
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
                setDefaultDates();
                loadBudgets();
            }
        });
    </script>
</body>
</html>