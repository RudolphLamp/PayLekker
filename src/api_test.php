<?php
/**
 * PayLekker API Test Suite
 * Test the external sewdani.co.za API endpoints
 * Use this to debug registration and authentication issues
 */

// Set PHP display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include config to get API_BASE_URL
require_once __DIR__ . '/includes/config.php';

class PayLekkerAPITester {
    private $apiBaseUrl;
    private $testResults = [];
    
    public function __construct() {
        $this->apiBaseUrl = API_BASE_URL;
    }
    
    /**
     * Run all tests
     */
    public function runAllTests() {
        echo "<h1>🧪 PayLekker API Test Suite</h1>";
        echo "<p><strong>Testing API:</strong> {$this->apiBaseUrl}</p>";
        echo "<hr>";
        
        // Test API connectivity
        $this->testApiConnectivity();
        
        // Test registration endpoint
        $this->testRegistration();
        
        // Test login endpoint  
        $this->testLogin();
        
        // Test protected endpoints
        $this->testProtectedEndpoints();
        
        // Show summary
        $this->showTestSummary();
    }
    
    /**
     * Test API connectivity
     */
    private function testApiConnectivity() {
        echo "<h2>🌐 API Connectivity Test</h2>";
        
        // Test basic connectivity to the API server
        $response = $this->makeApiCall('', 'GET');
        
        if ($response !== null) {
            echo "<div style='color: green;'>✅ API server is reachable</div>";
            $this->testResults['connectivity'] = true;
        } else {
            echo "<div style='color: red;'>❌ Cannot reach API server</div>";
            echo "<p><strong>Check:</strong></p>";
            echo "<ul>";
            echo "<li>Internet connection</li>";
            echo "<li>API server status at pay.sewdani.co.za</li>";
            echo "<li>Firewall settings</li>";
            echo "</ul>";
            $this->testResults['connectivity'] = false;
        }
        
        echo "<hr>";
    }
    
    /**
     * Test registration endpoint
     */
    private function testRegistration() {
        echo "<h2>👤 Registration API Test</h2>";
        
        // Test different registration scenarios
        $testCases = [
            [
                'name' => 'Valid Registration Data',
                'data' => [
                    'first_name' => 'Test',
                    'last_name' => 'User',
                    'email' => 'test' . time() . '@example.com',
                    'password' => 'TestPass123!',
                    'phone' => '0123456789'
                ],
                'should_succeed' => true
            ],
            [
                'name' => 'Missing Required Fields',
                'data' => [
                    'first_name' => 'Test',
                    'email' => 'incomplete@example.com'
                    // Missing last_name and password
                ],
                'should_succeed' => false
            ],
            [
                'name' => 'Invalid Email Format',
                'data' => [
                    'first_name' => 'Test',
                    'last_name' => 'User',
                    'email' => 'invalid-email',
                    'password' => 'TestPass123!'
                ],
                'should_succeed' => false
            ],
            [
                'name' => 'Short Password',
                'data' => [
                    'first_name' => 'Test',
                    'last_name' => 'User',
                    'email' => 'shortpass' . time() . '@example.com',
                    'password' => '123'
                ],
                'should_succeed' => false
            ]
        ];
        
        $registrationPassed = true;
        
        foreach ($testCases as $i => $testCase) {
            echo "<h3>Test Case " . ($i + 1) . ": " . $testCase['name'] . "</h3>";
            
            // Try both possible endpoint formats
            $endpoints = ['auth/register', 'auth.php?action=register'];
            $success = false;
            
            foreach ($endpoints as $endpoint) {
                echo "<h4>Testing endpoint: " . $endpoint . "</h4>";
                
                $response = $this->makeApiCall($endpoint, 'POST', $testCase['data']);
                
                if ($response) {
                    echo "<div style='background: #f8f9fa; padding: 10px; border-left: 4px solid #007bff; margin: 10px 0;'>";
                    echo "<strong>Request:</strong><br>";
                    echo "<pre>" . json_encode($testCase['data'], JSON_PRETTY_PRINT) . "</pre>";
                    echo "<strong>Response:</strong><br>";
                    echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
                    echo "</div>";
                    
                    // Check if response indicates success
                    $apiSuccess = false;
                    if (isset($response['success']) && $response['success']) {
                        $apiSuccess = true;
                    } elseif (isset($response['data']['success']) && $response['data']['success']) {
                        $apiSuccess = true;
                    } elseif (isset($response['user']) || isset($response['token'])) {
                        $apiSuccess = true;
                    }
                    
                    if ($apiSuccess && $testCase['should_succeed']) {
                        echo "<div style='color: green;'>✅ Registration successful as expected</div>";
                        $success = true;
                        break;
                    } elseif (!$apiSuccess && !$testCase['should_succeed']) {
                        echo "<div style='color: green;'>✅ Registration properly rejected as expected</div>";
                        $success = true;
                        break;
                    } elseif ($apiSuccess && !$testCase['should_succeed']) {
                        echo "<div style='color: orange;'>⚠️ Registration succeeded but should have failed</div>";
                    } else {
                        echo "<div style='color: red;'>❌ Registration failed unexpectedly</div>";
                        if (isset($response['message'])) {
                            echo "<div style='color: red;'>Error: " . htmlspecialchars($response['message']) . "</div>";
                        }
                    }
                } else {
                    echo "<div style='color: red;'>❌ No response from endpoint: " . $endpoint . "</div>";
                }
                
                echo "<br>";
            }
            
            if (!$success) {
                $registrationPassed = false;
            }
            
            echo "<hr>";
        }
        
        $this->testResults['registration'] = $registrationPassed;
    }
    
