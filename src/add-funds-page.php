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
    <style>
        /* Payment Methods Styling */
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .payment-method {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .payment-method:has(input:checked) {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
        }
        
        .payment-method-header {
            padding: 1rem;
            cursor: pointer;
        }
        
        .payment-method-label {
            cursor: pointer;
            margin: 0;
            width: 100%;
        }
        
        .payment-method input[type="radio"] {
            display: none;
        }
        
        .payment-method-details {
            padding: 0 1rem 1rem 1rem;
            border-top: 1px solid #e9ecef;
            margin-top: 1rem;
        }
        
        .payment-method:not(:has(input:checked)) .payment-method-details {
            display: none !important;
        }
        
        .payment-method:has(input:checked) .bi-chevron-down {
            transform: rotate(180deg);
        }
        
        .bi-chevron-down {
            transition: transform 0.3s ease;
        }
        
        /* Card number formatting */
        #card_number {
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }
        
        /* Network and bank logos styling */
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        /* Demo alerts styling */
        .alert {
            border-radius: 8px;
        }
        
        .alert-info {
            background-color: #e7f3ff;
            border-color: #b8daff;
            color: #0c5460;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
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

                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <label class="form-label">Choose Payment Method</label>
                            
                            <!-- Payment Method Options -->
                            <div class="payment-methods">
                                <!-- Credit/Debit Card -->
                                <div class="payment-method" data-method="card">
                                    <div class="payment-method-header">
                                        <input type="radio" name="payment_method" id="method_card" value="card" checked>
                                        <label for="method_card" class="payment-method-label">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-credit-card fs-4 me-3 text-primary"></i>
                                                <div>
                                                    <div class="fw-medium">Credit/Debit Card</div>
                                                    <small class="text-muted">Visa, Mastercard, American Express</small>
                                                </div>
                                                <i class="bi bi-chevron-down ms-auto"></i>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="payment-method-details" id="card_details">
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label for="card_number" class="form-label">Card Number</label>
                                                <input type="text" class="form-control" id="card_number" 
                                                       placeholder="1234 5678 9012 3456" maxlength="19">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="card_expiry" class="form-label">Expiry Date</label>
                                                <input type="text" class="form-control" id="card_expiry" 
                                                       placeholder="MM/YY" maxlength="5">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="card_cvv" class="form-label">CVV</label>
                                                <input type="text" class="form-control" id="card_cvv" 
                                                       placeholder="123" maxlength="4">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label for="card_name" class="form-label">Cardholder Name</label>
                                                <input type="text" class="form-control" id="card_name" 
                                                       placeholder="John Doe">
                                            </div>
                                        </div>
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Demo:</strong> Use any card details - all transactions are simulated
                                        </div>
                                    </div>
                                </div>

                                <!-- Instant EFT -->
                                <div class="payment-method" data-method="eft">
                                    <div class="payment-method-header">
                                        <input type="radio" name="payment_method" id="method_eft" value="eft">
                                        <label for="method_eft" class="payment-method-label">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-bank fs-4 me-3 text-success"></i>
                                                <div>
                                                    <div class="fw-medium">Instant EFT</div>
                                                    <small class="text-muted">FNB, ABSA, Standard Bank, Nedbank</small>
                                                </div>
                                                <i class="bi bi-chevron-down ms-auto"></i>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="payment-method-details" id="eft_details" style="display: none;">
                                        <div class="mb-3">
                                            <label for="bank_select" class="form-label">Select Your Bank</label>
                                            <select class="form-select" id="bank_select">
                                                <option value="">Choose your bank...</option>
                                                <option value="fnb">FNB (First National Bank)</option>
                                                <option value="absa">ABSA Bank</option>
                                                <option value="standard">Standard Bank</option>
                                                <option value="nedbank">Nedbank</option>
                                                <option value="capitec">Capitec Bank</option>
                                                <option value="investec">Investec</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="account_number" class="form-label">Account Number</label>
                                            <input type="text" class="form-control" id="account_number" 
                                                   placeholder="1234567890">
                                        </div>
                                        <div class="mb-3">
                                            <label for="account_holder" class="form-label">Account Holder Name</label>
                                            <input type="text" class="form-control" id="account_holder" 
                                                   placeholder="John Doe">
                                        </div>
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Demo:</strong> Enter any bank details - all EFT transfers are simulated
                                        </div>
                                    </div>
                                </div>

                                <!-- Airtime Transfer -->
                                <div class="payment-method" data-method="airtime">
                                    <div class="payment-method-header">
                                        <input type="radio" name="payment_method" id="method_airtime" value="airtime">
                                        <label for="method_airtime" class="payment-method-label">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-phone fs-4 me-3 text-warning"></i>
                                                <div>
                                                    <div class="fw-medium">Airtime Transfer</div>
                                                    <small class="text-muted">MTN, Vodacom, Cell C, Telkom</small>
                                                </div>
                                                <i class="bi bi-chevron-down ms-auto"></i>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="payment-method-details" id="airtime_details" style="display: none;">
                                        <div class="mb-3">
                                            <label for="network_select" class="form-label">Mobile Network</label>
                                            <select class="form-select" id="network_select">
                                                <option value="">Choose your network...</option>
                                                <option value="mtn">MTN</option>
                                                <option value="vodacom">Vodacom</option>
                                                <option value="cellc">Cell C</option>
                                                <option value="telkom">Telkom Mobile</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="phone_number" class="form-label">Mobile Number</label>
                                            <input type="tel" class="form-control" id="phone_number" 
                                                   placeholder="0823456789">
                                        </div>
                                        <div class="mb-3">
                                            <label for="airtime_pin" class="form-label">Airtime Transfer PIN</label>
                                            <input type="password" class="form-control" id="airtime_pin" 
                                                   placeholder="Enter your PIN">
                                        </div>
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            <strong>Demo:</strong> Airtime conversion rate: R1 airtime = R0.80 wallet credit
                                        </div>
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Demo:</strong> Use any mobile number and PIN - all transfers are simulated
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

        // Load current balance from user session data
        async function loadCurrentBalance() {
            try {
                // First try to get from session storage
                const userData = sessionStorage.getItem('user_data');
                if (userData) {
                    const user = JSON.parse(userData);
                    const balance = parseFloat(user.account_balance || 0);
                    document.getElementById('currentBalance').textContent = formatCurrency(balance);
                    return;
                }

                // Fallback to API call
                const token = sessionStorage.getItem('auth_token');
                if (!token) {
                    window.location.href = 'auth/login.php';
                    return;
                }

                const response = await fetch(API_BASE + 'profile.php', {
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success && result.data && result.data.user) {
                        const balance = parseFloat(result.data.user.account_balance || 0);
                        document.getElementById('currentBalance').textContent = formatCurrency(balance);
                        
                        // Update session storage
                        sessionStorage.setItem('user_data', JSON.stringify(result.data.user));
                    } else {
                        document.getElementById('currentBalance').textContent = 'R 0.00';
                    }
                } else {
                    throw new Error('Failed to fetch balance');
                }
            } catch (error) {
                console.error('Error loading balance:', error);
                document.getElementById('currentBalance').textContent = 'R 0.00';
            }
        }

        // Add funds function - now calls API to update database
        async function addFunds(event) {
            event.preventDefault();
            
            const amount = parseFloat(document.getElementById('amount').value);
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const btn = document.getElementById('addFundsBtn');
            
            if (amount < 10 || amount > 10000) {
                showAlert('Please enter an amount between R10.00 and R10,000.00', 'warning');
                return;
            }

            // Validate payment method details
            if (!validatePaymentMethod(paymentMethod)) {
                return;
            }

            // Calculate final amount (airtime has conversion rate)
            let finalAmount = amount;
            let paymentDetails = '';
            
            if (paymentMethod === 'card') {
                const cardNumber = document.getElementById('card_number').value;
                paymentDetails = `Card ending in ${cardNumber.slice(-4)}`;
            } else if (paymentMethod === 'eft') {
                const bank = document.getElementById('bank_select').options[document.getElementById('bank_select').selectedIndex].text;
                paymentDetails = `EFT from ${bank}`;
            } else if (paymentMethod === 'airtime') {
                const network = document.getElementById('network_select').options[document.getElementById('network_select').selectedIndex].text;
                finalAmount = amount * 0.8; // Airtime conversion rate
                paymentDetails = `Airtime transfer from ${network} (R${amount} airtime → R${finalAmount.toFixed(2)} credit)`;
            }

            // Disable button and show loading
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Processing ${paymentMethod.toUpperCase()}...`;

            const token = sessionStorage.getItem('auth_token');
            if (!token) {
                showAlert('Please login again.', 'danger');
                window.location.href = 'auth/login.php';
                return;
            }

            try {
                // Simulate processing time for different payment methods
                let processingTime = paymentMethod === 'card' ? 2000 : paymentMethod === 'eft' ? 3000 : 2500;
                await new Promise(resolve => setTimeout(resolve, processingTime));

                // Call the add-funds API endpoint
                const response = await fetch(API_BASE + 'add-funds.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: finalAmount,
                        payment_method: paymentMethod,
                        payment_details: paymentDetails
                    })
                });

                console.log('Add funds response status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Add funds error response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const responseText = await response.text();
                console.log('Add funds response text:', responseText);
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Raw response:', responseText);
                    throw new Error('Invalid response format from server');
                }
                
                if (result.success) {
                    // Update session storage with new user data
                    const updatedUser = {
                        ...JSON.parse(sessionStorage.getItem('user_data') || '{}'),
                        account_balance: result.data.new_balance
                    };
                    sessionStorage.setItem('user_data', JSON.stringify(updatedUser));
                    
                    // Show success modal
                    document.getElementById('successMessage').innerHTML = 
                        `<strong>${formatCurrency(finalAmount)}</strong> has been added to your account via ${paymentDetails}.<br>New balance: <strong>${formatCurrency(result.data.new_balance)}</strong>`;
                    
                    const modal = new bootstrap.Modal(document.getElementById('successModal'));
                    modal.show();

                    // Reset form
                    document.getElementById('addFundsForm').reset();
                    // Reset to default payment method
                    document.getElementById('method_card').checked = true;
                    updatePaymentMethodDisplay();
                    
                    // Update balance display
                    document.getElementById('currentBalance').textContent = formatCurrency(result.data.new_balance);
                    
                    // Add to recent funds display
                    addToRecentFunds(finalAmount, paymentMethod, paymentDetails);
                    
                    // Show success alert
                    showAlert(`Successfully added ${formatCurrency(finalAmount)} to your account via ${paymentMethod.toUpperCase()}!`, 'success');
                    
                } else {
                    throw new Error(result.error || 'Failed to add funds');
                }
                
            } catch (error) {
                console.error('Error adding funds:', error);
                showAlert('An error occurred while processing payment. Please try again. Error: ' + error.message, 'danger');
            } finally {
                // Re-enable button
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add Funds';
            }
        }

        // Validate payment method details
        function validatePaymentMethod(method) {
            if (method === 'card') {
                const cardNumber = document.getElementById('card_number').value;
                const cardExpiry = document.getElementById('card_expiry').value;
                const cardCvv = document.getElementById('card_cvv').value;
                const cardName = document.getElementById('card_name').value;
                
                if (!cardNumber || cardNumber.replace(/\s/g, '').length < 13) {
                    showAlert('Please enter a valid card number', 'warning');
                    return false;
                }
                if (!cardExpiry || cardExpiry.length < 5) {
                    showAlert('Please enter a valid expiry date', 'warning');
                    return false;
                }
                if (!cardCvv || cardCvv.length < 3) {
                    showAlert('Please enter a valid CVV', 'warning');
                    return false;
                }
                if (!cardName || cardName.length < 2) {
                    showAlert('Please enter cardholder name', 'warning');
                    return false;
                }
            } else if (method === 'eft') {
                const bank = document.getElementById('bank_select').value;
                const accountNumber = document.getElementById('account_number').value;
                const accountHolder = document.getElementById('account_holder').value;
                
                if (!bank) {
                    showAlert('Please select your bank', 'warning');
                    return false;
                }
                if (!accountNumber || accountNumber.length < 8) {
                    showAlert('Please enter a valid account number', 'warning');
                    return false;
                }
                if (!accountHolder || accountHolder.length < 2) {
                    showAlert('Please enter account holder name', 'warning');
                    return false;
                }
            } else if (method === 'airtime') {
                const network = document.getElementById('network_select').value;
                const phoneNumber = document.getElementById('phone_number').value;
                const pin = document.getElementById('airtime_pin').value;
                
                if (!network) {
                    showAlert('Please select your mobile network', 'warning');
                    return false;
                }
                if (!phoneNumber || phoneNumber.length < 10) {
                    showAlert('Please enter a valid mobile number', 'warning');
                    return false;
                }
                if (!pin || pin.length < 4) {
                    showAlert('Please enter your airtime transfer PIN', 'warning');
                    return false;
                }
            }
            return true;
        }

        // Add to recent funds display
        function addToRecentFunds(amount, method, details) {
            const recentFunds = document.getElementById('recentFunds');
            const now = new Date();
            
            let methodIcon = 'bi-plus-circle';
            let methodClass = 'received';
            
            if (method === 'card') {
                methodIcon = 'bi-credit-card';
            } else if (method === 'eft') {
                methodIcon = 'bi-bank';
            } else if (method === 'airtime') {
                methodIcon = 'bi-phone';
            }
            
            // Create new transaction item
            const fundItem = document.createElement('div');
            fundItem.className = 'transaction-item';
            fundItem.innerHTML = `
                <div class="transaction-icon ${methodClass}">
                    <i class="${methodIcon}"></i>
                </div>
                <div class="transaction-details">
                    <div class="transaction-name">Fund Addition - ${method.toUpperCase()}</div>
                    <div class="transaction-description">${details} - ${now.toLocaleString()}</div>
                </div>
                <div class="transaction-amount positive">+${formatCurrency(amount)}</div>
            `;
            
            // Replace empty state or prepend to existing
            if (recentFunds.querySelector('.text-muted')) {
                recentFunds.innerHTML = '';
            }
            recentFunds.insertBefore(fundItem, recentFunds.firstChild);
            
            // Limit to 5 recent transactions
            const items = recentFunds.querySelectorAll('.transaction-item');
            if (items.length > 5) {
                recentFunds.removeChild(items[items.length - 1]);
            }
        }

        // Payment method handling
        function updatePaymentMethodDisplay() {
            const methods = document.querySelectorAll('.payment-method');
            methods.forEach(method => {
                const radio = method.querySelector('input[type="radio"]');
                const details = method.querySelector('.payment-method-details');
                
                if (radio.checked) {
                    details.style.display = 'block';
                } else {
                    details.style.display = 'none';
                }
            });
        }

        // Input formatting functions
        function formatCardNumber(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            input.value = value;
        }

        function formatExpiry(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            input.value = value;
        }

        function formatPhoneNumber(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            input.value = value;
        }

        // Update user info in UI
        function updateUserInfo() {
            const userData = sessionStorage.getItem('user_data');
            if (userData) {
                try {
                    const user = JSON.parse(userData);
                    const userNameElement = document.getElementById('userName');
                    const userEmailElement = document.getElementById('userEmail');
                    
                    if (userNameElement) {
                        userNameElement.textContent = `${user.first_name} ${user.last_name}`;
                    }
                    if (userEmailElement) {
                        userEmailElement.textContent = user.email;
                    }
                } catch (e) {
                    console.error('Error parsing user data:', e);
                }
            }
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

        // Setup sidebar functionality
        function setupSidebar() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (sidebarToggle && sidebar && overlay) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('show');
                });
                
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('show');
                });
            }
        }

        // Logout function
        async function logout() {
            if (confirm('Are you sure you want to logout?')) {
                sessionStorage.removeItem('auth_token');
                sessionStorage.removeItem('user_data');
                window.location.href = 'auth/login.php';
            }
        }

        // Format currency helper
        function formatCurrency(amount) {
            return 'R ' + parseFloat(amount).toLocaleString('en-ZA', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Show alert helper
        function showAlert(message, type) {
            // Create alert element
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1060; max-width: 400px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', async function() {
            const isAuthenticated = await checkAuth();
            if (isAuthenticated) {
                updateUserInfo();
                loadCurrentBalance();
                setupSidebar();
                
                // Initialize payment methods
                initializePaymentMethods();
            }
        });

        function initializePaymentMethods() {
            // Payment method radio button listeners
            document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
                radio.addEventListener('change', updatePaymentMethodDisplay);
            });

            // Card number formatting
            document.getElementById('card_number').addEventListener('input', function() {
                formatCardNumber(this);
            });

            // Card expiry formatting
            document.getElementById('card_expiry').addEventListener('input', function() {
                formatExpiry(this);
            });

            // CVV formatting (numbers only)
            document.getElementById('card_cvv').addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
            });

            // Account number formatting (numbers only)
            document.getElementById('account_number').addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
            });

            // Phone number formatting
            document.getElementById('phone_number').addEventListener('input', function() {
                formatPhoneNumber(this);
            });

            // PIN formatting (numbers only, max 6 digits)
            document.getElementById('airtime_pin').addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').substring(0, 6);
            });

            // Initial display update
            updatePaymentMethodDisplay();
        }
    </script>
</body>
</html>