<?php
/**
 * PayLekker API - Web Database Setup
 * Navigate to this file in your browser to set up the database
 * 
 * URL: https://pay.sewdani.co.za/web_setup.php
 */

// Security: Only allow setup if not already done
// You should delete this file after running it once!

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker Database Setup</title>
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
        pre { background: #f8f9fa; padding: 15px; border-radius: 6px; overflow-x: auto; }
        .btn { 
            background: #007AFF; color: white; padding: 12px 24px; 
            border: none; border-radius: 6px; cursor: pointer; 
            text-decoration: none; display: inline-block; margin: 10px 5px;
        }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏦 PayLekker Database Setup</h1>
            <p>Initialize your PayLekker API database</p>
        </div>

        <?php
        if (isset($_POST['setup_database'])) {
            echo '<div class="status info"><strong>🔄 Setting up database...</strong></div>';
            
            try {
                require_once __DIR__ . '/config/setup.php';
                $setup = new DatabaseSetup();
                $success = $setup->createTables();
                
                if ($success) {
                    echo '<div class="status success">';
                    echo '<h3>✅ Database Setup Completed Successfully!</h3>';
                    echo '<p><strong>Tables Created:</strong></p>';
                    echo '<ul>';
                    echo '<li><strong>users</strong> - User accounts with secure authentication</li>';
                    echo '<li><strong>sessions</strong> - JWT token management</li>';
                    echo '<li><strong>transactions</strong> - Money transfer records</li>';
                    echo '<li><strong>budgets</strong> - Financial budgeting system</li>';
                    echo '</ul>';
                    echo '</div>';
                    
                    echo '<div class="status info">';
                    echo '<h3>🚀 Your API is Ready!</h3>';
                    echo '<p>API Base URL: <code>https://pay.sewdani.co.za/api/</code></p>';
                    echo '<p><strong>Available Endpoints:</strong></p>';
                    echo '<pre>';
                    echo 'POST /api/auth/register  - User registration' . "\n";
                    echo 'POST /api/auth/login     - User login' . "\n";
                    echo 'POST /api/auth/logout    - User logout' . "\n";
                    echo 'POST /api/transfer       - Send money' . "\n";
                    echo 'GET  /api/transactions   - Transaction history' . "\n";
                    echo 'GET  /api/budget         - Get budgets' . "\n";
                    echo 'POST /api/budget         - Create budget' . "\n";
                    echo 'POST /api/chatbot        - Chat support' . "\n";
                    echo 'GET  /api/health         - Health check' . "\n";
                    echo '</pre>';
                    echo '</div>';
                    
                    echo '<div class="status warning">';
                    echo '<h3>🔒 Important Security Notes:</h3>';
                    echo '<ul>';
                    echo '<li><strong>Delete this setup file</strong> after running it successfully</li>';
                    echo '<li>Your database credentials are configured in <code>config/database.php</code></li>';
                    echo '<li>Test your API endpoints before building your frontend</li>';
                    echo '</ul>';
                    echo '</div>';
                    
                    echo '<a href="/api/health" class="btn" target="_blank">🩺 Test Health Check</a>';
                    echo '<a href="/api/" class="btn" target="_blank">📖 View API Info</a>';
                    
                } else {
                    echo '<div class="status error">';
                    echo '<h3>❌ Database Setup Failed</h3>';
                    echo '<p>Please check your database credentials and try again.</p>';
                    echo '<p>Make sure your MySQL database is running and accessible.</p>';
                    echo '</div>';
                }
                
            } catch (Exception $e) {
                echo '<div class="status error">';
                echo '<h3>❌ Setup Error</h3>';
                echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<p>Please check your database configuration in <code>config/database.php</code></p>';
                echo '</div>';
            }
            
        } else {
            // Show setup form
            ?>
            
            <div class="status info">
                <h3>📋 Before You Begin</h3>
                <p>Make sure you have:</p>
                <ul>
                    <li>✅ Uploaded all PayLekker API files to your server</li>
                    <li>✅ MySQL database ready with credentials: <code>pnjdogwh_pay</code></li>
                    <li>✅ Database user: <code>pnjdogwh_pay</code></li>
                    <li>✅ Database password: <code>Boris44$$$</code></li>
                </ul>
            </div>

            <div class="status warning">
                <h3>⚠️ Security Warning</h3>
                <p>This setup script should only be run once. <strong>Delete this file after successful setup</strong> to prevent unauthorized access to your database setup.</p>
            </div>

            <form method="post" style="text-align: center; margin: 30px 0;">
                <button type="submit" name="setup_database" class="btn" style="font-size: 18px; padding: 15px 30px;">
                    🚀 Initialize PayLekker Database
                </button>
            </form>

            <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <h4>🛠️ Manual Setup (Alternative)</h4>
                <p>If you prefer command line setup, SSH into your server and run:</p>
                <pre>cd /path/to/your/payLekker/files
php setup_database.php</pre>
            </div>
            
            <?php
        }
        ?>
    </div>
</body>
</html>