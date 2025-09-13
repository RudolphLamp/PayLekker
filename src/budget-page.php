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
    <style>
        /* Force all icons to be black */
        i, .bi, [class*="bi-"] {
            color: #000000 !important;
        }
        
        /* Budget specific styling to match dashboard */
        .budget-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
            margin-bottom: 2rem;
        }
        
        .budget-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .budget-title {
            color: #000000;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        
        .budget-amount {
            color: #495057;
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .budget-progress {
            margin: 1rem 0;
        }
        
        .progress {
            height: 12px;
            border-radius: 6px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        
        .progress-bar {
            border-radius: 6px;
            transition: width 0.6s ease;
        }
        
        .progress-bar.bg-success {
            background: #495057 !important;
        }
        
        .progress-bar.bg-warning {
            background: #6c757d !important;
        }
        
        .progress-bar.bg-danger {
            background: #adb5bd !important;
        }
        
        .budget-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .stat-item {
            text-align: center;
            flex: 1;
        }
        
        .stat-value {
            font-size: 1.25rem;
            font-weight: 600;
            color: #000000;
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .add-budget-btn {
            background: #000000;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .add-budget-btn:hover {
            background: #343a40;
            transform: translateY(-2px);
        }
        
        .budget-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000000;
            font-size: 1.5rem;
            border: 1px solid #dee2e6;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .modal-header {
            background: #000000;
            color: white;
            border-bottom: none;
        }
        
        .modal-header h5 {
            color: white;
        }
        
        .modal-header .btn-close {
            filter: invert(1);
        }
        
        .form-control:focus {
            border-color: #6c757d;
            box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.15);
        }
        
        .btn-primary {
            background: #000000;
            border-color: #000000;
        }
        
        .btn-primary:hover {
            background: #343a40;
            border-color: #343a40;
        }
        
        .page-title {
            color: #000000;
            font-weight: 600;
        }
        
        .text-muted {
            color: #6c757d !important;
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
                <h2 class="page-title mb-1">Budget Management</h2>
                <p class="text-muted">Track your spending and manage your budgets</p>
            </div>
            <button class="add-budget-btn" data-bs-toggle="modal" data-bs-target="#addBudgetModal">
                <i class="bi bi-plus me-2"></i>Add Budget
            </button>
        </div>

        <!-- Budget Overview -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="budget-card">
                    <div class="stat-item">
                        <div class="stat-value" id="totalBudget">R 0</div>
                        <div class="stat-label">Total Budget</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="budget-card">
                    <div class="stat-item">
                        <div class="stat-value" id="totalSpent">R 0</div>
                        <div class="stat-label">Total Spent</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="budget-card">
                    <div class="stat-item">
                        <div class="stat-value" id="totalRemaining">R 0</div>
                        <div class="stat-label">Remaining</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget List -->
        <div class="budget-card">
            <h5 class="budget-title mb-4">Your Budgets</h5>
            <div id="budgetsList">
                <div class="empty-state">
                    <i class="bi bi-pie-chart-fill"></i>
                    <h5>No budgets yet</h5>
                    <p>Create your first budget to start tracking your spending</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Budget Modal -->
    <div class="modal fade" id="addBudgetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>Add New Budget
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addBudgetForm">
                        <div class="mb-3">
                            <label for="budgetCategory" class="form-label">Category</label>
                            <select class="form-select" id="budgetCategory" required>
                                <option value="">Select category</option>
                                <option value="groceries">🛒 Groceries</option>
                                <option value="transport">🚗 Transport</option>
                                <option value="entertainment">🎬 Entertainment</option>
                                <option value="utilities">💡 Utilities</option>
                                <option value="dining">🍽️ Dining</option>
                                <option value="shopping">🛍️ Shopping</option>
                                <option value="other">📝 Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="budgetAmount" class="form-label">Budget Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">R</span>
                                <input type="number" class="form-control" id="budgetAmount" 
                                       min="1" max="50000" step="0.01" required placeholder="0.00">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="budgetPeriod" class="form-label">Period</label>
                            <select class="form-select" id="budgetPeriod" required>
                                <option value="monthly">Monthly</option>
                                <option value="weekly">Weekly</option>
                                <option value="daily">Daily</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addBudget()">
                        <i class="bi bi-plus me-2"></i>Add Budget
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/common.js"></script>
    <script>
        let budgets = [];
        
        // Initialize budgets from database
        async function initializeBudgets() {
            try {
                const token = sessionStorage.getItem('auth_token');
                const response = await fetch(API_BASE + 'budget.php', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });

                console.log('Budget API response status:', response.status);

                if (response.ok) {
                    const responseText = await response.text();
                    console.log('Budget API response text:', responseText);
                    
                    try {
                        const result = JSON.parse(responseText);
                        if (result.success) {
                            budgets = result.data.budgets || [];
                            console.log('Loaded budgets:', budgets);
                            displayBudgets();
                            updateOverview();
                            return;
                        } else {
                            console.error('Budget API error:', result.error);
                        }
                    } catch (parseError) {
                        console.error('JSON parse error:', parseError);
                        console.error('Raw response:', responseText);
                    }
                }
                
                // If API fails, show empty state
                budgets = [];
                displayBudgets();
                updateOverview();
                
            } catch (error) {
                console.error('Error loading budgets:', error);
                budgets = [];
                displayBudgets();
                updateOverview();
                showAlert('Failed to load budgets. Please refresh the page.', 'warning');
            }
        }
        
        // Display budgets
        function displayBudgets() {
            const container = document.getElementById('budgetsList');
            
            if (budgets.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-pie-chart-fill"></i>
                        <h5>No budgets yet</h5>
                        <p>Create your first budget to start tracking your spending</p>
                    </div>
                `;
                return;
            }
            
            const budgetsHtml = budgets.map(budget => {
                const amount = parseFloat(budget.amount);
                const spent = parseFloat(budget.spent);
                const percentage = amount > 0 ? Math.min((spent / amount) * 100, 100) : 0;
                const remaining = amount - spent;
                const status = percentage > 100 ? 'danger' : (percentage > 75 ? 'warning' : 'success');
                
                return `
                    <div class="row align-items-center mb-3 p-3 border rounded">
                        <div class="col-md-1">
                            <div class="budget-icon">
                                <i class="bi ${getCategoryIcon(budget.category)}"></i>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <h6 class="mb-1">${budget.name}</h6>
                            <small class="text-muted">${budget.period}</small>
                        </div>
                        <div class="col-md-3">
                            <div class="budget-progress">
                                <div class="progress">
                                    <div class="progress-bar bg-${status}" 
                                         role="progressbar" 
                                         style="width: ${Math.min(percentage, 100)}%"
                                         aria-valuenow="${percentage}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small>R${spent.toFixed(2)} spent</small>
                                    <small>${percentage.toFixed(1)}%</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="budget-amount">R${amount.toFixed(2)}</div>
                            <small class="text-muted">Budget</small>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="budget-amount ${remaining < 0 ? 'text-danger' : 'text-success'}">
                                R${remaining.toFixed(2)}
                            </div>
                            <small class="text-muted">Remaining</small>
                        </div>
                        <div class="col-md-2">
                            <div class="btn-group-vertical d-grid gap-1">
                                <button class="btn btn-sm btn-outline-primary" onclick="addSpending(${budget.id})">
                                    <i class="bi bi-plus"></i> Add Expense
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="editBudget(${budget.id})">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteBudget(${budget.id})">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = budgetsHtml;
        }
        
        // Update overview
        function updateOverview() {
            const totalBudget = budgets.reduce((sum, budget) => sum + parseFloat(budget.amount), 0);
            const totalSpent = budgets.reduce((sum, budget) => sum + parseFloat(budget.spent), 0);
            const totalRemaining = totalBudget - totalSpent;
            
            document.getElementById('totalBudget').textContent = `R ${totalBudget.toLocaleString('en-ZA', {minimumFractionDigits: 2})}`;
            document.getElementById('totalSpent').textContent = `R ${totalSpent.toLocaleString('en-ZA', {minimumFractionDigits: 2})}`;
            document.getElementById('totalRemaining').textContent = `R ${totalRemaining.toLocaleString('en-ZA', {minimumFractionDigits: 2})}`;
        }
        
        // Get category icon
        function getCategoryIcon(category) {
            const icons = {
                groceries: 'bi-cart-fill',
                transport: 'bi-car-front-fill',
                entertainment: 'bi-film',
                utilities: 'bi-lightning-charge-fill',
                dining: 'bi-cup-hot-fill',
                shopping: 'bi-bag-fill',
                other: 'bi-three-dots'
            };
            return icons[category] || 'bi-three-dots';
        }
        
        // Add budget
        async function addBudget() {
            const category = document.getElementById('budgetCategory').value;
            const amount = parseFloat(document.getElementById('budgetAmount').value);
            const period = document.getElementById('budgetPeriod').value;
            
            if (!category || !amount || amount <= 0) {
                showAlert('Please fill in all fields with valid values', 'warning');
                return;
            }
            
            const token = sessionStorage.getItem('auth_token');
            
            try {
                const response = await fetch(API_BASE + 'budget.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        category: category,
                        amount: amount,
                        period: period
                    })
                });

                console.log('Add budget response status:', response.status);
                const responseText = await response.text();
                console.log('Add budget response:', responseText);

                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    throw new Error('Invalid response from server');
                }
                
                if (result.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addBudgetModal'));
                    modal.hide();
                    
                    // Reset form
                    document.getElementById('addBudgetForm').reset();
                    
                    // Refresh budgets
                    await initializeBudgets();
                    
                    showAlert('Budget added successfully!', 'success');
                } else {
                    showAlert(result.error || 'Failed to add budget', 'danger');
                }
                
            } catch (error) {
                console.error('Error adding budget:', error);
                showAlert('Network error occurred: ' + error.message, 'danger');
            }
        }

        // Add spending to budget (expense tracking)
        async function addSpending(budgetId) {
            const amount = prompt('Enter expense amount (R):');
            if (!amount || isNaN(amount) || parseFloat(amount) <= 0) {
                showAlert('Please enter a valid amount', 'warning');
                return;
            }

            const description = prompt('Enter expense description (optional):') || 'Budget expense';
            const token = sessionStorage.getItem('auth_token');

            try {
                const response = await fetch(API_BASE + 'budget.php', {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: budgetId,
                        add_spent: parseFloat(amount),
                        description: description
                    })
                });

                console.log('Update budget response status:', response.status);
                const responseText = await response.text();
                console.log('Update budget response:', responseText);

                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    throw new Error('Invalid response from server');
                }
                
                if (result.success) {
                    await initializeBudgets(); // Refresh budgets
                    showAlert(`Added R${parseFloat(amount).toFixed(2)} expense to budget`, 'success');
                } else {
                    showAlert(result.error || 'Failed to update budget', 'danger');
                }
                
            } catch (error) {
                console.error('Error updating budget:', error);
                showAlert('Network error occurred: ' + error.message, 'danger');
            }
        }

        // Edit budget
        async function editBudget(budgetId) {
            const budget = budgets.find(b => b.id == budgetId);
            if (!budget) {
                showAlert('Budget not found', 'danger');
                return;
            }

            const newAmount = prompt(`Edit budget amount for ${budget.name}:`, budget.amount);
            if (!newAmount || isNaN(newAmount) || parseFloat(newAmount) <= 0) {
                return;
            }

            const token = sessionStorage.getItem('auth_token');

            try {
                const response = await fetch(API_BASE + 'budget.php', {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: budgetId,
                        amount: parseFloat(newAmount)
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    await initializeBudgets(); // Refresh budgets
                    showAlert(`Budget updated to R${parseFloat(newAmount).toFixed(2)}`, 'success');
                } else {
                    showAlert(result.error || 'Failed to update budget', 'danger');
                }
                
            } catch (error) {
                console.error('Error updating budget:', error);
                showAlert('Network error occurred', 'danger');
            }
        }

        // Delete budget
        async function deleteBudget(budgetId) {
            const budget = budgets.find(b => b.id == budgetId);
            if (!budget) {
                showAlert('Budget not found', 'danger');
                return;
            }

            if (!confirm(`Are you sure you want to delete the ${budget.name} budget?`)) {
                return;
            }

            const token = sessionStorage.getItem('auth_token');

            try {
                const response = await fetch(API_BASE + 'budget.php', {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: budgetId
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    await initializeBudgets(); // Refresh budgets
                    showAlert('Budget deleted successfully', 'success');
                } else {
                    showAlert(result.error || 'Failed to delete budget', 'danger');
                }
                
            } catch (error) {
                console.error('Error deleting budget:', error);
                showAlert('Network error occurred', 'danger');
            }
        }
        
        // Update user info
        function updateUserInfo() {
            const userData = sessionStorage.getItem('user_data');
            if (userData) {
                try {
                    const user = JSON.parse(userData);
                    document.getElementById('userName').textContent = `${user.first_name} ${user.last_name}`;
                    document.getElementById('userEmail').textContent = user.email;
                } catch (e) {
                    console.error('Error parsing user data:', e);
                }
            }
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
                sessionStorage.removeItem('auth_token');
                sessionStorage.removeItem('user_data');
                window.location.href = 'auth/login.php';
            }
        }
        
        // Show alert
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1060; max-width: 400px;';
            alertDiv.innerHTML = `
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
        
        // Check authentication
        async function checkAuth() {
            const token = sessionStorage.getItem('auth_token');
            if (!token) {
                window.location.href = 'auth/login.php';
                return false;
            }
            return true;
        }
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', async function() {
            const isAuthenticated = await checkAuth();
            if (isAuthenticated) {
                updateUserInfo();
                setupSidebar();
                await initializeBudgets();
            }
        });
    </script>
</body>
</html>