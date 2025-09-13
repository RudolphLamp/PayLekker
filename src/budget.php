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
                <h2 class="text-gradient">Budget Management</h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#budgetModal">
                        <i class="fas fa-plus me-2"></i>New Budget
                    </button>
                    <a href="dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-wallet fa-2x mb-2"></i>
                    <h5 class="card-title" id="totalBudget">R0.00</h5>
                    <p class="card-text">Total Budget</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h5 class="card-title" id="remaining">R0.00</h5>
                    <p class="card-text">Remaining</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                    <h5 class="card-title" id="totalSpent">R0.00</h5>
                    <p class="card-text">Total Spent</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-chart-pie fa-2x mb-2"></i>
                    <h5 class="card-title" id="avgSpending">R0.00</h5>
                    <p class="card-text">Daily Average</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Progress Overview -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Monthly Budget Progress</h6>
        </div>
        <div class="card-body">
            <div id="budgetProgressContainer">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Categories -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Budget Categories</h5>
            <div class="d-flex align-items-center gap-2">
                <select class="form-select form-select-sm text-dark" id="monthFilter" style="width: auto;">
                    <option value="">Current Month</option>
                    <!-- Options will be populated by JavaScript -->
                </select>
            </div>
        </div>
        <div class="card-body">
            <div id="budgetCategoriesContainer">
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading your budgets...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Spending Insights -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Spending Trends</h6>
                </div>
                <div class="card-body">
                    <canvas id="spendingChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Budget Tips</h6>
                </div>
                <div class="card-body">
                    <div id="budgetTips">
                        <!-- Tips will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Budget Modal -->
