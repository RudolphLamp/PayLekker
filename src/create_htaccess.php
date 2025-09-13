<?php
/**
 * PayLekker API - .htaccess Generator
 * Run this script to create the .htaccess file in the api folder
 * 
 * Navigate to: https://pay.sewdani.co.za/create_htaccess.php
 * This will create the necessary .htaccess file for URL rewriting and security
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker .htaccess Generator</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            max-width: 800px; margin: 50px auto; padding: 20px; 
            background: #f5f5f7; color: #333;
        }
        .container { 
            background: white; padding: 40px; border-radius: 12px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header { color: #007AFF; text-align: center; margin-bottom: 30px; }
        .status { padding: 15px; border-radius: 8px; margin: 20px 0; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 6px; overflow-x: auto; font-size: 12px; }
        .btn { 
            background: #007AFF; color: white; padding: 12px 24px; 
            border: none; border-radius: 6px; cursor: pointer; 
            text-decoration: none; display: inline-block; margin: 10px 5px;
        }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ PayLekker .htaccess Generator</h1>
            <p>Create the necessary .htaccess file for your API</p>
        </div>

        <?php
        if (isset($_POST['create_htaccess'])) {
            
            // Define the .htaccess content
            $htaccessContent = <<<'HTACCESS'
# PayLekker API .htaccess
# URL rewriting and security configuration

RewriteEngine On

# Security Headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
Header always set Content-Security-Policy "default-src 'self'"

# CORS Headers
Header always set Access-Control-Allow-Origin "*"
Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
Header always set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"
Header always set Access-Control-Max-Age "86400"

# Handle preflight OPTIONS requests
RewriteCond %{REQUEST_METHOD} OPTIONS
RewriteRule ^(.*)$ $1 [R=200,L]

# API Routes - direct all requests to index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Block access to sensitive files
<FilesMatch "\.(htaccess|htpasswd|ini|log|sh|sql|conf)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Block access to config and includes directories
RedirectMatch 404 /\.git
RedirectMatch 404 /config/
RedirectMatch 404 /includes/

# Prevent access to PHP files in certain directories
<LocationMatch "/(config|auth)/.*\.php$">
    Order Allow,Deny
    Deny from all
</LocationMatch>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE application/json
</IfModule>

# Cache static files
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType application/json "access plus 0 seconds"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>

# Limit request size (10MB)
LimitRequestBody 10485760

# Directory browsing off
Options -Indexes

# Follow symbolic links
Options +FollowSymLinks

# Default charset
AddDefaultCharset UTF-8
HTACCESS;

            $apiDir = __DIR__ . '/api';
            $htaccessPath = $apiDir . '/.htaccess';
            
            // Create api directory if it doesn't exist
            if (!is_dir($apiDir)) {
                if (!mkdir($apiDir, 0755, true)) {
                    echo '<div class="status error">';
                    echo '<h3>❌ Failed to Create Directory</h3>';
                    echo '<p>Could not create the api directory. Please check permissions.</p>';
                    echo '</div>';
                    exit;
                }
            }
            
            // Write the .htaccess file
            if (file_put_contents($htaccessPath, $htaccessContent)) {
                echo '<div class="status success">';
                echo '<h3>✅ .htaccess File Created Successfully!</h3>';
                echo '<p><strong>Location:</strong> <code>' . $htaccessPath . '</code></p>';
                echo '<p><strong>File Size:</strong> ' . number_format(strlen($htaccessContent)) . ' bytes</p>';
                echo '</div>';
                
                echo '<div class="status info">';
                echo '<h3>🔧 What This File Does:</h3>';
                echo '<ul>';
                echo '<li><strong>URL Rewriting:</strong> Routes API requests to index.php</li>';
                echo '<li><strong>Security Headers:</strong> XSS protection, content type validation</li>';
                echo '<li><strong>CORS Support:</strong> Cross-origin requests for frontend apps</li>';
                echo '<li><strong>File Protection:</strong> Blocks access to sensitive files</li>';
                echo '<li><strong>Compression:</strong> Reduces bandwidth usage</li>';
                echo '<li><strong>Access Control:</strong> Prevents directory browsing</li>';
                echo '</ul>';
                echo '</div>';
                
                echo '<div class="status warning">';
                echo '<h3>🚀 Next Steps:</h3>';
                echo '<ol>';
                echo '<li>Test your API endpoints to make sure routing works</li>';
                echo '<li>Upload all your other API files to the <code>/api/</code> folder</li>';
                echo '<li>Run the database setup if you haven\'t already</li>';
                echo '<li><strong>Delete this generator file</strong> for security</li>';
                echo '</ol>';
                echo '</div>';
                
                // Show the contents for verification
                echo '<div style="margin-top: 30px;">';
                echo '<h4>📄 Generated .htaccess Contents:</h4>';
                echo '<pre>' . htmlspecialchars($htaccessContent) . '</pre>';
                echo '</div>';
                
                // Test links
                echo '<div style="text-align: center; margin: 30px 0;">';
                echo '<a href="/api/" class="btn" target="_blank">🧪 Test API Root</a>';
                echo '<a href="/api/health" class="btn" target="_blank">🩺 Test Health Check</a>';
                echo '</div>';
                
            } else {
                echo '<div class="status error">';
                echo '<h3>❌ Failed to Create .htaccess File</h3>';
                echo '<p>Could not write to <code>' . $htaccessPath . '</code></p>';
                echo '<p>Please check that:</p>';
                echo '<ul>';
                echo '<li>The directory has write permissions</li>';
                echo '<li>There\'s enough disk space</li>';
                echo '<li>No existing .htaccess file is write-protected</li>';
                echo '</ul>';
                echo '</div>';
            }
            
        } else {
            // Show the form
            ?>
            
            <div class="status info">
                <h3>📋 About This Tool</h3>
                <p>This script creates a <code>.htaccess</code> file in your <code>/api/</code> directory. This file is essential for:</p>
                <ul>
                    <li>🔄 <strong>URL Rewriting:</strong> Makes clean API URLs work (e.g., <code>/api/auth/login</code>)</li>
                    <li>🛡️ <strong>Security:</strong> Adds protection headers and blocks sensitive files</li>
                    <li>🌐 <strong>CORS:</strong> Allows frontend apps to connect to your API</li>
                    <li>⚡ <strong>Performance:</strong> Enables compression and caching</li>
                </ul>
            </div>

            <div class="status warning">
                <h3>⚠️ Before You Continue</h3>
                <p>Make sure:</p>
                <ul>
                    <li>✅ You have uploaded your PayLekker API files</li>
                    <li>✅ Your server supports <code>.htaccess</code> files (Apache)</li>
                    <li>✅ You have write permissions in the current directory</li>
                </ul>
            </div>

            <form method="post" style="text-align: center; margin: 30px 0;">
                <button type="submit" name="create_htaccess" class="btn" style="font-size: 18px; padding: 15px 30px;">
                    🚀 Create .htaccess File
                </button>
            </form>

            <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <h4>🔍 Current Directory Structure</h4>
                <p><strong>Current Directory:</strong> <code><?php echo __DIR__; ?></code></p>
                <p><strong>Will create:</strong> <code><?php echo __DIR__ . '/api/.htaccess'; ?></code></p>
                
                <?php
                $apiDir = __DIR__ . '/api';
                if (is_dir($apiDir)) {
                    echo '<p>✅ <code>/api/</code> directory exists</p>';
                } else {
                    echo '<p>ℹ️ <code>/api/</code> directory will be created</p>';
                }
                
                if (file_exists($apiDir . '/.htaccess')) {
                    echo '<p>⚠️ <code>.htaccess</code> file already exists (will be overwritten)</p>';
                }
                ?>
            </div>
            
            <?php
        }
        ?>
    </div>
</body>
</html>