<?php
/**
 * PayLekker API - Database Setup Script
 * Run this script to initialize the database tables
 * 
 * Usage: php setup_database.php
 */

require_once __DIR__ . '/config/setup.php';

echo "PayLekker Database Setup\n";
echo "========================\n\n";

echo "Connecting to database...\n";
$setup = new DatabaseSetup();

echo "Creating tables...\n";
$success = $setup->createTables();

if ($success) {
    echo "\n✅ Database setup completed successfully!\n";
    echo "\nTables created:\n";
    echo "- users (for user accounts)\n";
    echo "- sessions (for JWT token management)\n";
    echo "- transactions (for money transfers)\n";
    echo "- budgets (for budget management)\n";
    
    echo "\n📝 Next steps:\n";
    echo "1. Upload the API files to pay.sewdani.co.za\n";
    echo "2. Test the endpoints using a REST client\n";
    echo "3. Build your frontend application\n";
    
    echo "\n🔗 API Endpoints:\n";
    echo "POST /api/auth/register - User registration\n";
    echo "POST /api/auth/login - User login\n";
    echo "POST /api/auth/logout - User logout\n";
    echo "POST /api/transfer - Send money\n";
    echo "GET /api/transactions - Get transaction history\n";
    echo "GET /api/budget - Get budgets\n";
    echo "POST /api/budget - Create budget\n";
    echo "POST /api/chatbot - Chat with support\n";
    echo "GET /api/health - Health check\n";
    
} else {
    echo "\n❌ Database setup failed! Check the error messages above.\n";
}

echo "\n";
?>