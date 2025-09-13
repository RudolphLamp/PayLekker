<?php
/**
 * PayLekker API Configuration Switcher
 * Quick tool to switch between local and remote API
 */

require_once __DIR__ . '/includes/config.php';

$configFile = __DIR__ . '/includes/config.php';
$currentConfig = file_get_contents($configFile);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiType = $_POST['api_type'] ?? 'local';
    
    if ($apiType === 'local') {
        // Switch to local API
        $newConfig = preg_replace(
            "/define\('API_BASE_URL',\s*'[^']+'\);/",
            "// Auto-detect local API URL\n\$protocol = isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] === 'on' ? 'https' : 'http';\n\$host = \$_SERVER['HTTP_HOST'];\n\$path = rtrim(dirname(\$_SERVER['SCRIPT_NAME']), '/');\ndefine('API_BASE_URL', \$protocol . '://' . \$host . \$path . '/api/');",
            $currentConfig
        );
    } else {
        // Switch to remote API
        $newConfig = preg_replace(
            "/\/\/ Auto-detect local API URL.*?define\('API_BASE_URL',[^;]+\);/s",
            "define('API_BASE_URL', 'https://pay.sewdani.co.za/api/');",
            $currentConfig
        );
    }
    
    if ($newConfig && $newConfig !== $currentConfig) {
        file_put_contents($configFile, $newConfig);
        $message = "✅ API configuration updated to " . ($apiType === 'local' ? 'LOCAL' : 'REMOTE');
        $messageType = 'success';
    } else {
        $message = "❌ Failed to update configuration";
        $messageType = 'error';
    }
}

// Check current API type
$isLocal = strpos($currentConfig, 'Auto-detect local API URL') !== false;
$currentApiType = $isLocal ? 'local' : 'remote';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker API Configuration</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; margin: 20px; line-height: 1.6; background: #f8f9fa; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .alert { padding: 15px; margin: 15px 0; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .card { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #007bff; }
        .current { border-left-color: #28a745; }
        .btn { padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:hover { opacity: 0.9; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; border: 1px solid #dee2e6; }
        h1 { color: #007bff; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚙️ PayLekker API Configuration</h1>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <p><strong>Current API:</strong> <?php echo API_BASE_URL; ?></p>
        <p><strong>Configuration:</strong> <?php echo strtoupper($currentApiType); ?> API</p>
        
        <div class="card <?php echo $currentApiType === 'local' ? 'current' : ''; ?>">
            <h3>🏠 Local API (Recommended)</h3>
            <p><strong>Uses:</strong> Your own API files in the <code>/api/</code> directory</p>
            <p><strong>Benefits:</strong></p>
            <ul>
                <li>Full control and customization</li>
                <li>No external dependencies</li>
                <li>Works offline</li>
                <li>Perfect for development and hackathons</li>
            </ul>
            <?php if ($currentApiType !== 'local'): ?>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="api_type" value="local">
                    <button type="submit" class="btn btn-success">Switch to Local API</button>
                </form>
            <?php else: ?>
                <span class="btn btn-secondary">Currently Active</span>
            <?php endif; ?>
        </div>
        
        <div class="card <?php echo $currentApiType === 'remote' ? 'current' : ''; ?>">
            <h3>🌐 Remote API (pay.sewdani.co.za)</h3>
            <p><strong>Uses:</strong> External API at https://pay.sewdani.co.za/api/</p>
            <p><strong>Status:</strong> <span style="color: red;">⚠️ Currently returning 403 Forbidden errors</span></p>
            <p><strong>Issues:</strong></p>
            <ul>
                <li>Server is blocking requests</li>
                <li>May require special authentication</li>
                <li>Not reliable for development</li>
            </ul>
            <?php if ($currentApiType !== 'remote'): ?>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="api_type" value="remote">
                    <button type="submit" class="btn btn-primary">Switch to Remote API</button>
                </form>
            <?php else: ?>
                <span class="btn btn-secondary">Currently Active</span>
            <?php endif; ?>
        </div>
        
        <div style="background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3>🎯 Quick Setup Guide</h3>
            <p>To get your PayLekker app working immediately:</p>
            <ol>
                <li><strong>Switch to Local API</strong> (recommended button above)</li>
                <li><strong>Set up database:</strong> Visit <a href="web_setup.php" target="_blank">web_setup.php</a></li>
                <li><strong>Test your app:</strong> Try registering a new user</li>
            </ol>
        </div>
        
        <div style="margin: 30px 0;">
            <a href="api_diagnostic.php" class="btn btn-primary">🔍 Run API Diagnostic</a>
            <a href="register.php" class="btn btn-success">👤 Test Registration</a>
            <a href="web_setup.php" class="btn btn-secondary">🗄️ Database Setup</a>
        </div>
        
        <details style="margin: 20px 0;">
            <summary><strong>Current config.php content</strong></summary>
            <pre><?php echo htmlspecialchars($currentConfig); ?></pre>
        </details>
    </div>
</body>
</html>