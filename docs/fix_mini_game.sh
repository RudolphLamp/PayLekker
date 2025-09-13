#!/bin/bash

echo "🔧 PayLekker Mini Game Database Fix"
echo "=================================="
echo ""
echo "Running database migration to fix mini game challenges..."
echo ""

cd /Users/rudolph/Documents/PayLekker/src

# Run the database migration
php migrate_challenge_type.php

echo ""
echo "✅ Database migration completed!"
echo ""
echo "🎮 You can now test the mini game challenges:"
echo "1. Go to your game page"
echo "2. Click on the 'Mini Game' tab"
echo "3. The challenges should now load without errors"
echo ""
echo "If you still see errors, check the browser console for more details."