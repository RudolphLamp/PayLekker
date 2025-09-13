<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker - 403 Error Diagnostic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .test-result { margin: 10px 0; padding: 15px; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; max-height: 300px; overflow-y: auto; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">🚨 403 Error Diagnostic Tool</h3>
                        <small class="text-muted">Analyzing why pay.sewdani.co.za/api/ is returning 403 Forbidden</small>
                    </div>
                    <div class="card-body">
                        
                        <?php
                        require_once 'includes/config.php';
                        
                        echo "<div class='test-result info'>";
                        echo "<h5>🔍 Your Server Information</h5>";
                        echo "<p><strong>Hosting Provider:</strong> TrueHost</p>";
                        echo "<p><strong>Your Server IP:</strong> " . $_SERVER['SERVER_ADDR'] ?? 'Unknown' . "</p>";
                        echo "<p><strong>Your Domain:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
                        echo "<p><strong>User Agent:</strong> " . $_SERVER['HTTP_USER_AGENT'] ?? 'Not set' . "</p>";
                        echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' . "</p>";
                        echo "</div>";
                        
                        // Test different approaches to the API
                        $apiTests = [
                            'Direct register.php' => 'https://pay.sewdani.co.za/api/register.php',
                            'API base URL' => 'https://pay.sewdani.co.za/api/',
                            'Without HTTPS' => 'http://pay.sewdani.co.za/api/register.php'
                        ];
                        
                        foreach ($apiTests as $testName => $url) {
                            echo "<div class='test-result'>";
                            echo "<h5>🌐 Testing: $testName</h5>";
                            echo "<p><strong>URL:</strong> $url</p>";
                            
                            $curl = curl_init();
                            curl_setopt_array($curl, [
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_TIMEOUT => 15,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_SSL_VERIFYPEER => false,
                                CURLOPT_USERAGENT => 'PayLekker-TrueHost-Test/1.0',
                                CURLOPT_HEADER => true,
                                CURLOPT_NOBODY => false,
                                CURLOPT_CUSTOMREQUEST => 'GET'
                            ]);
                            
                            $response = curl_exec($curl);
                            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            $error = curl_error($curl);
                            $info = curl_getinfo($curl);
                            curl_close($curl);
                            
                            if ($error) {
                                echo "<div class='alert alert-danger'>❌ Connection Error: $error</div>";
                            } else {
                                $class = ($httpCode == 403) ? 'alert-danger' : (($httpCode >= 200 && $httpCode < 400) ? 'alert-success' : 'alert-warning');
                                echo "<div class='alert $class'>HTTP Status: $httpCode</div>";
                                
                                if ($httpCode == 403) {
                                    echo "<div class='alert alert-info'>🚨 This is the 403 Forbidden error you're experiencing</div>";
                                }
                                
                                // Show response headers
                                $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
                                $headers = substr($response, 0, $headerSize);
                                $body = substr($response, $headerSize);
                                
                                echo "<p><strong>Response Headers:</strong></p>";
                                echo "<pre>" . htmlspecialchars($headers) . "</pre>";
                                
                                if ($body && strlen($body) < 1000) {
                                    echo "<p><strong>Response Body:</strong></p>";
                                    echo "<pre>" . htmlspecialchars($body) . "</pre>";
                                }
                            }
                            echo "</div>";
                        }
                        
                        // Test with different HTTP methods
                        echo "<div class='test-result'>";
                        echo "<h5>🔧 Testing Different HTTP Methods</h5>";
                        
                        $methods = ['GET', 'POST', 'OPTIONS'];
                        $testUrl = 'https://pay.sewdani.co.za/api/register.php';
                        
                        foreach ($methods as $method) {
                            echo "<p><strong>$method Request:</strong></p>";
                            
                            $curl = curl_init();
                            curl_setopt_array($curl, [
                                CURLOPT_URL => $testUrl,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_TIMEOUT => 10,
                                CURLOPT_CUSTOMREQUEST => $method,
                                CURLOPT_SSL_VERIFYPEER => false,
                                CURLOPT_HTTPHEADER => [
                                    'Content-Type: application/json',
                                    'Accept: application/json',
                                    'Origin: https://' . $_SERVER['HTTP_HOST'],
                                    'Referer: https://' . $_SERVER['HTTP_HOST'],
                                ],
                            ]);
                            
                            if ($method == 'POST') {
                                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
                                    'name' => 'Test User',
                                    'email' => 'test@example.com',
                                    'password' => 'test123'
                                ]));
                            }
                            
                            $response = curl_exec($curl);
                            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            $error = curl_error($curl);
                            curl_close($curl);
                            
                            $class = ($httpCode == 403) ? 'text-danger' : (($httpCode >= 200 && $httpCode < 400) ? 'text-success' : 'text-warning');
                            echo "<span class='$class'>HTTP $httpCode</span>";
                            
                            if ($error) {
                                echo " - Error: $error";
                            }
                            echo "<br>";
                        }
                        echo "</div>";
                        
                        // Check if the API domain is accessible at all
                        echo "<div class='test-result'>";
                        echo "<h5>🏠 Domain Accessibility Check</h5>";
                        
                        $domains = [
                            'Main domain' => 'https://pay.sewdani.co.za',
                            'API subdirectory' => 'https://pay.sewdani.co.za/api',
                            'Root of sewdani.co.za' => 'https://sewdani.co.za'
                        ];
                        
                        foreach ($domains as $name => $domain) {
                            $curl = curl_init();
                            curl_setopt_array($curl, [
                                CURLOPT_URL => $domain,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_TIMEOUT => 10,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_SSL_VERIFYPEER => false,
                                CURLOPT_NOBODY => true // HEAD request only
                            ]);
                            
                            curl_exec($curl);
                            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            $error = curl_error($curl);
                            curl_close($curl);
                            
                            echo "<p><strong>$name ($domain):</strong> ";
                            if ($error) {
                                echo "<span class='text-danger'>❌ $error</span>";
                            } else {
                                $class = ($httpCode >= 200 && $httpCode < 400) ? 'text-success' : 'text-warning';
                                echo "<span class='$class'>HTTP $httpCode</span>";
                            }
                            echo "</p>";
                        }
                        echo "</div>";
                        ?>
                        
                        <div class="test-result warning">
                            <h5>🤔 Possible Causes of 403 Forbidden:</h5>
                            <ul>
                                <li><strong>CORS Policy:</strong> The API doesn't allow requests from your domain</li>
                                <li><strong>IP Blocking:</strong> TrueHost IPs might be blocked</li>
                                <li><strong>Authentication Required:</strong> The API needs special headers or tokens</li>
                                <li><strong>Rate Limiting:</strong> Too many requests from your server</li>
                                <li><strong>User Agent Filtering:</strong> The API blocks certain user agents</li>
                                <li><strong>Referer Checking:</strong> The API requires specific referer headers</li>
                            </ul>
                        </div>
                        
                        <div class="test-result info">
                            <h5>💡 Potential Solutions:</h5>
                            <ol>
                                <li><strong>Contact API Owner:</strong> Ask sewdani.co.za to whitelist your domain/IP</li>
                                <li><strong>Use Different Hosting:</strong> Try a different hosting provider</li>
                                <li><strong>Proxy Request:</strong> Route through a different server</li>
                                <li><strong>Build Your Own API:</strong> Create local PHP API instead</li>
                                <li><strong>Add Headers:</strong> Try different authentication headers</li>
                            </ol>
                        </div>
                        
                        <div class="mt-4">
                            <div class="alert alert-warning">
                                <h6>🎯 Recommended Next Steps:</h6>
                                <p>Since you're getting 403 errors from the external API, I recommend switching to a <strong>local API solution</strong> that will work reliably with TrueHost.</p>
                                <p>Would you like me to set up a local database and API system instead?</p>
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