<div class="modal fade" id="budgetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="budgetModalTitle">
                    <i class="fas fa-plus me-2"></i>Create New Budget
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="budgetForm">
                    <?php echo csrf_token_input(); ?>
                    <input type="hidden" id="budgetId" name="budget_id">
                    
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="categoryName" name="category_name" 
                               placeholder="e.g., Groceries, Transport, Entertainment" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="budgetAmount" class="form-label">Budget Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">R</span>
                            <input type="number" class="form-control" id="budgetAmount" name="budget_amount" 
                                   placeholder="0.00" min="1" max="100000" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="budgetPeriod" class="form-label">Budget Period</label>
                        <select class="form-select" id="budgetPeriod" name="budget_period" required>
                            <option value="">Select period</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly" selected>Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="budgetDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="budgetDescription" name="description" 
                                  rows="3" placeholder="Add notes about this budget category"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="enableAlerts" name="enable_alerts" checked>
                            <label class="form-check-label" for="enableAlerts">
                                Enable alerts when spending reaches 80% of budget
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveBudget">
                    <i class="fas fa-save me-2"></i>Save Budget
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2 text-danger"></i>Delete Budget
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this budget category?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    This action cannot be undone. All spending data for this category will be lost.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash me-2"></i>Delete Budget
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const budgetModal = new bootstrap.Modal(document.getElementById('budgetModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let budgetChart;
    let currentBudgetId = null;

    // Initialize
    loadBudgetOverview();
    loadBudgetCategories();
    loadSpendingChart();
    loadBudgetTips();
    populateMonthFilter();

    // Event listeners
    document.getElementById('saveBudget').addEventListener('click', saveBudget);
    document.getElementById('confirmDelete').addEventListener('click', deleteBudget);
    document.getElementById('monthFilter').addEventListener('change', function() {
        loadBudgetCategories();
        loadBudgetOverview();
    });

    function loadBudgetOverview() {
        const month = document.getElementById('monthFilter').value;
        const params = month ? `?month=${month}` : '';
        
        fetch(`api/budget.php?action=overview${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalBudget').textContent = 'R' + parseFloat(data.overview.total_budget || 0).toFixed(2);
                document.getElementById('remaining').textContent = 'R' + parseFloat(data.overview.remaining || 0).toFixed(2);
                document.getElementById('totalSpent').textContent = 'R' + parseFloat(data.overview.total_spent || 0).toFixed(2);
                document.getElementById('avgSpending').textContent = 'R' + parseFloat(data.overview.daily_average || 0).toFixed(2);
                
                displayBudgetProgress(data.overview);
            }
        })
        .catch(error => console.error('Error loading budget overview:', error));
    }

    function displayBudgetProgress(overview) {
        const container = document.getElementById('budgetProgressContainer');
        const totalBudget = parseFloat(overview.total_budget || 0);
        const totalSpent = parseFloat(overview.total_spent || 0);
        const percentage = totalBudget > 0 ? (totalSpent / totalBudget * 100) : 0;
        
        let progressClass = 'bg-success';
        if (percentage > 80) progressClass = 'bg-danger';
        else if (percentage > 60) progressClass = 'bg-warning';

        container.innerHTML = `
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold">Overall Budget Progress</span>
                        <span class="text-muted">${percentage.toFixed(1)}% used</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar ${progressClass}" role="progressbar" 
                             style="width: ${Math.min(percentage, 100)}%" 
                             aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100">
                            ${percentage.toFixed(1)}%
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <small class="text-muted">Spent: R${totalSpent.toFixed(2)}</small>
                        <small class="text-muted">Budget: R${totalBudget.toFixed(2)}</small>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="text-${percentage > 100 ? 'danger' : percentage > 80 ? 'warning' : 'success'}">
                        <i class="fas fa-${percentage > 100 ? 'exclamation-triangle' : percentage > 80 ? 'exclamation-circle' : 'check-circle'} fa-2x mb-2"></i>
                        <div class="fw-semibold">
                            ${percentage > 100 ? 'Over Budget!' : 
                              percentage > 80 ? 'Almost There' : 'On Track'}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function loadBudgetCategories() {
        const month = document.getElementById('monthFilter').value;
        const params = month ? `?month=${month}` : '';
        
        fetch(`api/budget.php${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayBudgetCategories(data.budgets);
            } else {
                document.getElementById('budgetCategoriesContainer').innerHTML = 
                    '<div class="text-center p-4"><i class="fas fa-exclamation-triangle text-warning fa-2x mb-3"></i><p class="text-muted">Error loading budgets</p></div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('budgetCategoriesContainer').innerHTML = 
                '<div class="text-center p-4"><i class="fas fa-exclamation-triangle text-danger fa-2x mb-3"></i><p class="text-muted">Failed to load budgets</p></div>';
        });
    }

    function displayBudgetCategories(budgets) {
        const container = document.getElementById('budgetCategoriesContainer');
        
        if (!budgets || budgets.length === 0) {
            container.innerHTML = `
                <div class="text-center p-5">
                    <i class="fas fa-piggy-bank text-muted fa-3x mb-3"></i>
                    <h5 class="text-muted">No budgets yet</h5>
                    <p class="text-muted">Create your first budget to start tracking your spending!</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#budgetModal">
                        <i class="fas fa-plus me-2"></i>Create Budget
                    </button>
                </div>
            `;
            return;
        }

        const budgetCards = budgets.map(budget => {
            const spent = parseFloat(budget.spent || 0);
            const budgetAmount = parseFloat(budget.budget_amount);
            const percentage = budgetAmount > 0 ? (spent / budgetAmount * 100) : 0;
            const remaining = budgetAmount - spent;
            
            let progressClass = 'bg-success';
            let statusIcon = 'fas fa-check-circle text-success';
            let statusText = 'On Track';
            
            if (percentage > 100) {
                progressClass = 'bg-danger';
                statusIcon = 'fas fa-exclamation-triangle text-danger';
                statusText = 'Over Budget';
            } else if (percentage > 80) {
                progressClass = 'bg-warning';
                statusIcon = 'fas fa-exclamation-circle text-warning';
                statusText = 'Nearly There';
            }

            return `
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="card-title fw-bold mb-1">${escapeHtml(budget.category_name)}</h6>
                                    <small class="text-muted text-uppercase">${budget.budget_period}</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" 
                                            data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="editBudget(${budget.id})">
                                            <i class="fas fa-edit me-2"></i>Edit</a></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="showDeleteModal(${budget.id})">
                                            <i class="fas fa-trash me-2"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Progress</span>
                                    <span class="fw-semibold">${percentage.toFixed(1)}%</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar ${progressClass}" role="progressbar" 
                                         style="width: ${Math.min(percentage, 100)}%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">R${spent.toFixed(2)} spent</small>
                                    <small class="text-muted">R${budgetAmount.toFixed(2)} budget</small>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="${statusIcon} me-2"></i>
                                    <small class="fw-semibold">${statusText}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold ${remaining >= 0 ? 'text-success' : 'text-danger'}">
                                        R${Math.abs(remaining).toFixed(2)} ${remaining >= 0 ? 'left' : 'over'}
                                    </div>
                                </div>
                            </div>
                            
                            ${budget.description ? `
                            <div class="mt-2 pt-2 border-top">
                                <small class="text-muted">${escapeHtml(budget.description)}</small>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = `<div class="row">${budgetCards}</div>`;
    }

    function loadSpendingChart() {
        fetch('api/budget.php?action=chart_data')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                createSpendingChart(data.chart_data);
            }
        })
        .catch(error => console.error('Error loading chart data:', error));
    }

    function createSpendingChart(chartData) {
        const ctx = document.getElementById('spendingChart').getContext('2d');
        
        if (budgetChart) {
            budgetChart.destroy();
        }
        
        budgetChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Daily Spending',
                    data: chartData.spending,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R' + value.toFixed(0);
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    function loadBudgetTips() {
        fetch('api/budget.php?action=tips')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.tips) {
                displayBudgetTips(data.tips);
            }
        })
        .catch(error => console.error('Error loading budget tips:', error));
    }

    function displayBudgetTips(tips) {
        const container = document.getElementById('budgetTips');
        
        if (!tips || tips.length === 0) {
            container.innerHTML = '<p class="text-muted">No specific tips available at the moment.</p>';
            return;
        }
        
        const tipsList = tips.map(tip => `
            <div class="d-flex align-items-start mb-3">
                <div class="flex-shrink-0">
                    <i class="${tip.icon} text-${tip.type} me-3"></i>
                </div>
                <div>
                    <h6 class="mb-1">${tip.title}</h6>
                    <p class="text-muted mb-0">${tip.message}</p>
                </div>
            </div>
        `).join('');
        
        container.innerHTML = tipsList;
    }

    function populateMonthFilter() {
        const select = document.getElementById('monthFilter');
        const currentDate = new Date();
        
        for (let i = 0; i < 12; i++) {
            const date = new Date(currentDate.getFullYear(), currentDate.getMonth() - i, 1);
            const value = date.toISOString().substring(0, 7); // YYYY-MM format
            const text = date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
            
            const option = document.createElement('option');
            option.value = value;
            option.textContent = text;
            if (i === 0) option.selected = true;
            
            select.appendChild(option);
        }
    }

    function saveBudget() {
        const form = document.getElementById('budgetForm');
        const formData = new FormData(form);
        const budgetId = document.getElementById('budgetId').value;
        
        const data = {
            category_name: formData.get('category_name'),
            budget_amount: parseFloat(formData.get('budget_amount')),
            budget_period: formData.get('budget_period'),
            description: formData.get('description'),
            enable_alerts: formData.get('enable_alerts') ? 1 : 0
        };
        
        if (budgetId) {
            data.id = budgetId;
        }

        const url = budgetId ? 'api/budget.php' : 'api/budget.php';
        const method = 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': formData.get('csrf_token')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                budgetModal.hide();
                form.reset();
                document.getElementById('budgetId').value = '';
                loadBudgetCategories();
                loadBudgetOverview();
                showAlert(budgetId ? 'Budget updated successfully!' : 'Budget created successfully!', 'success');
            } else {
                showAlert(data.message || 'Failed to save budget', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while saving the budget', 'danger');
        });
    }

    // Global functions
    window.editBudget = function(budgetId) {
        fetch(`api/budget.php?id=${budgetId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.budget) {
                const budget = data.budget;
                document.getElementById('budgetModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Budget';
                document.getElementById('budgetId').value = budget.id;
                document.getElementById('categoryName').value = budget.category_name;
                document.getElementById('budgetAmount').value = budget.budget_amount;
                document.getElementById('budgetPeriod').value = budget.budget_period;
                document.getElementById('budgetDescription').value = budget.description || '';
                document.getElementById('enableAlerts').checked = budget.enable_alerts;
                
                budgetModal.show();
            }
        })
        .catch(error => console.error('Error loading budget details:', error));
    };

    window.showDeleteModal = function(budgetId) {
        currentBudgetId = budgetId;
        deleteModal.show();
    };

    function deleteBudget() {
        if (!currentBudgetId) return;
        
        fetch('api/budget.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
            },
            body: JSON.stringify({ id: currentBudgetId })
        })
        .then(response => response.json())
        .then(data => {
            deleteModal.hide();
            if (data.success) {
                loadBudgetCategories();
                loadBudgetOverview();
                showAlert('Budget deleted successfully!', 'success');
            } else {
                showAlert(data.message || 'Failed to delete budget', 'danger');
            }
            currentBudgetId = null;
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while deleting the budget', 'danger');
            currentBudgetId = null;
        });
    }

    // Reset modal when closed
    document.getElementById('budgetModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('budgetModalTitle').innerHTML = '<i class="fas fa-plus me-2"></i>Create New Budget';
        document.getElementById('budgetForm').reset();
        document.getElementById('budgetId').value = '';
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showAlert(message, type) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>