    /**
     * Test login endpoint
     */
    private function testLogin() {
        echo "<h2>🔐 Login API Test</h2>";
        
        // First create a test user
        $testUser = [
            'first_name' => 'Login',
            'last_name' => 'Test',
            'email' => 'logintest' . time() . '@example.com',
            'password' => 'LoginTest123!',
            'phone' => '0987654321'
        ];
        
        echo "<h3>Creating test user for login tests...</h3>";
        
        // Try to register the test user
        $regResponse = null;
        $endpoints = ['auth/register', 'auth.php?action=register'];
        
        foreach ($endpoints as $endpoint) {
            $regResponse = $this->makeApiCall($endpoint, 'POST', $testUser);
            if ($regResponse && (
                (isset($regResponse['success']) && $regResponse['success']) ||
                (isset($regResponse['data']['success']) && $regResponse['data']['success']) ||
                isset($regResponse['user'])
            )) {
                echo "<div style='color: green;'>✅ Test user created successfully</div>";
                break;
            }
        }
        
        if (!$regResponse) {
            echo "<div style='color: red;'>❌ Could not create test user for login tests</div>";
            $this->testResults['login'] = false;
            echo "<hr>";
            return;
        }
        
        // Test login scenarios
        $loginTests = [
            [
                'name' => 'Correct Credentials',
                'data' => [
                    'email' => $testUser['email'],
                    'password' => $testUser['password']
                ],
                'should_succeed' => true
            ],
            [
                'name' => 'Wrong Password',
                'data' => [
                    'email' => $testUser['email'],
                    'password' => 'WrongPassword123!'
                ],
                'should_succeed' => false
            ],
            [
                'name' => 'Wrong Email',
                'data' => [
                    'email' => 'nonexistent@example.com',
                    'password' => $testUser['password']
                ],
                'should_succeed' => false
            ],
            [
                'name' => 'Missing Password',
                'data' => [
                    'email' => $testUser['email']
                ],
                'should_succeed' => false
            ]
        ];
        
        $loginPassed = true;
        
        foreach ($loginTests as $i => $test) {
            echo "<h3>Login Test " . ($i + 1) . ": " . $test['name'] . "</h3>";
            
            // Try both possible login endpoints
            $loginEndpoints = ['auth/login', 'auth.php?action=login'];
            $success = false;
            
            foreach ($loginEndpoints as $endpoint) {
                echo "<h4>Testing endpoint: " . $endpoint . "</h4>";
                
                $response = $this->makeApiCall($endpoint, 'POST', $test['data']);
                
                if ($response) {
                    echo "<div style='background: #f8f9fa; padding: 10px; border-left: 4px solid #28a745; margin: 10px 0;'>";
                    echo "<strong>Login Response:</strong><br>";
                    echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
                    echo "</div>";
                    
                    // Check if login was successful
                    $loginSuccess = false;
                    if (isset($response['success']) && $response['success']) {
                        $loginSuccess = true;
                    } elseif (isset($response['data']['success']) && $response['data']['success']) {
                        $loginSuccess = true;
                    } elseif (isset($response['token']) || isset($response['user'])) {
                        $loginSuccess = true;
                    }
                    
                    if ($loginSuccess && $test['should_succeed']) {
                        echo "<div style='color: green;'>✅ Login successful as expected</div>";
                        $success = true;
                        break;
                    } elseif (!$loginSuccess && !$test['should_succeed']) {
                        echo "<div style='color: green;'>✅ Login properly rejected as expected</div>";
                        $success = true;
                        break;
                    } elseif ($loginSuccess && !$test['should_succeed']) {
                        echo "<div style='color: orange;'>⚠️ Login succeeded but should have failed</div>";
                    } else {
                        echo "<div style='color: red;'>❌ Login failed unexpectedly</div>";
                    }
                } else {
                    echo "<div style='color: red;'>❌ No response from login endpoint: " . $endpoint . "</div>";
                }
                
                echo "<br>";
            }
            
            if (!$success) {
                $loginPassed = false;
            }
        }
        
        $this->testResults['login'] = $loginPassed;
        echo "<hr>";
    }
    
