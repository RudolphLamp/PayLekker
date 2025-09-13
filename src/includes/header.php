<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' . SITE_NAME : SITE_NAME . ' - ' . SITE_TAGLINE; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico">
    
    <meta name="description" content="PayLekker - South African Digital Banking Made Easy">
    <meta name="keywords" content="banking, digital wallet, money transfer, South Africa, fintech">
    <meta name="author" content="PayLekker">
</head>
<body class="<?php echo $bodyClass ?? ''; ?>">
    
    <!-- Navigation -->
    <?php if (isLoggedIn()): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <i class="bi bi-wallet2 me-2"></i>
                <strong><?php echo SITE_NAME; ?></strong>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                            <i class="bi bi-house-door me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'transfer.php' ? 'active' : ''; ?>" href="transfer.php">
                            <i class="bi bi-arrow-left-right me-1"></i> Transfer
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : ''; ?>" href="history.php">
                            <i class="bi bi-clock-history me-1"></i> History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'budget.php' ? 'active' : ''; ?>" href="budget.php">
                            <i class="bi bi-pie-chart me-1"></i> Budget
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'chat.php' ? 'active' : ''; ?>" href="chat.php">
                            <i class="bi bi-chat-dots me-1"></i> Chat
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i>
                            <span><?php echo e(getCurrentUser()['first_name']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">
                                <?php $user = getCurrentUser(); ?>
                                <?php echo e($user['first_name'] . ' ' . $user['last_name']); ?>
                            </h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    
    <!-- Flash Messages -->
    <?php $flashMessages = getFlashMessage(); ?>
    <?php if (!empty($flashMessages)): ?>
        <div class="container mt-3">
            <?php foreach ($flashMessages as $type => $message): ?>
                <div class="alert alert-<?php echo $type == 'error' ? 'danger' : $type; ?> alert-dismissible fade show" role="alert">
                    <?php if ($type == 'success'): ?>
                        <i class="bi bi-check-circle me-2"></i>
                    <?php elseif ($type == 'error'): ?>
                        <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php elseif ($type == 'warning'): ?>
                        <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php else: ?>
                        <i class="bi bi-info-circle me-2"></i>
                    <?php endif; ?>
                    <?php echo e($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Main Content Container -->
    <main class="main-content <?php echo isLoggedIn() ? 'authenticated' : 'guest'; ?>">