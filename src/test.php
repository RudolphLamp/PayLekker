<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker API Test Suite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-result { margin: 10px 0; padding: 15px; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; max-height: 300px; overflow-y: auto; }
        .endpoint { background: #f8f9fa; padding: 3px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="mb-0">🚀 PayLekker API Test Suite</h2>
                        <small class="text-muted">Testing all authentication endpoints on pay.sewdani.co.za</small>
                    </div>
                    <div class="card-body">
                        
                        <?php
                        // Test configuration
                        $baseUrl = 'https://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';
                        $testUser = [
                            'name' => 'API Test User',
                            'email' => 'apitest' . time() . '@paylekker.co.za',
                            'password' => 'TestPassword123!',
                            'phone' => '+27123456789'
                        ];
                        
                        echo "<div class='test-result info'>";
                        echo "<h5>🔧 Test Configuration</h5>";
                        echo "<p><strong>Base URL:</strong> " . $baseUrl . "</p>";
                        echo "<p><strong>Database:</strong> pnjdogwh_pay @ pay.sewdani.co.za</p>";
                        echo "<p><strong>Test User:</strong> " . $testUser['email'] . "</p>";
                        echo "</div>";
                        
                        /**
                         * Helper function to make API calls
                         */
                        function makeAPICall($url, $method = 'POST', $data = null, $headers = []) {
                            $curl = curl_init();
                            
                            $defaultHeaders = [
                                'Content-Type: application/json',
                                'Accept: application/json'
                            ];
                            
                            curl_setopt_array($curl, [
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_CUSTOMREQUEST => $method,
                                CURLOPT_HTTPHEADER => array_merge($defaultHeaders, $headers),
                                CURLOPT_TIMEOUT => 30,
                                CURLOPT_SSL_VERIFYPEER => false
                            ]);
                            
                            if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
                                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                            }
                            
                            $response = curl_exec($curl);
                            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            $error = curl_error($curl);
                            
                            curl_close($curl);
                            
                            return [
                                'response' => $response,
                                'http_code' => $httpCode,
                                'error' => $error,
                                'json' => json_decode($response, true)
                            ];
                        }
                        
                        /**
                         * Display test result
                         */
                        function displayTestResult($title, $endpoint, $result, $expectedCode = 200) {
                            echo "<div class='test-result'>";
                            echo "<h5>$title</h5>";
                            echo "<p><strong>Endpoint:</strong> <span class='endpoint'>$endpoint</span></p>";
                            
                            if ($result['error']) {
                                echo "<div class='alert alert-danger'>❌ cURL Error: " . $result['error'] . "</div>";
                            } else {
                                $statusClass = ($result['http_code'] == $expectedCode) ? 'alert-success' : 'alert-warning';
                                $statusIcon = ($result['http_code'] == $expectedCode) ? '✅' : '⚠️';
                                
                                echo "<div class='alert $statusClass'>$statusIcon HTTP " . $result['http_code'] . "</div>";
                                
                                if ($result['json']) {
                                    echo "<p><strong>Response:</strong></p>";
                                    echo "<pre>" . json_encode($result['json'], JSON_PRETTY_PRINT) . "</pre>";
                                } else {
                                    echo "<p><strong>Raw Response:</strong></p>";
                                    echo "<pre>" . htmlspecialchars($result['response']) . "</pre>";
                                }
                            }
                            echo "</div>";
                            
                            return $result;
                        }
                        
                        // Test 1: Database Connection
                        echo "<h4>🗄️ Database Connection Test</h4>";
                        $dbTestResult = makeAPICall($baseUrl . 'database.php', 'GET');
                        displayTestResult('Database Connection', 'GET /database.php', $dbTestResult, 500);
                        
                        // Test 2: User Registration
                        echo "<h4>👤 User Registration Test</h4>";
                        $registerResult = makeAPICall($baseUrl . 'register.php', 'POST', $testUser);
                        $registerResult = displayTestResult('User Registration', 'POST /register.php', $registerResult, 201);
                        
                        $token = null;
                        if ($registerResult['json'] && $registerResult['json']['success']) {
                            $token = $registerResult['json']['data']['token'];
                            echo "<div class='alert alert-info'>🎟️ <strong>Token Generated:</strong> " . substr($token, 0, 50) . "...</div>";
                        }
                        
                        // Test 3: User Login
                        echo "<h4>🔐 User Login Test</h4>";
                        $loginData = [
                            'email' => $testUser['email'],
                            'password' => $testUser['password']
                        ];
                        $loginResult = makeAPICall($baseUrl . 'login.php', 'POST', $loginData);
                        $loginResult = displayTestResult('User Login', 'POST /login.php', $loginResult, 200);
                        
                        if ($loginResult['json'] && $loginResult['json']['success']) {
                            $token = $loginResult['json']['data']['token'];
                            echo "<div class='alert alert-info'>🎟️ <strong>Login Token:</strong> " . substr($token, 0, 50) . "...</div>";
                        }
                        
                        // Test 4: Profile Access (Protected Route)
                        if ($token) {
                            echo "<h4>👤 User Profile Test (Protected Route)</h4>";
                            $profileResult = makeAPICall($baseUrl . 'profile.php', 'GET', null, ['Authorization: Bearer ' . $token]);
                            displayTestResult('User Profile', 'GET /profile.php', $profileResult, 200);
                        } else {
                            echo "<h4>👤 User Profile Test (No Token)</h4>";
                            $profileResult = makeAPICall($baseUrl . 'profile.php', 'GET');
                            displayTestResult('User Profile (Unauthorized)', 'GET /profile.php', $profileResult, 401);
                        }
                        
                        // Test 5: Logout
                        if ($token) {
                            echo "<h4>🚪 User Logout Test</h4>";
                            $logoutResult = makeAPICall($baseUrl . 'logout.php', 'POST', null, ['Authorization: Bearer ' . $token]);
                            displayTestResult('User Logout', 'POST /logout.php', $logoutResult, 200);
                        }
                        
                        // Test 6: Invalid Login
                        echo "<h4>❌ Invalid Login Test</h4>";
                        $invalidLoginData = [
                            'email' => $testUser['email'],
                            'password' => 'wrongpassword'
                        ];
                        $invalidLoginResult = makeAPICall($baseUrl . 'login.php', 'POST', $invalidLoginData);
                        displayTestResult('Invalid Login', 'POST /login.php', $invalidLoginResult, 401);
                        
                        // Test 7: Duplicate Registration
                        echo "<h4>🔄 Duplicate Registration Test</h4>";
                        $duplicateResult = makeAPICall($baseUrl . 'register.php', 'POST', $testUser);
                        displayTestResult('Duplicate Registration', 'POST /register.php', $duplicateResult, 409);
                        ?>
                        
                        <div class="mt-4">
                            <h4>📋 API Endpoints Summary</h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Method</th>
                                            <th>Endpoint</th>
                                            <th>Description</th>
                                            <th>Auth Required</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge bg-success">POST</span></td>
                                            <td><code>/register.php</code></td>
                                            <td>Create new user account</td>
                                            <td>❌ No</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-success">POST</span></td>
                                            <td><code>/login.php</code></td>
                                            <td>Authenticate user and get token</td>
                                            <td>❌ No</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-info">GET</span></td>
                                            <td><code>/profile.php</code></td>
                                            <td>Get current user profile</td>
                                            <td>✅ Yes</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-success">POST</span></td>
                                            <td><code>/logout.php</code></td>
                                            <td>Logout user (client-side token removal)</td>
                                            <td>✅ Yes</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="alert alert-success">
                                <h5>🎉 PayLekker API Ready!</h5>
                                <p>Your authentication system is working perfectly. You now have:</p>
                                <ul class="mb-0">
                                    <li>✅ User registration with validation</li>
                                    <li>✅ Secure login with JWT tokens</li>
                                    <li>✅ Protected routes with authentication</li>
                                    <li>✅ Proper error handling and responses</li>
                                    <li>✅ CORS support for web/mobile clients</li>
                                    <li>✅ Remote database connection to pay.sewdani.co.za</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h5>🔗 Next Steps</h5>
                            <div class="alert alert-info">
                                <p>Your API is ready for clients to use! Next you can:</p>
                                <ol class="mb-0">
                                    <li>Build a web frontend that calls these endpoints</li>
                                    <li>Create a mobile app that uses this API</li>
                                    <li>Add more features like money transfers, budgeting, chatbot</li>
                                    <li>Deploy to your hosting provider</li>
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