    /**
     * Test protected endpoints
     */
    private function testProtectedEndpoints() {
        echo "<h2>🔒 Protected Endpoints Test</h2>";
        
        // Test accessing protected endpoints without authentication
        $protectedEndpoints = [
            'transfers',
            'budget',
            'transactions',
            'chatbot'
        ];
        
        echo "<h3>Testing access without authentication...</h3>";
        $allBlocked = true;
        
        foreach ($protectedEndpoints as $endpoint) {
            $response = $this->makeApiCall($endpoint, 'GET');
            
            if ($response) {
                $blocked = false;
                
                // Check if properly blocked
                if (isset($response['success']) && !$response['success']) {
                    $blocked = true;
                } elseif (isset($response['data']['success']) && !$response['data']['success']) {
                    $blocked = true;
                } elseif (isset($response['error']) && stripos($response['error'], 'auth') !== false) {
                    $blocked = true;
                } elseif (isset($response['message']) && stripos($response['message'], 'auth') !== false) {
                    $blocked = true;
                }
                
                if ($blocked) {
                    echo "<div style='color: green;'>✅ " . $endpoint . " - Properly blocked</div>";
                } else {
                    echo "<div style='color: orange;'>⚠️ " . $endpoint . " - Should be blocked</div>";
                    $allBlocked = false;
                }
            } else {
                echo "<div style='color: red;'>❌ " . $endpoint . " - No response</div>";
                $allBlocked = false;
            }
        }
        
        $this->testResults['protected_endpoints'] = $allBlocked;
        echo "<hr>";
    }
    
