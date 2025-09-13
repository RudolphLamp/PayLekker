<?php
/**
 * PayLekker API Diagnostic Tool
 * Diagnose 403 Forbidden errors and API connectivity issues
 */

// Set PHP display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include config to get API_BASE_URL
require_once __DIR__ . '/includes/config.php';

class APIDiagnostic {
    private $apiBaseUrl;
    
    public function __construct() {
        $this->apiBaseUrl = API_BASE_URL;
    }
    
    public function runDiagnostics() {
        echo "<h1>🔍 PayLekker API Diagnostic Tool</h1>";
        echo "<p><strong>Target API:</strong> {$this->apiBaseUrl}</p>";
        echo "<hr>";
        
        $this->checkServerResponse();
        $this->checkCORS();
        $this->checkUserAgent();
        $this->checkEndpoints();
        $this->checkLocalAPI();
        $this->showRecommendations();
    }
    
    private function checkServerResponse() {
        echo "<h2>🌐 Server Response Analysis</h2>";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->apiBaseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => false,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'PayLekker-Test/1.0'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        echo "<div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 10px 0;'>";
        echo "<h3>Server Response Details:</h3>";
        echo "<ul>";
        echo "<li><strong>HTTP Status:</strong> " . $httpCode . "</li>";
        echo "<li><strong>Content Type:</strong> " . ($info['content_type'] ?? 'Unknown') . "</li>";
        echo "<li><strong>Server:</strong> " . ($info['server'] ?? 'Unknown') . "</li>";
        echo "<li><strong>Response Time:</strong> " . round($info['total_time'], 2) . "s</li>";
        echo "<li><strong>Final URL:</strong> " . ($info['url'] ?? 'Unknown') . "</li>";
        echo "</ul>";
        echo "</div>";
        
        if ($httpCode === 403) {
            echo "<div style='color: red;'>❌ <strong>403 Forbidden Error Detected</strong></div>";
            echo "<p>This usually means:</p>";
            echo "<ul>";
            echo "<li>The server is blocking your requests</li>";
            echo "<li>API endpoints don't exist at this location</li>";
            echo "<li>Server security is preventing API access</li>";
            echo "<li>Missing required authentication</li>";
            echo "</ul>";
        } elseif ($httpCode === 404) {
            echo "<div style='color: orange;'>⚠️ <strong>404 Not Found - API might not exist</strong></div>";
        } elseif ($httpCode >= 200 && $httpCode < 300) {
            echo "<div style='color: green;'>✅ <strong>Server responds successfully</strong></div>";
        }
        
        // Show first part of response
        if ($response) {
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headers = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            
            echo "<details style='margin: 10px 0;'>";
            echo "<summary><strong>Response Headers</strong></summary>";
            echo "<pre style='background: #f1f3f4; padding: 10px;'>" . htmlspecialchars($headers) . "</pre>";
            echo "</details>";
            
            echo "<details style='margin: 10px 0;'>";
            echo "<summary><strong>Response Body (first 1000 chars)</strong></summary>";
            echo "<pre style='background: #f1f3f4; padding: 10px;'>" . htmlspecialchars(substr($body, 0, 1000)) . "</pre>";
            echo "</details>";
        }
        
        echo "<hr>";
    }
    
    private function checkCORS() {
        echo "<h2>🌍 CORS (Cross-Origin) Test</h2>";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->apiBaseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_CUSTOMREQUEST => 'OPTIONS',
            CURLOPT_HTTPHEADER => [
                'Origin: ' . ($_SERVER['HTTP_HOST'] ?? 'localhost'),
                'Access-Control-Request-Method: POST',
                'Access-Control-Request-Headers: Content-Type'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "<p>Testing CORS preflight request...</p>";
        echo "<div style='color: blue;'>📡 OPTIONS request → HTTP " . $httpCode . "</div>";
        
        if (strpos($response, 'Access-Control-Allow-Origin') !== false) {
            echo "<div style='color: green;'>✅ CORS headers detected</div>";
        } else {
            echo "<div style='color: orange;'>⚠️ No CORS headers found - this might cause browser issues</div>";
        }
        
        echo "<hr>";
    }
    
    private function checkUserAgent() {
        echo "<h2>🤖 User Agent Test</h2>";
        
        $userAgents = [
            'PayLekker-App/1.0',
            'Mozilla/5.0 (compatible; PayLekker/1.0)',
            'curl/7.68.0'
        ];
        
        foreach ($userAgents as $ua) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $this->apiBaseUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_NOBODY => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_USERAGENT => $ua,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            echo "<div>User-Agent: <code>" . htmlspecialchars($ua) . "</code> → HTTP " . $httpCode . "</div>";
        }
        
        echo "<hr>";
    }
    
