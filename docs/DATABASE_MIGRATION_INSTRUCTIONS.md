# Database Migration Instructions

The error you're seeing is because the database schema needs to be updated to support the mini game challenges.

## Quick Fix

Run this command in your terminal from the PayLekker/src directory:

```bash
cd /Users/rudolph/Documents/PayLekker/src
php migrate_challenge_type.php
```

## What this fixes:

1. **ENUM Column Issue**: The `challenge_type` column only supported 'daily', 'weekly', 'one_time', 'milestone' but we need 'mini_game'
2. **Missing Table**: Creates the `mini_game_progress` table needed for tracking user progress
3. **Missing Column**: Adds `target_value` column if missing
4. **Schema Compatibility**: Makes the requirements column nullable for mini game challenges

## Alternative Fix (Manual)

If you prefer to run the SQL manually:

```sql
-- Update challenge_type ENUM
ALTER TABLE game_challenges 
MODIFY COLUMN challenge_type ENUM('daily', 'weekly', 'one_time', 'milestone', 'mini_game') 
DEFAULT 'daily';

-- Add target_value column if missing
ALTER TABLE game_challenges ADD COLUMN target_value INT DEFAULT 0;

-- Make requirements nullable
ALTER TABLE game_challenges MODIFY COLUMN requirements JSON NULL;

-- Create mini_game_progress table
CREATE TABLE IF NOT EXISTS mini_game_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    current_score INT DEFAULT 0,
    high_score INT DEFAULT 0,
    total_games_played INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user (user_id)
);
```

## Testing

After running the migration, refresh the game page and click on the "Mini Game" tab. The challenges should now load without errors.

## Verification

You can run the test script to verify everything is working:

```bash
php test_mini_game_setup.php
```

This will check all the database components and create sample challenges if they don't exist.