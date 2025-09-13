<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker - Local API Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-result { margin: 10px 0; padding: 15px; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; max-height: 300px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">✅ Local API Test - No External Dependencies!</h3>
                        <small class="text-muted">Testing local API files in src/ root - works on any hosting!</small>
                    </div>
                    <div class="card-body">
                        
                        <?php
                        require_once 'includes/config.php';
                        
                        echo "<div class='test-result info'>";
                        echo "<h5>🏠 Local API Setup</h5>";
                        echo "<p><strong>Method:</strong> Direct local PHP files (no .htaccess, no external API needed)</p>";
                        echo "<p><strong>API Files:</strong> api_auth.php, api_transfers.php, api_budget.php, api_chatbot.php</p>";
                        echo "<p><strong>Database:</strong> Local MySQL database</p>";
                        echo "</div>";
                        
                        // Test 1: Check if local API files exist
                        echo "<div class='test-result'>";
                        echo "<h5>📁 API Files Check</h5>";
                        
                        $apiFiles = [
                            'api_auth.php' => 'Authentication (register, login, check)',
                            'api_transfers.php' => 'Money transfers and history',
                            'api_budget.php' => 'Budget management',
                            'api_chatbot.php' => 'Financial chatbot'
                        ];
                        
                        $allFilesExist = true;
                        foreach ($apiFiles as $file => $description) {
                            $exists = file_exists($file);
                            $class = $exists ? 'text-success' : 'text-danger';
                            $icon = $exists ? '✅' : '❌';
                            echo "<p class='$class'>$icon <strong>$file:</strong> $description</p>";
                            if (!$exists) $allFilesExist = false;
                        }
                        
                        if ($allFilesExist) {
                            echo "<div class='alert alert-success'>✅ All API files are present and ready!</div>";
                        } else {
                            echo "<div class='alert alert-danger'>❌ Some API files are missing</div>";
                        }
                        echo "</div>";
                        
                        // Test 2: Database connection
                        echo "<div class='test-result'>";
                        echo "<h5>🗄️ Database Test</h5>";
                        if (isset($pdo) && $pdo) {
                            echo "<div class='alert alert-success'>✅ Database connected successfully</div>";
                            
                            // Check tables
                            try {
                                $tables = ['users', 'transactions', 'budget_categories'];
                                foreach ($tables as $table) {
                                    $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                                    if ($stmt->rowCount() > 0) {
                                        echo "<p class='text-success'>✅ Table '$table' exists</p>";
                                    } else {
                                        echo "<p class='text-warning'>⚠️ Table '$table' not found - will be created automatically</p>";
                                    }
                                }
                            } catch (Exception $e) {
                                echo "<p class='text-warning'>⚠️ Could not check tables: " . $e->getMessage() . "</p>";
                            }
                            
                        } else {
                            echo "<div class='alert alert-warning'>⚠️ Database not connected - update includes/database.php with your database credentials</div>";
                        }
                        echo "</div>";
                        
                        // Test 3: Test API endpoints directly
                        echo "<div class='test-result'>";
                        echo "<h5>🔌 Local API Endpoint Test</h5>";
                        
                        // Test auth endpoint
                        $testUrl = 'api_auth.php?action=check';
                        echo "<p><strong>Testing:</strong> $testUrl</p>";
                        
                        $curl = curl_init();
                        curl_setopt_array($curl, [
                            CURLOPT_URL => $testUrl,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_TIMEOUT => 10,
                            CURLOPT_FOLLOWLOCATION => true
                        ]);
                        
                        $response = curl_exec($curl);
                        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        $error = curl_error($curl);
                        curl_close($curl);
                        
                        if ($error) {
                            echo "<div class='alert alert-danger'>❌ cURL Error: $error</div>";
                        } else {
                            echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
                            
                            if ($httpCode == 200 || $httpCode == 401) {
                                echo "<div class='alert alert-success'>✅ API endpoint responding correctly</div>";
                                $jsonResponse = json_decode($response, true);
                                if ($jsonResponse) {
                                    echo "<p><strong>Response:</strong></p>";
                                    echo "<pre>" . json_encode($jsonResponse, JSON_PRETTY_PRINT) . "</pre>";
                                }
                            } else {
                                echo "<div class='alert alert-warning'>⚠️ Unexpected response code</div>";
                                echo "<pre>" . htmlspecialchars($response) . "</pre>";
                            }
                        }
                        echo "</div>";
                        
                        // Test 4: Test registration via callAPI function
                        if (isset($pdo) && $pdo) {
                            echo "<div class='test-result'>";
                            echo "<h5>📝 Registration Test via callAPI</h5>";
                            
                            $testUser = [
                                'name' => 'Local Test User',
                                'email' => 'localtest' . time() . '@example.com',
                                'password' => 'Test123!',
                                'phone' => '0123456789'
                            ];
                            
                            echo "<p><strong>Test User:</strong></p>";
                            echo "<pre>" . json_encode($testUser, JSON_PRETTY_PRINT) . "</pre>";
                            
                            $result = callAPI('POST', 'auth/register', $testUser);
                            
                            echo "<p><strong>Registration Result:</strong></p>";
                            echo "<pre>" . json_encode($result, JSON_PRETTY_PRINT) . "</pre>";
                            
                            if ($result['success']) {
                                echo "<div class='alert alert-success'>✅ Local API registration working perfectly!</div>";
                                
                                // Test login too
                                $loginResult = callAPI('POST', 'auth/login', [
                                    'email' => $testUser['email'],
                                    'password' => $testUser['password']
                                ]);
                                
                                echo "<p><strong>Login Test Result:</strong></p>";
                                echo "<pre>" . json_encode($loginResult, JSON_PRETTY_PRINT) . "</pre>";
                                
                                if ($loginResult['success']) {
                                    echo "<div class='alert alert-success'>✅ Login also working perfectly!</div>";
                                    
                                    // Clean up test user
                                    $pdo->prepare("DELETE FROM users WHERE email = ?")->execute([$testUser['email']]);
                                    echo "<p class='text-muted'>🧹 Test user cleaned up</p>";
                                }
                            } else {
                                echo "<div class='alert alert-warning'>⚠️ Registration test result - check database configuration</div>";
                            }
                            echo "</div>";
                        }
                        ?>
                        
                        <div class="test-result success">
                            <h5>🎉 Local API Advantages:</h5>
                            <ul class="mb-0">
                                <li>✅ <strong>No External Dependencies:</strong> Works without internet API</li>
                                <li>✅ <strong>No .htaccess Issues:</strong> Direct PHP file calls</li>
                                <li>✅ <strong>Works on Any Hosting:</strong> Compatible with all PHP hosting</li>
                                <li>✅ <strong>Faster Response:</strong> No network delays</li>
                                <li>✅ <strong>Full Control:</strong> You own and control the entire system</li>
                                <li>✅ <strong>Easy Debugging:</strong> All code is accessible</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <h5>🚀 Ready to Use!</h5>
                            <div class="btn-group">
                                <a href="register.php" class="btn btn-primary">Test Registration</a>
                                <a href="login.php" class="btn btn-secondary">Test Login</a>
                                <a href="setup_local_api.php" class="btn btn-info">Setup Database</a>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="alert alert-info">
                                <h6>📋 Next Steps:</h6>
                                <ol class="mb-0">
                                    <li>If database is not connected, visit <strong>setup_local_api.php</strong></li>
                                    <li>Configure your database credentials in <strong>includes/database.php</strong></li>
                                    <li>Test registration and login</li>
                                    <li>Upload to your TrueHost server</li>
                                    <li>Enjoy your fully working PayLekker app! 🎉</li>
                                </ol>
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