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
            background: linear-gradient(135deg, rgba(52, 58, 64, 0.8), rgba(33, 37, 41, 0.85));
            background-image: url('assets/hero.jpg');
            background-size: cover;
            background-position: center;
            background-blend-mode: overlay;
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.6), rgba(0,0,0,0.4));
            z-index: 1;
            /* Remove any spinning animation */
            animation: none !important;
            transform: none !important;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 4rem 0;
        }
        
        .hero-title {
            color: white;
            font-size: 4rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .hero-subtitle {
            color: white;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        .hero-description {
            color: rgba(255,255,255,0.9);
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        .cta-button {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin: 0 8px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .cta-button:hover {
            background: white;
            color: #000000;
            border-color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,255,255,0.3);
        }
        
        .cta-button.secondary {
            background: transparent;
            color: white;
            border: 2px solid rgba(255,255,255,0.5);
        }
        
        .cta-button.secondary:hover {
            background: rgba(255,255,255,0.1);
            border-color: white;
            color: white;
        }
        
        .sa-features {
            background: #f8f9fa;
            padding: 6rem 0;
        }
        
        .section-title {
            color: #000000;
            font-size: 2.5rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .section-subtitle {
            color: #6c757d;
            font-size: 1.2rem;
            text-align: center;
            margin-bottom: 4rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .feature-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            border-color: #000000;
        }
        
        .feature-icon {
            color: #000000 !important;
            background: #ffffff !important;
            background-color: #ffffff !important;
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            font-size: 2rem;
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
        }
        
        .feature-icon:hover {
            background: #000000 !important;
            background-color: #000000 !important;
            color: white !important;
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border-color: #000000;
        }
        
        /* Override global icon color for feature icons specifically */
        .feature-icon i,
        .feature-icon .bi,
        .feature-icon [class*="bi-"] {
            color: inherit !important;
            background: transparent !important;
            background-color: transparent !important;
        }
        
        /* Ensure feature icon containers always have white background */
        .sa-features .feature-icon {
            background: #ffffff !important;
            background-color: #ffffff !important;
        }
        
        /* Ensure hover state works properly */
        .sa-features .feature-icon:hover {
            background: #000000 !important;
            background-color: #000000 !important;
        }
        
        .sa-features .feature-icon:hover i,
        .sa-features .feature-icon:hover .bi,
        .sa-features .feature-icon:hover [class*="bi-"] {
            color: #ffffff !important;
        }
        
        .feature-title {
            color: #000000;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        .feature-description {
            color: #495057;
            font-size: 1rem;
            line-height: 1.6;
            margin: 0;
        }
        
        .sa-stats {
            background: #000000;
            color: white;
        }
        
        .why-paylekker {
            background: #f8f9fa;
            padding: 6rem 0;
        }
        
        .why-content {
            text-align: center;
        }
        
        .why-title {
            color: #000000;
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        .why-subtitle {
            color: #6c757d;
            font-size: 1.3rem;
            margin-bottom: 3rem;
            font-style: italic;
        }
        
        .why-story {
            max-width: 900px;
            margin: 0 auto;
            text-align: left;
        }
        
        .why-text {
            color: #495057;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 2.5rem;
        }
        
        .ubuntu-philosophy {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            margin: 2.5rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .ubuntu-quote {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .ubuntu-quote i {
            font-size: 2rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .ubuntu-quote p {
            font-size: 1.4rem;
            color: #000000;
            font-weight: 500;
            margin: 0;
        }
        
        .ubuntu-text {
            color: #495057;
            font-size: 1.1rem;
            line-height: 1.7;
            text-align: center;
            margin: 0;
        }
        
        .mission-points {
            margin: 3rem 0;
        }
        
        .mission-item {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .mission-item:hover {
            transform: translateY(-2px);
        }
        
        .mission-item i {
            font-size: 2rem;
            color: #000000;
            margin-bottom: 0.5rem;
        }
        
        .mission-item h4 {
            color: #000000;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .mission-item p {
            color: #6c757d;
            margin: 0;
            font-size: 0.95rem;
        }
        
        .hackathon-badge {
            background: linear-gradient(135deg, #000000, #343a40);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            margin-top: 3rem;
        }
        
        .badge-text {
            font-size: 1.1rem;
            margin: 0;
            line-height: 1.6;
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
        
        /* Responsive Design for Mobile */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.3rem;
            }
            
            .hero-description {
                font-size: 1rem;
                margin-bottom: 2rem;
            }
            
            .cta-button {
                display: block;
                margin: 8px 0;
                text-align: center;
            }
            
            .why-title {
                font-size: 2.2rem;
            }
            
            .why-subtitle {
                font-size: 1.1rem;
            }
            
            .ubuntu-quote p {
                font-size: 1.2rem;
            }
            
            .mission-item {
                margin-bottom: 1.5rem;
            }
        }
        
        /* Remove any spinning animations from hero section */
        .hero-section *,
        .hero-section::before,
        .hero-section::after {
            animation: none !important;
            transform: none !important;
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
                        <a class="nav-link" href="#why">Why PayLekker</a>
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

    <!-- Why PayLekker Section -->
    <section id="why" class="why-paylekker">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="why-content">
                        <h2 class="why-title">Why PayLekker Exists</h2>
                        <p class="why-subtitle">Born from the Intervarsity Hackathon Challenge</p>
                        
                        <div class="why-story">
                            <p class="why-text">PayLekker emerged when we were challenged by the prestigious <strong>Intervarsity Hackathon</strong> to tackle South Africa's most pressing technological problems. During the intense 24-hour coding marathon, our team recognized that while South Africa leads Africa in fintech innovation, <strong>millions of everyday citizens remain excluded</strong> from digital financial services.</p>
                            
                            <div class="ubuntu-philosophy">
                                <blockquote class="ubuntu-quote">
                                    <i class="bi bi-quote"></i>
                                    <p><em>"I am because we are"</em> - Ubuntu Philosophy</p>
                                </blockquote>
                                <p class="ubuntu-text">We believe financial inclusion isn't just about technology; it's about <strong>dignity, empowerment, and community</strong>. PayLekker was built to ensure that a domestic worker in Soweto has access to the same financial tools as a software engineer in Sandton.</p>
                            </div>
                            
                            <div class="mission-points">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mission-item">
                                            <i class="bi bi-heart-fill"></i>
                                            <h4>Social Impact</h4>
                                            <p>Bridge the digital divide in financial services</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mission-item">
                                            <i class="bi bi-lightning-charge-fill"></i>
                                            <h4>Economic Empowerment</h4>
                                            <p>Enable financial literacy through accessible technology</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mission-item">
                                            <i class="bi bi-flag-fill"></i>
                                            <h4>Cultural Relevance</h4>
                                            <p>Built by South Africans, for South Africans</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mission-item">
                                            <i class="bi bi-rocket-takeoff-fill"></i>
                                            <h4>Innovation Challenge</h4>
                                            <p>Prove that local solutions can rival international fintech giants</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="hackathon-badge">
                                <p class="badge-text"><strong>From hackathon prototype to production platform</strong> - PayLekker represents what's possible when passionate developers meet real-world challenges with innovative thinking and Ubuntu spirit.</p>
                            </div>
                        </div>
                    </div>
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