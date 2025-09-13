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
                <h2 class="text-gradient">Transfer Money</h2>
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">
            <!-- Transfer Form -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient text-white">
                    <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Send Money</h5>
                </div>
                <div class="card-body p-4">
                    <div id="alert-container"></div>
                    
                    <form id="transferForm">
                        <?php echo csrf_token_input(); ?>
                        
                        <!-- Recipient Selection -->
                        <div class="mb-4">
                            <label for="recipient" class="form-label fw-semibold">
                                <i class="fas fa-user me-2 text-primary"></i>Send to
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-at text-muted"></i>
                                </span>
                                <input type="email" class="form-control" id="recipient" name="recipient" 
                                       placeholder="Enter recipient's email address" required>
                                <button type="button" class="btn btn-outline-secondary" id="checkRecipient">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="form-text">Enter the email address of the person you want to send money to</div>
                            <div id="recipient-info" class="mt-2"></div>
                        </div>

                        <!-- Amount Input -->
                        <div class="mb-4">
                            <label for="amount" class="form-label fw-semibold">
                                <i class="fas fa-coins me-2 text-success"></i>Amount
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-success text-white fw-bold">R</span>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       placeholder="0.00" min="1" max="50000" step="0.01" required>
                            </div>
                            <div class="form-text">Minimum: R1.00 | Maximum: R50,000.00</div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">
                                <i class="fas fa-comment me-2 text-info"></i>Description (Optional)
                            </label>
                            <input type="text" class="form-control" id="description" name="description" 
                                   placeholder="What's this transfer for?" maxlength="100">
                            <div class="form-text">Add a note for this transfer</div>
                        </div>

                        <!-- Transfer Summary -->
                        <div class="card bg-light mb-4" id="transfer-summary" style="display: none;">
                            <div class="card-body">
                                <h6 class="fw-semibold mb-3">Transfer Summary</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">To:</small>
                                        <div id="summary-recipient" class="fw-semibold"></div>
                                    </div>
                                    <div class="col-6 text-end">
                                        <small class="text-muted">Amount:</small>
                                        <div id="summary-amount" class="fw-semibold text-success"></div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">Description:</small>
                                    <div id="summary-description" class="fw-semibold"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Send Money
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Recipients -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-muted"><i class="fas fa-clock me-2"></i>Recent Recipients</h6>
                </div>
                <div class="card-body">
                    <div id="recent-recipients" class="d-flex flex-wrap gap-2">
                        <!-- Recent recipients will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-shield-alt me-2 text-warning"></i>Confirm Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center mb-3">Are you sure you want to send this money?</p>
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">To:</small>
                                <div id="confirm-recipient" class="fw-semibold"></div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Amount:</small>
                                <div id="confirm-amount" class="fw-semibold text-success"></div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Description:</small>
                            <div id="confirm-description" class="fw-semibold"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <small class="text-danger">This action cannot be undone.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmTransfer">
                    <i class="fas fa-check me-2"></i>Yes, Send Money
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const transferForm = document.getElementById('transferForm');
    const recipientInput = document.getElementById('recipient');
    const amountInput = document.getElementById('amount');
    const descriptionInput = document.getElementById('description');
    const checkRecipientBtn = document.getElementById('checkRecipient');
    const transferSummary = document.getElementById('transfer-summary');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    let recipientValid = false;

    // Check recipient on input change
    recipientInput.addEventListener('blur', checkRecipient);
    checkRecipientBtn.addEventListener('click', checkRecipient);

    // Update transfer summary
    [recipientInput, amountInput, descriptionInput].forEach(input => {
        input.addEventListener('input', updateTransferSummary);
    });

    function checkRecipient() {
        const email = recipientInput.value.trim();
        const recipientInfo = document.getElementById('recipient-info');
        
        if (!email) {
            recipientInfo.innerHTML = '';
            recipientValid = false;
            return;
        }

        if (!isValidEmail(email)) {
            recipientInfo.innerHTML = '<div class="alert alert-warning alert-sm">Please enter a valid email address</div>';
            recipientValid = false;
            return;
        }

        // Check if recipient exists
        fetch('api/users.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
            },
            body: JSON.stringify({
                action: 'check_user',
                email: email
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.user) {
                recipientInfo.innerHTML = `
                    <div class="alert alert-success alert-sm">
                        <i class="fas fa-check-circle me-2"></i>
                        Recipient found: ${data.user.name}
                    </div>
                `;
                recipientValid = true;
                updateTransferSummary();
            } else {
                recipientInfo.innerHTML = '<div class="alert alert-danger alert-sm"><i class="fas fa-times-circle me-2"></i>Recipient not found</div>';
                recipientValid = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            recipientInfo.innerHTML = '<div class="alert alert-danger alert-sm">Error checking recipient</div>';
            recipientValid = false;
        });
    }

    function updateTransferSummary() {
        const recipient = recipientInput.value.trim();
        const amount = parseFloat(amountInput.value);
        const description = descriptionInput.value.trim();

        if (recipientValid && amount > 0) {
            document.getElementById('summary-recipient').textContent = recipient;
            document.getElementById('summary-amount').textContent = 'R' + amount.toFixed(2);
            document.getElementById('summary-description').textContent = description || 'No description';
            transferSummary.style.display = 'block';
        } else {
            transferSummary.style.display = 'none';
        }
    }

    // Handle form submission
    transferForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!recipientValid) {
            showAlert('Please enter a valid recipient email address', 'warning');
            return;
        }

        const amount = parseFloat(amountInput.value);
        if (amount < 1 || amount > 50000) {
            showAlert('Amount must be between R1.00 and R50,000.00', 'warning');
            return;
        }

        // Show confirmation modal
        const recipient = recipientInput.value.trim();
        const description = descriptionInput.value.trim();
        
        document.getElementById('confirm-recipient').textContent = recipient;
        document.getElementById('confirm-amount').textContent = 'R' + amount.toFixed(2);
        document.getElementById('confirm-description').textContent = description || 'No description';
        
        confirmModal.show();
    });

    // Handle transfer confirmation
    document.getElementById('confirmTransfer').addEventListener('click', function() {
        const formData = new FormData(transferForm);
        const data = {
            recipient_email: formData.get('recipient'),
            amount: parseFloat(formData.get('amount')),
            description: formData.get('description') || 'Money transfer'
        };

        fetch('api/transfers.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': formData.get('csrf_token')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            confirmModal.hide();
            if (data.success) {
                showAlert('Transfer completed successfully!', 'success');
                transferForm.reset();
                transferSummary.style.display = 'none';
                document.getElementById('recipient-info').innerHTML = '';
                recipientValid = false;
                
                // Redirect to dashboard after 2 seconds
                setTimeout(() => {
                    window.location.href = 'dashboard.php';
                }, 2000);
            } else {
                showAlert(data.message || 'Transfer failed. Please try again.', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            confirmModal.hide();
            showAlert('An error occurred. Please try again.', 'danger');
        });
    });

    // Load recent recipients
    loadRecentRecipients();

    function loadRecentRecipients() {
        fetch('api/transfers.php?action=recent_recipients', {
            headers: {
                'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.recipients) {
                const container = document.getElementById('recent-recipients');
                if (data.recipients.length === 0) {
                    container.innerHTML = '<small class="text-muted">No recent recipients</small>';
                } else {
                    container.innerHTML = data.recipients.map(recipient => `
                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                onclick="selectRecipient('${recipient.email}', '${recipient.name}')">
                            <i class="fas fa-user me-1"></i>${recipient.name}
                        </button>
                    `).join('');
                }
            }
        })
        .catch(error => console.error('Error loading recent recipients:', error));
    }

    // Global function to select recipient
    window.selectRecipient = function(email, name) {
        recipientInput.value = email;
        document.getElementById('recipient-info').innerHTML = `
            <div class="alert alert-success alert-sm">
                <i class="fas fa-check-circle me-2"></i>
                Recipient selected: ${name}
            </div>
        `;
        recipientValid = true;
        updateTransferSummary();
    };

    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function showAlert(message, type) {
        const alertContainer = document.getElementById('alert-container');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>