<?php
/**
 * PayLekker - Complete Setup Script
 * 
 * This script automatically sets up the entire PayLekker application including:
 * - Database creation and configuration
 * - Table structure setup
 * - Game system initialization
 * - User wallet setup
 * - Demo data (optional)
 * 
 * Run this script once to get PayLekker up and running quickly.
 * 
 * Usage:
 *   php setup.php
 *   php setup.php --with-demo-data
 *   php setup.php --reset-database
 * 
 * @author PayLekker Development Team
 * @version 1.0.0
 */

// Set error reporting for setup visibility
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Colors for terminal output
class Colors {
    const GREEN = "\033[32m";
    const RED = "\033[31m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const CYAN = "\033[36m";
    const WHITE = "\033[37m";
    const BOLD = "\033[1m";
    const RESET = "\033[0m";
}

class PayLekkerSetup {
    private $pdo;
    private $config;
    private $isWebRequest;
    
    public function __construct() {
        $this->isWebRequest = isset($_SERVER['REQUEST_METHOD']);
        $this->loadConfiguration();
    }
    
    /**
     * Main setup orchestrator
     */
    public function run($options = []) {
        $this->printHeader();
        
        try {
            $this->step1_CheckRequirements();
            $this->step2_CreateDatabase();
            $this->step3_SetupTables();
            $this->step4_SetupGameSystem();
            $this->step5_SetupUserSystem();
            
            if (isset($options['with-demo-data'])) {
                $this->step6_LoadDemoData();
            }
            
            $this->step7_FinalizeSetup();
            $this->printSuccess();
            
        } catch (Exception $e) {
            $this->printError("Setup failed: " . $e->getMessage());
            exit(1);
        }
    }
    
    /**
     * Load database configuration
     */
    private function loadConfiguration() {
        // Default configuration
        $this->config = [
            'host' => 'localhost',
            'dbname' => 'paylekker',
            'username' => 'paylekker_user',
            'password' => 'secure_password',
            'charset' => 'utf8mb4'
        ];
        
        // Try to load from environment or config file
        if (file_exists(__DIR__ . '/src/database.php')) {
            include __DIR__ . '/src/database.php';
            if (isset($host, $dbname, $username, $password)) {
                $this->config = [
                    'host' => $host,
                    'dbname' => $dbname,
                    'username' => $username,
                    'password' => $password,
                    'charset' => 'utf8mb4'
                ];
            }
        }
    }
    
    /**
     * Step 1: Check system requirements
     */
    private function step1_CheckRequirements() {
        $this->printStep("Checking system requirements");
        
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            throw new Exception("PHP 7.4+ required. Current version: " . PHP_VERSION);
        }
        $this->printSubStep("✓ PHP version: " . PHP_VERSION);
        
        // Check required extensions
        $required = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
        foreach ($required as $ext) {
            if (!extension_loaded($ext)) {
                throw new Exception("Required PHP extension missing: $ext");
            }
            $this->printSubStep("✓ PHP extension: $ext");
        }
        
        // Check file permissions
        if (!is_writable(__DIR__ . '/src')) {
            $this->printWarning("Warning: src/ directory may not be writable");
        }
        
        $this->printSuccess("✓ System requirements check passed");
    }
    
