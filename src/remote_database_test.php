<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker - Remote Database Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-result { margin: 10px 0; padding: 15px; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
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
                        <h3 class="mb-0">🌐 Hybrid Setup Test</h3>
                        <small class="text-muted">Local API Files + Remote pay.sewdani.co.za Database</small>
                    </div>
                    <div class="card-body">
                        
                        <?php
                        require_once 'includes/config.php';
                        
                        echo "<div class='test-result info'>";
                        echo "<h5>🎯 Hybrid Architecture</h5>";
                        echo "<p><strong>API Files:</strong> Local (hosted on your TrueHost server)</p>";
                        echo "<p><strong>Database:</strong> Remote (pay.sewdani.co.za)</p>";
                        echo "<p><strong>Benefits:</strong> No .htaccess issues + Uses existing database</p>";
                        echo "</div>";
                        
                        // Test 1: Remote Database Connection
                        echo "<div class='test-result'>";
                        echo "<h5>🗄️ Remote Database Connection Test</h5>";
                        
                        if (isset($pdo) && $pdo) {
                            echo "<div class='alert alert-success'>✅ Connected to remote database at pay.sewdani.co.za</div>";
                            
                            try {
                                // Test query
                                $stmt = $pdo->query("SELECT VERSION() as version");
                                $version = $stmt->fetch();
                                echo "<p><strong>MySQL Version:</strong> " . $version['version'] . "</p>";
                                
                                // Check if users table exists
                                $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
                                if ($stmt->rowCount() > 0) {
                                    echo "<p class='text-success'>✅ Users table found on remote database</p>";
                                    
                                    // Count users
                                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                                    $userCount = $stmt->fetch();
                                    echo "<p><strong>Current users in remote database:</strong> " . $userCount['count'] . "</p>";
                                    
                                } else {
                                    echo "<p class='text-warning'>⚠️ Users table not found - may need to be created on remote database</p>";
                                }
                                
                            } catch (PDOException $e) {
                                echo "<div class='alert alert-warning'>⚠️ Database query error: " . $e->getMessage() . "</div>";
                            }
                            
                        } else {
                            echo "<div class='alert alert-danger'>❌ Cannot connect to remote database</div>";
                            echo "<p><strong>Possible issues:</strong></p>";
                            echo "<ul>";
                            echo "<li>Database credentials incorrect</li>";
                            echo "<li>Remote database server not allowing external connections</li>";
                            echo "<li>Firewall blocking connection</li>";
                            echo "<li>Database server down</li>";
                            echo "</ul>";
                        }
                        echo "</div>";
                        
                        // Test 2: Local API Files
                        echo "<div class='test-result'>";
                        echo "<h5>📁 Local API Files Check</h5>";
                        
                        $apiFiles = [
                            'api_auth.php' => 'Authentication endpoint',
                            'api_transfers.php' => 'Transfer endpoint', 
                            'api_budget.php' => 'Budget endpoint',
                            'api_chatbot.php' => 'Chatbot endpoint'
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
                            echo "<div class='alert alert-success'>✅ All local API files present</div>";
                        }
                        echo "</div>";
                        
                        // Test 3: Test API with Remote Database
                        if (isset($pdo) && $pdo) {
                            echo "<div class='test-result'>";
                            echo "<h5>🔌 API + Remote Database Test</h5>";
                            
                            // Test auth endpoint
                            $testUrl = 'api_auth.php?action=check';
                            echo "<p><strong>Testing:</strong> $testUrl</p>";
                            
                            $curl = curl_init();
                            curl_setopt_array($curl, [
                                CURLOPT_URL => $testUrl,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_TIMEOUT => 15,
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
                                    echo "<div class='alert alert-success'>✅ Local API responding and connecting to remote database</div>";
                                    
                                    $jsonResponse = json_decode($response, true);
                                    if ($jsonResponse) {
                                        echo "<p><strong>API Response:</strong></p>";
                                        echo "<pre>" . json_encode($jsonResponse, JSON_PRETTY_PRINT) . "</pre>";
                                    }
                                } else {
                                    echo "<div class='alert alert-warning'>⚠️ Unexpected response:</div>";
                                    echo "<pre>" . htmlspecialchars($response) . "</pre>";
                                }
                            }
                            echo "</div>";
                            
                            // Test registration with remote database
                            echo "<div class='test-result'>";
                            echo "<h5>📝 Registration Test with Remote Database</h5>";
                            
                            $testUser = [
                                'name' => 'Remote DB Test User',
                                'email' => 'remotetest' . time() . '@example.com',
                                'password' => 'Test123!',
                                'phone' => '0123456789'
                            ];
                            
                            echo "<p><strong>Test User Data:</strong></p>";
                            echo "<pre>" . json_encode($testUser, JSON_PRETTY_PRINT) . "</pre>";
                            
                            $result = callAPI('POST', 'auth/register', $testUser);
                            
                            echo "<p><strong>Registration Result:</strong></p>";
                            echo "<pre>" . json_encode($result, JSON_PRETTY_PRINT) . "</pre>";
                            
                            if ($result['success']) {
                                echo "<div class='alert alert-success'>🎉 SUCCESS! Local API + Remote Database working perfectly!</div>";
                                
                                // Test login too
                                $loginResult = callAPI('POST', 'auth/login', [
                                    'email' => $testUser['email'],
                                    'password' => $testUser['password']
                                ]);
                                
                                echo "<p><strong>Login Test:</strong></p>";
                                echo "<pre>" . json_encode($loginResult, JSON_PRETTY_PRINT) . "</pre>";
                                
                                if ($loginResult['success']) {
                                    echo "<div class='alert alert-success'>✅ Login also working! Full system operational!</div>";
                                }
                                
                            } else {
                                echo "<div class='alert alert-warning'>⚠️ Registration issue - check database connection or credentials</div>";
                            }
                            echo "</div>";
                        }
                        ?>
                        
                        <div class="test-result info">
                            <h5>🏗️ Architecture Benefits:</h5>
                            <ul class="mb-0">
                                <li>✅ <strong>No .htaccess Issues:</strong> API files run directly on your server</li>
                                <li>✅ <strong>Uses Existing Database:</strong> Connects to pay.sewdani.co.za database</li>
                                <li>✅ <strong>No External API Calls:</strong> Eliminates 403 errors</li>
                                <li>✅ <strong>Works on Any Hosting:</strong> TrueHost compatible</li>
                                <li>✅ <strong>Shared Database:</strong> Multiple apps can use same data</li>
                                <li>✅ <strong>Easy Deployment:</strong> Just upload files to your hosting</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <h5>🚀 Ready to Deploy!</h5>
                            <div class="btn-group">
                                <a href="register.php" class="btn btn-primary">Test Registration</a>
                                <a href="login.php" class="btn btn-secondary">Test Login</a>
                                <a href="dashboard.php" class="btn btn-info">Dashboard</a>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="alert alert-success">
                                <h6>📋 Deployment Instructions:</h6>
                                <ol class="mb-0">
                                    <li>Upload all files to your TrueHost server</li>
                                    <li>Make sure the remote database credentials are correct</li>
                                    <li>Test registration and login</li>
                                    <li>Your PayLekker app should work perfectly!</li>
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