<?php
/**
 * PayLekker - User Registration
 */

require_once 'includes/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        // Validate input
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        
        // Basic validation
        if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
            $error = 'All required fields must be filled.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            // Call registration API
            $registrationData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => $password,
                'phone' => $phone
            ];
            
            $response = callAPI('POST', 'auth/register', $registrationData);
            
            if ($response['success']) {
                // Registration successful - store session data
                $_SESSION['user'] = $response['data']['user'];
                $_SESSION['token'] = $response['data']['token'];
                
                setFlashMessage('success', 'Registration successful! Welcome to PayLekker.');
                header('Location: dashboard.php');
                exit;
            } else {
                $error = $response['data']['error'] ?? 'Registration failed. Please try again.';
            }
        }
    }
}

$pageTitle = 'Register';
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
                        <h4 class="text-center mb-4">Create Your Account</h4>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?php echo e($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="first_name" 
                                               name="first_name" 
                                               value="<?php echo e($_POST['first_name'] ?? ''); ?>"
                                               required>
                                        <div class="invalid-feedback">
                                            Please enter your first name.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">Last Name *</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="last_name" 
                                               name="last_name" 
                                               value="<?php echo e($_POST['last_name'] ?? ''); ?>"
                                               required>
                                        <div class="invalid-feedback">
                                            Please enter your last name.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo e($_POST['email'] ?? ''); ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Please enter a valid email address.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       placeholder="+27 12 345 6789"
                                       value="<?php echo e($_POST['phone'] ?? ''); ?>">
                                <small class="form-text text-muted">Optional - for account recovery</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       minlength="6"
                                       required>
                                <div class="invalid-feedback">
                                    Password must be at least 6 characters long.
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Confirm Password *</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       minlength="6"
                                       required>
                                <div class="invalid-feedback">
                                    Please confirm your password.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="terms" 
                                           required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="terms.php" target="_blank">Terms of Service</a> 
                                        and <a href="privacy.php" target="_blank">Privacy Policy</a>
                                    </label>
                                    <div class="invalid-feedback">
                                        You must agree to the terms and conditions.
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="bi bi-person-plus me-2"></i>
                                Create Account
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-0">Already have an account?</p>
                            <a href="login.php" class="btn btn-outline-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Sign In
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-white-50">
                        Secure • Encrypted • Protected by Bank-Level Security
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Custom password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    }
});
</script>

<?php include 'includes/footer.php'; ?>