    /**
     * Step 2: Create database and user
     */
    private function step2_CreateDatabase() {
        $this->printStep("Setting up database");
        
        try {
            // Connect to MySQL as root first
            $rootPdo = new PDO(
                "mysql:host={$this->config['host']};charset={$this->config['charset']}", 
                'root', 
                '', 
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            // Create database
            $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->config['dbname']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->printSubStep("✓ Database '{$this->config['dbname']}' created/verified");
            
            // Create user (ignore if exists)
            $rootPdo->exec("CREATE USER IF NOT EXISTS '{$this->config['username']}'@'localhost' IDENTIFIED BY '{$this->config['password']}'");
            $rootPdo->exec("GRANT ALL PRIVILEGES ON `{$this->config['dbname']}`.* TO '{$this->config['username']}'@'localhost'");
            $rootPdo->exec("FLUSH PRIVILEGES");
            $this->printSubStep("✓ Database user '{$this->config['username']}' configured");
            
        } catch (PDOException $e) {
            // Fallback: try to connect with provided credentials
            $this->printWarning("Note: Using existing database credentials");
        }
        
        // Connect to the application database
        $this->pdo = new PDO(
            "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['charset']}", 
            $this->config['username'], 
            $this->config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        
        $this->printSuccess("✓ Database connection established");
    }
    
    /**
     * Step 3: Setup main application tables
     */
    private function step3_SetupTables() {
        $this->printStep("Creating application tables");
        
        // Include and run main database setup
        $this->runSetupFile('src/setup_database.php');
        $this->printSubStep("✓ Main application tables created");
    }
    
    /**
     * Step 4: Setup game system
     */
    private function step4_SetupGameSystem() {
        $this->printStep("Setting up game system");
        
        // Include and run game setup
        $this->runSetupFile('src/setup_game_database.php');
        $this->printSubStep("✓ Game system tables created");
    }
    
    /**
     * Step 5: Setup user system
     */
    private function step5_SetupUserSystem() {
        $this->printStep("Setting up user wallet system");
        
        // Include and run wallet setup
        $this->runSetupFile('src/setup_user_wallet.php');
        $this->printSubStep("✓ User wallet system configured");
    }
    
    /**
     * Step 6: Load demo data (optional)
     */
    private function step6_LoadDemoData() {
        $this->printStep("Loading demo data");
        
        // Create demo users
        $demoUsers = [
            [
                'email' => 'demo@paylekker.co.za',
                'password' => password_hash('demo123', PASSWORD_DEFAULT),
                'first_name' => 'Demo',
                'last_name' => 'User',
                'phone' => '0821234567',
                'account_balance' => 5000.00
            ],
            [
                'email' => 'family@paylekker.co.za',
                'password' => password_hash('family123', PASSWORD_DEFAULT),
                'first_name' => 'Family',
                'last_name' => 'Account',
                'phone' => '0827654321',
                'account_balance' => 2500.00
            ]
        ];
        
        $stmt = $this->pdo->prepare("
            INSERT IGNORE INTO users (email, password, first_name, last_name, phone, account_balance, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        foreach ($demoUsers as $user) {
            $stmt->execute(array_values($user));
        }
        
        $this->printSubStep("✓ Demo users created");
        
        // Create some demo transactions
        $this->pdo->exec("
            INSERT IGNORE INTO transactions (sender_id, recipient_id, amount, type, status, reference, created_at) 
            SELECT 
                1 as sender_id, 
                2 as recipient_id, 
                250.00 as amount, 
                'transfer' as type, 
                'completed' as status, 
                'Demo transfer' as reference, 
                NOW() - INTERVAL 2 DAY as created_at
            WHERE EXISTS (SELECT 1 FROM users WHERE id = 1)
        ");
        
        $this->printSubStep("✓ Demo transactions created");
    }
    
    /**
     * Step 7: Finalize setup
     */
    private function step7_FinalizeSetup() {
        $this->printStep("Finalizing setup");
        
        // Create .htaccess for Apache (if needed)
        if (!file_exists(__DIR__ . '/src/.htaccess')) {
            $htaccess = "
RewriteEngine On
RewriteBase /

# Security Headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection \"1; mode=block\"

# API Routes
RewriteRule ^api/([^/]+)/?$ $1.php [L,QSA]

# Handle missing files
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [L,QSA]
";
            file_put_contents(__DIR__ . '/src/.htaccess', trim($htaccess));
            $this->printSubStep("✓ .htaccess file created");
        }
        
        // Test database connection
        $userCount = $this->pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $this->printSubStep("✓ Database test passed ($userCount users)");
        
        $this->printSuccess("✓ Setup finalized successfully");
    }
    
    /**
     * Run a setup file safely
     */
    private function runSetupFile($file) {
        if (!file_exists(__DIR__ . '/' . $file)) {
            throw new Exception("Setup file not found: $file");
        }
        
        // Capture output and suppress it during setup
        ob_start();
        
        // Provide database connection to included files
        $pdo = $this->pdo;
        
        try {
            include __DIR__ . '/' . $file;
        } catch (Exception $e) {
            ob_end_clean();
            throw new Exception("Error in $file: " . $e->getMessage());
        }
        
        ob_end_clean();
    }
    
    // Output formatting methods
    private function printHeader() {
        if ($this->isWebRequest) {
            echo "<h1>PayLekker Setup</h1><pre>";
        } else {
            echo Colors::CYAN . Colors::BOLD . "\n";
            echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
            echo "║                               PayLekker Setup                                ║\n";
            echo "║                        South Africa's Digital Cash App                       ║\n";
            echo "╚══════════════════════════════════════════════════════════════════════════════╝\n";
            echo Colors::RESET . "\n";
        }
    }
    
    private function printStep($message) {
        if ($this->isWebRequest) {
            echo "\n<strong>$message...</strong>\n";
        } else {
            echo Colors::BLUE . Colors::BOLD . "→ $message..." . Colors::RESET . "\n";
        }
        flush();
    }
    
    private function printSubStep($message) {
        if ($this->isWebRequest) {
            echo "  $message\n";
        } else {
            echo "  $message\n";
        }
        flush();
    }
    
    private function printSuccess($message = null) {
        if ($message) {
            if ($this->isWebRequest) {
                echo "<span style='color: green'>$message</span>\n";
            } else {
                echo Colors::GREEN . $message . Colors::RESET . "\n";
            }
        } else {
            if ($this->isWebRequest) {
                echo "\n<h2 style='color: green'>🎉 PayLekker Setup Complete!</h2>";
                echo "<p>Your PayLekker installation is ready to use.</p>";
                echo "<p><strong>Next steps:</strong></p>";
                echo "<ul>";
                echo "<li>Visit <a href='index.php'>your PayLekker dashboard</a></li>";
                echo "<li>Register a new account or login with demo credentials</li>";
                echo "<li>Start exploring South Africa's premier digital cash app!</li>";
                echo "</ul>";
                echo "</pre>";
            } else {
                echo "\n" . Colors::GREEN . Colors::BOLD;
                echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
                echo "║                          🎉 Setup Complete! 🎉                              ║\n";
                echo "║                                                                              ║\n";
                echo "║  PayLekker is now ready to use!                                             ║\n";
                echo "║                                                                              ║\n";
                echo "║  Next steps:                                                                 ║\n";
                echo "║  • Start your web server: php -S localhost:8000 -t src/                    ║\n";
                echo "║  • Visit: http://localhost:8000                                             ║\n";
                echo "║  • Register an account or use demo credentials                              ║\n";
                echo "║                                                                              ║\n";
                echo "║  Demo accounts:                                                              ║\n";
                echo "║  • demo@paylekker.co.za / demo123                                           ║\n";
                echo "║  • family@paylekker.co.za / family123                                       ║\n";
                echo "╚══════════════════════════════════════════════════════════════════════════════╝\n";
                echo Colors::RESET . "\n";
            }
        }
        flush();
    }
    
    private function printWarning($message) {
        if ($this->isWebRequest) {
            echo "<span style='color: orange'>⚠ $message</span>\n";
        } else {
            echo Colors::YELLOW . "⚠ $message" . Colors::RESET . "\n";
        }
        flush();
    }
    
    private function printError($message) {
        if ($this->isWebRequest) {
            echo "<span style='color: red'>✗ $message</span>\n";
        } else {
            echo Colors::RED . Colors::BOLD . "✗ $message" . Colors::RESET . "\n";
        }
        flush();
    }
}

// Handle command line arguments
$options = [];
if (!empty($argv)) {
    for ($i = 1; $i < count($argv); $i++) {
        switch ($argv[$i]) {
            case '--with-demo-data':
                $options['with-demo-data'] = true;
                break;
            case '--reset-database':
                $options['reset-database'] = true;
                break;
        }
    }
}

// Run setup
try {
    $setup = new PayLekkerSetup();
    $setup->run($options);
} catch (Exception $e) {
    if (isset($_SERVER['REQUEST_METHOD'])) {
        echo "<p style='color: red'><strong>Setup Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    } else {
        echo Colors::RED . "Setup Error: " . $e->getMessage() . Colors::RESET . "\n";
    }
    exit(1);
}