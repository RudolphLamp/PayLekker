<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker - External API Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-result {
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
        }
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
                        <h3 class="mb-0">🌐 External API Test (No .htaccess needed)</h3>
                        <small class="text-muted">Testing direct PHP file calls to pay.sewdani.co.za/api/</small>
                    </div>
                    <div class="card-body">
                        
                        <?php
                        require_once 'includes/config.php';
                        
                        echo "<div class='test-result info'>";
                        echo "<h5>🎯 Testing Direct API Calls</h5>";
                        echo "<p><strong>API Base URL:</strong> " . API_BASE_URL . "</p>";
                        echo "<p><strong>Method:</strong> Direct PHP file calls (no routing needed)</p>";
                        echo "</div>";
                        
                        // Test 1: Check API endpoint directly
                        echo "<div class='test-result'>";
                        echo "<h5>🔗 API Endpoint Test</h5>";
                        
                        $testEndpoints = [
                            'auth/register' => 'register.php',
                            'auth/login' => 'login.php',
                            'auth/check' => 'check.php'
                        ];
                        
                        foreach ($testEndpoints as $endpoint => $phpFile) {
                            $url = rtrim(API_BASE_URL, '/') . '/' . $phpFile;
                            
                            echo "<p><strong>Testing:</strong> $endpoint → $url</p>";
                            
                            $curl = curl_init();
                            curl_setopt_array($curl, [
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_TIMEOUT => 10,
                                CURLOPT_CUSTOMREQUEST => 'GET',
                                CURLOPT_SSL_VERIFYPEER => false,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_USERAGENT => 'PayLekker-Test/1.0'
                            ]);
                            
                            $response = curl_exec($curl);
                            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            $error = curl_error($curl);
                            curl_close($curl);
                            
                            if ($error) {
                                echo "<div class='alert alert-danger'>❌ cURL Error: $error</div>";
                            } else {
                                $class = ($httpCode >= 200 && $httpCode < 400) ? 'alert-success' : 'alert-warning';
                                $icon = ($httpCode >= 200 && $httpCode < 400) ? '✅' : '⚠️';
                                
                                echo "<div class='alert $class'>$icon HTTP $httpCode</div>";
                                
                                if ($response) {
                                    $jsonResponse = json_decode($response, true);
                                    if ($jsonResponse) {
                                        echo "<pre>" . json_encode($jsonResponse, JSON_PRETTY_PRINT) . "</pre>";
                                    } else {
                                        echo "<pre>" . htmlspecialchars(substr($response, 0, 300)) . "</pre>";
                                    }
                                }
                            }
                            echo "<hr>";
                        }
                        echo "</div>";
                        
                        // Test 2: Try actual registration with external API
                        echo "<div class='test-result'>";
                        echo "<h5>📝 Registration Test</h5>";
                        
                        $testUser = [
                            'name' => 'Test User ' . date('His'),
                            'email' => 'test' . time() . '@example.com',
                            'password' => 'Test123!',
                            'phone' => '0123456789'
                        ];
                        
                        echo "<p><strong>Test User Data:</strong></p>";
                        echo "<pre>" . json_encode($testUser, JSON_PRETTY_PRINT) . "</pre>";
                        
                        $result = callAPI('POST', 'auth/register', $testUser);
                        
                        if ($result['success']) {
                            echo "<div class='alert alert-success'>✅ Registration API call successful!</div>";
                        } else {
                            echo "<div class='alert alert-warning'>⚠️ Registration response (HTTP " . $result['http_code'] . ")</div>";
                        }
                        
                        echo "<p><strong>API Response:</strong></p>";
                        echo "<pre>" . json_encode($result, JSON_PRETTY_PRINT) . "</pre>";
                        
                        echo "</div>";
                        
                        // Test 3: Try login check
                        echo "<div class='test-result'>";
                        echo "<h5>🔐 Login Check Test</h5>";
                        
                        $checkResult = callAPI('GET', 'auth/check');
                        
                        echo "<p><strong>Auth Check Response:</strong></p>";
                        echo "<pre>" . json_encode($checkResult, JSON_PRETTY_PRINT) . "</pre>";
                        
                        if ($checkResult['success']) {
                            echo "<div class='alert alert-success'>✅ Auth check endpoint responding</div>";
                        } else {
                            echo "<div class='alert alert-info'>ℹ️ Auth check response (expected without token)</div>";
                        }
                        
                        echo "</div>";
                        
                        // Test 4: Manual cURL test
                        echo "<div class='test-result'>";
                        echo "<h5>🛠️ Manual cURL Test</h5>";
                        echo "<p>Direct test without our callAPI function:</p>";
                        
                        $manualUrl = rtrim(API_BASE_URL, '/') . '/register.php';
                        
                        $curl = curl_init();
                        curl_setopt_array($curl, [
                            CURLOPT_URL => $manualUrl,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => json_encode($testUser),
                            CURLOPT_HTTPHEADER => [
                                'Content-Type: application/json',
                                'Accept: application/json',
                                'User-Agent: PayLekker-Manual-Test'
                            ],
                            CURLOPT_TIMEOUT => 15,
                            CURLOPT_SSL_VERIFYPEER => false
                        ]);
                        
                        $manualResponse = curl_exec($curl);
                        $manualHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        $manualError = curl_error($curl);
                        curl_close($curl);
                        
                        echo "<p><strong>URL:</strong> $manualUrl</p>";
                        echo "<p><strong>HTTP Code:</strong> $manualHttpCode</p>";
                        
                        if ($manualError) {
                            echo "<div class='alert alert-danger'>❌ Error: $manualError</div>";
                        } else {
                            $class = ($manualHttpCode >= 200 && $manualHttpCode < 400) ? 'alert-success' : 'alert-warning';
                            echo "<div class='alert $class'>Response received</div>";
                        }
                        
                        echo "<p><strong>Raw Response:</strong></p>";
                        echo "<pre>" . htmlspecialchars($manualResponse) . "</pre>";
                        
                        echo "</div>";
                        ?>
                        
                        <div class="mt-4">
                            <h5>🎯 Summary</h5>
                            <div class="alert alert-info">
                                <p><strong>This test verifies:</strong></p>
                                <ul class="mb-0">
                                    <li>✅ Direct PHP file access (no .htaccess routing needed)</li>
                                    <li>✅ External API connectivity to pay.sewdani.co.za</li>
                                    <li>✅ Registration endpoint functionality</li>
                                    <li>✅ JSON data transmission</li>
                                    <li>✅ HTTP response handling</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h5>🔗 Test Your App</h5>
                            <div class="btn-group">
                                <a href="register.php" class="btn btn-primary">Try Registration</a>
                                <a href="login.php" class="btn btn-secondary">Try Login</a>
                                <a href="debug_api.php" class="btn btn-info">Debug Tool</a>
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