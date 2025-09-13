<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>PayLekker API Debug</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 4px solid #007cba; }
        .error { background: #ffe6e6; border-left-color: #d00; }
        .success { background: #e6ffe6; border-left-color: #0a0; }
        .warning { background: #fff3cd; border-left-color: #ffc107; }
        pre { background: #f8f8f8; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 PayLekker API Debug Tool</h1>
    
    <?php
    echo "<div class='debug'><strong>Current Directory:</strong> " . getcwd() . "</div>";
    echo "<div class='debug'><strong>Server:</strong> " . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "</div>";
    echo "<div class='debug'><strong>PHP Version:</strong> " . PHP_VERSION . "</div>";
    
    // Check file existence
    $files = ['includes/config.php', 'api_router.php', 'includes/database.php'];
    foreach ($files as $file) {
        $exists = file_exists($file);
        $class = $exists ? 'success' : 'error';
        $icon = $exists ? '✅' : '❌';
        echo "<div class='debug $class'>$icon <strong>$file:</strong> " . ($exists ? 'EXISTS' : 'MISSING') . "</div>";
    }
    
    // Test database connection
    echo "<h2>Database Test</h2>";
    try {
        if (file_exists('includes/config.php')) {
            require_once 'includes/config.php';
            echo "<div class='debug success'>✅ Config loaded successfully</div>";
            
            if (isset($pdo)) {
                $test = $pdo->query("SELECT 1");
                echo "<div class='debug success'>✅ Database connected</div>";
                
                // Check users table
                try {
                    $users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                    echo "<div class='debug success'>✅ Users table exists ($users users)</div>";
                } catch (Exception $e) {
                    echo "<div class='debug error'>❌ Users table issue: " . $e->getMessage() . "</div>";
                }
            } else {
                echo "<div class='debug error'>❌ PDO not initialized</div>";
            }
        } else {
            echo "<div class='debug error'>❌ Config file missing</div>";
        }
    } catch (Exception $e) {
        echo "<div class='debug error'>❌ Database error: " . $e->getMessage() . "</div>";
    }
    
    // Test API Router directly
    echo "<h2>API Router Test</h2>";
    
    if (file_exists('api_router.php')) {
        echo "<div class='debug success'>✅ API Router file exists</div>";
        
        // Capture output from API router
        ob_start();
        $originalGet = $_GET;
        $_GET = ['endpoint' => 'auth', 'action' => 'check'];
        
        try {
            include 'api_router.php';
            $output = ob_get_clean();
            
            echo "<div class='debug success'>✅ API Router executed without fatal errors</div>";
            echo "<div class='debug'><strong>Output:</strong><pre>" . htmlspecialchars($output) . "</pre></div>";
            
            // Try to decode as JSON
            $json = json_decode($output, true);
            if ($json) {
                echo "<div class='debug success'>✅ Valid JSON response</div>";
                echo "<div class='debug'><strong>Parsed JSON:</strong><pre>" . print_r($json, true) . "</pre></div>";
            } else {
                echo "<div class='debug warning'>⚠️ Output is not valid JSON</div>";
            }
            
        } catch (Exception $e) {
            ob_end_clean();
            echo "<div class='debug error'>❌ API Router error: " . $e->getMessage() . "</div>";
        }
        
        $_GET = $originalGet;
    } else {
        echo "<div class='debug error'>❌ API Router file missing</div>";
    }
    
    // Test specific registration
    echo "<h2>Registration Test</h2>";
    
    if (isset($pdo)) {
        $testData = [
            'name' => 'Debug Test User',
            'email' => 'debug' . time() . '@test.com',
            'password' => 'Test123!',
            'phone' => '0123456789'
        ];
        
        echo "<div class='debug'><strong>Test Data:</strong><pre>" . print_r($testData, true) . "</pre></div>";
        
        // Manual registration test
        try {
            $hashedPassword = password_hash($testData['password'], PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, created_at) VALUES (?, ?, ?, ?, NOW())");
            $result = $stmt->execute([
                $testData['name'],
                $testData['email'],
                $hashedPassword,
                $testData['phone']
            ]);
            
            if ($result) {
                $userId = $pdo->lastInsertId();
                echo "<div class='debug success'>✅ Manual registration successful (User ID: $userId)</div>";
                
                // Clean up test user
                $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
                echo "<div class='debug'>🧹 Test user cleaned up</div>";
            } else {
                echo "<div class='debug error'>❌ Manual registration failed</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='debug error'>❌ Registration test error: " . $e->getMessage() . "</div>";
        }
    }
    
    // Show server environment
    echo "<h2>Server Environment</h2>";
    $serverInfo = [
        'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'Not set',
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'Not set',
        'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? 'Not set',
        'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? 'Not set',
        'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'] ?? 'Not set',
        'HTTPS' => isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'Not set'
    ];
    
    foreach ($serverInfo as $key => $value) {
        echo "<div class='debug'>$key: <strong>$value</strong></div>";
    }
    
    // Show PHP modules
    echo "<h2>PHP Modules</h2>";
    $requiredModules = ['pdo', 'pdo_mysql', 'curl', 'json', 'openssl'];
    foreach ($requiredModules as $module) {
        $loaded = extension_loaded($module);
        $class = $loaded ? 'success' : 'error';
        $icon = $loaded ? '✅' : '❌';
        echo "<div class='debug $class'>$icon <strong>$module:</strong> " . ($loaded ? 'LOADED' : 'MISSING') . "</div>";
    }
    ?>
    
    <h2>🔗 Actions</h2>
    <p>
        <a href="simple_test.php">Run Simple Test</a> |
        <a href="api_test.php">Full API Test</a> |
        <a href="register.php">Try Registration</a>
    </p>
    
</body>
</html>