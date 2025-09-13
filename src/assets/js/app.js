/**
 * PayLekker App JavaScript
 * Core functionality for the PayLekker application
 */

// Global app object
window.PayLekker = {
    apiBase: 'https://pay.sewdani.co.za/api/',
    currentUser: null,
    token: null,
    
    // Initialize the app
    init() {
        this.setupCSRF();
        this.setupFormValidation();
        this.setupTooltips();
        this.setupAutoLogout();
        this.loadUserData();
        
        // Enable tooltips everywhere
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },
    
    // Setup CSRF token for AJAX requests
    setupCSRF() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            this.csrfToken = token.getAttribute('content');
        }
    },
    
    // Setup form validation
    setupFormValidation() {
        // Custom validation styles
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
        
        // Real-time validation for important fields
        this.setupRealTimeValidation();
    },
    
    // Setup real-time validation
    setupRealTimeValidation() {
        // Email validation
        const emailInputs = document.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            input.addEventListener('blur', function() {
                const isValid = this.checkValidity();
                this.classList.toggle('is-valid', isValid);
                this.classList.toggle('is-invalid', !isValid);
            });
        });
        
        // Amount validation
        const amountInputs = document.querySelectorAll('input[name="amount"]');
        amountInputs.forEach(input => {
            input.addEventListener('input', function() {
                const value = parseFloat(this.value);
                const isValid = !isNaN(value) && value > 0 && value <= 10000;
                this.classList.toggle('is-valid', isValid);
                this.classList.toggle('is-invalid', !isValid);
                
                // Update validation message
                const feedback = this.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    if (value <= 0) {
                        feedback.textContent = 'Amount must be greater than R0';
                    } else if (value > 10000) {
                        feedback.textContent = 'Amount cannot exceed R10,000';
                    } else if (isNaN(value)) {
                        feedback.textContent = 'Please enter a valid amount';
                    }
                }
            });
        });
    },
    
    // Setup tooltips
    setupTooltips() {
        // Add helpful tooltips to form elements
        const helpElements = document.querySelectorAll('[data-help]');
        helpElements.forEach(element => {
            element.setAttribute('data-bs-toggle', 'tooltip');
            element.setAttribute('title', element.getAttribute('data-help'));
        });
    },
    
    // Setup auto logout on inactivity
    setupAutoLogout() {
        let timeout;
        const logoutTime = 30 * 60 * 1000; // 30 minutes
        
        const resetTimeout = () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                if (this.isAuthenticated()) {
                    this.showAlert('warning', 'Session expired due to inactivity. Please log in again.');
                    window.location.href = 'logout.php';
                }
            }, logoutTime);
        };
        
        // Reset timeout on user activity
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, resetTimeout, true);
        });
        
        resetTimeout();
    },
    
    // Load user data if authenticated
    loadUserData() {
        // This would be populated from PHP session
        const userDataElement = document.querySelector('#user-data');
        if (userDataElement) {
            try {
                this.currentUser = JSON.parse(userDataElement.textContent);
                this.token = userDataElement.getAttribute('data-token');
            } catch (e) {
                console.warn('Failed to parse user data');
            }
        }
    },
    
    // Check if user is authenticated
    isAuthenticated() {
        return this.currentUser && this.token;
    },
    
    // Make API calls
    async apiCall(endpoint, options = {}) {
        const url = this.apiBase + endpoint.replace(/^\//, '');
        
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        if (this.token) {
            defaultOptions.headers.Authorization = `Bearer ${this.token}`;
        }
        
        if (this.csrfToken) {
            defaultOptions.headers['X-CSRF-TOKEN'] = this.csrfToken;
        }
        
        const finalOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers
            }
        };
        
        if (finalOptions.body && typeof finalOptions.body === 'object') {
            finalOptions.body = JSON.stringify(finalOptions.body);
        }
        
        try {
            const response = await fetch(url, finalOptions);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.error || data.message || 'Request failed');
            }
            
            return data;
        } catch (error) {
            console.error('API call failed:', error);
            throw error;
        }
    },
    
    // Show alert messages
    showAlert(type, message, duration = 5000) {
        const alertContainer = document.querySelector('#alert-container') || document.body;
        
        const alertElement = document.createElement('div');
        alertElement.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
        alertElement.innerHTML = `
            <i class="bi bi-${this.getAlertIcon(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        alertContainer.appendChild(alertElement);
        
        // Auto-dismiss after duration
        setTimeout(() => {
            if (alertElement.parentNode) {
                alertElement.remove();
            }
        }, duration);
    },
    
    // Get alert icon based on type
    getAlertIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    },
    
    // Format currency
    formatCurrency(amount) {
        return new Intl.NumberFormat('en-ZA', {
            style: 'currency',
            currency: 'ZAR',
            minimumFractionDigits: 2
        }).format(amount).replace('ZAR', 'R');
    },
    
    // Format date
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-ZA', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },
    
    // Show loading spinner
    showLoading(element, show = true) {
        if (show) {
            element.innerHTML = '<span class="loading-spinner me-2"></span>Loading...';
            element.disabled = true;
        } else {
            element.disabled = false;
        }
    },
    
    // Confirm dialog
    async confirm(message, title = 'Confirm Action') {
        return new Promise((resolve) => {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirm-btn">Confirm</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            
            modal.querySelector('#confirm-btn').addEventListener('click', () => {
                resolve(true);
                bsModal.hide();
            });
            
            modal.addEventListener('hidden.bs.modal', () => {
                if (!modal.querySelector('#confirm-btn').clicked) {
                    resolve(false);
                }
                modal.remove();
            });
            
            bsModal.show();
        });
    },
    
    // Copy to clipboard
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            this.showAlert('success', 'Copied to clipboard!', 2000);
        } catch (err) {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            this.showAlert('success', 'Copied to clipboard!', 2000);
        }
    }
};

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    PayLekker.init();
});

// Utility functions for specific pages
window.PayLekkerUtils = {
    
    // Transfer page utilities
    transfer: {
        validateRecipient(email) {
            // Basic email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },
        
        async checkBalance() {
            try {
                const response = await PayLekker.apiCall('user/balance');
                return response.balance;
            } catch (error) {
                console.error('Failed to check balance:', error);
                return null;
            }
        }
    },
    
    // Chat utilities
    chat: {
        scrollToBottom() {
            const chatContainer = document.querySelector('.chat-container');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        },
        
        addMessage(content, isUser = false) {
            const chatContainer = document.querySelector('.chat-container');
            if (!chatContainer) return;
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${isUser ? 'user' : 'bot'}`;
            
            const bubble = document.createElement('div');
            bubble.className = `chat-bubble ${isUser ? 'user' : 'bot'}`;
            bubble.innerHTML = content.replace(/\n/g, '<br>');
            
            messageDiv.appendChild(bubble);
            chatContainer.appendChild(messageDiv);
            
            this.scrollToBottom();
        }
    },
    
    // Budget utilities
    budget: {
        calculateProgress(spent, total) {
            if (total <= 0) return 0;
            return Math.min((spent / total) * 100, 100);
        },
        
        getStatusClass(progress) {
            if (progress >= 100) return 'danger';
            if (progress >= 80) return 'warning';
            return 'success';
        }
    }
};