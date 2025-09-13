<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker - South Africa's Premier Cash App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/landing.css">
    <style>
        /* Force all icons to be black */
        i, .bi, [class*="bi-"] {
            color: #000000 !important;
        }
        
        /* Override all color variables with black/white theme */
        :root {
            --primary-color: #000000;
            --secondary-color: #ffffff;
            --sa-gold: #000000;
            --sa-orange: #343a40;
        }
        
        body {
            background: #f8f9fa;
            color: #000000;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            border-bottom: 1px solid #e9ecef;
        }
        
        .navbar-brand {
            color: #000000 !important;
        }
        
        .navbar-brand span {
            background: #000000 !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
        }
        
        .nav-link {
            color: #000000 !important;
        }
        
        .nav-link:hover {
            color: #343a40 !important;
        }
        
        .btn {
            background: #000000 !important;
            border-color: #000000 !important;
            color: white !important;
        }
        
        .btn:hover {
            background: #343a40 !important;
            border-color: #343a40 !important;
        }
        
        .hero-section {
            background: white;
            color: #000000;
        }
        
        .hero-title {
            color: #000000;
        }
        
        .hero-subtitle {
            color: #6c757d;
        }
        
        .hero-description {
            color: #495057;
        }
        
        .cta-button {
            background: #000000;
            color: white;
            border: 2px solid #000000;
        }
        
        .cta-button:hover {
            background: #343a40;
            border-color: #343a40;
        }
        
        .cta-button.secondary {
            background: white;
            color: #000000;
            border: 2px solid #000000;
        }
        
        .cta-button.secondary:hover {
            background: #f8f9fa;
        }
        
        .sa-features {
            background: #f8f9fa;
        }
        
        .section-title {
            color: #000000;
        }
        
        .section-subtitle {
            color: #6c757d;
        }
        
        .feature-card {
            background: white;
            border: 1px solid #e9ecef;
        }
        
        .feature-icon {
            color: #000000;
            background: #f8f9fa;
        }
        
        .feature-title {
            color: #000000;
        }
        
        .feature-description {
            color: #495057;
        }
        
        .sa-stats {
            background: #000000;
            color: white;
        }
        
        .stats-title {
            color: white;
        }
        
        .stat-number {
            color: white;
        }
        
        .stat-label {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .money-highlight {
            background: white;
        }
        
        .money-title {
            color: #000000;
        }
        
        .money-subtitle {
            color: #495057;
        }
        
        .money-cta {
            background: #000000;
            color: white;
            border: 2px solid #000000;
        }
        
        .money-cta:hover {
            background: #343a40;
            color: white;
        }
        
        .how-it-works {
            background: #f8f9fa;
        }
        
        .step-item {
            background: white;
            border: 1px solid #e9ecef;
        }
        
        .step-number {
            background: #000000;
            color: white;
        }
        
        .step-title {
            color: #000000;
        }
        
        .step-description {
            color: #495057;
        }
        
        .sa-footer {
            background: #000000;
            color: white;
        }
        
        .footer-title {
            color: white;
        }
        
        .footer-description {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .footer-link {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .footer-link:hover {
            color: white;
        }
        
        .footer-bottom {
            color: rgba(255, 255, 255, 0.6);
            border-top: 1px solid #343a40;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="bi bi-wallet2 me-2"></i>
                <span>PayLekker</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How it Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#stats">Stats</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/login.php">Login</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn" href="auth/register.php">Get Started</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">PayLekker</h1>
                <p class="hero-subtitle">🇿🇦 South Africa's Premier Cash App</p>
                <p class="hero-description">Send money instantly, manage your budget smartly, and experience the future of digital payments - all designed for South Africans, by South Africans.</p>
                <div class="hero-cta">
                    <a href="auth/register.php" class="cta-button">
                        <i class="bi bi-rocket-takeoff me-2"></i>Start Banking Now
                    </a>
                    <a href="#features" class="cta-button secondary">
                        <i class="bi bi-play-circle me-2"></i>See How It Works
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- SA Features Section -->
    <section id="features" class="sa-features">
        <div class="container">
            <h2 class="section-title">Built for South Africans</h2>
            <p class="section-subtitle">Experience digital payments designed specifically for the South African market, with features that understand your needs.</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                    <h3 class="feature-title">Lightning Fast Transfers</h3>
                    <p class="feature-description">Send money anywhere in South Africa in seconds, not hours. From Cape Town to Johannesburg, instant transfers at your fingertips.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-piggy-bank-fill"></i>
                    </div>
                    <h3 class="feature-title">Smart Budgeting</h3>
                    <p class="feature-description">Track your Rand and cents with intelligent budgeting tools designed for South African spending patterns and lifestyle.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-shield-fill-check"></i>
                    </div>
                    <h3 class="feature-title">Bank-Grade Security</h3>
                    <p class="feature-description">Your money is protected with military-grade encryption and biometric authentication. Safer than cash, more secure than cards.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SA Stats Section -->
    <section id="stats" class="sa-stats">
        <div class="stats-container">
            <h2 class="stats-title">Trusted Across South Africa</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">150K+</span>
                    <span class="stat-label">Active South Africans</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">R50M+</span>
                    <span class="stat-label">Transferred Monthly</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">9</span>
                    <span class="stat-label">Provinces Covered</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Local Support</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Money Highlight -->
    <section class="money-highlight">
        <div class="money-content">
            <h2 class="money-title">Send Money Like a Pro</h2>
            <p class="money-subtitle">Whether it's splitting a restaurant bill in Sandton or sending money home to family in the Eastern Cape - PayLekker makes it effortless.</p>
            <a href="auth/register.php" class="money-cta">
                <i class="bi bi-arrow-right-circle me-2"></i>Start Sending Money
            </a>
        </div>
    </section>

    <!-- How it Works -->
    <section id="how-it-works" class="how-it-works">
        <div class="steps-container">
            <h2 class="section-title">How PayLekker Works</h2>
            <p class="section-subtitle">Get started in just 3 simple steps and join thousands of South Africans already using PayLekker</p>
            
            <div class="steps-grid">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <h3 class="step-title">Sign Up Instantly</h3>
                    <p class="step-description">Create your PayLekker account in under 2 minutes using your SA ID number and phone number. No paperwork, no branch visits.</p>
                </div>
                
                <div class="step-item">
                    <div class="step-number">2</div>
                    <h3 class="step-title">Add Your Money</h3>
                    <p class="step-description">Load money from any South African bank, use EFT, or visit one of our 10,000+ cash deposit locations nationwide.</p>
                </div>
                
                <div class="step-item">
                    <div class="step-number">3</div>
                    <h3 class="step-title">Send & Spend</h3>
                    <p class="step-description">Send money to anyone with a phone number, pay at stores with QR codes, or budget your spending with smart categories.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="sa-footer">
        <div class="footer-content">
            <h3 class="footer-title">
                <i class="bi bi-wallet2 me-2"></i>PayLekker
            </h3>
            <p class="footer-description">Proudly South African. Making digital payments accessible, secure, and instant for every South African, from Limpopo to the Western Cape.</p>
            
            <div class="footer-links">
                <a href="#" class="footer-link">Privacy Policy</a>
                <a href="#" class="footer-link">Terms of Service</a>
                <a href="#" class="footer-link">Security</a>
                <a href="#" class="footer-link">Support</a>
                <a href="#" class="footer-link">About Us</a>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 PayLekker. All rights reserved. Licensed Financial Services Provider. 🇿🇦</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                navbar.style.borderBottom = '1px solid #dee2e6';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                navbar.style.borderBottom = '1px solid #e9ecef';
            }
        });

        // Add animation to stats when they come into view
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.8s ease forwards';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.stat-item, .feature-card, .step-item').forEach(el => {
            observer.observe(el);
        });
    </script>
    
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</body>
</html>