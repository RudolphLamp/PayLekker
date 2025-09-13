<?php
/**
 * PayLekker - User Login
 */

require_once 'includes/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$remember = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Basic validation
        if (empty($email) || empty($password)) {
            $error = 'Please enter both email and password.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            // Call login API
            $loginData = [
                'email' => $email,
                'password' => $password
            ];
            
            $response = callAPI('POST', 'auth/login', $loginData);
            
            if ($response['success']) {
                // Login successful - store session data
                $_SESSION['user'] = $response['data']['user'];
                $_SESSION['token'] = $response['data']['token'];
                
                // Set remember me cookie if requested
                if ($remember) {
                    // Store encrypted token in cookie for 30 days
                    $cookieToken = base64_encode($response['data']['token']);
                    setcookie('remember_token', $cookieToken, time() + (30 * 24 * 60 * 60), '/', '', true, true);
                }
                
                setFlashMessage('success', 'Welcome back to PayLekker!');
                
                // Redirect to intended page or dashboard
                $redirect = $_GET['redirect'] ?? 'dashboard.php';
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = $response['data']['error'] ?? 'Login failed. Please check your credentials.';
            }
        }
    }
}

// Check for remember me cookie
if (!isLoggedIn() && isset($_COOKIE['remember_token'])) {
    // You could implement auto-login here if needed
    // For security, we'll just show a message
    setFlashMessage('info', 'Welcome back! Please enter your password to continue.');
}

$pageTitle = 'Login';
$bodyClass = 'auth-page';
?>

<?php include 'includes/header.php'; ?>

<div class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="auth-card card">
                    <div class="auth-header">
                        <div class="auth-logo">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <h2 class="mb-2"><?php echo SITE_NAME; ?></h2>
                        <p class="text-muted"><?php echo SITE_TAGLINE; ?></p>
                    </div>
                    
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4">Welcome Back</h4>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?php echo e($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           placeholder="Enter your email"
                                           value="<?php echo e($_POST['email'] ?? $_COOKIE['remember_email'] ?? ''); ?>"
                                           required>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Enter your password"
                                           required>
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback">
                                        Please enter your password.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="remember" 
                                                   name="remember"
                                                   <?php echo $remember ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="remember">
                                                Remember me
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="forgot-password.php" class="text-decoration-none">
                                            Forgot password?
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3" id="loginBtn">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Sign In
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-0">Don't have an account?</p>
                            <a href="register.php" class="btn btn-outline-primary">
                                <i class="bi bi-person-plus me-2"></i>
                                Create Account
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-white-50">
                        Protected by 256-bit SSL encryption
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.className = 'bi bi-eye-slash';
    } else {
        passwordInput.type = 'password';
        toggleIcon.className = 'bi bi-eye';
    }
});

// Enhanced form submission
document.querySelector('form').addEventListener('submit', function() {
    const submitBtn = document.getElementById('loginBtn');
    PayLekker.showLoading(submitBtn);
});

// Auto-focus email field
document.addEventListener('DOMContentLoaded', function() {
    const emailField = document.getElementById('email');
    if (emailField && !emailField.value) {
        emailField.focus();
    } else {
        document.getElementById('password').focus();
    }
});
</script>

<?php include 'includes/footer.php'; ?>