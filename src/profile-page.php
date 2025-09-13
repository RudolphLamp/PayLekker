<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - PayLekker</title>
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
                <a href="profile-page.php" class="nav-link active">
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
                <h1><i class="bi bi-person me-3"></i>My Profile</h1>
                <p class="text-muted">Manage your account information and security settings</p>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <!-- Profile Card -->
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="profile-avatar mb-3">
                                <i class="bi bi-person-circle" style="font-size: 5rem; color: var(--primary-color, #2E8B57);"></i>
                            </div>
                            <h4 id="profileName" class="card-title">Loading...</h4>
                            <p id="profileEmail" class="card-text text-muted">Loading...</p>
                            <div class="d-flex justify-content-center gap-2 mt-3">
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Verified Account
                                </span>
                                <span class="badge bg-info">
                                    <i class="bi bi-shield-check me-1"></i>Secure
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Account Summary -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6><i class="bi bi-wallet me-2"></i>Account Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Current Balance:</span>
                                <strong id="currentBalance" class="text-success">R 0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Account Status:</span>
                                <strong class="text-success">Active</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Member Since:</span>
                                <strong id="memberSince">Loading...</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Last Updated:</span>
                                <strong id="lastUpdated">Loading...</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <!-- Profile Information -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="bi bi-person-lines-fill me-2"></i>Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <form id="profileForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="firstName" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="firstName" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="lastName" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="lastName" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" readonly>
                                    <div class="form-text">Email cannot be changed for security reasons</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone">
                                    <div class="form-text">Used for transfers and notifications</div>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-outline-secondary me-md-2" onclick="resetForm()">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5><i class="bi bi-shield-lock me-2"></i>Security Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="bi bi-key me-2"></i>Password</h6>
                                    <p class="text-muted">Keep your account secure with a strong password</p>
                                    <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                        <i class="bi bi-pencil-square me-2"></i>Change Password
                                    </button>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6><i class="bi bi-phone-vibrate me-2"></i>Two-Factor Authentication</h6>
                                    <p class="text-muted">Add an extra layer of security to your account</p>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="twoFactorAuth" disabled>
                                        <label class="form-check-label" for="twoFactorAuth">
                                            Enable 2FA <span class="badge bg-warning">Coming Soon</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preferences -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5><i class="bi bi-gear me-2"></i>Preferences</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="bi bi-bell me-2"></i>Notifications</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                        <label class="form-check-label" for="emailNotifications">
                                            Email notifications
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="smsNotifications" checked>
                                        <label class="form-check-label" for="smsNotifications">
                                            SMS notifications
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="transactionAlerts" checked>
                                        <label class="form-check-label" for="transactionAlerts">
                                            Transaction alerts
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6><i class="bi bi-palette me-2"></i>Display</h6>
                                    <div class="mb-3">
                                        <label for="language" class="form-label">Language</label>
                                        <select class="form-select" id="language">
                                            <option value="en" selected>English</option>
                                            <option value="af">Afrikaans</option>
                                            <option value="zu">Zulu</option>
                                            <option value="xh">Xhosa</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="currency" class="form-label">Currency</label>
                                        <select class="form-select" id="currency">
                                            <option value="ZAR" selected>South African Rand (ZAR)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-success" onclick="savePreferences()">
                                    <i class="bi bi-check-circle me-2"></i>Save Preferences
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="card mt-4 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5><i class="bi bi-exclamation-triangle me-2"></i>Danger Zone</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger">
                                <strong>Warning:</strong> These actions cannot be undone. Please be careful.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Download Account Data</h6>
                                    <p class="text-muted">Download a copy of all your account data</p>
                                    <button class="btn btn-outline-info" onclick="downloadData()">
                                        <i class="bi bi-download me-2"></i>Download Data
                                    </button>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6>Delete Account</h6>
                                    <p class="text-muted">Permanently delete your PayLekker account</p>
                                    <button class="btn btn-outline-danger" onclick="confirmDeleteAccount()">
                                        <i class="bi bi-trash me-2"></i>Delete Account
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="changePasswordModalLabel">
                        <i class="bi bi-key me-2"></i>Change Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Current Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="currentPassword" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('currentPassword', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="newPassword" minlength="6" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('newPassword', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">Password must be at least 6 characters long</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmPassword" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('confirmPassword', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="changePassword()">
                        <i class="bi bi-check-circle me-2"></i>Change Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/common.js"></script>
    <script>
        let currentUser = null;
        
        // Update user info in UI
        function updateUserInfo(user) {
            currentUser = user;
            
            const userNameElement = document.getElementById('userName');
            if (userNameElement) {
                userNameElement.textContent = user.first_name + ' ' + user.last_name;
            }
            
            // Update balance display with proper South African formatting
            const balanceElement = document.getElementById('currentBalance');
            if (balanceElement && user.balance !== undefined) {
                balanceElement.textContent = formatCurrency(user.balance);
            }
            
            // Also populate the profile form
            populateProfile(user);
            
            console.log('User info updated on profile page:', user);
        }
        
        // Populate profile form
        function populateProfile(user) {
            // Profile card
            document.getElementById('profileName').textContent = user.first_name + ' ' + user.last_name;
            document.getElementById('profileEmail').textContent = user.email;
            document.getElementById('currentBalance').textContent = `R ${parseFloat(user.account_balance).toLocaleString('en-ZA', {minimumFractionDigits: 2})}`;
            document.getElementById('memberSince').textContent = new Date(user.created_at).toLocaleDateString('en-ZA');
            document.getElementById('lastUpdated').textContent = new Date(user.updated_at || user.created_at).toLocaleDateString('en-ZA');
            
            // Profile form
            document.getElementById('firstName').value = user.first_name;
            document.getElementById('lastName').value = user.last_name;
            document.getElementById('email').value = user.email;
            document.getElementById('phone').value = user.phone || '';
        }
        
        // Reset form to original values
        function resetForm() {
            if (currentUser) {
                populateProfile(currentUser);
            }
        }
        
        // Update profile
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                first_name: document.getElementById('firstName').value.trim(),
                last_name: document.getElementById('lastName').value.trim(),
                phone: document.getElementById('phone').value.trim()
            };
            
            const token = sessionStorage.getItem('auth_token');
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Updating...';
            submitBtn.disabled = true;
            
            try {
                // Note: This would need a profile update endpoint in the API
                // For now, show a demo message
                showAlert('Profile update feature coming soon! Changes saved locally for demo.', 'info');
                
                // Update current user object
                currentUser.first_name = formData.first_name;
                currentUser.last_name = formData.last_name;
                currentUser.phone = formData.phone;
                
                // Update UI
                updateUserInfo(currentUser);
                document.getElementById('profileName').textContent = formData.first_name + ' ' + formData.last_name;
                
            } catch (error) {
                console.error('Profile update error:', error);
                showAlert('Failed to update profile. Please try again.', 'danger');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
        
        // Toggle password visibility
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
        
        // Change password
        function changePassword() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword !== confirmPassword) {
                showAlert('New passwords do not match!', 'danger');
                return;
            }
            
            if (newPassword.length < 6) {
                showAlert('Password must be at least 6 characters long!', 'danger');
                return;
            }
            
            // For demo purposes
            showAlert('Password change feature coming soon! This would securely update your password.', 'info');
            bootstrap.Modal.getInstance(document.getElementById('changePasswordModal')).hide();
            document.getElementById('changePasswordForm').reset();
        }
        
        // Save preferences
        function savePreferences() {
            const preferences = {
                email_notifications: document.getElementById('emailNotifications').checked,
                sms_notifications: document.getElementById('smsNotifications').checked,
                transaction_alerts: document.getElementById('transactionAlerts').checked,
                language: document.getElementById('language').value,
                currency: document.getElementById('currency').value
            };
            
            showAlert('Preferences saved successfully!', 'success');
            console.log('Saved preferences:', preferences);
        }
        
        // Download account data
        function downloadData() {
            showAlert('Account data download feature coming soon! This would generate a comprehensive report of your account.', 'info');
        }
        
        // Confirm account deletion
        function confirmDeleteAccount() {
            if (confirm('Are you sure you want to permanently delete your account? This action cannot be undone.')) {
                if (confirm('This will permanently delete all your data, including transaction history and settings. Type "DELETE" to confirm.')) {
                    const userInput = prompt('Please type DELETE to confirm account deletion:');
                    if (userInput === 'DELETE') {
                        showAlert('Account deletion feature is disabled in demo mode. In production, this would permanently delete your account.', 'warning');
                    } else {
                        showAlert('Account deletion cancelled.', 'info');
                    }
                }
            }
        }
        
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
            }, 7000);
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
            }
        });
    </script>
</body>
</html>