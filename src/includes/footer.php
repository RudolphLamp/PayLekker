    </main>
    
    <!-- Footer -->
    <footer class="footer mt-auto py-4 <?php echo isLoggedIn() ? 'bg-light' : 'bg-dark text-white'; ?>">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-wallet2 me-2"></i>
                        <strong><?php echo SITE_NAME; ?></strong>
                    </div>
                    <p class="text-muted mb-0">
                        <?php echo SITE_TAGLINE; ?>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex justify-content-md-end justify-content-start mb-2">
                        <small class="text-muted">
                            Made with ❤️ in South Africa
                        </small>
                    </div>
                    <?php if (isLoggedIn()): ?>
                        <small class="text-muted">
                            Secure • Encrypted • Protected
                        </small>
                    <?php else: ?>
                        <div class="social-links">
                            <a href="#" class="text-decoration-none me-3">
                                <i class="bi bi-twitter"></i> Twitter
                            </a>
                            <a href="#" class="text-decoration-none me-3">
                                <i class="bi bi-linkedin"></i> LinkedIn
                            </a>
                            <a href="#" class="text-decoration-none">
                                <i class="bi bi-github"></i> GitHub
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (isLoggedIn()): ?>
            <hr>
            <div class="row">
                <div class="col text-center">
                    <small class="text-muted">
                        &copy; <?php echo date('Y'); ?> PayLekker. All rights reserved. 
                        | <a href="privacy.php" class="text-decoration-none">Privacy Policy</a>
                        | <a href="terms.php" class="text-decoration-none">Terms of Service</a>
                    </small>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="assets/js/app.js"></script>
    
    <!-- Page-specific JavaScript -->
    <?php if (isset($additionalJS)): ?>
        <?php foreach ((array)$additionalJS as $jsFile): ?>
            <script src="<?php echo e($jsFile); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Inline JavaScript -->
    <?php if (isset($inlineJS)): ?>
        <script><?php echo $inlineJS; ?></script>
    <?php endif; ?>
</body>
</html>