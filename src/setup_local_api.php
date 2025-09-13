<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker - Setup Local API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">🔧 Setup Local API for TrueHost</h3>
                        <small class="text-muted">Configure your database and switch to local API</small>
                    </div>
                    <div class="card-body">
                        
                        <?php
                        // Database setup form
                        if ($_POST) {
                            $host = $_POST['db_host'] ?? 'localhost';
                            $dbname = $_POST['db_name'] ?? '';
                            $username = $_POST['db_user'] ?? '';
                            $password = $_POST['db_pass'] ?? '';
                            
                            if ($host && $dbname && $username && $password) {
                                // Test connection
                                try {
                                    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
                                    $testPdo = new PDO($dsn, $username, $password, [
                                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                                    ]);
                                    
                                    echo "<div class='alert alert-success'>✅ Database connection successful!</div>";
                                    
                                    // Update database.php file
                                    $dbConfig = "<?php
/**
 * PayLekker Database Configuration - TrueHost Setup
 */

\$db_config = [
    'host' => '$host',
    'dbname' => '$dbname',
    'username' => '$username',
    'password' => '$password',
    'charset' => 'utf8mb4'
];

try {
    \$dsn = \"mysql:host={\$db_config['host']};dbname={\$db_config['dbname']};charset={\$db_config['charset']}\";
    \$pdo = new PDO(\$dsn, \$db_config['username'], \$db_config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    // Create tables
    \$pdo->exec(\"
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            balance DECIMAL(10,2) DEFAULT 0.00,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    \");
    
    \$pdo->exec(\"
        CREATE TABLE IF NOT EXISTS transactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            type ENUM('transfer', 'deposit', 'withdrawal') NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            recipient_email VARCHAR(100),
            description TEXT,
            status ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    \");
    
} catch (PDOException \$e) {
    error_log('Database error: ' . \$e->getMessage());
    \$pdo = null;
}
?>";
                                    
                                    if (file_put_contents('includes/database.php', $dbConfig)) {
                                        echo "<div class='alert alert-success'>✅ Database configuration saved!</div>";
                                        
                                        // Update config.php to use local API
                                        $configPath = 'includes/config.php';
                                        if (file_exists($configPath)) {
                                            $configContent = file_get_contents($configPath);
                                            
                                            // Replace API_BASE_URL to point to local
                                            $configContent = str_replace(
                                                "define('API_BASE_URL', 'https://pay.sewdani.co.za/api/');",
                                                "define('API_BASE_URL', 'LOCAL'); // Use local API",
                                                $configContent
                                            );
                                            
                                            // Add database include
                                            if (strpos($configContent, 'require_once') === false) {
                                                $configContent = str_replace(
                                                    "<?php",
                                                    "<?php\nrequire_once __DIR__ . '/database.php';",
                                                    $configContent
                                                );
                                            }
                                            
                                            file_put_contents($configPath, $configContent);
                                            echo "<div class='alert alert-success'>✅ Config updated to use local API!</div>";
                                        }
                                        
                                        echo "<div class='alert alert-info'>";
                                        echo "<h5>🎉 Setup Complete!</h5>";
                                        echo "<p>Your PayLekker app is now configured to use local database instead of external API.</p>";
                                        echo "<a href='register.php' class='btn btn-primary'>Test Registration</a>";
                                        echo "</div>";
                                        
                                    } else {
                                        echo "<div class='alert alert-danger'>❌ Could not save database configuration</div>";
                                    }
                                    
                                } catch (PDOException $e) {
                                    echo "<div class='alert alert-danger'>❌ Database connection failed: " . $e->getMessage() . "</div>";
                                }
                            }
                        }
                        ?>
                        
                        <?php if (!$_POST || !empty($error)): ?>
                        <form method="POST">
                            <div class="mb-4">
                                <h5>📊 TrueHost Database Configuration</h5>
                                <p class="text-muted">Enter your TrueHost database details (found in cPanel):</p>
                            </div>
                            
                            <div class="mb-3">
                                <label for="db_host" class="form-label">Database Host</label>
                                <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                                <div class="form-text">Usually "localhost" for TrueHost</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="db_name" class="form-label">Database Name</label>
                                <input type="text" class="form-control" id="db_name" name="db_name" placeholder="your_database_name" required>
                                <div class="form-text">Your MySQL database name from TrueHost cPanel</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="db_user" class="form-label">Database Username</label>
                                <input type="text" class="form-control" id="db_user" name="db_user" placeholder="your_db_username" required>
                                <div class="form-text">Your MySQL username from TrueHost cPanel</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="db_pass" class="form-label">Database Password</label>
                                <input type="password" class="form-control" id="db_pass" name="db_pass" placeholder="your_db_password" required>
                                <div class="form-text">Your MySQL password from TrueHost cPanel</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">💾 Setup Local Database</button>
                        </form>
                        
                        <div class="mt-4">
                            <div class="alert alert-info">
                                <h6>📝 How to find your TrueHost database details:</h6>
                                <ol>
                                    <li>Login to your TrueHost cPanel</li>
                                    <li>Go to "MySQL Databases" section</li>
                                    <li>Create a new database (or use existing)</li>
                                    <li>Create a database user and assign to database</li>
                                    <li>Use those credentials above</li>
                                </ol>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <h5>🔍 Diagnostic Tools</h5>
                            <div class="btn-group">
                                <a href="403_diagnostic.php" class="btn btn-outline-warning">403 Diagnostic</a>
                                <a href="external_api_test.php" class="btn btn-outline-info">External API Test</a>
                                <a href="debug_api.php" class="btn btn-outline-secondary">General Debug</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>