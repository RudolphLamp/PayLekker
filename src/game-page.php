<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Games & Rewards - PayLekker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/dashboard-enhancements.css">
    <link rel="stylesheet" href="assets/css/force-fix.css">
    <link rel="stylesheet" href="assets/css/game.css">
    <style>
        /* ABSOLUTE FINAL FORCE - INLINE CSS CANNOT BE OVERRIDDEN */
        /* Force ALL Bootstrap Icons to Black */
        i[class*="bi"]:before,
        .bi:before,
        [class*="bi-"]:before,
        i.bi:before {
            color: #000000 !important;
        }

        /* Force icon containers and parents */
        i, .bi, [class*="bi-"], 
        .card-icon, .card-icon *, 
        .sidebar-header i, .sidebar-header .bi,
        .nav-link i, .nav-link .bi {
            color: #000000 !important;
            fill: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            background-color: transparent !important;
        }
        
        /* Game specific overrides */
        .progress-card i,
        .game-rewards-section i,
        .celebration-modal i {
            color: inherit !important;
        }
        
        /* Make progress card match dashboard balance card */
        .progress-card.transactions-card {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .progress-details h3 {
            color: #212529;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        
        .level-badge {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }
        
        .stat-value {
            color: #212529;
        }
        
        .stat-label {
            color: #6c757d;
        }
        
        /* Maintain dashboard consistency */
        .main-content {
            margin-left: 280px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        
        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1040; /* Below Bootstrap modals (1055) */
            backdrop-filter: blur(5px);
        }
        
        .loading-overlay .loading-spinner {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        /* Ensure modals work properly */
        .modal {
            z-index: 1055;
        }
        
        .modal-backdrop {
            z-index: 1050;
        }
        
        .modal-content {
            position: relative;
            z-index: 1056;
        }
        
        /* Balance display styling */
        .balance-display {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #28a745;
        }
        
        .balance-display i {
            font-size: 1.2rem;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
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
                <a href="budget-page.php" class="nav-link">
                    <i class="bi bi-pie-chart"></i>
                    Budget
                </a>
            </div>
            <div class="nav-item">
                <a href="add-funds-page.php" class="nav-link">
                    <i class="bi bi-plus-circle"></i>
                    Add Funds
                </a>
            </div>
            <div class="nav-item">
                <a href="history-page.php" class="nav-link">
                    <i class="bi bi-clock-history"></i>
                    Transaction History
                </a>
            </div>
            <div class="nav-item">
                <a href="chat-page.php" class="nav-link">
                    <i class="bi bi-chat-dots"></i>
                    AI Assistant
                </a>
            </div>
            <div class="nav-item">
                <a href="game-page.php" class="nav-link active">
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
                <div class="user-avatar">
                    <span id="userInitials">PL</span>
                </div>
                <div>
                    <div class="fw-semibold" id="userNameNav">Loading...</div>
                    <div class="text-muted small">Games & Rewards</div>
                </div>
            </div>
        </div>
        <!-- User Progress Header -->
        <div class="progress-header">
            <div class="progress-card transactions-card">
                <div class="progress-info">
                    <div class="level-badge">
                        <i class="bi bi-award"></i>
                        Level <span id="user-level">1</span>
                    </div>
                    <div class="progress-details">
                        <h3>Welcome, Player!</h3>
                        <div class="balance-display">
                            <i class="bi bi-wallet2"></i>
                            <span id="account-balance">R0.00</span>
                        </div>
                        <div class="xp-bar">
                            <div class="xp-fill" id="xp-fill"></div>
                            <span class="xp-text" id="xp-text">0 / 100 XP</span>
                        </div>
                    </div>
                </div>
                <div class="progress-stats">
                    <div class="stat">
                        <i class="bi bi-star"></i>
                        <div>
                            <div class="stat-value" id="total-points">0</div>
                            <div class="stat-label">Points</div>
                        </div>
                    </div>
                    <div class="stat">
                        <i class="bi bi-fire"></i>
                        <div>
                            <div class="stat-value" id="current-streak">0</div>
                            <div class="stat-label">Streak</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div id="notification-container"></div>

        <!-- Unclaimed Rewards -->
        <div class="rewards-section transactions-card" id="rewards-section" style="display: none;">
            <div class="transactions-header">
                <h3><i class="bi bi-gift me-2"></i>Unclaimed Rewards</h3>
            </div>
            <div class="rewards-grid" id="rewards-grid">
                <!-- Rewards will be populated here -->
            </div>
        </div>

        <!-- Game Tabs -->
        <div class="game-tabs">
            <button class="tab-btn active" data-tab="challenges">
                <i class="bi bi-list-task"></i> Challenges
            </button>
            <button class="tab-btn" data-tab="achievements">
                <i class="bi bi-trophy"></i> Achievements
            </button>
            <button class="tab-btn" data-tab="minigame">
                <i class="bi bi-controller"></i> Mini Game
            </button>
        </div>

        <!-- Challenges Tab -->
        <div class="tab-content active" id="challenges-tab">
            <div class="challenges-container">
                <div class="challenges-filter">
                    <button class="filter-btn active" data-filter="all">All Challenges</button>
                    <button class="filter-btn" data-filter="daily">Daily</button>
                    <button class="filter-btn" data-filter="weekly">Weekly</button>
                    <button class="filter-btn" data-filter="milestone">Milestone</button>
                </div>
                
                <div class="challenges-grid" id="challenges-grid">
                    <!-- Challenges will be populated here -->
                </div>
            </div>
        </div>

        <!-- Achievements Tab -->
        <div class="tab-content" id="achievements-tab">
            <div class="achievements-container transactions-card">
                <div class="transactions-header">
                    <h3><i class="bi bi-trophy me-2"></i>Your Achievements</h3>
                </div>
                <div class="achievements-grid" id="achievements-grid">
                    <!-- Achievements will be populated here -->
                </div>
            </div>
        </div>

        <!-- Mini Game Tab -->
        <div class="tab-content" id="minigame-tab">
            <div class="row">
                <!-- Game Area -->
                <div class="col-lg-8">
                    <div class="transactions-card">
                        <div class="transactions-header">
                            <h3><i class="bi bi-controller me-2"></i>Flappy Bird Challenge</h3>
                            <div class="game-stats">
                                <span class="badge bg-success me-2">High Score: <span id="high-score">0</span></span>
                                <span class="badge bg-primary">Current Score: <span id="current-score">0</span></span>
                            </div>
                        </div>
                        <div class="game-container">
                            <canvas id="flappy-canvas" width="800" height="400"></canvas>
                            <div class="game-overlay" id="game-overlay">
                                <div class="game-start-screen" id="start-screen">
                                    <h4>Flappy Bird Challenge</h4>
                                    <p>Click or press SPACE to make the bird fly!</p>
                                    <p>Avoid the pipes and earn points</p>
                                    <button class="btn btn-primary btn-lg" id="start-game-btn">
                                        <i class="bi bi-play-fill"></i> Start Game
                                    </button>
                                </div>
                                <div class="game-over-screen" id="game-over-screen" style="display: none;">
                                    <h4>Game Over!</h4>
                                    <p>Final Score: <span id="final-score">0</span></p>
                                    <p id="reward-message" style="display: none;"></p>
                                    <button class="btn btn-primary" id="restart-game-btn">
                                        <i class="bi bi-arrow-clockwise"></i> Play Again
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Game Challenges -->
                <div class="col-lg-4">
                    <div class="transactions-card">
                        <div class="transactions-header">
                            <h4><i class="bi bi-target me-2"></i>Game Challenges</h4>
                        </div>
                        <div class="mini-game-challenges" id="mini-game-challenges">
                            <!-- Mini game challenges will be populated here -->
                        </div>
                    </div>
                    
                    <div class="transactions-card mt-3">
                        <div class="transactions-header">
                            <h4><i class="bi bi-info-circle me-2"></i>How to Play</h4>
                        </div>
                        <div class="game-instructions">
                            <ul class="list-unstyled">
                                <li><i class="bi bi-mouse"></i><span>Click to flap</span></li>
                                <li><i class="bi bi-keyboard"></i><span>Press SPACE to flap</span></li>
                                <li><i class="bi bi-arrow-through-heart"></i><span>Avoid the pipes</span></li>
                                <li><i class="bi bi-trophy"></i><span>Complete challenges for rewards</span></li>
                                <li><i class="bi bi-cash-coin"></i><span>Earn money for high scores</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Challenge Completion Modal -->
    <div class="modal fade" id="challenge-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Complete Challenge</h5>
                    <button type="button" class="btn-close" id="modal-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="modal-description">Challenge description will appear here.</p>
                    <div class="modal-form" id="modal-form">
                        <!-- Dynamic form fields will be added here -->
                    </div>
                    <div class="modal-rewards" id="modal-rewards">
                        <!-- Reward preview will be shown here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="modal-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="modal-complete">Complete Challenge</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reward Celebration Modal -->
    <div class="modal fade celebration-modal" id="celebration-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="celebration-header text-center p-4">
                    <i class="bi bi-trophy celebration-icon" style="font-size: 4rem; color: #ffd700;"></i>
                    <h2>Congratulations!</h2>
                    <p id="celebration-message">You completed a challenge!</p>
                </div>
                <div class="celebration-rewards" id="celebration-rewards">
                    <!-- Celebration rewards will be shown here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary w-100" id="celebration-close">Awesome!</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-spinner text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading game data...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/common.js"></script>
    <script src="assets/js/game.js"></script>
    <script src="assets/js/flappy-bird.js"></script>
    
    <script>
        // Initialize sidebar functionality from common.js
        document.addEventListener('DOMContentLoaded', function() {
            setupSidebar();
        });
    </script>
</body>
</html>