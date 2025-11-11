-- Add new columns to the registrations table
ALTER TABLE `registrations` 
ADD COLUMN `branch` VARCHAR(100) NULL AFTER `registered_at`,
ADD COLUMN `year` VARCHAR(20) NULL AFTER `branch`,
ADD COLUMN `college` VARCHAR(200) NULL AFTER `year`;

-- Update existing NULL records if needed (optional)
-- UPDATE `registrations` SET `branch` = 'Not specified', `year` = 'Not specified', `college` = 'Not specified' WHERE `branch` IS NULL;

-- This comment explains what this update does:
-- 1. Adds three new columns to track participant academic information:
--    - branch: For department or field of study
--    - year: For current year of study
--    - college: For institution name
-- 2. All columns are nullable to maintain backward compatibility with existing data 