    private function checkEndpoints() {
        echo "<h2>🎯 Endpoint Accessibility Test</h2>";
        
        $endpoints = [
            '' => 'Root API',
            'auth' => 'Auth endpoint',
            'auth/register' => 'Registration',
            'auth/login' => 'Login',
            'users' => 'Users endpoint',
            'api.php' => 'API file',
            'index.php' => 'Index file'
        ];
        
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background: #f8f9fa;'><th>Endpoint</th><th>HTTP Status</th><th>Content Type</th><th>Notes</th></tr>";
        
        foreach ($endpoints as $endpoint => $description) {
            $url = $this->apiBaseUrl . $endpoint;
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_NOBODY => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_FOLLOWLOCATION => true
            ]);
            
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);
            
            $statusColor = $httpCode === 403 ? 'red' : ($httpCode === 404 ? 'orange' : ($httpCode >= 200 && $httpCode < 300 ? 'green' : 'gray'));
            $notes = $httpCode === 403 ? 'Forbidden' : ($httpCode === 404 ? 'Not Found' : ($httpCode >= 200 && $httpCode < 300 ? 'Accessible' : 'Error'));
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($description) . "<br><small><code>" . htmlspecialchars($endpoint) . "</code></small></td>";
            echo "<td style='color: {$statusColor}; font-weight: bold;'>" . $httpCode . "</td>";
            echo "<td>" . htmlspecialchars($contentType ?? 'Unknown') . "</td>";
            echo "<td>" . $notes . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "<hr>";
    }
    
    private function checkLocalAPI() {
        echo "<h2>🏠 Local API Setup Check</h2>";
        
        $localApiPath = __DIR__ . '/api';
        
        if (is_dir($localApiPath)) {
            echo "<div style='color: green;'>✅ Local API directory exists</div>";
            
            $apiFiles = glob($localApiPath . '/*.php');
            if ($apiFiles) {
                echo "<p><strong>Available local API files:</strong></p>";
                echo "<ul>";
                foreach ($apiFiles as $file) {
                    $filename = basename($file);
                    echo "<li><code>" . htmlspecialchars($filename) . "</code></li>";
                }
                echo "</ul>";
                
                echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 15px 0;'>";
                echo "<h4 style='color: #155724;'>💡 Recommendation: Use Local API</h4>";
                echo "<p>Since you have local API files and the remote API is giving 403 errors, you should:</p>";
                echo "<ol>";
                echo "<li><strong>Update your config.php</strong> to use local API endpoints</li>";
                echo "<li><strong>Set up your local database</strong> using web_setup.php</li>";
                echo "<li><strong>Use local development</strong> instead of the remote sewdani.co.za API</li>";
                echo "</ol>";
                
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $path = rtrim(dirname($_SERVER['REQUEST_URI']), '/');
                $localApiUrl = $protocol . '://' . $host . $path . '/api/';
                
                echo "<p><strong>Suggested local API URL:</strong> <code>" . htmlspecialchars($localApiUrl) . "</code></p>";
                echo "</div>";
                
            } else {
                echo "<div style='color: orange;'>⚠️ Local API directory is empty</div>";
            }
        } else {
            echo "<div style='color: red;'>❌ No local API directory found</div>";
        }
        
        echo "<hr>";
    }
    
    private function showRecommendations() {
        echo "<h2>🎯 Recommendations</h2>";
        
        echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; margin: 20px 0;'>";
        echo "<h3>🚨 API Connection Issues Detected</h3>";
        echo "<p>The remote API at <strong>pay.sewdani.co.za</strong> is returning 403 Forbidden errors. Here are your options:</p>";
        
        echo "<div style='margin: 20px 0;'>";
        echo "<h4>Option 1: Switch to Local API (Recommended)</h4>";
        echo "<ol>";
        echo "<li><strong>Update includes/config.php:</strong>";
        echo "<pre style='background: #f8f9fa; padding: 10px;'>// Change this line:
define('API_BASE_URL', 'https://pay.sewdani.co.za/api/');

// To this:
\$protocol = isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
\$host = \$_SERVER['HTTP_HOST'];
\$path = rtrim(dirname(\$_SERVER['SCRIPT_NAME']), '/');
define('API_BASE_URL', \$protocol . '://' . \$host . \$path . '/api/');</pre>";
        echo "</li>";
        echo "<li><strong>Run database setup:</strong> Access <code>web_setup.php</code> to create your local database</li>";
        echo "<li><strong>Test locally:</strong> Your app will use your own API files</li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<div style='margin: 20px 0;'>";
        echo "<h4>Option 2: Fix Remote API Access</h4>";
        echo "<ol>";
        echo "<li><strong>Contact API provider:</strong> Ask about 403 errors and proper access</li>";
        echo "<li><strong>Check authentication:</strong> API might require special headers or tokens</li>";
        echo "<li><strong>Verify endpoints:</strong> Confirm the correct API URLs and methods</li>";
        echo "</ol>";
        echo "</div>";
        
        echo "<div style='background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff;'>";
        echo "<h4>🎓 For Hackathon Success</h4>";
        echo "<p><strong>I recommend Option 1</strong> - using your local API. This gives you:</p>";
        echo "<ul>";
        echo "<li>Full control over your application</li>";
        echo "<li>No dependency on external services</li>";
        echo "<li>Ability to customize and debug</li>";
        echo "<li>Guaranteed availability during demo</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "</div>";
    }
}

// Auto-run diagnostics if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'api_test.php') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PayLekker API Diagnostic</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; margin: 20px; line-height: 1.6; background: #f8f9fa; }
            .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            pre { background: #f8f9fa; padding: 12px; border-radius: 5px; overflow-x: auto; border: 1px solid #dee2e6; font-size: 0.9em; }
            h1, h2, h3 { color: #333; }
            h1 { border-bottom: 3px solid #dc3545; padding-bottom: 10px; color: #dc3545; }
            h2 { border-bottom: 2px solid #6c757d; padding-bottom: 5px; margin-top: 30px; }
            hr { margin: 30px 0; border: none; border-top: 2px solid #eee; }
            details { margin: 15px 0; }
            summary { cursor: pointer; font-weight: bold; padding: 8px; background: #e9ecef; border-radius: 4px; }
            table { border-collapse: collapse; width: 100%; margin: 15px 0; }
            th, td { padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6; }
            th { background: #f8f9fa; font-weight: bold; }
            code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; font-family: 'Monaco', 'Consolas', monospace; }
        </style>
    </head>
    <body>
        <div class="container">
    <?php
    
    $diagnostic = new APIDiagnostic();
    $diagnostic->runDiagnostics();
    
    ?>
        </div>
    </body>
    </html>
    <?php
}
?>