/**
 * PayLekker - Common Authentication & Utility Functions
 * Shared JavaScript functions for all authenticated pages
 */

const API_BASE = '';

// Utility function for South African currency formatting
function formatCurrency(amount) {
    const num = parseFloat(amount);
    return `R ${num.toLocaleString('en-ZA', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`;
}

// Enhanced authentication check with fallback methods
async function checkAuth() {
    const token = sessionStorage.getItem('auth_token');
    const userData = sessionStorage.getItem('user_data');
    
    console.log('Checking authentication...', {
        hasToken: !!token,
        hasUserData: !!userData,
        page: window.location.pathname
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
            console.log('Using cached user data');
            if (typeof updateUserInfo === 'function') {
                updateUserInfo(user);
            }
        } catch (e) {
            console.error('Failed to parse cached user data:', e);
        }
    }
    
    try {
        // Test token validation first
        let testResponse = await makeAuthenticatedRequest('test-token.php', 'GET');
        
        if (!testResponse.success) {
            throw new Error('Token test failed: ' + testResponse.error);
        }
        
        console.log('Token validated successfully');
        
        // Now try to get profile data
        const profileResult = await makeAuthenticatedRequest('profile.php', 'GET');
        
        if (profileResult.success) {
            sessionStorage.setItem('user_data', JSON.stringify(profileResult.data.user));
            if (typeof updateUserInfo === 'function') {
                updateUserInfo(profileResult.data.user);
            }
            console.log('Authentication successful, user profile updated');
            return profileResult.data.user;
        } else {
            throw new Error('Invalid profile API response: ' + (profileResult.error || 'Unknown error'));
        }
        
    } catch (error) {
        console.error('Authentication error:', error);
        
        // Don't redirect immediately on network errors if we have cached data
        if (userData) {
            console.warn('Using cached user data due to authentication error');
            if (typeof showAlert === 'function') {
                showAlert('Connection issue detected. Some data may not be up to date.', 'warning');
            }
            return JSON.parse(userData);
        }
        
        // On critical error without cached data, redirect to login
        console.error('No cached data available, redirecting to login');
        alert('Authentication failed: ' + error.message + '. Please login again.');
        sessionStorage.removeItem('auth_token');
        sessionStorage.removeItem('user_data');
        window.location.href = 'auth/login.php';
    }
}

// Make authenticated API request with fallback methods
async function makeAuthenticatedRequest(endpoint, method = 'GET', data = null) {
    const token = sessionStorage.getItem('auth_token');
    
    if (!token) {
        throw new Error('No authentication token available');
    }
    
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (data && (method === 'POST' || method === 'PUT')) {
        options.body = JSON.stringify(data);
    }
    
    let response;
    let result;
    
    try {
        // Method 1: Try with Authorization header
        options.headers['Authorization'] = 'Bearer ' + token;
        response = await fetch(API_BASE + endpoint, options);
        
        if (response.ok) {
            result = await response.json();
            return result;
        } else if (response.status === 401) {
            // Token expired
            throw new Error('Token expired or invalid');
        } else {
            // Try fallback method
            throw new Error('Header method failed, trying fallback');
        }
        
    } catch (headerError) {
        console.warn('Authorization header method failed, trying URL parameter method');
        
        // Method 2: Try with token as URL parameter
        delete options.headers['Authorization'];
        const separator = endpoint.includes('?') ? '&' : '?';
        const fallbackUrl = endpoint + separator + 'token=' + encodeURIComponent(token);
        
        response = await fetch(API_BASE + fallbackUrl, options);
        
        if (response.ok) {
            result = await response.json();
            return result;
        } else if (response.status === 401) {
            throw new Error('Token expired or invalid');
        } else {
            const errorText = await response.text();
            throw new Error('API request failed: ' + response.status + ' - ' + errorText);
        }
    }
}

// Show alert notification
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer') || createAlertContainer();
    const alertId = 'alert-' + Date.now();
    
    const iconMap = {
        'success': 'check-circle',
        'warning': 'exclamation-triangle',
        'danger': 'x-circle',
        'error': 'x-circle',
        'info': 'info-circle'
    };
    
    const alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${iconMap[type] || 'info-circle'} me-2"></i>
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

// Logout function
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        sessionStorage.removeItem('auth_token');
        sessionStorage.removeItem('user_data');
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_data');
        window.location.href = 'auth/login.php';
    }
}

// Setup responsive sidebar
function setupSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
        });
    }
    
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }
    
    // Close sidebar when clicking on nav items on mobile
    const navLinks = document.querySelectorAll('.sidebar-item');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
            }
        });
    });
}

// Format currency for South African Rand
function formatCurrency(amount) {
    const numAmount = parseFloat(amount.toString().replace(/[^\d.-]/g, '')) || 0;
    return `R ${numAmount.toLocaleString('en-ZA', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`;
}

// Format date for display
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-ZA', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

console.log('PayLekker common utilities loaded');