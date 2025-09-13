<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker - Simple API Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-result {
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">🧪 PayLekker API Simple Test</h3>
                        <small class="text-muted">Testing hosting-friendly API router</small>
                    </div>
                    <div class="card-body">
                        
                        <?php
                        // Include config for database connection
                        require_once 'includes/config.php';
                        
                        echo "<div class='test-result info'>";
                        echo "<h5>📋 Test Environment</h5>";
                        echo "<p><strong>Server:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
                        echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
                        echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
                        echo "</div>";
                        
                        // Test 1: Check if API router file exists
                        echo "<div class='test-result " . (file_exists('api_router.php') ? 'success' : 'error') . "'>";
                        echo "<h5>📁 File Check</h5>";
                        if (file_exists('api_router.php')) {
                            echo "<p>✅ api_router.php exists</p>";
                        } else {
                            echo "<p>❌ api_router.php not found</p>";
                        }
                        echo "</div>";
                        
                        // Test 2: Database connection
                        echo "<div class='test-result'>";
                        echo "<h5>🗄️ Database Connection</h5>";
                        try {
                            $testQuery = $pdo->query("SELECT 1");
                            echo "<p class='text-success'>✅ Database connected successfully</p>";
                            
                            // Check if users table exists
                            $tableCheck = $pdo->query("SHOW TABLES LIKE 'users'");
                            if ($tableCheck->rowCount() > 0) {
                                echo "<p class='text-success'>✅ Users table exists</p>";
                                
                                // Count existing users
                                $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                                echo "<p class='text-info'>📊 Current users in database: " . $userCount . "</p>";
                            } else {
                                echo "<p class='text-warning'>⚠️ Users table not found</p>";
                            }
                            
                        } catch (Exception $e) {
                            echo "<p class='text-danger'>❌ Database error: " . $e->getMessage() . "</p>";
                        }
                        echo "</div>";
                        
                        // Test 3: API Router Direct Test
                        echo "<div class='test-result'>";
                        echo "<h5>🔌 API Router Test</h5>";
                        
                        $testUrl = "api_router.php?endpoint=auth&action=check";
                        
                        // Use cURL to test the API
                        $curl = curl_init();
                        curl_setopt_array($curl, [
                            CURLOPT_URL => $testUrl,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_TIMEOUT => 10,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_SSL_VERIFYPEER => false,
                        ]);
                        
                        $response = curl_exec($curl);
                        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        $error = curl_error($curl);
                        curl_close($curl);
                        
                        if ($error) {
                            echo "<p class='text-danger'>❌ cURL Error: " . $error . "</p>";
                        } else {
                            echo "<p><strong>URL Tested:</strong> <code>" . $testUrl . "</code></p>";
                            echo "<p><strong>HTTP Response Code:</strong> " . $httpCode . "</p>";
                            
                            if ($httpCode == 200) {
                                echo "<p class='text-success'>✅ API Router responding</p>";
                                
                                $jsonResponse = json_decode($response, true);
                                if ($jsonResponse) {
                                    echo "<p class='text-success'>✅ JSON response valid</p>";
                                    echo "<p><strong>Response:</strong></p>";
                                    echo "<pre>" . json_encode($jsonResponse, JSON_PRETTY_PRINT) . "</pre>";
                                } else {
                                    echo "<p class='text-warning'>⚠️ Response not valid JSON</p>";
                                    echo "<p><strong>Raw response:</strong></p>";
                                    echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
                                }
                            } else {
                                echo "<p class='text-danger'>❌ API Router error (HTTP " . $httpCode . ")</p>";
                                echo "<p><strong>Response:</strong></p>";
                                echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
                            }
                        }
                        echo "</div>";
                        
                        // Test 4: Test Registration Endpoint
                        echo "<div class='test-result'>";
                        echo "<h5>📝 Registration Test</h5>";
                        echo "<p>Test with a sample user registration:</p>";
                        
                        $testData = [
                            'name' => 'Test User ' . time(),
                            'email' => 'test' . time() . '@example.com',
                            'password' => 'Test123!',
                            'phone' => '0123456789'
                        ];
                        
                        $regCurl = curl_init();
                        curl_setopt_array($regCurl, [
                            CURLOPT_URL => "api_router.php?endpoint=auth&action=register",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => json_encode($testData),
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_TIMEOUT => 10,
                            CURLOPT_SSL_VERIFYPEER => false,
                        ]);
                        
                        $regResponse = curl_exec($regCurl);
                        $regHttpCode = curl_getinfo($regCurl, CURLINFO_HTTP_CODE);
                        $regError = curl_error($regCurl);
                        curl_close($regCurl);
                        
                        if ($regError) {
                            echo "<p class='text-danger'>❌ Registration cURL Error: " . $regError . "</p>";
                        } else {
                            echo "<p><strong>Test Data:</strong> " . json_encode($testData) . "</p>";
                            echo "<p><strong>HTTP Code:</strong> " . $regHttpCode . "</p>";
                            
                            $regJsonResponse = json_decode($regResponse, true);
                            if ($regJsonResponse) {
                                if (isset($regJsonResponse['success']) && $regJsonResponse['success']) {
                                    echo "<p class='text-success'>✅ Registration test successful!</p>";
                                } else {
                                    echo "<p class='text-info'>ℹ️ Registration response (may be expected):</p>";
                                }
                                echo "<pre>" . json_encode($regJsonResponse, JSON_PRETTY_PRINT) . "</pre>";
                            } else {
                                echo "<p class='text-warning'>⚠️ Non-JSON response:</p>";
                                echo "<pre>" . htmlspecialchars(substr($regResponse, 0, 500)) . "</pre>";
                            }
                        }
                        echo "</div>";
                        ?>
                        
                        <div class="mt-4">
                            <h5>🔗 Quick Links</h5>
                            <div class="btn-group">
                                <a href="register.php" class="btn btn-primary">Go to Registration</a>
                                <a href="login.php" class="btn btn-secondary">Go to Login</a>
                                <a href="api_test.php" class="btn btn-info">Full API Test</a>
                                <a href="api_diagnostic.php" class="btn btn-warning">API Diagnostics</a>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="alert alert-info">
                                <h6>✅ What This Test Checks:</h6>
                                <ul class="mb-0">
                                    <li>API router file exists</li>
                                    <li>Database connection works</li>
                                    <li>Users table exists</li>
                                    <li>API endpoints respond correctly</li>
                                    <li>JSON responses are valid</li>
                                    <li>Registration functionality works</li>
                                </ul>
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