    /**
     * Make API call using the same method as the frontend
     */
    private function makeApiCall($endpoint, $method = 'GET', $data = null, $token = null) {
        $url = $this->apiBaseUrl . ltrim($endpoint, '/');
        
        $ch = curl_init();
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: PayLekker-Test-Suite/1.0'
        ];
        
        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false, // For development
        ]);
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        echo "<div style='color: blue; font-size: 0.9em;'>📡 " . $method . " " . $url . " → HTTP " . $httpCode . "</div>";
        
        if ($error) {
            echo "<div style='color: red;'>❌ cURL Error: " . htmlspecialchars($error) . "</div>";
            return null;
        }
        
        if ($response) {
            $jsonResponse = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $jsonResponse;
            } else {
                echo "<div style='color: orange;'>⚠️ Non-JSON response (first 500 chars):</div>";
                echo "<pre style='max-height: 200px; overflow-y: auto;'>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
                return ['raw_response' => $response, 'http_code' => $httpCode];
            }
        }
        
        return ['http_code' => $httpCode];
    }
    
    /**
     * Show test summary
     */
    private function showTestSummary() {
        echo "<h2>📊 Test Summary</h2>";
        echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
        echo "<tr style='background: #f8f9fa;'><th>Test Category</th><th>Status</th><th>Notes</th></tr>";
        
        $statusMap = [
            'connectivity' => ['API Connectivity', 'Basic connection to sewdani.co.za API'],
            'registration' => ['User Registration', 'New user account creation'],
            'login' => ['User Login', 'Authentication with credentials'], 
            'protected_endpoints' => ['Protected Endpoints', 'Authorization checks']
        ];
        
        foreach ($this->testResults as $test => $result) {
            $info = $statusMap[$test] ?? [ucfirst($test), 'Test results'];
            $status = $result ? "<span style='color: green; font-weight: bold;'>✅ PASSED</span>" : "<span style='color: red; font-weight: bold;'>❌ FAILED</span>";
            echo "<tr><td>{$info[0]}</td><td>{$status}</td><td style='font-size: 0.9em;'>{$info[1]}</td></tr>";
        }
        
        echo "</table>";
        
        $passedTests = array_filter($this->testResults);
        $totalTests = count($this->testResults);
        $passedCount = count($passedTests);
        
        $overallColor = $passedCount === $totalTests ? 'green' : ($passedCount > 0 ? 'orange' : 'red');
        echo "<div style='background: #f8f9fa; border: 2px solid {$overallColor}; padding: 15px; margin: 20px 0;'>";
        echo "<h3 style='margin: 0; color: {$overallColor};'>Overall Result: {$passedCount}/{$totalTests} tests passed</h3>";
        echo "</div>";
        
        if ($passedCount < $totalTests) {
            echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin: 20px 0;'>";
            echo "<h3>🔧 Troubleshooting Guide:</h3>";
            echo "<ul>";
            echo "<li><strong>API Connection Issues:</strong> Check internet connection and API server status</li>";
            echo "<li><strong>Registration Failures:</strong> Verify API endpoint format (auth/register vs auth.php?action=register)</li>";
            echo "<li><strong>Authentication Problems:</strong> Check password requirements and email validation</li>";
            echo "<li><strong>Server Errors:</strong> API server may be experiencing issues</li>";
            echo "<li><strong>CORS Issues:</strong> May need to run tests from same domain as API</li>";
            echo "</ul>";
            echo "<p><strong>API Base URL:</strong> " . htmlspecialchars($this->apiBaseUrl) . "</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0;'>";
            echo "<h3 style='color: #155724;'>🎉 All tests passed!</h3>";
            echo "<p>Your PayLekker API is working correctly. Registration and login should work in your application.</p>";
            echo "</div>";
        }
    }
    
    /**
     * Quick manual test form
     */
    public function showManualTestForm() {
        echo "<div style='background: #e9ecef; padding: 20px; margin: 20px 0; border-radius: 5px;'>";
        echo "<h3>🧪 Manual API Test</h3>";
        echo "<p>Use this form to manually test specific API calls:</p>";
        
        echo "<form method='post' action='?' style='margin: 10px 0;'>";
        echo "<input type='hidden' name='manual_test' value='1'>";
        echo "<div style='margin: 10px 0;'>";
        echo "<label>Endpoint: </label>";
        echo "<input type='text' name='endpoint' placeholder='auth/register' style='padding: 5px; width: 200px;'>";
        echo "</div>";
        echo "<div style='margin: 10px 0;'>";
        echo "<label>Method: </label>";
        echo "<select name='method' style='padding: 5px;'>";
        echo "<option value='GET'>GET</option>";
        echo "<option value='POST'>POST</option>";
        echo "<option value='PUT'>PUT</option>";
        echo "<option value='DELETE'>DELETE</option>";
        echo "</select>";
        echo "</div>";
        echo "<div style='margin: 10px 0;'>";
        echo "<label>JSON Data: </label><br>";
        echo "<textarea name='data' rows='5' cols='50' placeholder='{\"email\":\"test@example.com\",\"password\":\"test123\"}'></textarea>";
        echo "</div>";
        echo "<div style='margin: 10px 0;'>";
        echo "<label>Auth Token: </label>";
        echo "<input type='text' name='token' placeholder='Bearer token (optional)' style='padding: 5px; width: 300px;'>";
        echo "</div>";
        echo "<button type='submit' style='padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 3px;'>Test API Call</button>";
        echo "</form>";
        echo "</div>";
        
        // Process manual test
        if (isset($_POST['manual_test'])) {
            $endpoint = $_POST['endpoint'] ?? '';
            $method = $_POST['method'] ?? 'GET';
            $data = $_POST['data'] ?? '';
            $token = $_POST['token'] ?? '';
            
            if ($endpoint) {
                echo "<h3>Manual Test Result:</h3>";
                $jsonData = $data ? json_decode($data, true) : null;
                $response = $this->makeApiCall($endpoint, $method, $jsonData, $token);
                
                if ($response) {
                    echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6;'>";
                    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    echo "</pre>";
                } else {
                    echo "<div style='color: red;'>No response received</div>";
                }
            }
        }
    }
}

// Auto-run tests if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'api_test.php') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PayLekker API Test Suite</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; margin: 20px; line-height: 1.6; background: #f8f9fa; }
            .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; border: 1px solid #dee2e6; }
            h1, h2, h3 { color: #333; }
            h1 { border-bottom: 3px solid #007bff; padding-bottom: 10px; }
            h2 { border-bottom: 2px solid #6c757d; padding-bottom: 5px; margin-top: 30px; }
            hr { margin: 30px 0; border: none; border-top: 2px solid #eee; }
            details { margin: 10px 0; }
            summary { cursor: pointer; font-weight: bold; padding: 5px; background: #e9ecef; border-radius: 3px; }
            table { border-collapse: collapse; width: 100%; }
            th, td { padding: 10px; text-align: left; border-bottom: 1px solid #dee2e6; }
            th { background: #f8f9fa; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="container">
    <?php
    
    $tester = new PayLekkerAPITester();
    
    // Show manual test form
    $tester->showManualTestForm();
    
    // Run automated tests
    $tester->runAllTests();
    
    ?>
        </div>
    </body>
    </html>
    <?php
}
?>