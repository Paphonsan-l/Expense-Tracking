-- Migration Script: Update avatar column to profile_image
-- Run this script if you have existing database

USE expense_tracking;

-- Check if avatar column exists and profile_image doesn't
-- Add profile_image column if needed
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS profile_image VARCHAR(255) DEFAULT NULL AFTER full_name;

-- Copy data from avatar to profile_image if avatar exists
UPDATE users 
SET profile_image = avatar 
WHERE avatar IS NOT NULL AND profile_image IS NULL;

-- Drop avatar column if exists (optional - uncomment if you want to remove old column)
-- ALTER TABLE users DROP COLUMN IF EXISTS avatar;

-- Verify the change
-- SELECT id, username, email, full_name, profile_image, created_at FROM users LIMIT 5;

SELECT 'Migration completed successfully!' as message;
