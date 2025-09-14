<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Games & Rewards - PayLekker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* PayLekker Game Page - Clean Modern Dashboard Style */
        
        /* Force ALL Bootstrap Icons to Black for sidebar/navigation */
        i[class*="bi"]:before,
        .bi:before,
        [class*="bi-"]:before,
        i.bi:before,
        .sidebar i:before,
        .nav-link i:before,
        .top-bar i:before {
            color: #000000 !important;
            font-weight: 900 !important;
        }

        /* Force icon visibility and color */
        i, .bi, [class*="bi-"] {
            color: #000000 !important;
            fill: #000000 !important;
            -webkit-text-fill-color: #000000 !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-block !important;
        }

        /* Sidebar Styles - Same as Dashboard */
        .sidebar {
            background: #ffffff;
            border-right: 1px solid #e9ecef;
            width: 280px;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
            box-shadow: none;
        }

        .sidebar.collapsed {
            transform: translateX(-280px);
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            text-align: center;
        }

        .sidebar-header h4 {
            color: #000000;
            font-weight: 600;
            margin: 0;
        }

        /* Ensure logo icon is visible */
        .sidebar-header h4 i,
        .sidebar-header h4 .bi,
        .sidebar-header h4 .bi-wallet2 {
            color: #000000 !important;
            margin-right: 0.5rem !important;
            display: inline-block !important;
            font-size: 1.2em !important;
            vertical-align: middle !important;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.25rem 1rem;
        }

        .nav-link {
            color: #000000;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            font-weight: 500;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #f8f9fa;
            color: #343a40;
            transform: translateX(5px);
            border-left: 3px solid #dee2e6;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            color: #000000 !important;
            font-size: 1.1rem !important;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            transition: margin-left 0.3s ease;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .main-content.sidebar-collapsed {
            margin-left: 0;
        }

        /* Top Bar */
        .top-bar {
            background: white;
            padding: 1rem 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #e9ecef;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #6c757d;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background: #f8f9fa;
            color: #343a40;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-weight: 600;
            border: 2px solid #dee2e6;
        }

        /* Progress Card - Hero Background (White Text) */
        .progress-card.transactions-card {
            background: white;
            color: #343a40;
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .progress-card.transactions-card .progress-content {
            position: relative;
            z-index: 2;
            background: linear-gradient(135deg, rgba(52, 58, 64, 0.8), rgba(33, 37, 41, 0.85));
            background-image: url('assets/hero.jpg');
            background-size: cover;
            background-position: center;
            background-blend-mode: overlay;
            border-radius: 16px;
            padding: 3rem;
            color: white;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        /* White text only for progress card content */
        .progress-card .progress-content *,
        .progress-card .progress-content h1,
        .progress-card .progress-content h2,
        .progress-card .progress-content h3,
        .progress-card .progress-content h4,
        .progress-card .progress-content .stat-value,
        .progress-card .progress-content .stat-label {
            color: white !important;
            text-shadow: 0 1px 3px rgba(0,0,0,0.7) !important;
        }

        /* All Other Cards - White Backgrounds with Black Text */
        .transactions-card:not(.progress-card),
        .rewards-section,
        .achievements-container,
        .card,
        .game-card,
        .reward-card,
        .achievement-card,
        .mini-game-item,
        .leaderboard-card,
        .challenge-card,
        .modal-content {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            color: #343a40 !important;
        }

        /* Black text for all regular cards */
        .transactions-card:not(.progress-card) *,
        .rewards-section *,
        .achievements-container *,
        .card *,
        .game-card *,
        .reward-card *,
        .achievement-card *,
        .mini-game-item *,
        .leaderboard-card *,
        .challenge-card *,
        .modal-content * {
            color: #343a40 !important;
        }

        /* Headers styling */
        .transactions-header h3,
        .transactions-header h4 {
            color: #343a40 !important;
            font-weight: 600;
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }

        /* Game Tab Buttons */
        .game-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .tab-btn {
            background: white;
            color: #343a40;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .tab-btn:hover,
        .tab-btn.active {
            background: #f8f9fa;
            border-color: #dee2e6;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Tab Content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Form Styling - Clean and Neutral */
        .form-control,
        .form-select,
        input,
        textarea,
        select {
            background: white !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
            color: #343a40 !important;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus,
        input:focus,
        textarea:focus,
        select:focus {
            border-color: #adb5bd !important;
            box-shadow: 0 0 0 0.2rem rgba(173, 181, 189, 0.25) !important;
            outline: none;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #6c757d;
            border-color: #6c757d;
            color: white;
        }

        .btn-primary:hover {
            background: #495057;
            border-color: #495057;
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
            z-index: 1040;
            backdrop-filter: blur(5px);
        }

        .loading-overlay .loading-spinner {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: #343a40;
        }

        /* Modals */
        .modal {
            z-index: 1055;
        }

        .modal-backdrop {
            z-index: 1050;
        }

        /* Notifications */
        #notification-container .notification {
            background: white !important;
            color: #343a40 !important;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }

        /* Mobile Responsiveness */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-280px);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
                margin-left: 0;
            }
            
            .game-tabs {
                flex-direction: column;
            }
            
            .tab-btn {
                text-align: center;
            }
        }

        /* Sidebar Overlay for Mobile */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Additional Professional Enhancements */
        .dashboard-card {
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Icon colors for cards */
        .card-icon i,
        .transactions-card:not(.progress-card) i,
        .rewards-section i,
        .achievements-container i {
            color: #6c757d !important;
        }

        /* Progress card icons stay white */
        .progress-card i {
            color: white !important;
        }

        /* Progress stats specific layout */
        .progress-stats {
            display: flex;
            gap: 1.5rem;
            margin-top: 2rem;
            justify-content: center;
        }

        .progress-stats .stat {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(255,255,255,0.15);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            min-width: 120px;
        }

        .progress-stats .stat i {
            font-size: 1.5rem;
            color: white;
        }

        .progress-stats .stat-value {
            font-size: 1.25rem;
            font-weight: bold;
            color: white;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .progress-stats .stat-label {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.9);
        }

        /* Level badge styling */
        .level-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 0.75rem 1.25rem;
            border-radius: 25px;
            backdrop-filter: blur(10px);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        /* XP Bar styling */
        .xp-bar {
            position: relative;
            height: 12px;
            background: rgba(255,255,255,0.2);
            border-radius: 6px;
            overflow: hidden;
            margin: 1rem 0;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .xp-fill {
            height: 100%;
            background: linear-gradient(90deg, #ffffff, rgba(255,255,255,0.8));
            border-radius: 6px;
            transition: width 0.5s ease;
            width: 0%;
        }

        .xp-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
            white-space: nowrap;
        }

        /* Game instructions styling */
        .game-instructions ul li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
            color: #343a40;
        }

        .game-instructions ul li i {
            color: #6c757d;
            width: 20px;
        }

        /* Badge styling */
        .badge {
            color: white !important;
        }

        .bg-success {
            background-color: #6c757d !important;
        }

        .bg-primary {
            background-color: #495057 !important;
        }

        /* Game container */
        .game-container {
            position: relative;
            display: flex;
            justify-content: center;
            margin: 2rem 0;
        }

        #flappy-canvas {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            background: #f8f9fa;
            max-width: 100%;
            height: auto;
        }

        .game-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.8);
            border-radius: 8px;
            color: white;
            text-align: center;
        }

        .game-start-screen,
        .game-over-screen {
            padding: 2rem;
        }

        .game-start-screen h4,
        .game-over-screen h4 {
            color: white;
            margin-bottom: 1rem;
        }

        .game-start-screen p,
        .game-over-screen p {
            color: rgba(255,255,255,0.9);
            margin-bottom: 0.5rem;
        }

        /* Celebration modal specific */
        .celebration-icon {
            color: #ffd700 !important;
        }

        .celebration-header h2 {
            color: #343a40 !important;
        }

        .celebration-header p {
            color: #6c757d !important;
        }

        /* Challenge Filter Buttons - Professional Styling */
        .challenges-filter {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 2rem;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            backdrop-filter: blur(10px);
            flex-wrap: wrap;
            justify-content: center;
        }

        .filter-btn {
            background: white;
            color: #6c757d;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            min-width: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(108, 117, 125, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .filter-btn:hover::before {
            left: 100%;
        }

        .filter-btn:hover {
            background: #f8f9fa;
            color: #495057;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-color: #dee2e6;
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            border-color: #495057;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(108, 117, 125, 0.3);
        }

        .filter-btn.active:hover {
            background: linear-gradient(135deg, #495057, #343a40);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.4);
        }

        /* Add icons to filter buttons */
        .filter-btn[data-filter="all"]::before {
            content: '📋';
            margin-right: 0.5rem;
        }

        .filter-btn[data-filter="daily"]::before {
            content: '☀️';
            margin-right: 0.5rem;
        }

        .filter-btn[data-filter="weekly"]::before {
            content: '📅';
            margin-right: 0.5rem;
        }

        .filter-btn[data-filter="milestone"]::before {
            content: '🏆';
            margin-right: 0.5rem;
        }

        /* Challenges Grid Styling */
        .challenges-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        /* Challenge Card Styling */
        .challenge-card {
            background: white !important;
            border: 1px solid #e9ecef !important;
            border-radius: 16px !important;
            padding: 2rem !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;
            position: relative;
            overflow: hidden;
        }

        .challenge-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #6c757d, #495057);
            border-radius: 16px 16px 0 0;
        }

        .challenge-card:hover {
            transform: translateY(-4px) !important;
            box-shadow: 0 12px 24px rgba(0,0,0,0.1) !important;
            border-color: #dee2e6 !important;
        }

        .challenge-card .challenge-header {
            margin-bottom: 1.5rem;
        }

        .challenge-card .challenge-header h5 {
            color: #343a40 !important;
            font-weight: 600 !important;
            font-size: 1.2rem !important;
            margin-bottom: 0.75rem !important;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .challenge-card .challenge-header p {
            color: #6c757d !important;
            font-size: 0.95rem !important;
            line-height: 1.5 !important;
            margin-bottom: 1rem !important;
        }

        /* Challenge Type Badges */
        .challenge-type {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .challenge-type.daily {
            background: linear-gradient(135deg, #ffeaa7, #fdcb6e);
            color: #2d3436;
        }

        .challenge-type.weekly {
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            color: white;
        }

        .challenge-type.milestone {
            background: linear-gradient(135deg, #fd79a8, #e84393);
            color: white;
        }

        /* Challenge Progress */
        .challenge-progress {
            margin: 1.5rem 0;
        }

        .challenge-progress .progress-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #495057;
            font-weight: 500;
        }

        .challenge-progress .progress-bar-container {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .challenge-progress .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #6c757d, #495057);
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        /* Challenge Rewards */
        .challenge-rewards {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin: 1.5rem 0;
        }

        .challenge-reward {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #495057;
        }

        .challenge-reward i {
            color: #6c757d !important;
        }

        /* Challenge Action Buttons */
        .challenge-action {
            width: 100%;
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.875rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .challenge-action::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .challenge-action:hover::before {
            left: 100%;
        }

        .challenge-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.3);
            background: linear-gradient(135deg, #495057, #343a40);
        }

        .challenge-action:disabled {
            background: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .challenge-action:disabled::before {
            display: none;
        }

        /* Challenge Status Indicators */
        .challenge-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .challenge-status.completed {
            background: #d4edda;
            color: #155724;
        }

        .challenge-status.in-progress {
            background: #fff3cd;
            color: #856404;
        }

        .challenge-status.available {
            background: #cce5ff;
            color: #004085;
        }

        /* Mobile Responsiveness for Challenges */
        @media (max-width: 768px) {
            .challenges-filter {
                flex-direction: column;
                gap: 0.5rem;
            }

            .filter-btn {
                min-width: auto;
                width: 100%;
            }

            .challenges-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .challenge-card {
                padding: 1.5rem !important;
            }
        }

        /* Enhanced Tab Button Styling */
        .game-tabs {
            background: rgba(255, 255, 255, 0.9);
            padding: 0.75rem;
            border-radius: 16px;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            backdrop-filter: blur(10px);
        }

        .tab-btn {
            background: white !important;
            color: #6c757d !important;
            border: 1px solid #e9ecef !important;
            border-radius: 10px !important;
            padding: 1rem 1.75rem !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tab-btn:hover {
            background: #f8f9fa !important;
            color: #495057 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 16px rgba(0,0,0,0.1) !important;
            border-color: #dee2e6 !important;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #6c757d, #495057) !important;
            color: white !important;
            border-color: #495057 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.3) !important;
        }

        .tab-btn.active:hover {
            background: linear-gradient(135deg, #495057, #343a40) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 10px 24px rgba(108, 117, 125, 0.4) !important;
        }

        .tab-btn i {
            color: inherit !important;
        }

        /* Modal Enhancements */
        .modal-content {
            border-radius: 16px !important;
            border: none !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        }

        .modal-header {
            background: linear-gradient(135deg, #6c757d, #495057) !important;
            color: white !important;
            border-radius: 16px 16px 0 0 !important;
            border-bottom: none !important;
            padding: 1.5rem 2rem !important;
        }

        .modal-header .modal-title {
            color: white !important;
            font-weight: 600 !important;
            font-size: 1.25rem !important;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1) !important;
            opacity: 0.8 !important;
        }

        .modal-header .btn-close:hover {
            opacity: 1 !important;
        }

        .modal-body {
            padding: 2rem !important;
            color: #343a40 !important;
        }

        .modal-body #modal-description {
            color: #6c757d !important;
            font-size: 1rem !important;
            line-height: 1.6 !important;
            margin-bottom: 1.5rem !important;
            padding: 1rem !important;
            background: #f8f9fa !important;
            border-radius: 8px !important;
            border-left: 4px solid #6c757d !important;
        }

        /* Modal Form Styling */
        .modal-form {
            margin: 1.5rem 0 !important;
        }

        .modal-form .form-group {
            margin-bottom: 1.5rem !important;
        }

        .modal-form label {
            color: #495057 !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            margin-bottom: 0.75rem !important;
            display: block !important;
        }

        .modal-form .form-control,
        .modal-form input,
        .modal-form textarea,
        .modal-form select {
            background: white !important;
            border: 2px solid #e9ecef !important;
            border-radius: 10px !important;
            color: #343a40 !important;
            padding: 0.875rem 1.25rem !important;
            font-size: 1rem !important;
            transition: all 0.3s ease !important;
            width: 100% !important;
        }

        .modal-form .form-control:focus,
        .modal-form input:focus,
        .modal-form textarea:focus,
        .modal-form select:focus {
            border-color: #6c757d !important;
            box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.15) !important;
            outline: none !important;
            background: white !important;
        }

        .modal-form .form-text {
            color: #6c757d !important;
            font-size: 0.875rem !important;
            margin-top: 0.5rem !important;
        }

        /* Modal Rewards Section */
        .modal-rewards {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;
            border-radius: 12px !important;
            padding: 1.5rem !important;
            margin: 1.5rem 0 !important;
            border: 1px solid #dee2e6 !important;
        }

        .modal-rewards h6 {
            color: #495057 !important;
            font-weight: 600 !important;
            margin-bottom: 1rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }

        .modal-rewards .reward-item {
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            background: white !important;
            border: 1px solid #e9ecef !important;
            border-radius: 8px !important;
            margin-bottom: 0.75rem !important;
            transition: all 0.3s ease !important;
        }

        .modal-rewards .reward-item:hover {
            transform: translateX(5px) !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
        }

        .modal-rewards .reward-item i {
            color: #6c757d !important;
            font-size: 1.1rem !important;
        }

        .modal-rewards .reward-item .reward-amount {
            font-weight: 600 !important;
            color: #495057 !important;
        }

        .modal-rewards .reward-item .reward-type {
            color: #6c757d !important;
            font-size: 0.9rem !important;
        }

        /* Modal Footer */
        .modal-footer {
            padding: 1.5rem 2rem !important;
            border-top: 1px solid #e9ecef !important;
            background: #f8f9fa !important;
            border-radius: 0 0 16px 16px !important;
        }

        .modal-footer .btn {
            padding: 0.75rem 1.5rem !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
        }

        .modal-footer .btn-secondary {
            background: #e9ecef !important;
            border-color: #e9ecef !important;
            color: #6c757d !important;
        }

        .modal-footer .btn-secondary:hover {
            background: #dee2e6 !important;
            border-color: #dee2e6 !important;
            color: #495057 !important;
            transform: translateY(-1px) !important;
        }

        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #6c757d, #495057) !important;
            border-color: #495057 !important;
            color: white !important;
        }

        .modal-footer .btn-primary:hover {
            background: linear-gradient(135deg, #495057, #343a40) !important;
            border-color: #343a40 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3) !important;
        }

        /* Celebration Modal Enhancements */
        .celebration-modal .modal-content {
            text-align: center !important;
        }

        .celebration-header {
            background: linear-gradient(135deg, #fff, #f8f9fa) !important;
            padding: 3rem 2rem !important;
            border-radius: 16px 16px 0 0 !important;
        }

        .celebration-header i {
            margin-bottom: 1rem !important;
            animation: bounce 2s infinite !important;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }

        .celebration-header h2 {
            color: #343a40 !important;
            font-weight: 700 !important;
            margin-bottom: 1rem !important;
            font-size: 2rem !important;
        }

        .celebration-header p {
            color: #6c757d !important;
            font-size: 1.1rem !important;
        }

        .celebration-rewards {
            padding: 2rem !important;
            background: #f8f9fa !important;
        }

        .celebration-rewards .reward-showcase {
            display: flex !important;
            justify-content: center !important;
            gap: 1.5rem !important;
            flex-wrap: wrap !important;
        }

        .celebration-rewards .reward-showcase-item {
            background: white !important;
            border: 2px solid #e9ecef !important;
            border-radius: 12px !important;
            padding: 1.5rem !important;
            min-width: 120px !important;
            text-align: center !important;
            transition: all 0.3s ease !important;
        }

        .celebration-rewards .reward-showcase-item:hover {
            transform: scale(1.05) !important;
            border-color: #6c757d !important;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
        }

        .celebration-rewards .reward-showcase-item i {
            font-size: 2rem !important;
            color: #6c757d !important;
            margin-bottom: 0.75rem !important;
        }

        .celebration-rewards .reward-showcase-item .reward-value {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #343a40 !important;
            margin-bottom: 0.5rem !important;
        }

        .celebration-rewards .reward-showcase-item .reward-label {
            font-size: 0.9rem !important;
            color: #6c757d !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        /* Enhanced Loading Overlay */
        .loading-overlay {
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
        }

        .loading-overlay .loading-spinner {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 16px !important;
            padding: 3rem !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        }

        .loading-overlay .loading-spinner .spinner-border {
            width: 3rem !important;
            height: 3rem !important;
            color: #6c757d !important;
        }

        .loading-overlay .loading-spinner p {
            color: #495057 !important;
            font-weight: 500 !important;
            margin-top: 1.5rem !important;
            font-size: 1.1rem !important;
        }

        /* Additional Professional Touches */
        .challenges-container {
            position: relative;
        }

        .challenges-container::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: linear-gradient(45deg, rgba(108, 117, 125, 0.05), rgba(173, 181, 189, 0.05));
            border-radius: 20px;
            z-index: -1;
        }

        /* Empty State Styling */
        .challenges-grid:empty::after {
            content: '🎯 No challenges available at the moment. Check back soon!';
            display: block;
            text-align: center;
            padding: 3rem 2rem;
            color: #6c757d;
            font-size: 1.1rem;
            font-weight: 500;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            margin: 2rem 0;
        }

        /* Achievement Grid Enhancement */
        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .achievement-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .achievement-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #ffd700, #ffed4e);
        }

        .achievement-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .achievement-item.locked {
            opacity: 0.6;
            background: #f8f9fa;
        }

        .achievement-item.locked::before {
            background: #dee2e6;
        }

        .achievement-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: block;
        }

        .achievement-item.unlocked .achievement-icon {
            color: #ffd700;
        }

        .achievement-item.locked .achievement-icon {
            color: #6c757d;
        }

        .achievement-title {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .achievement-description {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: 1rem;
        }

        .achievement-progress {
            background: #e9ecef;
            height: 6px;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 0.75rem;
        }

        .achievement-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #6c757d, #495057);
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .achievement-progress-text {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
        }

        /* Responsive Design Improvements */
        @media (max-width: 992px) {
            .main-content {
                padding: 1.5rem;
            }
            
            .progress-card .progress-content {
                padding: 2rem;
                min-height: 200px;
            }
            
            .progress-stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            .progress-stats .stat {
                min-width: auto;
            }
        }

        @media (max-width: 576px) {
            .game-tabs {
                padding: 0.5rem;
            }
            
            .tab-btn {
                padding: 0.75rem 1rem !important;
                font-size: 0.9rem !important;
            }
            
            .progress-card .progress-content {
                padding: 1.5rem;
            }
            
            .level-badge {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }

        /* Smooth Animations */
        * {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Focus States for Accessibility */
        .filter-btn:focus,
        .tab-btn:focus,
        .challenge-action:focus {
            outline: 2px solid rgba(108, 117, 125, 0.5);
            outline-offset: 2px;
        }

        /* Print Styles */
        @media print {
            .sidebar,
            .sidebar-overlay,
            .loading-overlay,
            .modal {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
                background: white !important;
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
                <div class="progress-content">
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
            <div class="challenges-container transactions-card">
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